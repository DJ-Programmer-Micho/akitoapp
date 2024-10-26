<div class="page-content">
    <div class="container">
        <table class="table table-wishlist table-mobile">
            <thead>
                <tr>
                    <th>Product</th>
                    <th class="text-center">Price</th>
                    <th class="text-center">Stock Status</th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>

            <tbody>
                @forelse($wishlistItems as $item)
                <tr>
                    <td class="product-col">
                        <div class="product">
                            <figure class="product-media">
                                <a href="{{ route('business.productDetail', ['locale' => app()->getLocale(),'slug' => $item['product']['product_translation'][0]['slug']])}}">
                                    <img src="{{ app('cloudfront') . $item['product']['variation']['images'][0]['image_path'] ?? '' }}" alt="Product image">
                                </a>
                            </figure>

                            <h3 class="product-title d-block">
                                <a href="{{ route('business.productDetail', ['locale' => app()->getLocale(),'slug' => $item['product']['product_translation'][0]['slug']])}}">
                                    <h6 class="mx-1 mb-0"> {{$item['product']['product_translation'][0]['name'] ?? 'unKnown'}}</h6>
                                    <p class="mx-1 mb-0">{{$item['product']['product_translation'][0]['description'] ?? 'unKnown'}}</p>
                                </a>
                            </h3><!-- End .product-title -->
                        </div><!-- End .product -->
                    </td>

                    <td class="price-col text-center">
                    @if ($item['product']['discount_price'] == $item['product']['customer_discount_price'] && $item['product']['customer_discount_price'] != $item['product']['base_price'])
                        <div class="product-price mt-1 fw-bold">
                            <span class="mx-2 w-100" style="text-decoration: line-through; color: #cc0022; font-size: 16px">
                            $ {{ number_format($item['product']['base_price'], 2) }}
                            </span>
                            <span class="w-100">
                                $ {{ number_format($item['product']['discount_price'], 2) }}
                            </span>
                        </div><!-- End .product-price -->
                    @elseif ($item['product']['discount_price'] != $item['product']['customer_discount_price'] && $item['product']['customer_discount_price'] != $item['product']['base_price'])
                        <div class="product-price mt-1 fw-bold">
                            <span class="mx-2 w-100" style="text-decoration: line-through; color: #cc0022; font-size: 16px">
                                $ {{ number_format($item['product']['base_price'], 2) }}
                            </span>
                            <span class="text-gold w-100" data-toggle="tooltip" data-placement="top" title="{{__('Only For You')}}">
                                <i class="fa-solid fa-star fa-beat-fade"></i>$ {{ number_format($item['product']['customer_discount_price'], 2) }}<i class="fa-solid fa-star fa-beat-fade"></i>
                            </span>
                        </div><!-- End .product-price -->
                    @else
                        <div class="product-price mt-1 fw-bold">
                            <span class="w-100">
                                $ {{ number_format($item['product']['base_price'], 2) }}
                            </span>
                        </div><!-- End .product-price -->
                    @endif
                    </td>
                    <td class="stock-col text-center">
                        @if ($item['product']['variation']['order_limit'])
                        <span class="text-success">In Stock</span>
                        @else
                        <span class="text-danger">Out Of Stock</span>
                        @endif
                    </td>
                    <td class="action-col">
                        <button class="btn btn-block btn-outline-primary-2"  wire:click="singleAddWishlist({{$item['product']['id']}})"><i class="icon-cart-plus"></i>Add to Cart</button>
                    </td>
                    <td class="remove-col"><button class="btn-remove"  wire:click="removeFromWishlist({{$item['product']['id']}})"><i class="fa-regular fa-trash-can"></i></button></td>
                </tr>
                @empty
                    {{__('No Items Are Added')}}
                @endforelse
            </tbody>
        </table><!-- End .table table-wishlist -->
        {{-- <div class="wishlist-share">
            <div class="social-icons social-icons-sm mb-2">
                <label class="social-label">Share on:</label>
                <a href="#" class="social-icon" title="Facebook" target="_blank"><i class="icon-facebook-f"></i></a>
                <a href="#" class="social-icon" title="Twitter" target="_blank"><i class="icon-twitter"></i></a>
                <a href="#" class="social-icon" title="Instagram" target="_blank"><i class="icon-instagram"></i></a>
                <a href="#" class="social-icon" title="Youtube" target="_blank"><i class="icon-youtube"></i></a>
                <a href="#" class="social-icon" title="Pinterest" target="_blank"><i class="icon-pinterest"></i></a>
            </div><!-- End .soial-icons -->
        </div><!-- End .wishlist-share --> --}}
    </div><!-- End .container -->
</div><!-- End .page-content -->