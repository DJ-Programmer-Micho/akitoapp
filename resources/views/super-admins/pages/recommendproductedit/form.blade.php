<div class="page-content">  
    <style>
    .select2-container--default .select2-results>.select2-results__options {
        max-height: 400px!important;
    }    
    [lang="ar"] .dir-ar,
    [lang="ku"] .dir-ar{
        direction: rtl;
        text-align: right
    }

    .image-container {
        display: flex;
        justify-content: center; /* Horizontal center */
        align-items: center; /* Vertical center */
        border: 1px solid #ccc; /* Optional border for visibility */
    }
    </style>      
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">{{ __('Recommendation Products') }}</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">{{ __('Dashboard') }}</a></li>
                            <li class="breadcrumb-item active">{{ __('Recommendation') }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->


        <div class="card">
            <div class="product product-list p-2">
                <div class="row dir-ar">
                    <div class="col-12 col-lg-2">
                        <figure class="product-media image-container">
                            <a href="{{ route('business.productDetail', ['locale' => app()->getLocale(),'slug' => $product->productTranslation->first()->slug])}}">
                                <img src="{{app('cloudfront').$product->variation->images[0]->image_path ?? "sdf"}}" alt="{{$product->productTranslation->first()->name[0]}}" class="product-image" width="100%" style="max-width: 300px">
                            </a>
                        </figure><!-- End .product-media -->
                    </div><!-- End .col-sm-6 col-lg-3 -->
        
            
                    <div class="col-lg-10">
                        <div class="product-body product-action-inner">
                            {{-- <a href="{{ route('business.productDetail', ['locale' => app()->getLocale(),'slug' => $product->productTranslation->first()->slug])}}" class="btn-product btn-wishlist" title="Add to wishlist"><span>add to wishlist</span></a> --}}
                            <div class="product-cat">
                                <a href="{{ route('business.productShop', ['locale' => app()->getLocale(), 'categories[]' => $product->categories[0]->id]) }}">{{$product->categories[0]->categoryTranslation->name}}</a>
                            </div><!-- End .product-cat -->
                            <h3 class="product-title text-clamp-2"><a href="{{ route('business.productDetail', ['locale' => app()->getLocale(),'slug' => $product->productTranslation->first()->slug])}}">{{$product->productTranslation->first()->name}}</a></h3><!-- End .product-title -->
            
                            <div class="product-content">
                                <p class="text-clamp-2">{{$product->productTranslation->first()->description}}</p>
                            </div><!-- End .product-content -->
                            
                            @if ($product->variation->discount)
                            <div class="product-price">
                                $ {{$product->variation->discount}}
                                <span class="mx-2" style="text-decoration: line-through; color: #cc0022; font-size: 16px">
                                    $ {{$product->variation->price}}
                                </span>
                            </div><!-- End .product-price -->
                            @else
                                <div class="product-price">
                                    $ {{$product->variation->price}}
                                </div><!-- End .product-price -->
                            @endif
            
                        </div><!-- End .product-body -->
                    </div><!-- End .col-lg-6 -->
                </div><!-- End .row -->
            </div><!-- End .row -->
        </div>
        
        <div class="card mb-3">
            <div class="card-header">
                <div class="d-flex mb-3">
                    <div class="flex-grow-1">
                        <h5 class="fs-16">{{ __('Add New Product') }}</h5>
                    </div>
                    <div class="flex-shrink-0">
                        <button type="submit" class="btn btn-success" wire:click="submit">
                            <i class="ri-add-line align-bottom me-1"></i> {{ __('Submit') }}
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="filter-choices-input">
                    <div wire:ignore class="mb-3 dir-ar">
                        <label for="recommendedProducts">{{ __('Recommended Products') }}</label>
                        <select class="js-example-basic-multiple form-select" name="recommendedProducts[]" multiple="multiple" wire:model="recommendedProducts">
                            @foreach($allProducts as $product)
                                <option value="{{ $product->id }}" data-image="{{ app('cloudfront') . $product->variation->images[0]->image_path }}">
                                    {{ $product->productTranslation->first()->name ?? 'Unknown' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0">{{ __('The Products Selected') }}</h5>
            </div>
            <div class="card-body">
                <ul>
                    @foreach($recommendedProducts as $productId)
                        @php
                            $product = $allProducts->firstWhere('id', $productId);
                        @endphp

                        <li>{{ $product->producttranslation->first()->name ?? 'Unknown Product' }}</li>
                    @endforeach
                </ul>
            </div>
        </div>

    </div>

    @push('tproductscriptedit')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-UgvvN8vBkgO0luPSUl2s8TIlOSYRoGFAX4jlCIm9Adc=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="{{ asset('dashboard/css/select2.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function () {
            // Initialize Select2
            function formatState (state) {
                if (!state.id) {
                    return state.text; // optgroup
                }
                var imageUrl = $(state.element).data('image'); // Get the data-image attribute
                var $state = $(
                    '<div class="d-flex align-items-center dir-ar">' +
                        '<div class="flex-shrink-0 me-2">' +
                            '<img src="' + imageUrl + '" class="img-fluid" style="max-width: 60px; max-height: 60px; object-fit: cover;" />' +
                        '</div>' +
                        '<div class="mx-2">' + state.text + '</div>' +
                    '</div>'
                );
                return $state; // Return the formatted result
            }
        
            // Function to initialize Select2
            function initializeSelect2() {
                $('.js-example-basic-multiple').select2({
                    templateResult: formatState,
                    templateSelection: formatState // for selected item display
                });
            }
        
            // Initial call to set up Select2
            initializeSelect2();
        
            // Bind change event to update Livewire data
            $('.js-example-basic-multiple').on('change', function (e) {
                var recommendedProducts = $(this).val(); // Get selected values
                @this.set('recommendedProducts', recommendedProducts); // Update Livewire property
            });
        });
        </script>
        
    @endpush
</div>
