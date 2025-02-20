<?php

namespace App\Http\Controllers\Api\V1\Main;

use App\Models\Zone;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use App\Models\Transaction;
use App\Models\DiscountRule;
use Illuminate\Http\Request;
use App\Models\PaymentMethods;
use App\Models\CustomerAddress;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CheckoutOldControllerApi extends Controller
{
    public function getOrdersList(Request $request)
    {
        $customerId = Auth::guard('sanctum')->user()->id ?? null;
        $orders = Order::where('customer_id', $customerId)
            ->orderBy('created_at', 'DESC')
            ->paginate(10);

        return response()->json([
            'orders' => $orders->map(function ($order) {
                return [
                    'id' => $order->id,
                    'tracking_number' => $order->tracking_number,
                    'total_amount' => number_format($order->total_amount, 2, '.', ''),
                    'payment_method' => $order->payment_method,
                    'payment_status' => $order->payment_status,
                    'status' => $order->status,
                    'created_at' => $order->created_at->format('Y-m-d'),
                    'updated_at' => $order->updated_at->format('Y-m-d'),
                ];
            }),
        ]);
    }

    public function getOrderDetails(Request $request, $orderId)
    {
        $customerId = Auth::guard('sanctum')->user()->id ?? null;
        $addressId = $request->query('address_id');
        $paymentId = $request->query('payment_id');

        $order = Order::where('customer_id', $customerId)
            ->where('id', $orderId)
            ->with('orderItems.product.variation.images')
            ->first();
    
        if (!$order) {
            return response()->json(['message' => 'Order not found.'], 404);
        }

        // Fetch selected address and payment method (use request values if provided, otherwise use order values)
        $address = CustomerAddress::where('customer_id', $customerId)->where('id', $addressId)->first() ?? $order;
        $paymentMethod = PaymentMethods::find($paymentId) ?? PaymentMethods::where('name', $order->payment_method)->first();
    
        if (!$paymentMethod) {
            return response()->json(['message' => 'Invalid payment method.'], 400);
        }

        $totalPrice = 0;
        $totalDiscount = 0;
        $orderItems = [];
    
        foreach ($order->orderItems as $item) {
            $product = $item->product;
            $discountDetails = $this->calculateFinalPrice($product, $customerId);
    
            $productPrice = $discountDetails['customer_discount_price'] ?? $discountDetails['discount_price'] ?? $discountDetails['base_price'];
    
            $orderItems[] = [
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
    
        // Fetch the correct zone-based shipping fee
        $latitude = $address->latitude;
        $longitude = $address->longitude;
        $deliveryCharge = 0;
    
        $zones = Zone::all();
        foreach ($zones as $zone) {
            $polygon = json_decode($zone->coordinates, true);
            if (pointInPolygon($latitude, $longitude, $polygon)) {
                $deliveryCharge = $zone->delivery_cost;
                break;
            }
        }
    
        // Payment Transaction Fee Calculation
        $transactionFeePercentage = $paymentMethod->transaction_fee ?? 0;
        $transactionFee = ($totalPrice * $transactionFeePercentage) / 100;
    
        // Final Total Calculation
        $grandTotal = $totalPrice + $deliveryCharge + $transactionFee;
    
        return response()->json([
            'order_data' => [
                'tracking_number' => $order->tracking_number,
                'payment_method' => $paymentMethod->name,
            ],
            'order_items' => $orderItems,
            'subtotal' => number_format($totalPrice, 2, '.', ''),
            'payment_fee' => number_format($transactionFeePercentage, 2, '.', ''),
            'shipping_fee' => number_format($deliveryCharge, 2, '.', ''),
            'grand_total' => number_format($grandTotal, 2, '.', ''),
        ]);
    }
    
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

    public function cancelOrder(Request $request, $orderId)
    {
        try {
            $customerId = Auth::guard('sanctum')->user()->id ?? null;
            $order = Order::where('customer_id', $customerId)->where('id', $orderId)->first();

            if (!$order) {
                return response()->json(['message' => 'Order not found.'], 404);
            }

            if ($order->status == 'canceled') {
                return response()->json(['message' => 'Order is already canceled.'], 400);
            }

            // Update order status
            $order->update([
                'status' => 'canceled',
                'payment_status' => 'failed',
            ]);

            // If there’s a transaction linked, mark it as declined
            $transaction = Transaction::where('order_id', $orderId)->first();
            if ($transaction) {
                $transaction->update([
                    'status' => 'declined',
                    'response' => ['reason' => $request->input('reason', 'User Cancelled')],
                ]);
            }

            return response()->json(['message' => 'Order canceled successfully.']);
        } catch (\Exception $e) {
            return response()->json(['error' => '⚠️ Internal server error'], 500);
        }
    }
}
