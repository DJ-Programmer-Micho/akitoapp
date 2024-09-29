<?php

namespace App\Http\Livewire\Cart;

use App\Models\Zone;
use App\Models\Product;
use Livewire\Component;
use App\Models\CartItem;
use App\Models\ShippingCost;
use App\Models\PaymentMethods;
use App\Models\CustomerAddress;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class CheckoutListOneLivewire extends Component
{
    public $cartListItems = [];
    public $totalListQuantity = 0;
    public $totalListPrice = 0;
    public $totalDiscount = 0;
    public $orderNote = 0;
    public $transactionFee = 0; // New property for the transaction fee

    public $addressList;
    public $paymentList;
    
    public $addressSelected;
    public $paymentSelected;

    public $digitPaymentStatus;
    public $deliveryCharge;


    protected $listeners = ['addToCartList','cartListUpdated' => 'loadCartList'];

    public function mount()
    {
        $this->loadaddresses();
        
        if ($this->addressList->isNotEmpty()) {
            $this->addressSelected = $this->addressList->first()->id;
            $this->selectAddress($this->addressSelected);
        }
        
        $this->loadPayments();
        
        if ($this->paymentList->isNotEmpty()) {
            $this->paymentSelected = $this->paymentList->first()->id;
            $this->selectPayment($this->paymentSelected);        
        }
        $this->loadZoneData();

        $this->loadCartList();
        $this->calculateTotals();
    }
    
    protected function loadZoneData()
    {
        // Assuming you have a method to get the zone data based on the address selected
        $zone = Zone::where('coordinates', $this->getSelectedAddressCoordinates())->first();
    
        if ($zone) {
            $this->digitPaymentStatus = $zone->cod_payment; // Save the COD payment status
        } else {
            $this->digitPaymentStatus = null; // No zone found, reset COD payment status
        }
    }

    private function getSelectedAddressCoordinates()
    {
        // Retrieve coordinates based on the selected address
        $address = CustomerAddress::find($this->addressSelected);
        
        // Return coordinates if available, else null
        return $address ? [$address->latitude, $address->longitude] : null;
    }
    
    public function loadaddresses() {
        $this->addressList = CustomerAddress::where('customer_id', Auth::guard('customer')->user()->id)->get();
    }

    public function selectAddress($addressId)
    {
        $this->addressSelected = $addressId;
        $this->loadZoneData(); // Load zone data after selecting a new address
        $this->calculateShippingCosts(); // Update shipping costs based on the new address
    }


    public function loadPayments() {
        $this->paymentList = PaymentMethods::where('active', true)->get();
    }
    public function selectPayment($paymentId)
    {
        $this->paymentSelected = $paymentId;
        // Get the selected payment method
        $paymentMethod = PaymentMethods::find($paymentId);

        // Update the transaction fee
        if ($paymentMethod) {
            $this->transactionFee = $paymentMethod->transaction_fee;
        }

        // Recalculate the total
        $this->calculateTotals();
    }

    public function loadCartList()
    {
        $locale = app()->getLocale(); // Get the current locale
    
        $this->cartListItems = CartItem::where('customer_id', Auth::guard('customer')->id())
            ->with(['product' => function ($query) use ($locale) {
                $query->with(['productTranslation' => function ($subQuery) use ($locale) {
                    // Fetch the translation for the current locale
                    $subQuery->where('locale', $locale);
                }, 'variation','variation.images','categories','categories.categoryTranslation']);
            }])
            ->get()
            ->toArray();
    
        $this->calculateTotals(); // Calculate totals after loading cart items
    }
    
    // FOR THE DISCOUNT CALCULATIONS TOTAL
    public function calculateTotals()
    {
        $this->totalListQuantity = array_sum(array_column($this->cartListItems, 'quantity'));
        
        // Initialize total price and total discount
        $this->totalListPrice = 0;
        $this->totalDiscount = 0;
        
        // Loop through the cart items to calculate total price and discount
        foreach ($this->cartListItems as $item) {
            // Retrieve the current price and original price from the product's variation
            $productPrice = $item['product']['variation']['price'] ?? 0;
            $originalPrice = $item['product']['variation']['original_price'] ?? $productPrice;
    
            // Check if the product is on sale and adjust the price accordingly
            if (!empty($item['product']['variation']['on_sale'])) {
                $discountPrice = $item['product']['variation']['discount'] ?? $productPrice; // Use the discount price if available
                $this->totalDiscount += ($originalPrice - $discountPrice) * $item['quantity']; // Add discount to total discount
                $productPrice = $discountPrice; // Use the discount price for total price calculation
            }
    
            // Add the product price multiplied by quantity to the total price
            $this->totalListPrice += $item['quantity'] * $productPrice;
        }
    }

    public function calculateShippingCosts()
{
    // Company address coordinates
    $companyLat = 36.22202;
    $companyLng = 43.99596;

    // Get the selected address coordinates
    $address = CustomerAddress::find($this->addressSelected);
    if (!$address) {
        $this->deliveryCharge = 0; // No delivery charge if no address is selected
        return;
    }

    $customerLat = $address->latitude;
    $customerLng = $address->longitude;

    // Call Google Distance Matrix API
    $apiKey = 'AIzaSyCQuIFgYGBzpKpzzp3puSrqzL6uK7sXiTo'; // Ensure you have this in your .env file
    $url = "https://maps.googleapis.com/maps/api/distancematrix/json?units=metric&origins={$customerLat},{$customerLng}&destinations={$companyLat},{$companyLng}&key={$apiKey}";

    $response = Http::get($url);
    $data = $response->json();

    if ($data['status'] == 'OK' && isset($data['rows'][0]['elements'][0]['distance']['value'])) {
        $distanceInMeters = $data['rows'][0]['elements'][0]['distance']['value'];

        // Fetch shipping cost settings
        $shippingCost = ShippingCost::first(); // Assuming you have one shipping cost configuration
        if ($shippingCost) {
            // Calculate the delivery charge based on the first km and additional km costs
            if ($distanceInMeters <= 1000) {
                $this->deliveryCharge = $shippingCost->first_km_cost;
            } else {
                $additionalKm = ceil(($distanceInMeters - 1000) / 1000); // Additional kilometers
                $this->deliveryCharge = $shippingCost->first_km_cost + ($additionalKm * $shippingCost->additional_km_cost);
            }

            // Free delivery if the charge exceeds the specified amount
            if ($this->deliveryCharge >= $shippingCost->free_delivery_over) {
                $this->deliveryCharge = 0;
            }
        } else {
            $this->deliveryCharge = 5; // Default charge if no shipping cost configuration exists
        }
    } else {
        $this->deliveryCharge = 5; // Default charge if API call fails
    }
}

    
    public function render()
    {
        return view('mains.components.livewire.cart.checkout-list-one', [
            'cartListItems' => $this->cartListItems,
            'totalListQuantity' => $this->totalListQuantity,
            'totalListPrice' => $this->totalListPrice,
            'totalDiscount' => $this->totalDiscount,
        ]);
    }
}
