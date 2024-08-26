
<div class="page-content">
<script src="https://code.jquery.com/jquery-3.7.1.slim.js" integrity="sha256-UgvvN8vBkgO0luPSUl2s8TIlOSYRoGFAX4jlCIm9Adc=" crossorigin="anonymous"></script>
<link rel="stylesheet" href="{{asset('texteditor/summernote-bs5.css')}}">
<script src="{{asset('texteditor/summernote-bs5.js')}}"></script>
<link rel="stylesheet" href="{{asset('texteditor/summernote-lite.css')}}">
<script src="{{asset('texteditor/summernote-lite.js')}}"></script>
<script src="{{asset('texteditor/lang/summernote-en-US.min.js')}}"></script>
<link rel="stylesheet" href="{{asset('dashboard/css/select2.css')}}">
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<style>
    .note-frame {
        color: #222 !important;
        background-color: #ccc !important;
    }
</style>
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Create Product</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Ecommerce</a></li>
                            <li class="breadcrumb-item active">Create Product</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>
        <!-- end page title -->
        <form wire:submit.prevent="productSave">
            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header" wire:ignore>
                            <ul class="nav nav-tabs-custom card-header-tabs border-bottom-0" role="tablist">
                                @foreach ($filteredLocales as $index => $locale)
                                <li class="nav-item">
                                    <a 
                                        class="nav-link {{ $index === 0 ? 'active' : '' }}" 
                                        id="tab-{{ $locale }}" 
                                        data-bs-toggle="tab" 
                                        href="#addproduct-{{ $locale }}" 
                                        role="tab"
                                    >
                                        Product Info In {{ $locale }}
                                    </a>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        <!-- end card header -->
                        
                        <div class="card-body" >
                            <div class="tab-content" >
                                @foreach ($filteredLocales as $locale)
                                <div wire:ignore.self
                                    class="tab-pane {{ $loop->first ? 'active' : '' }}" 
                                    id="addproduct-{{ $locale }}" 
                                    role="tabpanel"
                                >
                                    <div class="mb-3">
                                        <label for="products.{{ $locale }}">In {{ $locale }} Language</label>
                                        <input 
                                            type="text" 
                                            class="form-control 
                                            @error('products.' . $locale) is-invalid @enderror
                                            @if(!$errors->has('products.' . $locale) && !empty($products[$locale])) is-valid @endif"
                                            wire:model="products.{{ $locale }}" 
                                            placeholder="Product Name"
                                        >
                                        @error('products.' . $locale)
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                        
                                    <div class="mb-3" wire:ignore>
                                        <label for="product_content_{{ $locale }}">In {{ $locale }} Language</label>
                                        <div 
                                            class="summernote" 
                                            id="product_content_{{ $locale }}"
                                        >
                                            {{ $contents[$locale] }}
                                        </div>
                                        @error('contents.' . $locale)
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                @endforeach
                                <!-- end tab pane -->
                            </div>
                            <!-- end tab content -->
                        </div>
                        <!-- end card body -->
                    </div>
                    <!-- end card -->
                    <div class="card">
                        <div class="card-header" wire:ignore>
                            <ul class="nav nav-tabs-custom card-header-tabs border-bottom-0" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" data-bs-toggle="tab" href="#variation-color" role="tab">
                                        Colors
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#variation-naterial" role="tab">
                                        Materials
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#variation-size" role="tab">
                                        Sizes
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#variation-capacity" role="tab">
                                        Capacity
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#variation-sku" role="tab">
                                        SKU
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <div class="tab-content">
                                <div wire:ignore.self class="tab-pane active" id="variation-color" role="tabpanel">
                                    <button type="button" class="btn btn-success mb-3" wire:click="addColorSelect">Add Color</button>
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Choose a color</th>
                                                <th>Preview</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($selectedColors as $index => $selectedColor)
                                                <tr>
                                                    <!-- Select color dropdown -->
                                                    <td>
                                                        <select class="form-select" wire:model="selectedColors.{{ $index }}.color_id">
                                                            <option value="">Select Color</option>
                                                            @foreach ($colors as $color)
                                                                <option value="{{ $color->id }}">
                                                                    {{ $color->variationColorTranslation->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <!-- Preview of the selected color -->
                                                    <td>
                                                        @if ($selectedColors[$index]['color_id'])
                                                            @php
                                                                $colorObj = $colors->find($selectedColors[$index]['color_id']);
                                                                $hexValue = $colorObj ? $colorObj->code : '#ffffff';
                                                            @endphp
                                                            <input type="color" value="{{ $hexValue }}" class="w-100" disabled>
                                                        @else
                                                            <input type="color" value="#ffffff" class="w-100" disabled>
                                                        @endif
                                                    </td>
                                                    <!-- Button to remove the color row -->
                                                    <td>
                                                        <button type="button" class="btn btn-danger" wire:click="removeColorSelect({{ $index }})">Remove</button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <!-- end tab-pane -->

                                <div wire:ignore.self class="tab-pane" id="variation-naterial" role="tabpanel">
                                    <button type="button" class="btn btn-success mb-3" wire:click="addMaterialSelect">Add Material</button>
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Choose a Material</th>
                                                <th>Preview</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($selectedMaterials as $index => $selectedMaterial)
                                                <tr>
                                                    <!-- Select color dropdown -->
                                                    <td>
                                                        <select class="form-select" wire:model="selectedMaterials.{{ $index }}.material_id">
                                                            <option value="">Select Material</option>
                                                            @foreach ($materials as $material)
                                                                <option value="{{ $material->id }}">
                                                                    {{ $material->variationMaterialeTranslation->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <!-- Preview of the selected color -->
                                                    <td>
                                                        @if ($selectedMaterials[$index]['material_id'])
                                                            @php
                                                                $codeObj = $materials->find($selectedMaterials[$index]['material_id']);
                                                                $codeValue = $codeObj ? $codeObj->code : 'Error';
                                                            @endphp
                                                            <input type="text" class="form-control" value="{{ $codeValue }}" class="w-100" disabled>
                                                        @else
                                                            <input type="text" class="form-control" value="No Code" class="w-100" disabled>
                                                        @endif
                                                    </td>
                                                    <!-- Button to remove the color row -->
                                                    <td>
                                                        <button type="button" class="btn btn-danger" wire:click="removeMaterialSelect({{ $index }})">Remove</button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <div wire:ignore.self class="tab-pane" id="variation-size" role="tabpanel">
                                    <button type="button" class="btn btn-success mb-3" wire:click="addSizeSelect">Add Size</button>
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Choose a Size</th>
                                                <th>Preview</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($selectedSizes as $index => $selectedSize)
                                                <tr>
                                                    <!-- Select color dropdown -->
                                                    <td>
                                                        <select class="form-select" wire:model="selectedSizes.{{ $index }}.size_id">
                                                            <option value="">Select size</option>
                                                            @foreach ($sizes as $size)
                                                                <option value="{{ $size->id }}">
                                                                    {{ $size->variationSizeTranslation->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <!-- Preview of the selected color -->
                                                    <td>
                                                        @if ($selectedSizes[$index]['size_id'])
                                                            @php
                                                                $codeObj = $sizes->find($selectedSizes[$index]['size_id']);
                                                                $codeValue = $codeObj ? $codeObj->code : 'Error';
                                                            @endphp
                                                            <input type="text" class="form-control" value="{{ $codeValue }}" class="w-100" disabled>
                                                        @else
                                                            <input type="text" class="form-control" value="No Code" class="w-100" disabled>
                                                        @endif
                                                    </td>
                                                    <!-- Button to remove the color row -->
                                                    <td>
                                                        <button type="button" class="btn btn-danger" wire:click="removeSizeSelect({{ $index }})">Remove</button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <!-- end tab-pane -->

                                <div wire:ignore.self class="tab-pane" id="variation-capacity" role="tabpanel">
                                    <button type="button" class="btn btn-success mb-3" wire:click="addCapacitySelect">Add Capacity</button>
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Choose a Capacity</th>
                                                <th>Preview</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($selectedCapacities as $index => $selectedCapacity)
                                                <tr>
                                                    <!-- Select color dropdown -->
                                                    <td>
                                                        <select class="form-select" wire:model="selectedCapacities.{{ $index }}.capacity_id">
                                                            <option value="">Select Capacity</option>
                                                            @foreach ($capacities as $capacity)
                                                                <option value="{{ $capacity->id }}">
                                                                    {{ $capacity->variationCapacityTranslation->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <!-- Preview of the selected color -->
                                                    <td>
                                                        @if ($selectedCapacities[$index]['capacity_id'])
                                                            @php
                                                                $codeObj = $capacities->find($selectedCapacities[$index]['capacity_id']);
                                                                $codeValue = $codeObj ? $codeObj->code : 'Error';
                                                            @endphp
                                                            <input type="text" class="form-control" value="{{ $codeValue }}" class="w-100" disabled>
                                                        @else
                                                            <input type="text" class="form-control" value="No Code" class="w-100" disabled>
                                                        @endif
                                                    </td>
                                                    <!-- Button to remove the color row -->
                                                    <td>
                                                        <button type="button" class="btn btn-danger" wire:click="removeCapacitySelect({{ $index }})">Remove</button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <!-- end tab pane -->

                                <div wire:ignore.self class="tab-pane" id="variation-sku" role="tabpanel">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="meta-title-input">Meta title</label>
                                                <input type="text" class="form-control" placeholder="Enter meta title" id="meta-title-input">
                                            </div>
                                        </div>
                                        <!-- end col -->

                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="meta-keywords-input">Meta Keywords</label>
                                                <input type="text" class="form-control" placeholder="Enter meta keywords" id="meta-keywords-input">
                                            </div>
                                        </div>
                                        <!-- end col -->
                                    </div>
                                    <!-- end row -->

                                    <div>
                                        <label class="form-label" for="meta-description-input">Meta Description</label>
                                        <textarea class="form-control" id="meta-description-input" placeholder="Enter meta description" rows="3"></textarea>
                                    </div>
                                </div>
                                <!-- end tab pane -->
                            </div>
                            <!-- end tab content -->
                        </div>
                        <!-- end card body -->
                    </div>

                    <div class="card">
                        <div class="card-header" wire:ignore>
                            <h5 class="card-title mb-0">Meta Properties</h5>
                        </div>
                        <!-- end card header -->
                        
                        <div class="card-body" >

                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="meta-title-input">Meta title</label>
                                        <input type="text" class="form-control" placeholder="Enter meta title" id="meta-title-input">
                                    </div>
                                </div>
                                <!-- end col -->

                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="meta-keywords-input">Meta Keywords</label>
                                        <input type="text" class="form-control" placeholder="Enter meta keywords" id="meta-keywords-input">
                                    </div>
                                </div>
                                <!-- end col -->
                            </div>
                            <!-- end row -->

                            <div>
                                <label class="form-label" for="meta-description-input">Meta Description</label>
                                <textarea class="form-control" id="meta-description-input" placeholder="Enter meta description" rows="3"></textarea>
                            </div>
                        </div>
                        <!-- end card body -->
                    </div>
                    {{-- <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Product Gallery</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-4">
                                <h5 class="fs-13 mb-1">Product Image</h5>
                                <p class="text-muted">Add Product main Image.</p>
                                <div class="text-center">
                                    <div class="position-relative d-inline-block">
                                        <div class="position-absolute top-100 start-100 translate-middle">
                                            <label for="product-image-input" class="mb-0" data-bs-toggle="tooltip" data-bs-placement="right" title="Select Image">
                                                <div class="avatar-xs">
                                                    <div class="avatar-title bg-light border rounded-circle text-muted cursor-pointer">
                                                        <i class="ri-image-fill"></i>
                                                    </div>
                                                </div>
                                            </label>
                                            <input class="form-control d-none" value="" id="product-image-input" type="file" accept="image/png, image/gif, image/jpeg">
                                        </div>
                                        <div class="avatar-lg">
                                            <div class="avatar-title bg-light rounded">
                                                <img src="#" id="product-img" class="avatar-md h-auto" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <h5 class="fs-13 mb-1">Product Gallery</h5>
                                <p class="text-muted">Add Product Gallery Images.</p>

                                <div class="dropzone">
                                    <div class="fallback">
                                        <input name="file" type="file" multiple="multiple">
                                    </div>
                                    <div class="dz-message needsclick">
                                        <div class="mb-3">
                                            <i class="display-4 text-muted ri-upload-cloud-2-fill"></i>
                                        </div>

                                        <h5>Drop files here or click to upload.</h5>
                                    </div>
                                </div>

                                <ul class="list-unstyled mb-0" id="dropzone-preview">
                                    <li class="mt-2" id="dropzone-preview-list">
                                        <!-- This is used as the file preview template -->
                                        <div class="border rounded">
                                            <div class="d-flex p-2">
                                                <div class="flex-shrink-0 me-3">
                                                    <div class="avatar-sm bg-light rounded">
                                                        <img data-dz-thumbnail class="img-fluid rounded d-block" src="#" alt="Product-Image" />
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <div class="pt-1">
                                                        <h5 class="fs-13 mb-1" data-dz-name>&nbsp;</h5>
                                                        <p class="fs-13 text-muted mb-0" data-dz-size></p>
                                                        <strong class="error text-danger" data-dz-errormessage></strong>
                                                    </div>
                                                </div>
                                                <div class="flex-shrink-0 ms-3">
                                                    <button data-dz-remove class="btn btn-sm btn-danger">Delete</button>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                                <!-- end dropzon-preview -->
                            </div>
                        </div>
                    </div> --}}
                    <!-- end card -->

                    {{-- <div class="card">
                        <div class="card-header">
                            <ul class="nav nav-tabs-custom card-header-tabs border-bottom-0" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" data-bs-toggle="tab" href="#addproduct-general-info" role="tab">
                                        General Info
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#addproduct-metadata" role="tab">
                                        Meta Data
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <div class="tab-content">
                                <div class="tab-pane active" id="addproduct-general-info" role="tabpanel">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="manufacturer-name-input">Manufacturer Name</label>
                                                <input type="text" class="form-control" id="manufacturer-name-input" placeholder="Enter manufacturer name">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="manufacturer-brand-input">Manufacturer Brand</label>
                                                <input type="text" class="form-control" id="manufacturer-brand-input" placeholder="Enter manufacturer brand">
                                            </div>
                                        </div>
                                    </div>
                                    <!-- end row -->

                                    <div class="row">
                                        <div class="col-lg-3 col-sm-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="stocks-input">Stocks</label>
                                                <input type="text" class="form-control" id="stocks-input" placeholder="Stocks" required>
                                                <div class="invalid-feedback">Please Enter a product stocks.</div>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 col-sm-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="product-price-input">Price</label>
                                                <div class="input-group has-validation mb-3">
                                                    <span class="input-group-text" id="product-price-addon">$</span>
                                                    <input type="text" class="form-control" id="product-price-input" placeholder="Enter price" aria-label="Price" aria-describedby="product-price-addon" required>
                                                    <div class="invalid-feedback">Please Enter a product price.</div>
                                                </div>

                                            </div>
                                        </div>
                                        <div class="col-lg-3 col-sm-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="product-discount-input">Discount</label>
                                                <div class="input-group mb-3">
                                                    <span class="input-group-text" id="product-discount-addon">%</span>
                                                    <input type="text" class="form-control" id="product-discount-input" placeholder="Enter discount" aria-label="discount" aria-describedby="product-discount-addon">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 col-sm-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="orders-input">Orders</label>
                                                <input type="text" class="form-control" id="orders-input" placeholder="Orders" required>
                                                <div class="invalid-feedback">Please Enter a product orders.</div>
                                            </div>
                                        </div>
                                        <!-- end col -->
                                    </div>
                                    <!-- end row -->
                                </div>
                                <!-- end tab-pane -->

                                <div class="tab-pane" id="addproduct-metadata" role="tabpanel">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="meta-title-input">Meta title</label>
                                                <input type="text" class="form-control" placeholder="Enter meta title" id="meta-title-input">
                                            </div>
                                        </div>
                                        <!-- end col -->

                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="meta-keywords-input">Meta Keywords</label>
                                                <input type="text" class="form-control" placeholder="Enter meta keywords" id="meta-keywords-input">
                                            </div>
                                        </div>
                                        <!-- end col -->
                                    </div>
                                    <!-- end row -->

                                    <div>
                                        <label class="form-label" for="meta-description-input">Meta Description</label>
                                        <textarea class="form-control" id="meta-description-input" placeholder="Enter meta description" rows="3"></textarea>
                                    </div>
                                </div>
                                <!-- end tab pane -->
                            </div>
                            <!-- end tab content -->
                        </div>
                        <!-- end card body -->
                    </div> --}}
                    <!-- end card -->
                    <div class="text-end mb-3">
                        <button type="submit" class="btn btn-success w-sm">Submit</button>
                    </div>
                </div>
                <!-- end col -->

                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Product Properties</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="flexSwitchCheckDefault" style="margin-bottom: 1rem;">
                                    <label class="form-check-label" for="flexSwitchCheckDefault">Spare Part</label>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="flexSwitchCheckDefault">
                                    <label class="form-check-label" for="flexSwitchCheckDefault">On Stock</label>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="flexSwitchCheckDefault">
                                    <label class="form-check-label" for="flexSwitchCheckDefault">On Sale</label>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="flexSwitchCheckDefault">
                                    <label class="form-check-label" for="flexSwitchCheckDefault">Featured</label>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="flexSwitchCheckDefault">
                                    <label class="form-check-label" for="flexSwitchCheckDefault">Status</label>
                                </div>
                            </div>
                        </div>
                        <!-- end card body -->
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Product Price</h5>
                        </div>
                        <!-- end card body -->
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="datepicker-publish-input" class="form-label">Orginal Price</label>
                                <input type="text" id="datepicker-publish-input" class="form-control" placeholder="Enter publish date" data-provider="flatpickr" data-date-format="d.m.y" data-enable-time>
                            </div>
                            <div>
                                <label for="datepicker-publish-input" class="form-label">Discount Price</label>
                                <input type="text" id="datepicker-publish-input" class="form-control" placeholder="Enter publish date" data-provider="flatpickr" data-date-format="d.m.y" data-enable-time>
                            </div>
                        </div>
                    </div>
                    <!-- end card -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Product Brand</h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted mb-2"> <a href="#" class="float-end text-decoration-underline">Add
                                    New</a>Select product category</p>
                            <select class="form-select" id="choices-category-input" name="choices-category-input" data-choices data-choices-search-false>
                                @foreach($brands as $brand)
                                    <option value="{{ $brand->id }}">{{ $brand->brandtranslation->name ?? 'unKnown' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <!-- end card body -->
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Product Categories</h5>
                        </div>
                        <!-- end card body -->
                        <div class="card-body">
                            @foreach($categoriesData as $category)
                            <div class="form-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="{{ $category->id }}" id="category{{ $category->id }}">
                                    <label class="form-check-label" for="category{{ $category->id }}">
                                        {{ $category->categoryTranslation->name ?? $category->name }}
                                    </label>
                                </div>
                    
                                <!-- Subcategories for the current category -->
                                <div class="ms-4">
                                    @foreach($category->subCategory as $subcategory)
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="{{ $subcategory->id }}" id="subcategory{{ $subcategory->id }}">
                                            <label class="form-check-label" for="subcategory{{ $subcategory->id }}">
                                                {{ $subcategory->subCategoryTranslation->name ?? $subcategory->name }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Product Tags</h5>
                        </div>
                        <div class="card-body" wire:ignore>
                            <p class="text-muted mb-2">
                                <a href="#" class="float-end text-decoration-underline">Add New</a>Select product category
                            </p>

                            <select class="js-example-basic-multiple form-select" name="states[]" multiple="multiple">
                                @foreach($tags as $tag)
                                    <option value="{{ $tag->id }}">{{ $tag->tagtranslation->name ?? $tag->name }}</option>
                                @endforeach
                              </select>
                        </div>
                        <!-- end card body -->
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Product Short Description</h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted mb-2">Add short description for product</p>
                            <textarea class="form-control" placeholder="Must enter minimum of a 100 characters" rows="3"></textarea>
                        </div>
                        <!-- end card body -->
                    </div>
                    <!-- end card -->

                </div>
                <!-- end col -->
            </div>
            <!-- end row -->

        </form>

    </div>
    <!-- container-fluid -->
</div>

@push('tproductscript')
<!-- jQuery is required -->
<script>

    // Locale-specific configuration for Summernote editors
const locales = ['en', 'ar', 'ku'];

locales.forEach(locale => {
    $(`#product_content_${locale}`).summernote({
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'underline', 'clear']],
            ['fontname', ['fontname']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
            ['insert', ['link', 'picture', 'video']],
            ['view', ['codeview', 'help']],
        ],
        height: 200,
        callbacks: {
            onChange: function(contents) {
                // Dynamically set the content in Livewire property
                @this.set(`contents.${locale}`, contents);
            }
        }
    });
});


    $('.js-example-basic-multiple').select2();

</script>

@endpush