<div class="header-middle">
    <div class="container">
        <div class="header-left">
            <a href=" {{ route('business.home', ['locale' => app()->getLocale()]) }}">
                <img src="{{ app('cloudfront').'web-setting/logo.png' }}" alt="Akito" width="120" height="20">
            </a>

        </div>
        <div class="header-center">
            <a href=" {{ route('business.home', ['locale' => app()->getLocale()]) }}" class="logo d-lg-none">
                <img src="{{ app('cloudfront').'web-setting/logo.png' }}" alt="Akito" width="120" height="20">
            </a>
        </div><!-- End .header-left -->

        <div class="header-right">
            <div class="header-search header-search-extended header-search-visible d-none d-lg-block mx-0 px-0">
                <a href="#" class="search-toggle" role="button"><i class="icon-search"></i></a>
                <form action="#" method="get" class="border rounded-pill p-3">
                    <div class="header-search-wrapper search-wrapper-wide ">
                        <label for="q" class="sr-only">Search</label>
                        <input type="text" class="form-control p-2" name="q" id="q" placeholder="Search product ..." required>
                        <button class="btn btn-primary w-25" type="submit"><i class="icon-search"></i></button>
                    </div><!-- End .header-search-wrapper -->
                </form>
            </div><!-- End .header-search -->
            <a href="wishlist.html" class="wishlist-link">
                <lord-icon
                    src="https://cdn.lordicon.com/jjoolpwc.json"
                    trigger="loop"
                    delay="2000"
                    colors="primary:#3080e8,secondary:#000000"
                    style="width:40px;height:40px">
                </lord-icon>
                <span class="wishlist-count">3</span>
            </a>

            <div class="dropdown cart-dropdown">
                <a href="#" class="dropdown-toggle" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-display="static">
                    <lord-icon
                        src="https://cdn.lordicon.com/odavpkmb.json"
                        trigger="loop"
                        delay="2000"
                        colors="primary:#3080e8,secondary:#000000"
                        style="width:40px;height:40px">
                    </lord-icon>
                    <span class="cart-count">2</span>
                </a>

                <a href="" class="wishlist-link">
                    <lord-icon
                    src="https://cdn.lordicon.com/bgebyztw.json"
                    trigger="loop"
                    delay="2000"
                    state="hover-looking-around"
                    colors="primary:#3080e8,secondary:#000000"
                    style="width:40px;height:40px">
                </lord-icon>
            </a>
            <p class="flex-grow-1" style="font-size: 16px">Furat Hariri</p>

                <div class="dropdown-menu dropdown-menu-right">
                    <div class="dropdown-cart-products">
                        <div class="product">
                            <div class="product-cart-details">
                                <h4 class="product-title">
                                    <a href="product.html">Beige knitted elastic runner shoes</a>
                                </h4>

                                <span class="cart-product-info">
                                    <span class="cart-product-qty">1</span>
                                    x $84.00
                                </span>
                            </div><!-- End .product-cart-details -->

                            <figure class="product-image-container">
                                <a href="product.html" class="product-image">
                                    <img src="assets/images/products/cart/product-1.jpg" alt="product">
                                </a>
                            </figure>
                            <a href="#" class="btn-remove" title="Remove Product"><i class="icon-close"></i></a>
                        </div><!-- End .product -->

                        <div class="product">
                            <div class="product-cart-details">
                                <h4 class="product-title">
                                    <a href="product.html">Blue utility pinafore denim dress</a>
                                </h4>

                                <span class="cart-product-info">
                                    <span class="cart-product-qty">1</span>
                                    x $76.00
                                </span>
                            </div><!-- End .product-cart-details -->

                            <figure class="product-image-container">
                                <a href="product.html" class="product-image">
                                    <img src="assets/images/products/cart/product-2.jpg" alt="product">
                                </a>
                            </figure>
                            <a href="#" class="btn-remove" title="Remove Product"><i class="icon-close"></i></a>
                        </div><!-- End .product -->
                    </div><!-- End .cart-product -->

                    <div class="dropdown-cart-total">
                        <span>Total</span>

                        <span class="cart-total-price">$160.00</span>
                    </div><!-- End .dropdown-cart-total -->

                    <div class="dropdown-cart-action">
                        <a href="cart.html" class="btn btn-primary">View Cart</a>
                        <a href="checkout.html" class="btn btn-outline-primary-2"><span>Checkout</span><i class="icon-long-arrow-right"></i></a>
                    </div><!-- End .dropdown-cart-total -->
                </div><!-- End .dropdown-menu -->
            </div><!-- End .cart-dropdown -->
        </div>
    </div><!-- End .container -->
</div><!-- End .header-middle -->