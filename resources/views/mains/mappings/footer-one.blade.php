<footer class="footer bg-light">
{{-- <footer class="footer" style="background-color: #eb4034; color: white;"> --}}
    <div class="footer-middle">
        <div class="container">
            <div class="row">
                <div class="col-sm-6 col-lg-3">
                    <div class="widget widget-about">
                        <img src="{{ app('cloudfront').'web-setting/logo3.png' }}" alt="Akito" class="footer-logo" width="105" height="25">
                        <p>Akitu Store Company is one of the leading e-commerce websites in Iraq, specializing in coffee products and accessories.</p>

                        <div class="widget-call">
                            <i class="icon-phone"></i>
                            Got Question? Call us 24/7
                            <a href="tel:009647507747742" style="font-size: 15px">+964 750 774 7742</a>         
                        </div><!-- End .widget-call -->
                    </div><!-- End .widget about-widget -->
                </div><!-- End .col-sm-6 col-lg-3 -->

                <div class="col-sm-6 col-lg-3">
                    <div class="widget">
                        <h4 class="widget-title">Useful Links</h4><!-- End .widget-title -->

                        <ul class="widget-list">
                            <li><a href="{{route('business.aboutus',['locale' => app()->getLocale()])}}">About Us</a></li>
                            <li><a href="{{route('business.contactus',['locale' => app()->getLocale()])}}">Contact Us</a></li>
                            <li><a href="#">Our Services</a></li>
                            {{-- <li><a href="#">How to shop on Akito</a></li> --}}
                            <li><a href="{{route('business.faq',['locale' => app()->getLocale()])}}">FAQ</a></li>
                            {{-- <li><a href="contact.html">Contact us</a></li> --}}
                        </ul><!-- End .widget-list -->
                    </div><!-- End .widget -->
                </div><!-- End .col-sm-6 col-lg-3 -->

                <div class="col-sm-6 col-lg-3">
                    <div class="widget">
                        <h4 class="widget-title">Customer Service</h4><!-- End .widget-title -->

                        <ul class="widget-list">
                            <li><a href="#">Payment Methods</a></li>
                            <li><a href="#">Money-back guarantee!</a></li>
                            <li><a href="#">Returns</a></li>
                            <li><a href="#">Shipping</a></li>
                            <li><a href="{{route('law.terms')}}" target="_blank">Terms and conditions</a></li>
                            <li><a href="{{route('law.privacy')}}" target="_blank">">Privacy Policy</a></li>
                        </ul><!-- End .widget-list -->
                    </div><!-- End .widget -->
                </div><!-- End .col-sm-6 col-lg-3 -->

                <div class="col-sm-6 col-lg-3">
                    <div class="widget">
                        <h4 class="widget-title">My Account</h4><!-- End .widget-title -->

                        <ul class="widget-list">
                            @guest('customer')
                            <li><a href="#signin-modal" data-toggle="modal">Login</a></li>
                            <li><a href="{{ route('business.register', ['locale' => app()->getLocale()]) }}">Register</a></li>
                            @endguest
                            @auth('customer')
                            <li><a href="{{ route('business.account', ['locale' => app()->getLocale()]) }}">Dashboard</a></li>
                            <li><a href="{{ route('business.viewcart', ['locale' => app()->getLocale()]) }}">View Cart</a></li>
                            <li><a href="{{ route('business.whishlist', ['locale' => app()->getLocale()]) }}">My Wishlist</a></li>
                            <li><a href="#">Track My Order</a></li>
                            <li><a href="#">Help</a></li>
                            @endauth
                        </ul><!-- End .widget-list -->
                    </div><!-- End .widget -->
                </div><!-- End .col-sm-6 col-lg-3 -->
            </div><!-- End .row -->
        </div><!-- End .container -->
    </div><!-- End .footer-middle -->

    <div class="footer-bottom">
        <div class="container">
            <p class="footer-copyright" style="color: rgb(0, 0, 0)">Copyright © 2024 Akitu-co Store. All Rights Reserved.</p><!-- End .footer-copyright -->
            <figure class="footer-payments">
                {{-- <img src="{{ asset('lang/payments.png')}}" alt="Payment methods" width="272" height="20"> --}}
            </figure><!-- End .footer-payments -->
        </div><!-- End .container -->
    </div><!-- End .footer-bottom -->
</footer><!-- End .footer -->