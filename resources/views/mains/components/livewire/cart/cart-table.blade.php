<div class="page-content">
    <div class="cart">
        <div class="container">
            <div class="row">
                <div class="col-lg-9">
                    <table class="table table-cart table-mobile">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th class="text-center">Price</th>
                                <th class="text-center">Quantity</th>
                                <th class="text-center">Total</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($cartListItems as $item)
                            <tr>
                                <td class="product-col">
                                    <div class="product">
                                        <figure class="product-media">
                                            <a href="{{ route('business.productDetail', ['locale' => app()->getLocale(),'slug' => $item['product']['product_translation'][0]['slug']])}}">
                                                <img src="{{ app('cloudfront') . $item['product']['variation']['images'][0]['image_path'] ?? '' }}" alt="">
                                            </a>
                                        </figure>

                                        <h3 class="product-title">
                                            <a href="{{ route('business.productDetail', ['locale' => app()->getLocale(),'slug' => $item['product']['product_translation'][0]['slug']])}}">
                                                <h6 class="mx-1 mb-0">{{$item['product']['product_translation'][0]['name'] ?? 'unKnown' }}</h6>
                                                <p class="mx-1 mb-0">{{__('Category:')}} {{ $item['product']['categories'][0]['category_translation']['name'] }}</p>
                                            </a>
                                        </h3><!-- End .product-title -->
                                    </div><!-- End .product -->
                                </td>
                                <td class="total-col align-middle text-center">
                                    {{-- ${{ !empty($item['product']['variation']['on_sale']) ? $item['product']['variation']['discount'] : $item['product']['variation']['price'] }} --}}
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
                                            {{-- <i class="fa-solid fa-star fa-beat-fade"></i> --}}$ {{ number_format($item['product']['customer_discount_price'], 2) }}{{-- <i class="fa-solid fa-star fa-beat-fade"></i> --}}
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
                                <td class="align-middle text-center">
                                    <div class="quantity-action d-flex align-items-center justify-content-center">
                                        @if($item['quantity'] > 1)
                                            <button wire:click="updateQuantity({{ $item['id'] }}, {{ $item['quantity'] - 1 }})" class="btn btn-outline-secondary btn-sm" style="min-width: 35px; padding: 5px; border-radius: 40px 0 0 40px">
                                                <i class="fa-solid fa-minus"></i>
                                            </button>
                                        @else
                                            <button wire:click="removeFromCartList({{ $item['id'] }})" class="btn btn-outline-danger btn-sm" style="min-width: 35px; padding: 5px; border-radius: 40px 0 0 40px">
                                                <i class="fa-regular fa-trash-can"></i>
                                            </button>
                                        @endif
                                        <span class="mx-2">{{ $item['quantity'] }}</span>
                                        <button wire:click="updateQuantity({{ $item['id'] }}, {{ $item['quantity'] + 1 }})" class="btn btn-outline-secondary btn-sm" style="min-width: 35px; padding: 5px; border-radius: 0 40px 40px 0">
                                            <i class="fa-solid fa-plus"></i>
                                        </button>
                                    </div>
                                </td>
                                
                                <td class="total-col align-middle text-center">
                                    ${{ 
                                        number_format(
                                            (
                                                $item['product']['customer_discount_price'] ?? 
                                                ($item['product']['discount_price'] ?? $item['product']['base_price'])
                                            ) * $item['quantity'], 
                                            2
                                        ) 
                                    }}
                                    {{-- ${{ (!empty($item['product']['variation']['on_sale']) ? $item['product']['variation']['discount'] : $item['product']['variation']['price']) *  $item['quantity'] }} --}}
                                </td>
                            </tr>
                            @empty
                            {{__('No Items Are Added')}}
                
                            @endforelse
                        </tbody>
                    </table><!-- End .table table-wishlist -->

                    {{-- <div class="cart-bottom">
                        <div class="cart-discount">
                            <form action="#">
                                <div class="input-group">
                                    <input type="text" class="form-control" required placeholder="coupon code">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-primary-2" type="submit"><i class="icon-long-arrow-right"></i></button>
                                    </div><!-- .End .input-group-append -->
                                </div><!-- End .input-group -->
                            </form>
                        </div><!-- End .cart-discount -->

                        <a href="#" class="btn btn-outline-dark-2"><span>UPDATE CART</span><i class="icon-refresh"></i></a>
                    </div><!-- End .cart-bottom --> --}}
                </div><!-- End .col-lg-9 -->
                <aside class="col-lg-3">
                    <div class="summary summary-cart">
                        <h3 class="summary-title">Cart Total</h3><!-- End .summary-title -->

                        <table class="table table-summary">
                            <tbody>
                                <tr class="summary-subtotal">
                                    <td>Subtotal:</td>
                                    <td>${{ $totalListPrice }}</td>
                                </tr><!-- End .summary-subtotal -->
                                {{-- <tr class="summary-shipping">
                                    <td>Shipping:</td>
                                    <td>&nbsp;</td>
                                </tr>

                                <tr class="summary-shipping-row">
                                    <td>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" id="free-shipping" name="shipping" class="custom-control-input">
                                            <label class="custom-control-label" for="free-shipping">Free Shipping</label>
                                        </div><!-- End .custom-control -->
                                    </td>
                                    <td>$0.00</td>
                                </tr><!-- End .summary-shipping-row -->

                                <tr class="summary-shipping-row">
                                    <td>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" id="standart-shipping" name="shipping" class="custom-control-input">
                                            <label class="custom-control-label" for="standart-shipping">Standart:</label>
                                        </div><!-- End .custom-control -->
                                    </td>
                                    <td>$10.00</td>
                                </tr><!-- End .summary-shipping-row -->

                                <tr class="summary-shipping-row">
                                    <td>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" id="express-shipping" name="shipping" class="custom-control-input">
                                            <label class="custom-control-label" for="express-shipping">Express:</label>
                                        </div><!-- End .custom-control -->
                                    </td>
                                    <td>$20.00</td>
                                </tr><!-- End .summary-shipping-row --> --}}

                                {{-- <tr class="summary-shipping-estimate">
                                    <td>Estimate for Your Country<br> <a href="dashboard.html">Change address</a></td>
                                    <td>&nbsp;</td>
                                </tr><!-- End .summary-shipping-estimate --> --}}

                                {{-- <tr class="summary-total">
                                    <td>Total:</td>
                                    <td>{{ $totalListPrice }}</td>
                                </tr><!-- End .summary-total --> --}}
                            </tbody>
                        </table><!-- End .table table-summary -->

                        <a href="{{ route('business.checkout', ['locale' => app()->getLocale()]) }}" class="btn btn-outline-primary-2 btn-order btn-block">PROCEED TO CHECKOUT</a>
                    </div><!-- End .summary -->

                    <a href="{{ route('business.productShop', ['locale' => app()->getLocale()]) }}" class="btn btn-outline-dark-2 btn-block mb-3"><span>CONTINUE SHOPPING</span><i class="icon-refresh"></i></a>
                </aside><!-- End .col-lg-3 -->
            </div><!-- End .row -->
        </div><!-- End .container -->
    </div><!-- End .cart -->
</div><!-- End .page-content -->