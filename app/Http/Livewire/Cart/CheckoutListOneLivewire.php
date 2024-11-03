<?php

namespace App\Http\Livewire\Cart;

use App\Models\Zone;
use App\Models\Product;
use Livewire\Component;
use App\Models\CartItem;
use App\Models\DiscountRule;
use App\Models\ShippingCost;
use App\Models\PaymentMethods;
use App\Models\CustomerAddress;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
    public $manualePaymentStatus;
    public $deliveryCharge;
    public $inZone;


    protected $listeners = ['addToCartList','cartListUpdated' => 'loadCartList'];

    public function mount()
    {
        $this->digitPaymentStatus = null;
        $this->loadaddresses();
        
        if ($this->addressList->isNotEmpty()) {
            $this->addressSelected = $this->addressList->first()->id;
            $this->selectAddress($this->addressSelected);
            $this->loadZoneData();
        }
        
        $this->loadPayments();
        
        if ($this->paymentList->isNotEmpty()) {
            $this->paymentSelected = 2;
            $this->selectPayment($this->paymentSelected);        
        }

        $this->loadCartList();
        $this->calculateTotals();
    }
    
    protected function loadZoneData()
    {
        // Get the coordinates of the selected address
        $selectedCoordinates = $this->getSelectedAddressCoordinates();
        if (!$selectedCoordinates) {
            $this->dispatchBrowserEvent('alert', ['type' => 'error', 'message' => __('Address coordinates not found.')]);
            return;
        }
    
        $latitude = $selectedCoordinates[0];
        $longitude = $selectedCoordinates[1];
    
        // Get all zones from the database
        $zones = Zone::all();
        // Loop through each zone and check if the address is inside the zone
        foreach ($zones as $zone) {
            $polygon = json_decode($zone->coordinates, true); // Decode the JSON coordinates
            
            if (pointInPolygon($latitude, $longitude, $polygon)) {
                // Address is inside this zone, set COD payment status
                $this->digitPaymentStatus = $zone->digit_payment;
                $this->manualePaymentStatus = $zone->cod_payment;

                if($this->paymentSelected) {
                    if($this->manualePaymentStatus == 0) {
                        $this->paymentSelected = 1;
                    } else {
                        $this->paymentSelected = 2;
                    }
                }
                $this->deliveryCharge = $zone->delivery_cost;
                
                $this->inZone = true;
                $this->selectPayment($this->paymentSelected);
                $this->loadPayments();
                return; // Exit the loop once a matching zone is found
            }
        }
    
        // If no zone matched, reset COD payment status
        $this->digitPaymentStatus = null;
        $this->inZone = false;
        // Alert if no matching zone found
        $this->dispatchBrowserEvent('alert', ['type' => 'error', 'message' => __('No matching zone found.')]);
    }
    

    private function getSelectedAddressCoordinates()
    {
        // Retrieve coordinates based on the selected address
        $address = CustomerAddress::find($this->addressSelected);
        if($address->latitude && $address->longitude) {
            $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => __('Address Has Been Updated')]);
            try {
                $this->loadPayments();
                return [$address->latitude, $address->longitude];
            } catch (\Exception $e) {
                $this->dispatchBrowserEvent('alert', ['type' => 'error',  'message' => __('Something Went Wrong, CODE_3311')]);
            }
        } else {
            $this->dispatchBrowserEvent('alert', ['type' => 'error',  'message' => __('Something Went Wrong, CODE_3310')]);
        }
    }
    
    public function loadaddresses() {
        $this->addressList = CustomerAddress::where('customer_id', Auth::guard('customer')->user()->id)->get();
    }

    public function selectAddress($addressId)
    {
        $this->addressSelected = $addressId;
        $this->loadZoneData(); // Load zone data after selecting a new address
        // $this->calculateShippingCosts(); // Update shipping costs based on the new address
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

    private function calculateFinalPrice($product, $customerId) {
        // Use the original price and discount price if available
        $basePrice = $product->variation->price; // Original price
        $discountPrice = $product->variation->discount ?? $basePrice; // Use discounted price if applicable

        // Initialize total discount percentage
        $totalDiscountPercentage = 0;

        // Check for applicable discounts
    
        if ($customerId) {
            // Fetch discount rules for the customer
            $discountRules = DiscountRule::where('customer_id', $customerId)
                ->where(function ($query) use ($product) {
                    $query->where('product_id', $product->id)
                        ->orWhere('category_id', $product->categories->first()->id)
                        ->orWhere('sub_category_id',$product->subCategories->first()->id) // Assuming you have this relation
                        ->orWhere('brand_id', $product->brand_id);
                })
                ->get();

            // Iterate through the discount rules and accumulate applicable discounts
            foreach ($discountRules as $rule) {
                // Sum discounts for the same product, brand, category, and subcategory
                if ($rule->product_id === $product->id && $rule->type === 'product') {
                    $totalDiscountPercentage += (float) $rule->discount_percentage; // Product discount
                }

                if ($rule->brand_id === $product->brand_id && $rule->type === 'brand') {
                    $totalDiscountPercentage += (float) $rule->discount_percentage; // Brand discount
                }

                if ($rule->category_id === $product->categories->first()->id && $rule->type === 'category') {
                    $totalDiscountPercentage += (float) $rule->discount_percentage; // Category discount
                }

                if ($rule->sub_category_id === $product->subCategories->first()->id && $rule->type === 'subcategory') {
                    $totalDiscountPercentage += (float) $rule->discount_percentage; // Subcategory discount
                }
            }
        }

        // Ensure the total discount percentage does not exceed 100%
        $totalDiscountPercentage = min($totalDiscountPercentage, 100);

        // Calculate the final customer discount price based on the total applicable discounts
        $customerDiscountPrice = $discountPrice * (1 - ($totalDiscountPercentage / 100));
        return [
            'base_price' => $basePrice,
            'discount_price' => $discountPrice,
            'customer_discount_price' => $customerDiscountPrice,
            'total_discount_percentage' => $totalDiscountPercentage
        ];
    }
    
    public function loadCartList()
    {
        $locale = app()->getLocale(); // Get the current locale
        $customerId = Auth::guard('customer')->id();

        $this->cartListItems = CartItem::where('customer_id', Auth::guard('customer')->id())
            ->with(['product' => function ($query) use ($locale) {
                $query->with(['productTranslation' => function ($subQuery) use ($locale) {
                    // Fetch the translation for the current locale
                    $subQuery->where('locale', $locale);
                }, 'variation', 
        'categories.categoryTranslation' => function ($categoryQuery) use ($locale) {
            // Optional: Filter category translations by locale if needed
            $categoryQuery->where('locale', $locale);
        },
        'variation.images' => function ($imageQuery) {
            // Filter images to include only those with priority 0 or is_primary 1
            $imageQuery->where(function ($query) {
                $query->where('priority', 0)
                      ->orWhere('is_primary', 1);
            });
        }]);
            }])
            ->get()
            ->transform(function ($cartListItems) use ($customerId) {
                $product = $cartListItems->product;
        
                // Calculate the discount for the product
                $discountDetails = $this->calculateFinalPrice($product, $customerId);
        
                // Assign calculated discount details to the product
                $product->base_price = $discountDetails['base_price'];
                $product->discount_price = $discountDetails['discount_price'];
                $product->customer_discount_price = $discountDetails['customer_discount_price'];
                $product->total_discount_percentage = $discountDetails['total_discount_percentage'];
        
                return $cartListItems;
            })
            ->toArray();
    
        $this->calculateTotals(); // Calculate totals after loading cart items
    }
    
        // is for calculating from COMPANY to CUSTOMER HOUSE (KM DISTANCE)
    // public function calculateShippingCosts()
    // {
    //     // Company address coordinates
    //     $companyLat = 36.22202;
    //     $companyLng = 43.99596;

    //     // Get the selected address coordinates
    //     $address = CustomerAddress::find($this->addressSelected);
    //     if (!$address) {
    //         $this->deliveryCharge = 0; // No delivery charge if no address is selected
    //         return;
    //     }

    //     $customerLat = $address->latitude;
    //     $customerLng = $address->longitude;

    //     // Call Google Distance Matrix API
    //     $apiKey = 'AIzaSyCQuIFgYGBzpKpzzp3puSrqzL6uK7sXiTo'; // Ensure you have this in your .env file
    //     $url = "https://maps.googleapis.com/maps/api/distancematrix/json?units=metric&origins={$customerLat},{$customerLng}&destinations={$companyLat},{$companyLng}&key={$apiKey}";

    //     $response = Http::get($url);
    //     $data = $response->json();

    //     if ($data['status'] == 'OK' && isset($data['rows'][0]['elements'][0]['distance']['value'])) {
    //         $distanceInMeters = $data['rows'][0]['elements'][0]['distance']['value'];

    //         // Fetch shipping cost settings
    //         $shippingCost = ShippingCost::first(); // Assuming you have one shipping cost configuration
    //         if ($shippingCost) {
    //             // Calculate the delivery charge based on the first km and additional km costs
    //             if ($distanceInMeters <= 1000) {
    //                 $this->deliveryCharge = $shippingCost->first_km_cost;
    //             } else {
    //                 $additionalKm = ceil(($distanceInMeters - 1000) / 1000); // Additional kilometers
    //                 $this->deliveryCharge = $shippingCost->first_km_cost + ($additionalKm * $shippingCost->additional_km_cost);
    //             }

    //             // Free delivery if the charge exceeds the specified amount
    //             if ($this->deliveryCharge >= $shippingCost->free_delivery_over) {
    //                 $this->deliveryCharge = 0;
    //             }
    //         } else {
    //             $this->deliveryCharge = 5; // Default charge if no shipping cost configuration exists
    //         }
    //     } else {
    //         $this->deliveryCharge = 5; // Default charge if API call fails
    //     }
    // }
    
    // FOR THE DISCOUNT CALCULATIONS TOTAL
    public function calculateTotals()
    {
        $this->totalListQuantity = array_sum(array_column($this->cartListItems, 'quantity'));
        
        // Initialize total price and total discount
        $this->totalListPrice = 0;
        $this->totalDiscount = 0;
        
        // Loop through the cart items to calculate total price and discount
        foreach ($this->cartListItems as $item) {
            // Initialize variables for pricing
            $basePrice = $item['product']['variation']['price'] ?? 0;
            $discountPrice = $item['product']['variation']['discount'] ?? $basePrice;
            $customerDiscountPrice = $item['product']['customer_discount_price'] ?? null;
    
            // Determine the price to use based on priority
            $finalPrice = $customerDiscountPrice ?? $discountPrice;
    
            // Calculate total discount if applicable
            if ($customerDiscountPrice && $customerDiscountPrice < $basePrice) {
                $this->totalDiscount += ($basePrice - $customerDiscountPrice) * $item['quantity'];
            } elseif ($discountPrice < $basePrice) {
                $this->totalDiscount += ($basePrice - $discountPrice) * $item['quantity'];
            }
    
            // Add the product price multiplied by quantity to the total price
            $this->totalListPrice += $item['quantity'] * $finalPrice;
        }
    }
    
    public function render()
    {
        if($this->totalListPrice > 100){
            $this->deliveryCharge = 0;
        }
        return view('mains.components.livewire.cart.checkout-list-one', [
            'cartListItems' => $this->cartListItems,
            'totalListQuantity' => $this->totalListQuantity,
            'totalListPrice' => $this->totalListPrice,
            'totalDiscount' => $this->totalDiscount,
        ]);
    }
}
