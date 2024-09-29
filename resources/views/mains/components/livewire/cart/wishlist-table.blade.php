<div class="page-content">
    <div class="container">
        <table class="table table-wishlist table-mobile">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Stock Status</th>
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

                            <h3 class="product-title">
                                <a href="{{ route('business.productDetail', ['locale' => app()->getLocale(),'slug' => $item['product']['product_translation'][0]['slug']])}}">
                                    {{$item['product']['product_translation'][0]['name']}}
                                </a>
                            </h3><!-- End .product-title -->
                        </div><!-- End .product -->
                    </td>
                    <td class="price-col">${{ !empty($item['product']['variation']['on_sale']) ? $item['product']['variation']['discount'] : $item['product']['variation']['price'] }}</td>
                    <td class="stock-col"><span class="in-stock">In stock</span></td>
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