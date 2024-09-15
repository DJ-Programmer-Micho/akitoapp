<div class="page-content">
    <script src="https://code.jquery.com/jquery-3.7.1.slim.js" integrity="sha256-UgvvN8vBkgO0luPSUl2s8TIlOSYRoGFAX4jlCIm9Adc=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="{{asset('texteditor/summernote-bs5.css')}}">
    <script src="{{asset('texteditor/summernote-bs5.js')}}"></script>
    <link rel="stylesheet" href="{{asset('texteditor/summernote-lite.css')}}">
    <script src="{{asset('texteditor/summernote-lite.js')}}"></script>
    <script src="{{asset('texteditor/lang/summernote-en-US.min.js')}}"></script>
    <link rel="stylesheet" href="{{asset('dashboard/css/select2.css')}}">
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    {{-- File Upload --}}
    <!-- Include FilePond CSS -->
    <link href="https://unpkg.com/filepond/dist/filepond.min.css" rel="stylesheet">
    <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css" rel="stylesheet">
    <link href="https://unpkg.com/filepond-plugin-image-edit/dist/filepond-plugin-image-edit.min.css" rel="stylesheet">

    <!-- Include FilePond JS -->
    <script src="https://unpkg.com/filepond/dist/filepond.min.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-crop/dist/filepond-plugin-image-crop.min.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-transform/dist/filepond-plugin-image-transform.min.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.min.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.min.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-exif-orientation/dist/filepond-plugin-image-exif-orientation.min.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-edit/dist/filepond-plugin-image-edit.min.js"></script>


    <style>
        .note-frame {
            color: #222 !important;
            background-color: #ccc !important;
        }
        .filepond--item {
        width: calc(25% - 0.5em);
    }
    @media (max-width: 450px) {
        .filepond--item {
            width: calc(50% - 0.5em);
        }
    }
    </style>
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">{{__('Create Product')}}</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">{{__('Dashboard')}}</a></li>
                            <li class="breadcrumb-item active">{{__('Create Product')}}</li>
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
                                    >{{__('Product Info In '.$locale )}}
                                    </a>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="card-body" >
                            <div class="tab-content" >
                                @foreach ($filteredLocales as $locale)
                                <div wire:ignore.self
                                    class="tab-pane {{ $loop->first ? 'active' : '' }}" 
                                    id="addproduct-{{ $locale }}" 
                                    role="tabpanel"
                                >
                                    <div class="mb-3">
                                        <label for="products.{{ $locale }}">{{__('In ' . $locale . ' Language')}}</label>
                                        <input 
                                            type="text" 
                                            class="form-control @if($locale != 'en') ar-shift @endif 
                                            @error('products.' . $locale) is-invalid @enderror
                                            @if(!$errors->has('products.' . $locale) && !empty($products[$locale])) is-valid @endif"
                                            wire:model="products.{{ $locale }}" 
                                            placeholder="{{__('Product Name')}}"
                                        >
                                        @error('products.' . $locale)
                                            <div class="@if(app()->getLocale() != 'en') ar-shift @endif">
                                            <span class="text-danger">{{ __($message) }}</span>
                                        </div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="contents.{{ $locale }}">{{__('In ' . $locale . ' Language')}}</label>
                                        <div class="form-floating">
                                            <textarea 
                                            class="form-control @if($locale != 'en') ar-shift @endif 
                                            @error('contents.' . $locale) is-invalid @enderror
                                            @if(!$errors->has('contents.' . $locale) && !empty($contents[$locale])) is-valid @endif" 
                                            placeholder="{{__('Description')}}"  
                                            style="height: 100px;"
                                            wire:model="contents.{{$locale}}"></textarea>
                                            <label for="floatingTextarea2">{{__('Description')}}</label>
                                          </div>
                                            @error('contents.' . $locale)
                                            <div class="@if(app()->getLocale() != 'en') ar-shift @endif">
                                                <span class="text-danger">{{ __($message) }}</span>
                                            </div>
                                            @enderror
                                        
                                        {{-- <div 
                                            class="summernote" 
                                            id="product_content_{{ $locale }}"
                                        >
                                            {{ $contents[$locale] }}
                                        </div>
                                        @error('contents.' . $locale)
                                        <div class="@if(app()->getLocale() != 'en') ar-shift @endif">
                                            <span class="text-danger">{{ __($message) }}</span>
                                        </div>
                                        @enderror --}}
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
                            <h5 class="card-title mb-0">{{__('Image Uploader')}}</h5>
                        </div>
                        <div class="card-body" wire:ignore>
                            <input type="file" wire:model="images" multiple id="imageUploader" accept="image/*">
                            
                            @if ($images)
                            <div class="preview-container">
                                @foreach ($images as $key => $image)
                                    <div class="image-preview">
                                        <!-- Use the URL for both newly uploaded and existing images -->
                                        @php
                                        // Determine if $image is an object or array and access the temporary URL accordingly
                                        if (is_array($image)) {
                                            $temporaryUrl = $image['temporaryUrl'] ?? null;
                                        } elseif (is_object($image)) {
                                            $temporaryUrl = is_callable([$image, 'temporaryUrl']) ? $image->temporaryUrl() : $image->temporaryUrl;
                                        } else {
                                            $temporaryUrl = null;
                                        }
                                    @endphp
                                    </div>
                                @endforeach
                            </div>
                            @endif
                        </div>
                    </div>
                    
                    

                    <!-- end card -->
                    <div class="card">
                        <div class="card-header" wire:ignore>
                            <ul class="nav nav-tabs-custom card-header-tabs border-bottom-0" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" data-bs-toggle="tab" href="#variation-color" role="tab">
                                        {{__('Colors')}}
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#variation-naterial" role="tab">
                                        {{__('Materials')}}
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#variation-size" role="tab">
                                        {{__('Sizes')}}
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#variation-capacity" role="tab">
                                        {{__('Capacity')}}
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#variation-sku" role="tab">
                                        {{__('SKU')}}
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <div class="tab-content">
                                <div wire:ignore.self class="tab-pane active" id="variation-color" role="tabpanel">
                                    <button type="button" class="btn btn-success mb-3" wire:click="addColorSelect">{{__('Add Color')}}</button>
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>{{__('Choose a color')}}</th>
                                                <th>{{__('Preview')}}</th>
                                                <th>{{__('Action')}}</th>
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
                                                        <button type="button" class="btn btn-danger" wire:click="removeColorSelect({{ $index }})">{{__('Remove')}}</button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <!-- end tab-pane -->

                                <div wire:ignore.self class="tab-pane" id="variation-naterial" role="tabpanel">
                                    <button type="button" class="btn btn-success mb-3" wire:click="addMaterialSelect">{{__('Add Material')}}</button>
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>{{__('Choose a Material')}}</th>
                                                <th>{{__('Preview')}}</th>
                                                <th>{{__('Action')}}</th>
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
                                                                    {{ $material->variationMaterialTranslation->name }}
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
                                                        <button type="button" class="btn btn-danger" wire:click="removeMaterialSelect({{ $index }})">{{__('Remove')}}</button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <div wire:ignore.self class="tab-pane" id="variation-size" role="tabpanel">
                                    <button type="button" class="btn btn-success mb-3" wire:click="addSizeSelect">{{__('Add Size')}}</button>
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>{{__('Choose a Size')}}</th>
                                                <th>{{__('Preview')}}</th>
                                                <th>{{__('Action')}}</th>
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
                                                        <button type="button" class="btn btn-danger" wire:click="removeSizeSelect({{ $index }})">{{__('Remove')}}</button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <!-- end tab-pane -->

                                <div wire:ignore.self class="tab-pane" id="variation-capacity" role="tabpanel">
                                    <button type="button" class="btn btn-success mb-3" wire:click="addCapacitySelect">{{__('Add Capacity')}}</button>
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>{{__('Choose a Capacity')}}</th>
                                                <th>{{__('Preview')}}</th>
                                                <th>{{__('Action')}}</th>
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
                                                        <button type="button" class="btn btn-danger" wire:click="removeCapacitySelect({{ $index }})">{{__('Remove')}}</button>
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
                                                <label class="form-label" for="meta-title-input">{{__('SKU')}}</label>
                                                <input type="text" class="form-control" wire:model="sku" placeholder="Enter meta title" id="meta-title-input">
                                            </div>
                                        </div>
                                        <!-- end col -->
                                    </div>
                                    <!-- end row -->
                                </div>
                                <!-- end tab pane -->
                            </div>
                            <!-- end tab content -->
                        </div>
                        <!-- end card body -->
                    </div>
                    <div class="card">
                        <div class="card-header" wire:ignore>
                            <ul class="nav nav-tabs-custom card-header-tabs border-bottom-0" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" data-bs-toggle="tab" href="#product-description" role="tab">
                                        {{__('Description')}}
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#product-information" role="tab">
                                        {{__('Additionally Information')}}
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#product-ship" role="tab">
                                        {{__('Shipping & Returns')}}
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#product-faq" role="tab">
                                        {{__('FAQ')}}
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <div class="tab-content">
                                <div wire:ignore.self class="tab-pane active" id="product-description" role="tabpanel">
                                    <div class="mb-3" wire:ignore>
                                        @foreach ($filteredLocales as $locale)
                                        <label for="product-description{{ $locale }}">{{__('In ' . $locale . ' Language')}}</label>
                                        <div class="summernote" id="product-description{{ $locale }}">
                                            {!! $productDescriptions[$locale] !!}
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                <div wire:ignore.self class="tab-pane" id="product-information" role="tabpanel">
                                    <div class="mb-3" wire:ignore>
                                        @foreach ($filteredLocales as $locale)
                                        <label for="product-information{{ $locale }}">{{__('In ' . $locale . ' Language')}}</label>
                                        <div class="summernote" id="product-information{{ $locale }}"
                                        >
                                        {!! $productInformations[$locale] !!}
                                        </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div wire:ignore.self class="tab-pane" id="product-ship" role="tabpanel">
                                    <div class="mb-3" wire:ignore>
                                        @foreach ($filteredLocales as $locale)
                                        <label for="product-ship{{ $locale }}">{{__('In ' . $locale . ' Language')}}</label>
                                        <div 
                                            class="summernote" 
                                            id="product-ship{{ $locale }}"
                                        >
                                            {{ $productShip[$locale] }}
                                        </div>
                                        @endforeach
                                    </div>
                                </div>


                                <div wire:ignore.self class="tab-pane" id="product-faq" role="tabpanel">
                                    <button type="button" class="btn btn-success mb-3" wire:click="addFaq">{{__('Add FAQ')}}</button>
                                
                                    <div class="accordion" id="faqAccordion">
                                        @foreach ($faqs as $faqIndex => $faq)
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="heading-{{ $faqIndex }}">
                                                <button class="accordion-button {{ $faqIndex > 0 ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-{{ $faqIndex }}" aria-expanded="{{ $faqIndex === 0 ? 'true' : 'false' }}" aria-controls="collapse-{{ $faqIndex }}">
                                                    {{__('FAQ:')}} {{ (int) $faqIndex + 1 }}
                                                </button>
                                            </h2>
                                            <div id="collapse-{{ $faqIndex }}" class="accordion-collapse collapse {{ $faqIndex === 0 ? 'show' : '' }}" aria-labelledby="heading-{{ $faqIndex }}" data-bs-parent="#faqAccordion">
                                                <div class="accordion-body">
                                                    @foreach ($filteredLocales as $locale)
                                                    <div class="mb-3">
                                                        <label for="question-{{ $faqIndex }}-{{ $locale }}" class="form-label">{{__('Question '.$locale.':')}}</label><br>
                                                        @error('faqs.' . $faqIndex . '.' . $locale . '.question')
                                                            <div class="@if(app()->getLocale() != 'en') ar-shift @endif">
                                                                <span class="text-danger">{{ __($message) }}</span>
                                                            </div>
                                                        @enderror
                                                        <input type="text" 
                                                            class="form-control @if($locale != 'en') ar-shift @endif 
                                                            @error('faqs.' . $faqIndex . '.' . $locale . '.question') is-invalid @enderror
                                                            @if(!$errors->has('faqs.' . $faqIndex . '.' . $locale . '.question') && !empty($faqs[$faqIndex][$locale]['question'])) is-valid @endif" 
                                                            id="question-{{ $faqIndex }}-{{ $locale }}" 
                                                            wire:model="faqs.{{ $faqIndex }}.{{ $locale }}.question" 
                                                            placeholder="{{__('Enter the question')}}">
                                                    </div>
                                
                                                    <div class="mb-3">
                                                        <label for="answer-{{ $faqIndex }}-{{ $locale }}" class="form-label">{{__('Answer '.$locale.':')}}</label><br>
                                                        @error('faqs.' . $faqIndex . '.' . $locale . '.answer')
                                                            <div class="@if(app()->getLocale() != 'en') ar-shift @endif">
                                                                <span class="text-danger">{{ __($message) }}</span>
                                                            </div>
                                                        @enderror
                                                        <textarea 
                                                            class="form-control @if($locale != 'en') ar-shift @endif 
                                                            @error('faqs.' . $faqIndex . '.' . $locale . '.answer') is-invalid @enderror
                                                            @if(!$errors->has('faqs.' . $faqIndex . '.' . $locale . '.answer') && !empty($faqs[$faqIndex][$locale]['answer'])) is-valid @endif" 
                                                            id="answer-{{ $faqIndex }}-{{ $locale }}" 
                                                            wire:model="faqs.{{ $faqIndex }}.{{ $locale }}.answer" 
                                                            placeholder="{{__('Enter the answer')}}" 
                                                            rows="3"></textarea>
                                                    </div>
                                                    @endforeach
                                                    <br>
                                                    <button type="button" class="btn btn-danger" wire:click="removeFaq({{ $faqIndex }})">{{__('Remove FAQ')}}</button>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                
                            </div>
                            <!-- end tab content -->
                        </div>
                        <!-- end card body -->
                    </div>
                    <!-- end card -->
                    <div class="text-end mb-3">
                        <button type="submit" class="btn btn-success w-sm">{{__('Submit')}}</button>
                    </div>
                </div>
                <!-- end col -->

                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{__('Product Properties')}}</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="onStockSwitch" wire:model="is_on_stock">
                                    <label class="form-check-label" for="onStockSwitch">{{__('On Stock')}}</label>
                                </div>

                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="sparePartSwitch" wire:model="is_spare_part" style="margin-bottom: 1rem;">
                                    <label class="form-check-label" for="sparePartSwitch">{{__('Spare Part')}}</label>
                                </div>
                                
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="featuredSwitch" wire:model="is_featured">
                                    <label class="form-check-label" for="featuredSwitch">{{__('Featured')}}</label>
                                </div>
                                
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="statusSwitch" wire:model="status">
                                    <label class="form-check-label" for="statusSwitch">{{__('Status')}}</label>
                                </div>
                            </div>
                        </div>
                        <!-- end card body -->
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{__('Product Price')}}</h5>
                        </div>
                        <!-- end card body -->
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="original-price-input" class="form-label">{{__('Original Price')}}</label>
                                <div class="input-group">
                                    <span class="input-group-text" id="basic-addon1">$</span>
                                    <input type="number"
                                    class="form-control
                                    @error('originalPrice') is-invalid @enderror
                                    @if(!$errors->has('originalPrice') && !empty($originalPrice)) is-valid @endif"
                                    wire:model.debounce.500ms="originalPrice" placeholder="0.00">
                                </div>
                                @error('originalPrice')
                                    <div class="@if(app()->getLocale() != 'en') ar-shift @endif">
                                            <span class="text-danger">{{ __($message) }}</span>
                                    </div>
                                @enderror
                            </div>
                        
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="onSaleSwitch" wire:model="is_on_sale">
                                <label class="form-check-label" for="onSaleSwitch">{{__('On Sale')}}</label>
                            </div>
                        
                            @if ($is_on_sale)
                            <div>
                                <label for="discount-price-input" class="form-label">{{__('Discount Percentage')}}</label>
                                <div class="input-group">
                                    <input type="number" 
                                    class="form-control
                                    @error('discountPrice') is-invalid @enderror
                                    @if(!$errors->has('discountPrice') && !empty($discountPrice)) is-valid @endif"
                                    wire:model.debounce.500ms="discountPrice" wire:change="updateDiscountValue" placeholder="0.00">
                                    <span class="input-group-text" id="basic-addon1">%</span>
                                </div>
                                @error('discountPrice')
                                    <div class="@if(app()->getLocale() != 'en') ar-shift @endif">
                                        <span class="text-danger">{{ __($message) }}</span>
                                    </div>
                                @enderror
                                <small class="text-info">{{__('Discounted by ')}} {{ number_format($discountPercentage, 0) }}{{__('% from the original price.')}}</small>
                            </div>
                            @endif
                        </div>
                    </div>
                    <!-- end card -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{__('Product Brand')}}</h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted mb-2"> <a href="#" class="float-end text-decoration-underline">{{__('Add New')}}</a>{{__('Select product brand')}}</p>
                                <select class="form-select
                                    @error('selectedBrand') is-invalid @enderror
                                    @if(!$errors->has('selectedBrand') && !empty($selectedBrand)) is-valid @endif" 
                                wire:model="selectedBrand" data-choices data-choices-search-true>
                                <option value="" selected>{{__('Select a Brand')}}</option>
                                @foreach($brands as $brand)
                                    <option value="{{ $brand->id }}">{{ $brand->brandtranslation->name ?? 'unKnown' }}</option>
                                @endforeach
                            </select>
                            @error('selectedBrand')
                                <div class="@if(app()->getLocale() != 'en') ar-shift @endif">
                                    <span class="text-danger">{{ __($message) }}</span>
                                </div>
                            @enderror
                        </div>
                        <!-- end card body -->
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{__('Product Categories')}}</h5>
                        </div>
                        <!-- end card body -->
                        <div class="card-body">
                            @error('selectedCategories')<div class="@if(app()->getLocale() != 'en') ar-shift @endif">
                                <span class="text-danger">{{ __($message) }}</span>
                            </div>
                            @enderror
                            @error('selectedSubCategories')<div class="@if(app()->getLocale() != 'en') ar-shift @endif">
                                <span class="text-danger">{{ __($message) }}</span>
                            </div>
                            @enderror
                            {{-- <div wire:ignore> --}}
                                @foreach($categoriesData as $category)
                                <div class="form-group">
                                    <div class="form-check">
                                        <input 
                                            class="form-check-input main-category-checkbox" 
                                            type="checkbox" 
                                            value="{{ $category->id }}" 
                                            id="category{{ $category->id }}"
                                            wire:model="selectedCategories"
                                        >
                                        <label class="form-check-label" for="category{{ $category->id }}">
                                            {{ $category->categoryTranslation->name ?? $category->name }}
                                        </label>
                                    </div>
                        
                                    <!-- Subcategories for the current category -->
                                    <div class="ms-4">
                                        @foreach($category->subCategory as $subcategory)
                                            <div class="form-check">
                                                <input 
                                                    class="form-check-input subcategory-checkbox" 
                                                    type="checkbox" 
                                                    value="{{ $subcategory->id }}" 
                                                    id="subcategory{{ $subcategory->id }}"
                                                    wire:model="selectedSubCategories"
                                                >
                                                <label class="form-check-label" for="subcategory{{ $subcategory->id }}">
                                                    {{ $subcategory->subCategoryTranslation->name ?? $subcategory->name }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                            {{-- </div> --}}
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{__('Product Tags')}}</h5>
                        </div>
                        <div class="card-body" wire:ignore>
                            <p class="text-muted mb-2">
                                <a href="#" class="float-end text-decoration-underline">{{__('Add New')}}</a>{{__('Select Tags')}}
                            </p>
                    
                            <!-- Bind the select element to Livewire property -->
                            <select class="js-example-basic-multiple form-select" name="selectedTags[]" multiple="multiple" wire:model="selectedTags">
                                @foreach($tags as $tag)
                                    <option value="{{ $tag->id }}">{{ $tag->tagtranslation->first()->name ?? $tag->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <!-- end card body -->
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{__('Meta Keywords')}}</h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted mb-2">{{__('Add a short Keywords for the product (Multi Langage if needed)')}}</p>
                            <textarea 
                                class="form-control" 
                                placeholder="Must enter a minimum of 100 characters" 
                                rows="3" 
                                wire:model.lazy="keywords"
                            ></textarea>
                            <small class="text-info">coffee beans, coffee machine, حبوب البن, آلة القهوة</small>
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
let imagesData = [];
locales.forEach(locale => {
    // $(`#product_content_${locale}`).summernote({
    //     toolbar: [
    //         ['style', ['style']],
    //         ['font', ['bold', 'underline', 'clear']],
    //         ['fontname', ['fontname']],
    //         ['color', ['color']],
    //         ['para', ['ul', 'ol', 'paragraph']],
    //         ['table', ['table']],
    //         ['insert', ['link', 'picture', 'video']],
    //         ['view', ['codeview', 'help']],
    //     ],
    //     height: 200,
    //     callbacks: {
    //         onChange: function(contents) {
    //             // Dynamically set the content in Livewire property
    //             @this.set(`contents.${locale}`, contents);
    //         }
    //     }
    // });

    $(`#product-description${locale}`).summernote({
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
                @this.set(`productDescriptions.${locale}`, contents);
            }
        }
    });

    $(`#product-information${locale}`).summernote({
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
                @this.set(`productInformations.${locale}`, contents);
            }
        }
    });

    $(`#product-ship${locale}`).summernote({
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
                @this.set(`productShip.${locale}`, contents);
            }
        }
    });
});

    FilePond.registerPlugin(
        FilePondPluginImagePreview,
        FilePondPluginImageCrop,
        FilePondPluginImageTransform,
        FilePondPluginFileValidateSize,
        FilePondPluginFileValidateType,
        FilePondPluginImageExifOrientation
    );



    // Initialize FilePond
    const inputElement = document.querySelector('#imageUploader');
        const pond = FilePond.create(inputElement, {
            allowMultiple: true,
            allowImageCrop: true,
            // imagePreviewMaxHeight: 40,
            imagePreviewMaxFileSize: '3MB',
            imageCropAspectRatio: '1:1',
            imagePreviewTransparencyIndicator: 'grid',
            allowImageExifOrientation: true,
            allowReorder: true,
        });

        pond.setOptions({
            server: {
                process: (fieldName, file, metadata, load, error, progress, abort) => {
                    @this.upload('images', file, load, error, progress);
                },
                revert: (fileId, load, error) => {
                    @this.call('removeImage', fileId);
                    load(); // Indicate to FilePond that the revert is complete
                }
            }
        });


        // document.addEventListener('livewire:load', function () {
        //     const images = @json($images); // Ensure this is an array of objects

        //     images.forEach(image => {
        //         if (image.temporaryUrl) {
        //             fetch(image.temporaryUrl)
        //                 .then(response => response.blob())
        //                 .then(blob => {
        //                     const file = new File([blob], image.id, { type: blob.type });
        //                     pond.addFile(file);
        //                 })
        //                 .catch(error => {
        //                     console.error('Error adding file to FilePond:', error);
        //                 });
        //         } else {
        //             console.error('Image object does not have temporaryUrl:', image);
        //         }
        //     });
        // });
        
        // pond.on('processfile', function (error, file) {
        //     if (error) {
        //         console.error('File upload error:', error);
        //     } else {
        //         console.log('File uploaded:', file);
        //     }
        // });
        document.addEventListener('livewire:load', function () {
            const images = @json($images); // Ensure this is an array of objects
            imagesData = [];

            // Load existing files into FilePond and initialize the imagesData array
            images.forEach(image => {
                if (image.temporaryUrl) {
                    fetch(image.temporaryUrl)
                        .then(response => response.blob())
                        .then(blob => {
                            const file = new File([blob], image.tempFileName, { type: blob.type });
                            pond.addFile(file);

                            // Add existing file data to imagesData
                            imagesData.push({
                                id: image.id,
                                tempFileName: image.tempFileName,
                                temporaryUrl: image.temporaryUrl,
                                is_existing: image.is_existing,
                                priority: image.priority,
                                is_removed: image.is_removed,
                                file: {
                                    name: file.name,
                                    size: file.size,
                                    type: file.type,
                                    lastModified: file.lastModified
                                }
                            });
                        })
                        .catch(error => {
                            console.error('Error adding file to FilePond:', error);
                        });
                } else {
                    console.error('Image object does not have a temporary URL:', image);
                }
            });

            pond.on('processfile', function (error, file) {
                if (error) {
                    console.error('File upload error:', error);
                } else {
                    // Find existing image by tempFileName
     
                    const existingImageIndex = imagesData.findIndex(image => image.tempFileName === file.file.name);
                    if (existingImageIndex !== -1) {
                        // Update existing image metadata
                        imagesData[existingImageIndex] = {
                            ...imagesData[existingImageIndex],
                            file: {
                                name: file.file.name,
                                size: file.file.size,
                                type: file.file.type,
                                lastModified: file.file.lastModified,
                                temporaryUrl: file.serverId
                            }
                        };
                    } else {
                        // Add new image data
                        imagesData.push({
                            id: 0, // Default for new files
                            tempFileName: file.file.name,
                            temporaryUrl: file.serverId, 
                            is_existing: false,
                            priority: 0,
                            is_removed: false,
                            file: {
                                name: file.file.name,
                                size: file.file.size,
                                type: file.file.type,
                                lastModified: file.file.lastModified,
                                temporaryUrl: file.serverId
                            }
                        });
                    }

                    // Emit the updated imagesData to Livewire
                    window.livewire.emit('fileProcessed', imagesData);
                }
            });
        });


        pond.on('removefile', function (error, file) {
            if (error) {
                console.error('File removal error:', error);
            } else {
                // Find the file by temporaryUrl in imagesData
                const imageIndex = imagesData.findIndex(image => image.file.temporaryUrl === file.serverId);
                
                if (imageIndex !== -1) {
                    // Remove the file from imagesData
                    imagesData.splice(imageIndex, 1);
                    
                    // Emit the updated imagesData to Livewire
                    window.livewire.emit('removeImage', imagesData);
                }
            }
        });

        let reorderTimeout;

        pond.on('reorderfiles', function (files) {
            // Clear any existing timeout to prevent multiple emits
            clearTimeout(reorderTimeout);
            
            // Set a timeout to emit the updated data after a short delay
            reorderTimeout = setTimeout(() => {
                // Update the priority or reorder imagesData based on the new order
                imagesData = files.map((file, index) => {
                    // Find the corresponding image by temporaryUrl
                    const imageData = imagesData.find(image => image.file.temporaryUrl === file.serverId);
                    
                    if (imageData) {
                        // Update priority (optional) and return the updated image data
                        return {
                            ...imageData,
                            priority: index // You can use the index to set a priority
                        };
                    }
                }).filter(Boolean); // Filter out any undefined entries

                // Emit the updated imagesData to Livewire
                window.livewire.emit('filesReordered', imagesData);
            }, 1000); // Adjust the timeout duration if needed
        });
        // pond.on('reorderfiles', function (files) {
        //     // Update the priority or reorder imagesData based on the new order
        //     imagesData = files.map((file, index) => {
        //         // Find the corresponding image by temporaryUrl
        //         const imageData = imagesData.find(image => image.file.temporaryUrl === file.serverId);
                
        //         if (imageData) {
        //             // Update priority (optional) and return the updated image data
        //             return {
        //                 ...imageData,
        //                 priority: index // You can use the index to set a priority
        //             };
        //         }
        //     }).filter(Boolean); // Filter out any undefined entries

        //     // Emit the updated imagesData to Livewire
        //     window.livewire.emit('filesReordered', imagesData);
        // });


    window.addEventListener('clean-image', () => {
        pond.removeFiles();
        $('.js-example-basic-multiple').val([]).trigger('change'); // Clear Select2 selections
    })

    document.querySelectorAll('.subcategory-checkbox').forEach(subCheckbox => {
        subCheckbox.addEventListener('change', function () {
            const mainCheckbox = this.closest('.form-group').querySelector('.main-category-checkbox');
            if (this.checked) {
                mainCheckbox.checked = true;
            } else {
                const allUnchecked = Array.from(this.closest('.ms-4').querySelectorAll('.subcategory-checkbox')).every(sub => !sub.checked);
                if (allUnchecked) {
                    mainCheckbox.checked = false;
                }
            }
        });
    });

    
    $('.js-example-basic-multiple').select2();

    var selectedTags = @json($selectedTags);
    $('.js-example-basic-multiple').val(selectedTags).trigger('change');
    
    // Bind change event to update Livewire data
    $('.js-example-basic-multiple').on('change', function (e) {
        var selectedTags = $(this).val(); // Get selected values
        @this.set('selectedTags', selectedTags); // Update Livewire property
    });

    // Reinitialize Select2 after each Livewire update
    Livewire.hook('message.processed', () => {
        $('.js-example-basic-multiple').select2();
    });

</script>

@endpush