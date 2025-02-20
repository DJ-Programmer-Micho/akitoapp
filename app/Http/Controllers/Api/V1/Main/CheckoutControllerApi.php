<?php

namespace App\Http\Controllers\Api\V1\Main;

use App\Models\Zone;
use App\Models\Product;
use App\Models\CartItem;
use App\Models\WebSetting;
use App\Models\DiscountRule;
use Illuminate\Http\Request;
use App\Models\PaymentMethods;
use App\Models\CustomerAddress;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CheckoutControllerApi extends Controller
{
    protected $selectedAddress;
    protected $selectedPayment;

    /**
     * Get available shipping addresses
     */
    public function getAddresses()
    {
        $customerId = Auth::guard('sanctum')->user()->id ?? null;
        $addresses = CustomerAddress::where('customer_id', $customerId)->get();

        return response()->json(['addresses' => $addresses]);
    }

    /**
     * Get available payment methods
     */
    public function getPaymentMethods()
    {
        $paymentMethods = PaymentMethods::where('active', true)->get();
        return response()->json(['payment_methods' => $paymentMethods]);
    }

    /**
     * Get cart totals including discounts and transaction fees
     */
    public function getCartTotals(Request $request)
    {
        $customerId = Auth::guard('sanctum')->user()->id ?? null;
        $addressId = $request->query('address_id');
        $paymentId = $request->query('payment_id');

        // Fetch selected address and payment method
        $address = CustomerAddress::where('customer_id', $customerId)->where('id', $addressId)->first();
        $paymentMethod = PaymentMethods::find($paymentId);

        if (!$address || !$paymentMethod) {
            return response()->json(['message' => 'Invalid address or payment method.'], 400);
        }

        // Fetch cart items
        $cartItems = CartItem::where('customer_id', $customerId)->with(['product.variation', 'product.categories', 'product.subCategories'])->get();

        $totalPrice = 0;
        $totalDiscount = 0;
        $cartData = [];

        foreach ($cartItems as $item) {
            $product = $item->product;
            $discountDetails = $this->calculateFinalPrice($product, $customerId);

            $productPrice = $discountDetails['customer_discount_price'] ?? $discountDetails['discount_price'] ?? $discountDetails['base_price'];

            $cartData[] = [
                'id' => $item->id,
                'product' => [
                    'id' => $product->id,
                    'name' => $product->productTranslation->first()->name ?? '',
                    'image' => app('cloudfront') . ($product->variation->images->first()->image_path ?? ''),
                    'price' => $discountDetails['base_price'],
                    'discount_price' => $discountDetails['customer_discount_price'] ?? $discountDetails['discount_price'],
                    'quantity' => $item->quantity,
                ],
                'quantity' => $item->quantity,
                'total_item_price' => $item->quantity * $productPrice,
            ];

            $totalPrice += $item->quantity * $productPrice;
            $totalDiscount += ($discountDetails['base_price'] - $productPrice) * $item->quantity;
        }

        // Fetch the correct zone based on address coordinates
        $latitude = $address->latitude;
        $longitude = $address->longitude;
        $deliveryCharge = 0; // Default to 0 if no matching zone is found

        $zones = Zone::all();
        foreach ($zones as $zone) {
            $polygon = json_decode($zone->coordinates, true); // Decode the JSON polygon data

            if (pointInPolygon($latitude, $longitude, $polygon)) {
                $deliveryCharge = $zone->delivery_cost;
                break; // Stop checking once a valid zone is found
            }
        }

        // Payment Transaction Fee Calculation
        $transactionFeePercentage = $paymentMethod->transaction_fee ?? 0;
        $transactionFee = ($totalPrice * $transactionFeePercentage) / 100;

        // Final Total Calculation
        $grandTotal = $totalPrice + $deliveryCharge + $transactionFee;

        return response()->json([
            'cart_items' => $cartData,
            'address_id' => $addressId,
            'payment_id' => $paymentId,
            'subtotal' => number_format($totalPrice, 2, '.', ''),
            'payment_fee' => number_format($transactionFeePercentage, 2, '.', ''),
            'shipping_fee' => number_format($deliveryCharge, 2, '.', ''),
            'grand_total' => number_format($grandTotal, 2, '.', ''),
        ]);
    }

    // HELPER FUNCTIONS

    
    private function calculateFinalPrice(Product $product, ?int $customerId): array
    {
        $basePrice = $product->variation->price;
        $discountPrice = $product->variation->discount ?? null; // If no discount, keep it null
        $customerDiscountPrice = $discountPrice ?? $basePrice; // If no discount, use base price
        $totalDiscountPercentage = 0;

        if ($customerId) {
            $discountRules = DiscountRule::where('customer_id', $customerId)
                ->where(fn($query) => $query->where('product_id', $product->id)
                    ->orWhere('category_id', optional($product->categories->first())->id)
                    ->orWhere('sub_category_id', optional($product->subCategories->first())->id)
                    ->orWhere('brand_id', $product->brand_id))
                ->get();

            foreach ($discountRules as $rule) {
                $totalDiscountPercentage += $rule->discount_percentage;
            }

            $totalDiscountPercentage = min($totalDiscountPercentage, 100);

            // Apply customer discount if applicable
            if ($totalDiscountPercentage > 0) {
                $customerDiscountPrice = $customerDiscountPrice * (1 - ($totalDiscountPercentage / 100));
            }
        }

        return [
            'base_price'              => number_format($basePrice, 2, '.', ''),
            'discount_price'          => $discountPrice !== null ? number_format($discountPrice, 2, '.', '') : null,
            'customer_discount_price' => $customerDiscountPrice != $basePrice ? number_format($customerDiscountPrice, 2, '.', '') : null,
        ];
    }
}
