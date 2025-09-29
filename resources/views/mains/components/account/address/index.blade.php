@extends('mains.layout.app')
@section('business-content')
<div class="main">
    <x-mains.components.account.image-header-one />
    <x-mains.components.account.nav-one />
    <div class="container address">
        <form method="POST" action="{{ route('customer.addresses.store', ['locale' => app()->getLocale()]) }}">
            @csrf
            <div class="card card-dashboard">
                <div class="row custom-row p-3">
                    <div class="col-md-4 col-sm-12">
                        <div class="form-group">
                            <label for="first_name">First Name *</label>
                            <input type="text" name="first_name" class="form-control" value="{{$fName}}" disabled>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-12">
                        <div class="form-group">
                            <label for="last_name">Last Name *</label>
                            <input type="text" name="last_name" class="form-control" value="{{$lName}}" disabled>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-12">
                        <div class="form-group">
                            <label for="email_address">Email Address *</label>
                            <input type="text" name="email_address" class="form-control" value="{{$email}}" disabled>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card card-dashboard">
                <div class="row custom-row p-3">
                    <div class="col-md-4 col-sm-12">
                        <div class="form-group">
                            <label for="type">Address Type *</label>
                            <select name="type" class="form-control" id="addressType" required>
                                <option value="Apartment">Apartment</option>
                                <option value="House">House</option>
                                <option value="Office">Office</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-12">
                        <div class="form-group">
                            <label for="address_name">Address Name</label>
                            <input type="text" name="address_name" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-12" id="buildingNameDiv">
                        <div class="form-group">
                            <label for="building_name">Building Name</label>
                            <input type="text" name="building_name" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-12" id="aptOrCompanyDiv">
                        <div class="form-group">
                            <label for="apt_or_company">Apartment No./Company</label>
                            <input type="text" name="apt_or_company" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-12" id="floorDiv">
                        <div class="form-group">
                            <label for="floor">Floor</label>
                            <input type="text" name="floor" class="form-control">
                        </div>
                    </div>
    
                </div>
            </div>
            <div class="card card-dashboard">
                <div class="row custom-row p-3">
                    <div class="col-md-4 col-sm-12">
                        <div class="form-group">
                            <label for="country">Country *</label>
                            <input id="country_selector" name="country" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-12">
                        <div class="form-group">
                            <label for="city">City</label>
                            <input type="text" name="city" class="form-control" value="{{$city}}" required>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-12">
                        <div class="form-group">
                            <label for="address">address</label>
                            <input type="text" name="address" class="form-control" value="{{$address}}" required>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-12">
                        <div class="form-group">
                            <label for="zip_code">Zip Code</label>
                            <input type="text" name="zip_code" class="form-control" value="{{$zip_code}}" required>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-12">
                        <div class="form-group">
                            <label for="phone_number">Phone Number</label>
                            <input type="text" name="phone_number" class="form-control" value="{{$phone}}" required>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card card-dashboard">
                <div class="row custom-row p-3">
                    <div class="col-md-6 col-sm-12">
                        <div class="form-group">
                            <label for="address_label">Address Label (Optional)</label>
                            <input type="text" name="address_label" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="additional_directions">Additional Directions (Optional)</label>
                            <textarea name="additional_directions" class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12">
                        <div class="form-group">
                            <label for="additional_directions">Map</label>
                            <div id="map" style="height: 400px; width: 100%;"></div>
                            
                            <!-- Hidden Fields for Latitude and Longitude -->
                            <input type="hidden" name="latitude" id="latitude" required>
                            <input type="hidden" name="longitude" id="longitude" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary mx-auto">Save Address</button>
                </div>
            </div>
        </form>
    </div>
    

    
    <!-- Include Leaflet.js -->
    @push('geo')
    <link rel="stylesheet" href="{{ asset('main/assets/lib/country_select/countrySelect.min.css') }}">
    <script src="{{ asset('main/assets/lib/country_select/countrySelect.min.js') }}"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{ app('gmap') }}&callback=initMap" async defer></script>
    <script>
        let map, marker;
        
        function initMap() {
            const zones = @json($zones);
            const defaultLat = 36.2222; // Default latitude
            const defaultLng = 43.9953; // Default longitude

            // Create a map centered at the default location
            map = new google.maps.Map(document.getElementById("map"), {
                center: { lat: defaultLat, lng: defaultLng },
                zoom: 13,
            });

            // Create a marker and add it to the map
            marker = new google.maps.Marker({
                position: { lat: defaultLat, lng: defaultLng },
                map: map,
                draggable: true, // Allow marker dragging
            });

            zones.forEach(zone => {
        const zoneCoords = JSON.parse(zone.coordinates).map(coord => ({
            lat: parseFloat(coord.lat),
            lng: parseFloat(coord.lng)
        }));

        const zonePolygon = new google.maps.Polygon({
            paths: zoneCoords,
            strokeColor: '#28a745',
            strokeOpacity: 0.2,
            strokeWeight: 1,
            fillColor: '#28a745',
            fillOpacity: 0.15,
        });

        zonePolygon.setMap(map);
    });
            // Update hidden fields when marker is dragged
            google.maps.event.addListener(marker, 'dragend', function (event) {
                document.getElementById('latitude').value = event.latLng.lat();
                document.getElementById('longitude').value = event.latLng.lng();
            });

            // Check for geolocation support
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function (position) {
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;

                        // Set map view to current location
                        const pos = { lat: lat, lng: lng };
                        map.setCenter(pos);
                        marker.setPosition(pos);
                        document.getElementById('latitude').value = lat;
                        document.getElementById('longitude').value = lng;
                    },
                    function () {
                        // Handle error
                        handleLocationError(true, map.getCenter());
                    }
                );
            } else {
                // Browser doesn't support Geolocation
                handleLocationError(false, map.getCenter());
            }
        }

        function handleLocationError(browserHasGeolocation, pos) {
            console.log(browserHasGeolocation ?
                'Error: The Geolocation service failed.' :
                'Error: Your browser doesn\'t support geolocation.');
        }
            document.addEventListener('DOMContentLoaded', function() {
            var countrySelector = $("#country_selector");
            var defaultCountry = "{{ $country }}"; // Use the country passed from the controller

            // Fetch and populate country codes
            $.getJSON("{{ asset('main/assets/lib/country_name/restcountries.json') }}", function(data) {
                var countryOptions = '';
                var iso2Code = ''; // To store the ISO2 code for the default country

                data.forEach(function(country) {
                    var code = country.cca2.toLowerCase(); // ISO2 code
                    var name = country.name.common; // Country name
                    var dialCode = country.idd.root;

                    // Check if suffixes exist and append the first suffix if available
                    if (country.idd.suffixes && country.idd.suffixes.length > 0) {
                        dialCode += country.idd.suffixes[0];
                    }

                    // Build the dropdown options
                    countryOptions += `<option value="${name}" data-dialcode="${dialCode}">${name} (+${dialCode})</option>`;

                    // Find the matching country name
                    if (name === defaultCountry) {
                        iso2Code = code; // Store the ISO2 code for the default country
                    }
                });

                // Populate the country selector with options
                countrySelector.append(countryOptions);

                // Set the default country by name
                countrySelector.val(defaultCountry).change(); // Set the default country

                // Initialize the country select plugin with the correct ISO2 code
                countrySelector.countrySelect({
                    defaultCountry: iso2Code,
                    preferredCountries: ['iq', 'sa', 'ae'] // Preferred countries to show at the top
                });

            });
        });

    </script>
    <script>
        $(document).ready(function() {
            // Function to check the selected value and show/hide fields accordingly
            function toggleFields() {
                var addressType = $('#addressType').val();
                if (addressType === 'House') {
                    $('#buildingNameDiv').hide();
                    $('#aptOrCompanyDiv').hide();
                    $('#floorDiv').hide();
                } else {
                    $('#buildingNameDiv').show();
                    $('#aptOrCompanyDiv').show();
                    $('#floorDiv').show();
                }
            }
    
            // Initial check when the page loads
            toggleFields();
    
            // Listen for changes in the address type dropdown
            $('#addressType').change(function() {
                toggleFields();
            });
        });
    </script>
    @endpush
    
    
</div>
@endsection

