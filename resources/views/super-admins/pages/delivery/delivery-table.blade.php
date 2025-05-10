<div class="page-content">
    @include('super-admins.pages.delivery.delivery-form')

    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">{{__('Delivery Zones')}}</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">{{__('Dashboard')}}</a></li>
                            <li class="breadcrumb-item active">{{__('Delivery Zones')}}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->
        <div class="row">
            <div class="col-6">
                <div class="card p-3">
                    <div>
                        <h3>{{ __('Instructions') }}</h3>
                        <p>{{ __('Create zone by click on map and connect the dots together') }}</p>
                        <ul>
                            <li><i class="fa-regular fa-hand-pointer"></i> {{ __('Use this to drag map to find proper area') }}</li>
                            <li><i class="fa-solid fa-circle-nodes"></i> {{ __('Click this icon to start pin points in the map and connect them to draw a zone . Minimum 3 points required') }}</li>
                        </ul>
                    </div>
                    <hr class="my-3">
                    <form wire:submit.prevent="storeZone">
                        <div class="form-group mb-3">
                            <label for="name">{{ __('Zone Name') }}</label>
                            <input type="text" id="name" class="form-control" wire:model="name" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="name">{{ __('Delivery Cost') }}</label>
                            <input type="number" id="delivery_cost" class="form-control" wire:model="delivery_cost" step="250" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="name">{{ __('Delivary Team') }}</label>
                            <select class="form-control" wire:model="delivery_team" required>
                                <option value="" selected>{{__('Select Delivary Team')}}</option>
                                @foreach ($teamList as $team)
                                    <option value="{{$team->id}}">{{$team->team_name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="digit_payment" wire:model="digit_payment">
                                <label class="form-check-label" for="digit_payment">{{__('Digital Payment')}}</label>
                            </div>

                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="cod_payment" wire:model="cod_payment" style="margin-bottom: 1rem;">
                                <label class="form-check-label" for="cod_payment">{{__('Cash On Delivery')}}</label>
                            </div>
                        </div>
                        <input type="hidden" id="coordinates" wire:model="coordinates">
                        <button type="submit" class="btn btn-primary">{{ __('Save Zone') }}</button>
                    </form>
                    <!-- Button to update coordinates -->
                </div>
            </div>
            <div class="col-6">
                <div wire:ignore class="card">
                    <div id="map"></div>
                    <div id="map-canvas" style="height: 500px; width: 100%;"></div>
                </div>
                <button id="update-coordinates" 
                class="btn btn-secondary w-100"
                wire:click="updateZone"
                @if(is_null($selectedZoneId)) disabled @endif>
                {{ __('Update Coordinates') }}
            </button>
            </div>
        </div>

        <div class="card">
            <div class="card-header border-0">
                <div class="row g-4">
                    <div class="col-sm-auto">
                        <div>
                            {{-- <a href="apps-ecommerce-add-product.html" class="btn btn-success" id="addproduct-btn"><i class="ri-add-line align-bottom me-1"></i> Add Product</a> --}}
                        </div>
                    </div>
                    <div class="col-sm">
                        <div class="d-flex justify-content-sm-end">
                            <div class="search-box ms-2">
                                <input type="search" wire:model="search" class="form-control" id="searchProductList" placeholder="{{__('Search Brands...')}}">
                                <i class="ri-search-line search-icon"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <ul class="nav nav-tabs-custom card-header-tabs border-bottom-0" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link @if($statusFilter === 'all') active @endif" style="cursor: pointer" 
                                        wire:click="changeTab('all')" 
                                       role="tab">
                                        {{__('All')}} 
                                        <span class="badge bg-danger-subtle text-danger align-middle rounded-pill ms-1">{{ $activeCount + $nonActiveCount}}</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link @if($statusFilter === 'active') active @endif" style="cursor: pointer"
                                        wire:click="changeTab('active')" 
                                       role="tab">
                                        {{__('Active')}} 
                                        <span class="badge bg-danger-subtle text-danger align-middle rounded-pill ms-1">{{ $activeCount }}</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link @if($statusFilter === 'non-active') active @endif" style="cursor: pointer"
                                       wire:click="changeTab('non-active')"
                                       role="tab">
                                        {{__('Non-Active')}}
                                        <span class="badge bg-danger-subtle text-danger align-middle rounded-pill ms-1">{{ $nonActiveCount }}</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            <!-- end card header -->
            <div class="card-body">
                @if ($zones->isNotEmpty())
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover table-sm">
                        <thead>
                            <tr>
                                <th>{{__('ID')}}</th>
                                <th>{{__('Name')}}</th>
                                <th class="text-center">{{__('Delivery Team')}}</th>
                                <th class="text-center">{{__('Delivery Cost (IQD)')}}</th>
                                <th class="text-center">{{__('Digital Payment')}}</th>
                                <th class="text-center">{{__('Cash On Delivery Payment')}}</th>
                                <th class="text-center">{{__('Status')}}</th>
                                <th class="text-center">{{__('Action')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($zones as $index => $data)
                                <tr wire:key="zone-{{ $data->id }}">
                                    <td class="align-middle text-center">
                                        {{ $index + 1 }}
                                    </td>
                                    <td class="align-middle text-center">
                                        {{ $data->name }}
                                    </td>
                                    <td class="align-middle text-center">
                                        {{ $data->driverTeam->team_name }}
                                    </td>
                                    <td class="align-middle text-center">
                                        <div class="d-flex justify-content-center align-items-center">
                                            <input type="text" id="cost_{{ $data->id }}" value="{{ number_format($data->delivery_cost, 0) }}" class="form-control bg-dark text-white" style="max-width: 120px">
                                            <button type="button" class="btn btn-success btn-icon"  onclick="updateCostValue({{ $data->id }})">
                                                {{ __('IQD') }}
                                            </button>
                                        </div>
                                    </td>
                                    <td class="align-middle text-center">
                                        <div class="form-check form-switch align-middle text-center">
                                            <input class="form-check-input" type="checkbox" id="update_digit_payment" wire:click="updateDigPayment({{ $data->id }})" {{ $data->digit_payment ? 'checked' : '' }}>
                                        </div>
                                    </td>
                                    <td class="align-middle text-center">
                                        <div class="form-check form-switch align-middle text-center">
                                            <input class="form-check-input" type="checkbox" id="update_cod_payment" wire:click="updateCodPayment({{ $data->id }})" {{ $data->cod_payment ? 'checked' : '' }}>
                                        </div>
                                    </td>
                                    <td class="align-middle text-center">
                                        <span class="badge {{ $data->status ? 'bg-success' : 'bg-danger' }} p-2" style="font-size: 0.7rem;">
                                            {{ $data->status ? __('Active') : __('Non-Active') }}
                                        </span>
                                    </td>
                                    <td class="align-middle text-center">
                                        <span>
                                            <div class="dropdown"><button
                                                    class="btn btn-soft-secondary btn-sm dropdown"
                                                    type="button" data-bs-toggle="dropdown"
                                                    aria-expanded="false"><i
                                                        class="ri-more-fill"></i></button>
                                                <ul class="dropdown-menu dropdown-menu-end" style="">
                                                    <li><button class="dropdown-item" type="button" wire:click="updateStatus({{ $data->id }})">
                                                        {{-- <i class="codicon align-bottom me-2 text-muted"></i> --}}
                                                        @if ( $data->status == 1)
                                                        <span class="text-danger"><i class="fa-solid fa-xmark me-2"></i> {{__('De-Active')}}</span>
                                                        @else
                                                        <span class="text-success"><i class="fa-solid fa-check me-2"></i> {{__('Active')}}</span>
                                                        @endif
                                                        </button>
                                                    </li>
                                                    <li><button type="button" class="dropdown-item edit-list" onclick="editZone({{ $data->id }})">
                                                        <i class="fa-regular fa-pen-to-square me-2"></i>{{__('Edit Zone')}}</button>
                                                    </li>
                                                    <li class="dropdown-divider"></li>
                                                    <li><button type="button" class="dropdown-item edit-list" data-bs-toggle="modal" data-bs-target="#deleteZoneModal" wire:click="removeZone({{ $data->id }})">
                                                        <i class="fa-regular fa-trash-can me-2"></i>{{__('Delete')}}</button>
                                                    </li>

                                                </ul>
                                            </div>
                                        </span>
                                    </td>
                                </tr>
                            @empty
                            <div class="tab-pane">
                                <div class="py-4 text-center">
                                    <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop" colors="primary:#405189,secondary:#0ab39c" style="width:72px;height:72px">
                                    </lord-icon>
                                    <h5 class="mt-4">{{ __('Sorry! No Result Found') }}</h5>
                                </div>
                            </div>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{-- {{ $zones->links('') }} --}}
                </div>
                @else
                <div class="tab-pane">
                    <div class="py-4 text-center">
                        <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop" colors="primary:#405189,secondary:#0ab39c" style="width:72px;height:72px">
                        </lord-icon>
                        <h5 class="mt-4">{{ __('Sorry! No Result Found') }}</h5>
                    </div>
                </div>
                @endif
            <!-- end card body -->
            </div>
            </div>
        </div>


    </div>
</div>
@push('materialsScripts')
{{-- <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCQuIFgYGBzpKpzzp3puSrqzL6uK7sXiTo&callback=initMap" async defer></script> --}}
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCQuIFgYGBzpKpzzp3puSrqzL6uK7sXiTo&callback=initialize&libraries=drawing,places&v=3.49" async defer></script>
<script>
let map;
let drawingManager;
let lastPolygon;
let savedPolygons = []; // Track all polygons on the map

// Initialize the map and drawing manager
function initialize() {
    const defaultLocation = { lat: 36.1340360117696, lng: 43.92909996000821 };
    map = new google.maps.Map(document.getElementById("map-canvas"), {
        center: defaultLocation,
        zoom: 13,
    });

    // Initialize the DrawingManager
    drawingManager = new google.maps.drawing.DrawingManager({
        drawingMode: google.maps.drawing.OverlayType.POLYGON,
        drawingControl: true,
        drawingControlOptions: {
            drawingModes: [google.maps.drawing.OverlayType.POLYGON]
        },
        polygonOptions: {
            editable: true,
            fillColor: '#038cfc', // Default color for new polygons
            strokeColor: '#000000',
            strokeOpacity: 0.8,
            strokeWeight: 2,
        }
    });
    drawingManager.setMap(map);

    // Listen for when a new polygon is drawn
    google.maps.event.addListener(drawingManager, 'overlaycomplete', function(event) {
        if (event.type === google.maps.drawing.OverlayType.POLYGON) {
            addNewPolygon(event.overlay);
        }
    });

    // Load saved polygons and labels
    loadSavedZones();

    // Fit the map based on the zones
    adjustMapView(savedPolygons);
}

// Add a new polygon and attach event listeners
function addNewPolygon(polygon) {
    lastPolygon = polygon;
    polygon.setEditable(true);
    savedPolygons.push(polygon); // Store the newly created polygon
    attachPolygonEditListener(polygon);
    updatePolygonCoords.call(polygon.getPath()); // Immediately send the coords
}

// Attach listeners to handle polygon editing
function attachPolygonEditListener(polygon) {
    google.maps.event.addListener(polygon.getPath(), 'set_at', updatePolygonCoords);
    google.maps.event.addListener(polygon.getPath(), 'insert_at', updatePolygonCoords);
    google.maps.event.addListener(polygon.getPath(), 'remove_at', updatePolygonCoords);
}

function editZone(zoneId) {
    resetPolygonStyles();

    let selectedPolygon = savedPolygons.find(polygon => {
        return polygon.zoneId === zoneId; 
    });

    if (selectedPolygon) {
        selectedPolygon.setEditable(true);
        selectedPolygon.setOptions({ fillColor: '#FFFF00' }); // Highlight in yellow
        attachPolygonEditListener(selectedPolygon);
        updatePolygonCoords.call(selectedPolygon.getPath(), zoneId);

        // Set the selectedZoneId in Livewire
        @this.set('selectedZoneId', zoneId);
    } else {
        alert('Zone not found');
    }
}
function resetPolygonStyles() {
    savedPolygons.forEach(polygon => {
        polygon.setEditable(false); // Disable editing
        // Reset the fill color to its original state
        polygon.setOptions({ fillColor: polygon.originalFillColor || '#038cfc' }); // Use original color
    });
}
// Update the coordinates of the polygon and send them to Livewire
function updatePolygonCoords() {
    let polygonCoords = this.getArray().map(coord => ({
        lat: coord.lat(),
        lng: coord.lng()
    }));
    @this.set('coordinates', polygonCoords); // Send the updated coordinates to Livewire
}

// Load saved polygons and their labels onto the map
function loadSavedZones() {
    let savedZones = @json($zones);

    savedZones.forEach(zone => {
        let coordinates = JSON.parse(zone.coordinates).map(coord => ({
            lat: parseFloat(coord.lat),
            lng: parseFloat(coord.lng)
        }));

        let savedPolygon = new google.maps.Polygon({
            paths: coordinates,
            editable: true,
            fillColor: zone.status == 1 ? '#00FF00' : '#FF0000', // Active/Inactive
            strokeColor: '#000000',
            strokeOpacity: 0.8,
            strokeWeight: 2,
        });

        savedPolygon.zoneId = zone.id;
        savedPolygon.originalFillColor = zone.status == 1 ? '#00FF00' : '#FF0000';
        savedPolygon.setMap(map);

        savedPolygons.push(savedPolygon);
        attachPolygonEditListener(savedPolygon);
        addLabelToPolygon(savedPolygon, zone.name); // Add label with zone name
    });
}

// Add a label to the center of the polygon
function addLabelToPolygon(polygon, labelText) {
    const bounds = new google.maps.LatLngBounds();
    polygon.getPath().forEach(point => bounds.extend(point));

    const labelMarker = new google.maps.Marker({
        position: bounds.getCenter(),
        map: map,
        label: {
            text: labelText,
            color: "#000",
            fontSize: "14px",
            fontWeight: "bold",
        },
        icon: {
            path: google.maps.SymbolPath.CIRCLE,
            scale: 0, // Hide the actual marker, just show the label
        }
    });

    return labelMarker;
}

// Adjust the map's viewport to fit the polygons on the map
function adjustMapView(polygons) {
    if (polygons.length === 0) return;

    let bounds = new google.maps.LatLngBounds();
    polygons.forEach(polygon => polygon.getPath().forEach(point => bounds.extend(point)));
    map.fitBounds(bounds);
}

// Event listener for updating polygon color
window.addEventListener('updatePolygonColor', event => {
    const { zoneId, fillColor } = event.detail;
    const polygonToUpdate = savedPolygons.find(polygon => polygon.zoneId === zoneId);
    
    if (polygonToUpdate) {
        polygonToUpdate.setOptions({ fillColor });
    } else {
        console.error('Polygon not found for zoneId:', zoneId);
    }
});

// Event listener for removing a polygon
window.addEventListener('removePolygon', event => {
    const { zoneId } = event.detail;
    const polygonToRemove = savedPolygons.find(polygon => polygon.zoneId === zoneId);
    
    if (polygonToRemove) {
        polygonToRemove.setMap(null); // Remove from map
        savedPolygons = savedPolygons.filter(polygon => polygon.zoneId !== zoneId); // Update savedPolygons
    } else {
        console.error('Polygon not found for zoneId:', zoneId);
    }
});

// Initialize the map when the window loads
google.maps.event.addDomListener(window, 'load', initialize);
</script>
<script>
    window.addEventListener('updatePolygonColor',event => {
        console.log('Received updatePolygonColor event:', event.detail); // Debugging
        const { zoneId, fillColor } = event.detail;

        // Find the polygon with the corresponding zoneId
        const polygonToUpdate = savedPolygons.find(polygon => polygon.zoneId === zoneId);
        
        if (polygonToUpdate) {
            console.log('Updating color for zoneId:', zoneId, 'to fillColor:', fillColor); // Debugging
            polygonToUpdate.setOptions({ fillColor: fillColor }); // Update the polygon's fill color
        } else {
            console.error('Polygon not found for zoneId:', zoneId); // Debugging
        }
    });
</script>
<script>
    window.addEventListener('removePolygon', function(event) {
    const { zoneId } = event.detail;
    
    // Find the polygon with the corresponding zoneId
    const polygonToRemove = savedPolygons.find(polygon => polygon.zoneId === zoneId);
    
    if (polygonToRemove) {
        // Remove the polygon from the map
        polygonToRemove.setMap(null);

        // Remove the polygon from the savedPolygons array
        savedPolygons = savedPolygons.filter(polygon => polygon.zoneId !== zoneId);

        console.log('Polygon with zoneId:', zoneId, 'has been removed from the map.');
    } else {
        console.error('Polygon not found for zoneId:', zoneId);
    }
});
</script>
<script>
    function updateCostValue(itemId) {
        var input = document.getElementById('cost_' + itemId);
        var updatedCost = input.value;
        @this.call('updateCost', itemId, updatedCost);
    }
</script>
@endpush
