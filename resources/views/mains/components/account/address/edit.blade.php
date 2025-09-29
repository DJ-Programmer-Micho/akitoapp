@extends('mains.layout.app')
@section('business-content')
<div class="main">
    <x-mains.components.account.image-header-one />
    <x-mains.components.account.nav-one />
    <div class="container address">
        <form method="POST" action="{{ route('customer.addresses.update', ['locale' => app()->getLocale(), 'addressId' => $address->id]) }}">
            @csrf
            @method('PUT')
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
                                <option value="Apartment" {{ $address->type === 'Apartment' ? 'selected' : '' }}>Apartment</option>
                                <option value="House" {{ $address->type === 'House' ? 'selected' : '' }}>House</option>
                                <option value="Office" {{ $address->type === 'Office' ? 'selected' : '' }}>Office</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-12">
                        <div class="form-group">
                            <label for="address_name">Address Name</label>
                            <input type="text" name="address_name" class="form-control" value="{{ old('address_name', $address->address_name) }}" required>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-12" id="buildingNameDiv">
                        <div class="form-group">
                            <label for="building_name">Building Name</label>
                            <input type="text" name="building_name" class="form-control" value="{{ old('building_name', $address->building_name) }}">
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-12" id="aptOrCompanyDiv">
                        <div class="form-group">
                            <label for="apt_or_company">Apartment No./Company</label>
                            <input type="text" name="apt_or_company" class="form-control" value="{{ old('apt_or_company', $address->apt_or_company) }}">
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-12" id="floorDiv">
                        <div class="form-group">
                            <label for="floor">Floor</label>
                            <input type="text" name="floor" class="form-control" value="{{ old('floor', $address->floor) }}">
                        </div>
                    </div>
                </div>
            </div>
            <div class="card card-dashboard">
                <div class="row custom-row p-3">
                    <div class="col-md-4 col-sm-12">
                        <div class="form-group">
                            <label for="country">Country *</label>
                            <input id="country_selector" name="country" class="form-control" required value="{{ old('country', $address->country) }}">
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-12">
                        <div class="form-group">
                            <label for="city">City</label>
                            <input type="text" name="city" class="form-control" value="{{ old('city', $address->city) }}" required>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-12">
                        <div class="form-group">
                            <label for="address">Address</label>
                            <input type="text" name="address" class="form-control" value="{{ old('address', $address->address) }}" required>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-12">
                        <div class="form-group">
                            <label for="zip_code">Zip Code</label>
                            <input type="text" name="zip_code" class="form-control" value="{{ old('zip_code', $address->zip_code) }}" required>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-12">
                        <div class="form-group">
                            <label for="phone_number">Phone Number</label>
                            <input type="text" name="phone_number" class="form-control" value="{{ old('phone_number', $address->phone_number) }}" required>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card card-dashboard">
                <div class="row custom-row p-3">
                    <div class="col-md-6 col-sm-12">
                        <div class="form-group">
                            <label for="address_label">Address Label (Optional)</label>
                            <input type="text" name="address_label" class="form-control" value="{{ old('address_label', $address->address_label) }}">
                        </div>
                        <div class="form-group">
                            <label for="additional_directions">Additional Directions (Optional)</label>
                            <textarea name="additional_directions" class="form-control">{{ old('additional_directions', $address->additional_directions) }}</textarea>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12">
                        <div class="form-group">
                            <label for="additional_directions">Map</label>
                            <div id="map" style="height: 400px; width: 100%;"></div>
                            <!-- Hidden Fields for Latitude and Longitude -->
                            <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude', $address->latitude) }}" required>
                            <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude', $address->longitude) }}" required>
                        </div>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary mt-3">Save Address</button>
        </form>
    </div>

    @push('geo')
    <link rel="stylesheet" href="{{ asset('main/assets/lib/country_select/countrySelect.min.css') }}">
    <script src="{{ asset('main/assets/lib/country_select/countrySelect.min.js') }}"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{ app('gmap') }}&callback=initMap" async defer></script>
    <script>
        let map, marker;

        function initMap() {
            const zones = @json($zones);
            const defaultLat = {{ $address->latitude ?? 36.2222 }}; // Default latitude
            const defaultLng = {{ $address->longitude ?? 43.9953 }}; // Default longitude

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
                navigator.geolocation.getCurrentPosition(position => {
                    const pos = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                    };

                    // Center the map on the user's location
                    map.setCenter(pos);
                    marker.setPosition(pos); // Move the marker to user's location
                    document.getElementById('latitude').value = pos.lat;
                    document.getElementById('longitude').value = pos.lng;
                });
            }
        }

        $(document).ready(function () {
            $('#addressType').change(function () {
                const selectedType = $(this).val();

                // Show or hide fields based on selected address type
                $('#buildingNameDiv').toggle(selectedType === 'Apartment');
                $('#aptOrCompanyDiv').toggle(selectedType === 'Apartment' || selectedType === 'Office');
                $('#floorDiv').toggle(selectedType === 'Apartment');
            });

            // Initialize country selector
            $("#country_selector").countrySelect({
                preferredCountries: ['iq', 'us', 'gb'],
                onCountryChange: function (countryData) {
                    console.log("Selected country: ", countryData.name);
                }
            });
        });
    </script>
    @endpush
</div>
@endsection
