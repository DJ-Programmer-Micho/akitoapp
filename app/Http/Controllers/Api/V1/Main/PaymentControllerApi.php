<?php

namespace App\Http\Controllers\Api\V1\Main;

use App\Models\Order;
use App\Models\Product;
use App\Models\CartItem;
use App\Models\OrderItem;
use Illuminate\Support\Str;
use App\Models\DiscountRule;
use Illuminate\Http\Request;
use App\Models\PaymentMethods;
use App\Models\CustomerAddress;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\PaymentServiceManager;

class PaymentControllerApi extends Controller
{
    /**
     * Handles the checkout process and payment initiation.
     */
    public function checkoutChecker(Request $request)
    {
        if (!Auth::guard('sanctum')->check()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $customer = Auth::guard('sanctum')->user();
        if ($customer->status != 1) {
            return response()->json(['error' => 'Invalid customer'], 403);
        }

        try {
            // ✅ Validate Request Data
            $validatedData = $request->validate([
                'shipping_amount' => 'required|numeric|min:0',
                'total_amount' => 'required|numeric|min:0',
                'address_id' => 'required|integer|exists:customer_addresses,id',
                'payment_id' => 'required|integer|exists:payment_methods,id',
            ]);

            // ✅ Retrieve Active Payment Method
            $paymentMethod = PaymentMethods::where('id', $validatedData['payment_id'])
                ->where('active', 1)
                ->first();

            if (!$paymentMethod) {
                return response()->json(['error' => 'Selected payment method is not available.'], 400);
            }

            // ✅ Retrieve Customer Details
            $customerProfile = $customer->customer_profile;
            $customerAddress = CustomerAddress::where('id', $validatedData['address_id'])
                ->where('customer_id', $customer->id)
                ->first();

            if (!$customerAddress) {
                return response()->json(['error' => 'Invalid address.'], 400);
            }

            // ✅ Retrieve Cart Items & Apply Discounts
            $cartItems = CartItem::with('product', 'product.variation', 'product.productTranslation')
                ->where('customer_id', $customer->id)
                ->get()
                ->transform(function ($item) use ($customer) {
                    $product = $item->product;
                    $discountDetails = $this->calculateFinalPrice($product, $customer->id);
                    $item->final_price = $discountDetails['customer_discount_price'] ?? $discountDetails['discount_price'] ?? $discountDetails['base_price'];
                    return $item;
                });

            if ($cartItems->isEmpty()) {
                return response()->json(['error' => 'Your cart is empty.'], 400);
            }

            DB::beginTransaction();

            // ✅ Generate Tracking Number
            $trackingNumber = strtoupper(Str::random(6));

            // ✅ Create Order
            $order = Order::create([
                'customer_id' => $customer->id,
                'first_name' => $customerProfile->first_name,
                'last_name' => $customerProfile->last_name,
                'email' => $customer->email,
                'country' => $customerAddress->country,
                'city' => $customerAddress->city,
                'address' => $customerAddress->address,
                'zip_code' => $customerAddress->zip_code,
                'latitude' => $customerAddress->latitude,
                'longitude' => $customerAddress->longitude,
                'phone_number' => $customerAddress->phone_number,
                'payment_method' => $paymentMethod->name,
                'payment_status' => 'pending',
                'status' => 'pending',
                'tracking_number' => $trackingNumber,
                'shipping_amount' => $validatedData['shipping_amount'],
                'total_amount' => $validatedData['total_amount'],
            ]);

            // ✅ Store Order Items
            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'product_name' => $item->product->productTranslation[0]->name,
                    'price' => $item->final_price,
                    'total' => $item->quantity * $item->final_price,
                ]);
            }

            // ✅ Handle Cash On Delivery (COD)
            if ($paymentMethod->online == 0) {
                DB::commit();
                CartItem::where('customer_id', $customer->id)->delete();
                return response()->json([
                    'message' => 'Order placed successfully',
                    'order_id' => $order->id,
                    'status' => 'pending',
                ]);
            }

            // ✅ Online Payment Processing
            $paymentService = PaymentServiceManager::getInstance()
                ->setOrder($order)
                ->setPaymentMethod($paymentMethod->name)
                ->setAmount($order->total_amount);

            $paymentResponse = $paymentService->processPayment();

            if (!$paymentResponse || !isset($paymentResponse['paymentId'])) {
                Log::error("PaymentServiceManager returned an invalid response!");
                DB::rollBack();
                return response()->json(['error' => 'Payment processing failed.'], 500);
            }

            // ✅ Commit Order and Proceed to Payment Gateway
            DB::commit();
            CartItem::where('customer_id', $customer->id)->delete();

            return response()->json([
                'message' => 'Proceed to payment',
                'order_id' => $order->id,
                'payment_url' => route('payment.process', [
                    'locale' => app()->getLocale(),
                    'orderId' => $order->id,
                    'paymentId' => $paymentResponse['paymentId'],
                    'paymentMethod' => $paymentMethod->id,
                ]),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Checkout Error: " . $e->getMessage());
            return response()->json(['error' => 'Payment processing failed.'], 500);
        }
    }

    /**
     * Calculate the final price with discounts
     */
    private function calculateFinalPrice(Product $product, ?int $customerId): array
    {
        $basePrice = $product->variation->price;
        $discountPrice = $product->variation->discount ?? null;
        $customerDiscountPrice = $discountPrice ?? $basePrice;
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

            if ($totalDiscountPercentage > 0) {
                $customerDiscountPrice = $customerDiscountPrice * (1 - ($totalDiscountPercentage / 100));
            }
        }

        return [
            'base_price' => number_format($basePrice, 2, '.', ''),
            'discount_price' => $discountPrice !== null ? number_format($discountPrice, 2, '.', '') : null,
            'customer_discount_price' => $customerDiscountPrice != $basePrice ? number_format($customerDiscountPrice, 2, '.', '') : null,
        ];
    }
}
