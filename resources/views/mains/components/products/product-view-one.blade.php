<div class="product-details-top">
    <div class="row">
        <div class="col-md-6">
            <div class="product-gallery product-gallery-vertical">
                <div class="row">
                    <figure class="product-main-image">
                        
                        <img id="product-zoom" src="{{app('cloudfront').$productDetail->variation->images[0]->image_path ?? "sdf"}}" data-zoom-image="assets/images/products/single/1-big.jpg" alt="product image">

                        <a href="#" id="btn-product-gallery" class="btn-product-gallery">
                            <i class="icon-arrows"></i>
                        </a>
                    </figure><!-- End .product-main-image -->

                    @if ($productDetail->variation->images->count() > 1)
                    <div id="product-zoom-gallery" class="product-image-gallery">
                        @foreach ($productDetail->variation->images as $index => $image)
                        <a class="product-gallery-item active" href="#" data-image="assets/images/products/single/1.jpg" data-zoom-image="assets/images/products/single/1-big.jpg">
                            <img src="{{app('cloudfront').$image->image_path ?? "sdf"}}" alt="product side">
                        </a>
                        @endforeach
                    </div><!-- End .product-image-gallery -->
                    @endif
                </div><!-- End .row -->
            </div><!-- End .product-gallery -->
        </div><!-- End .col-md-6 -->

        <div class="col-md-6">
            <div class="product-details">
                <h1 class="product-title">{{$productDetail->productTranslation->name}}</h1><!-- End .product-title -->

                <div class="ratings-container">
                    <div class="ratings">
                        <div class="ratings-val" style="width: 80%;"></div><!-- End .ratings-val -->
                    </div><!-- End .ratings -->
                    <a class="ratings-text" href="#product-review-link" id="review-link">( 2 Reviews )</a>
                </div><!-- End .rating-container -->

                <div class="product-price">
                    $ {{$productDetail->variation->price}}
                </div><!-- End .product-price -->

                <div class="product-content">
                    <p>{{$productDetail->productTranslation->description}}</p>
                </div><!-- End .product-content -->

                @if ($productDetail->variation->colors->count() > 1)
                <div class="details-filter-row details-row-size">
                    <label>Color:</label>

                    <div class="product-nav product-nav-thumbs">
                        @foreach ($productDetail->variation->colors as $index => $color)
                        <a>
                            <div style="height: 100%; width: 100%; background-color: {{$color->code}};"></div>
                        </a>
                        @endforeach
                    </div><!-- End .product-nav -->
                </div><!-- End .details-filter-row -->
                @endif

                @if ($productDetail->variation->sizes->count() > 1)
                <div class="details-filter-row details-row-size">
                    <label for="size">Size:</label>
                    <div class="select-custom">
                        <select name="size" id="size" class="form-control">
                            <option value="#" selected="selected">Select a size</option>
                            @foreach ($productDetail->variation->sizes as $index => $size)
                            <option value="{{$size->id}}">{{$size->variationSizeTranslation->name}}</option>
                            @endforeach
                        </select>
                    </div><!-- End .select-custom -->

                    {{-- <a href="#" class="size-guide"><i class="icon-th-list"></i>size guide</a> --}}
                </div><!-- End .details-filter-row -->
                @endif
                @if ($productDetail->variation->sizes->count() > 1)
                <div class="details-filter-row details-row-size">
                    <label for="size">Size:</label>
                    <div class="select-custom">
                        <select name="size" id="size" class="form-control">
                            <option value="#" selected="selected">Select a size</option>
                            @foreach ($productDetail->variation->sizes as $index => $size)
                            <option value="{{$size->id}}">{{$size->variationSizeTranslation->name}}</option>
                            @endforeach
                        </select>
                    </div><!-- End .select-custom -->

                    <a href="#" class="size-guide"><i class="icon-th-list"></i>size guide</a>
                </div><!-- End .details-filter-row -->
                @endif
                @if ($productDetail->variation->sizes->count() > 1)
                <div class="details-filter-row details-row-size">
                    <label for="size">Size:</label>
                    <div class="select-custom">
                        <select name="size" id="size" class="form-control">
                            <option value="#" selected="selected">Select a size</option>
                            @foreach ($productDetail->variation->sizes as $index => $size)
                            <option value="{{$size->id}}">{{$size->variationSizeTranslation->name}}</option>
                            @endforeach
                        </select>
                    </div><!-- End .select-custom -->

                    <a href="#" class="size-guide"><i class="icon-th-list"></i>size guide</a>
                </div><!-- End .details-filter-row -->
                @endif
                <div class="details-filter-row details-row-size">
                    <label for="qty">Qty:</label>
                    <div class="product-details-quantity">
                        <input type="number" id="qty" class="form-control" value="1" min="1" max="10" step="1" data-decimals="0" required>
                    </div><!-- End .product-details-quantity -->
                </div><!-- End .details-filter-row -->

                <div class="product-details-action">
                    <a href="#" class="btn-product btn-cart"><span>add to cart</span></a>

                    <div class="details-action-wrapper">
                        <a href="#" class="btn-product btn-wishlist" title="Wishlist"><span>Add to Wishlist</span></a>
                        <a href="#" class="btn-product btn-compare" title="Compare"><span>Add to Compare</span></a>
                    </div><!-- End .details-action-wrapper -->
                </div><!-- End .product-details-action -->

                <div class="product-details-footer">
                    @if ($productDetail->variation->materials->count() > 1)
                    <div class="product-cat">
                        <span>Materials:</span>
                        @foreach ($productDetail->variation->materials as $index => $material)
                        <a href="#">{{$material->variationMaterialeTranslation->name}}</a>,
                        @endforeach
                    </div><!-- End .product-cat -->
     
                    @endif
                    @if ($productDetail->variation->capacities->count() > 1)
                    <div class="product-cat">
                        <span>Capacities:</span>
                        @foreach ($productDetail->variation->capacities as $index => $capacity)
                        <a href="#">{{$capacity->variationCapacityTranslation->name}}</a>,
                        @endforeach
                    </div><!-- End .product-cat -->
                    @endif

                    <div class="social-icons social-icons-sm">
                        <span class="social-label">Share:</span>
                        <a href="#" class="social-icon" title="Facebook" target="_blank"><i class="icon-facebook-f"></i></a>
                        <a href="#" class="social-icon" title="Twitter" target="_blank"><i class="icon-twitter"></i></a>
                        <a href="#" class="social-icon" title="Instagram" target="_blank"><i class="icon-instagram"></i></a>
                        <a href="#" class="social-icon" title="Pinterest" target="_blank"><i class="icon-pinterest"></i></a>
                    </div>
                </div><!-- End .product-details-footer -->
            </div><!-- End .product-details -->
        </div><!-- End .col-md-6 -->
    </div><!-- End .row -->
</div><!-- End .product-details-top -->