<?php

namespace App\Http\Livewire\Cart;

use App\Models\Zone;
use App\Models\Order;
use App\Models\Product;
use Livewire\Component;
use App\Models\OrderItem;
use App\Models\WebSetting;
use App\Models\DiscountRule;
use App\Models\PaymentMethods;
use App\Models\CustomerAddress;
use Illuminate\Support\Facades\Auth;

class CheckoutListOldOneLivewire extends Component
{
    public $totalListQuantity = 0;
    public $totalListPrice = 0;
    public $totalDiscount = 0;
    public $orderNote = 0;
    public $transactionFee = 0;
    
    public $addressList;
    public $paymentList;
    
    public $deliveryLimit;
    public $addressSelected;
    public $paymentSelected;

    public $digitPaymentStatus;
    public $manualePaymentStatus;
    public $deliveryCharge;
    public $inZone;

    public $orderId;
    public $order;
    public $orderItemsArray = [];

    public $exchange_rate;

    protected $casts = [
        'orderItemsArray' => 'array'
    ];

    public function mount($orderId)
    {
        $this->exchange_rate = config('currency.exchange_rate');
        $this->orderId = $orderId;
        $this->digitPaymentStatus = null;
        $this->loadAddresses();
        $this->loadOrderItems();
        $this->loadPayments();

        if ($this->addressList->isNotEmpty()) {
            $this->addressSelected = $this->addressList->first()->id;
            $this->selectAddress($this->addressSelected);
            $this->loadZoneData();
        }

        if ($this->order && $this->order->payment_method) {
            // Get the ID of the payment method by matching its name
            $paymentMethod = PaymentMethods::where('name', $this->order->payment_method)->first();
    
            if ($paymentMethod) {
                $this->paymentSelected = $paymentMethod->id; // Assign the correct payment method ID
            } else {
                $this->paymentSelected = 2; // Default to 2 if no match is found
            }
        } else {
            $this->paymentSelected = 2; // Default to payment method 2
        }
        $this->selectPayment($this->paymentSelected);
        $this->deliveryLimit = WebSetting::find(1)->free_delivery;
    }

    protected function loadZoneData()
    {
        $selectedCoordinates = $this->getSelectedAddressCoordinates();
        if (!$selectedCoordinates) {
            return;
        }

        $latitude = $selectedCoordinates[0];
        $longitude = $selectedCoordinates[1];

        $zones = Zone::all();
        foreach ($zones as $zone) {
            $polygon = json_decode($zone->coordinates, true);
            if (pointInPolygon($latitude, $longitude, $polygon)) {
                $this->digitPaymentStatus = $zone->digit_payment;
                $this->manualePaymentStatus = $zone->cod_payment;
                $this->paymentSelected = ($this->manualePaymentStatus == 0) ? 1 : 2;
                $this->deliveryCharge = $zone->delivery_cost;
                $this->inZone = true;
                $this->selectPayment($this->paymentSelected);
                $this->loadPayments();
                return;
            }
        }

        $this->digitPaymentStatus = null;
        $this->inZone = false;
    }

    private function getSelectedAddressCoordinates()
    {
        $address = CustomerAddress::find($this->addressSelected);
        if ($address->latitude && $address->longitude) {
            $this->dispatchBrowserEvent('alert', ['type' => 'success', 'message' => __('Address Has Been Updated')]);
            return [$address->latitude, $address->longitude];
        } else {
            $this->dispatchBrowserEvent('alert', ['type' => 'error', 'message' => __('Invalid Address')]);
        }
    }

    public function loadAddresses()
    {
        $this->addressList = CustomerAddress::where('customer_id', Auth::guard('customer')->user()->id)->get();
    }

    public function selectAddress($addressId)
    {
        $this->addressSelected = $addressId;
        $this->loadZoneData();
    }

    public function loadPayments()
    {
        $this->paymentList = PaymentMethods::where('active', true)->get();
    }

    public function selectPayment($paymentId)
    {
        $this->paymentSelected = $paymentId;
        $paymentMethod = PaymentMethods::find($paymentId);
        if ($paymentMethod) {
            $this->transactionFee = $paymentMethod->transaction_fee;
        }
        $this->digitPaymentStatus = $paymentId;
        $this->calculateTotals($this->orderItemsArray);
    }

    public function loadOrderItems()
    {
        $this->order = Order::with([
            'orderItems.product.variation.images',
            'orderItems.product.categories',
            'orderItems.product.productTranslation'
        ])->find($this->orderId);
    
        if (!$this->order || $this->order->customer_id !== Auth::guard('customer')->id()) {
            $this->dispatchBrowserEvent('alert', ['type' => 'error', 'message' => __('Order not found or unauthorized.')]);
            return;
        }
    
        $this->orderItemsArray = $this->order->orderItems->map(function ($orderItem) {
            $product = $orderItem->product;
            $variation = $product->variation;
            $productTranslation = $product->productTranslation->first();
            
            // Calculate discount prices
            $discountDetails = $this->calculateFinalPrice($product, Auth::guard('customer')->id());
            
            return [
                'product' => [
                    'id' => $product->id,
                    'variation' => [
                        'price' => $variation->price ?? 0,
                        'discount' => $variation->discount ?? 0,
                        'images' => $variation->images->map(function ($image) {
                            return ['image_path' => $image->image_path];
                        })->toArray()
                    ],
                    'productTranslation' => [
                        [
                            'name' => $productTranslation->name ?? 'Unknown'
                        ]
                    ],
                    'base_price' => $discountDetails['base_price'],
                    'discount_price' => $discountDetails['discount_price'],
                    'customer_discount_price' => $discountDetails['customer_discount_price']
                ],
                'quantity' => $orderItem->quantity
            ];
        })->toArray();
    
        $this->calculateTotals();
    }
    

    public function calculateFinalPrice($product, $customerId)
    {
        
        $basePrice = $product->variation->price ?? 0;
        $discountPrice = $product->variation->discount ?? $basePrice;
        $totalDiscountPercentage = 0;

        if ($customerId && $product->categories->first()) {
            $discountRules = DiscountRule::where('customer_id', $customerId)
                ->where(function ($query) use ($product) {
                    $query->where('product_id', $product->id)
                        ->orWhere('category_id', $product->categories->first()->id)
                        ->orWhere('brand_id', $product->brand_id);
                })->get();

            foreach ($discountRules as $rule) {
                $totalDiscountPercentage += (float) $rule->discount_percentage;
            }
        }

        $totalDiscountPercentage = min($totalDiscountPercentage, 100);
        $customerDiscountPrice = $discountPrice * (1 - ($totalDiscountPercentage / 100));

        return [
            'base_price' => $basePrice * $this->exchange_rate,
            'discount_price' => $discountPrice * $this->exchange_rate,
            'customer_discount_price' => $customerDiscountPrice * $this->exchange_rate,
            'total_discount_percentage' => $totalDiscountPercentage
        ];
    }

    public function calculateTotals()
    {
        $this->totalListQuantity = 0;
        $this->totalListPrice = 0;
        $this->totalDiscount = 0;

        foreach ($this->orderItemsArray as $item) {
            $quantity = $item['quantity'];
            $basePrice = $item['product']['base_price'];
            $finalPrice = $item['product']['customer_discount_price'] ?? $item['product']['discount_price'];
            
            $this->totalDiscount += ($basePrice - $finalPrice) * $quantity;
            $this->totalListPrice += $finalPrice * $quantity;
            $this->totalListQuantity += $quantity;
        }
    }

    public function render()
    {
        if($this->totalListPrice > $this->deliveryLimit){
            $this->deliveryCharge = 0;
        }

        $grandTotal = $this->totalListPrice + $this->deliveryCharge + ($this->totalListPrice * $this->transactionFee / 100);
        return view('mains.components.livewire.cart.checkout-list-old-one', [
            'exchangeRate' => $this->exchange_rate,
            'totalListQuantity' => $this->totalListQuantity,
            'totalListPrice' => $this->totalListPrice,
            'totalDiscount' => $this->totalDiscount,
            'grandTotal' => $grandTotal,
            'orderItems' => $this->orderItemsArray,
        ]);
    }
}
