<?php

namespace App\Http\Controllers\Customer;

use App\Models\Zone;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;


class CustomerAddressController extends Controller
{

    public function index(){
        $customer = Auth::guard('customer')->user();
        $zones = Zone::all();
        return view('mains.components.account.address.index', [
            'fName' => $customer->customer_profile->first_name,
            'lName' => $customer->customer_profile->last_name,
            'email' => $customer->email,
            'country' => $customer->customer_profile->country,
            'city' => $customer->customer_profile->city,
            'address' => $customer->customer_profile->address,
            'zip_code' => $customer->customer_profile->zip_code,
            'phone' => $customer->customer_profile->phone_number,
            'zones' => $zones
        ]);
    }

    public function store(Request $request)
    {
        // try {
            //code...
            // Check if the customer already has 5 addresses
            $customer = Auth::guard('customer')->user();// Assuming customer is logged in
            if ($customer->customer_addresses()->count() >= 5) {
                return back()->with('alert', [
                    'type' => 'error', 
                'message' => 'You can only have up to 5 delivery addresses..'
            ]);;
        }
    
        // Validate the request
        $validated = $request->validate([
            'type' => 'required|in:Apartment,House,Office',
            'building_name' => 'nullable|string|max:255',
            'apt_or_company' => 'nullable|string|max:255',
            'floor' => 'nullable|string|max:255',
            'country' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'zip_code' => 'required|string|max:255',
            'phone_number' => 'required|string|max:15|regex:/^\+?[0-9]{10,15}$/', // Example phone validation
            'additional_directions' => 'nullable|string',
            'address_label' => 'nullable|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ], [
            'type.required' => 'Please select the type of address.',
            'phone_number.required' => 'A valid phone number is required.',
            'phone_number.regex' => 'Please enter a valid phone number.',
            'latitude.required' => 'Latitude is required for geolocation.',
            'longitude.required' => 'Longitude is required for geolocation.',
        ]);
    
        $isAvailable = $this->checkZoneAvailability($validated['latitude'], $validated['longitude']);

        if (!$isAvailable) {
            return back()->withInput()->with('alert', [
                'type' => 'error', 
                'message' => 'The provided address is not within our service area.'
            ]);
        }


        // Create the new address
        $customer->customer_addresses()->create($validated);
    
        // Return back with success message
        return redirect()->route('business.account', ['locale' => app()->getLocale()])->with('alert', [
            'type' => 'success', 
            'message' => 'Address added successfully.'
        ]);
        // } catch (\Exception $e) {
        //     dd('input form',$e);
        // }
    }
    
    private function checkZoneAvailability($latitude, $longitude)
    {
        // Get all zones from the database
        $zones = DB::table('zones')->get();
    
        foreach ($zones as $zone) {
            // First decode the string to remove extra escaping
            $coordinates = json_decode($zone->coordinates, true); // Decode the JSON
    
            // If the decoded coordinates is a string, decode again
            if (is_string($coordinates)) {
                $coordinates = json_decode($coordinates, true); // Second decoding for proper structure
            }
    
    
            // Ensure the decoded coordinates are valid and an array
            if (is_array($coordinates) && count($coordinates) > 0) {
                // Check if the point is inside the polygon
                if ($this->isPointInPolygon($latitude, $longitude, $coordinates)) {
                    return true; // Address is within the zone
                }
            } else {
                // Log::error('Invalid or empty zone coordinates for zone ID: ' . $zone->id);
            }
        }
    
        return false; // Address is not within any zone
    }
    
    
    
    private function isPointInPolygon($lat, $lng, $polygon)
    {
        $numPoints = count($polygon);
        $j = $numPoints - 1;
        $inside = false;
    
        for ($i = 0; $i < $numPoints; $i++) {
            // Replace indexed array access with 'lat' and 'lng'
            if (
                ($polygon[$i]['lng'] > $lng != $polygon[$j]['lng'] > $lng) &&
                ($lat < ($polygon[$j]['lat'] - $polygon[$i]['lat']) * ($lng - $polygon[$i]['lng']) / ($polygon[$j]['lng'] - $polygon[$i]['lng']) + $polygon[$i]['lat'])
            ) {
                $inside = !$inside;
            }
            $j = $i;
        }
    
        return $inside;
    }
    
    
    
}
