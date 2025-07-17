<footer class="footer">
{{-- <footer class="footer" style="background-color: #eb4034; color: white;"> --}}
    <div class="footer-middle">
        <div class="container nav-dir">
            <div class="row">
                <div class="col-sm-6 col-lg-3">
                    <div class="widget widget-about">
                        <img src="{{ app('negative_logo') }}" alt="Akitu Star" class="footer-logo" width="105" height="25">
                        <p>{{__('Akitu Star Store Company is one of the leading e-commerce websites in Iraq, specializing in coffee products and accessories.')}}</p>

                        <div class="widget-call mb-1">
                            <i class="icon-phone"></i>
                            {{__('Call Center')}}
                            <a href="tel:{{app('phoneNumber')}}" style="font-size: 15px; color: white">{{app('phoneNumber')}}</a>         
                        </div><!-- End .widget-call -->
                        <div class="widget-call">
                            <i class="icon-phone"></i>
                            {{__('Call Center')}}
                            <a href="tel:{{app('phoneNumber2')}}" style="font-size: 15px; color: white">{{app('phoneNumber2')}}</a>         
                        </div><!-- End .widget-call -->
                    </div><!-- End .widget about-widget -->
                </div><!-- End .col-sm-6 col-lg-3 -->

                <div class="col-sm-6 col-lg-3">
                    <div class="widget">
                        <h4 class="widget-title">{{__('Useful Links')}}</h4><!-- End .widget-title -->

                        <ul class="widget-list">
                            <li><a href="{{route('business.aboutus',['locale' => app()->getLocale()])}}">{{__('About Us')}}</a></li>
                            <li><a href="{{route('business.contactus',['locale' => app()->getLocale()])}}">{{__('Contact Us')}}</a></li>
                            <li><a href="{{route('business.faq',['locale' => app()->getLocale()])}}">{{__('Our Services')}}</a></li>
                            {{-- <li><a href="#">How to shop on Akito</a></li> --}}
                            <li><a href="{{route('business.faq',['locale' => app()->getLocale()])}}">{{__('FAQ')}}</a></li>
                            {{-- <li><a href="contact.html">Contact us</a></li> --}}
                        </ul><!-- End .widget-list -->
                    </div><!-- End .widget -->
                </div><!-- End .col-sm-6 col-lg-3 -->

                <div class="col-sm-6 col-lg-3">
                    <div class="widget">
                        <h4 class="widget-title">{{__('Customer Service')}}</h4><!-- End .widget-title -->

                        <ul class="widget-list">
                            <li><a href="{{route('business.faq',['locale' => app()->getLocale()])}}">{{__('Payment Methods')}}</a></li>
                            <li><a href="{{route('business.faq',['locale' => app()->getLocale()])}}">{{__('Money-back guarantee!')}}</a></li>
                            <li><a href="{{route('business.faq',['locale' => app()->getLocale()])}}">{{__('Returns')}}</a></li>
                            <li><a href="{{route('business.faq',['locale' => app()->getLocale()])}}">{{__('Shipping')}}</a></li>
                            <li><a href="{{route('law.terms')}}" target="_blank">{{__('Terms and conditions')}}</a></li>
                            <li><a href="{{route('law.privacy')}}" target="_blank">{{__('Privacy Policy')}}</a></li>
                        </ul><!-- End .widget-list -->
                    </div><!-- End .widget -->
                </div><!-- End .col-sm-6 col-lg-3 -->

                <div class="col-sm-6 col-lg-3">
                    <div class="widget">
                        <h4 class="widget-title">{{__('My Account')}}</h4><!-- End .widget-title -->

                        <ul class="widget-list">
                            @guest('customer')
                            <li><a href="#signin-modal" data-toggle="modal">Login</a></li>
                            <li><a href="{{ route('business.register', ['locale' => app()->getLocale()]) }}">Register</a></li>
                            @endguest
                            @auth('customer')
                            <li><a href="{{ route('business.account', ['locale' => app()->getLocale()]) }}">Dashboard</a></li>
                            <li><a href="{{ route('business.viewcart', ['locale' => app()->getLocale()]) }}">View Cart</a></li>
                            <li><a href="{{ route('business.whishlist', ['locale' => app()->getLocale()]) }}">My Wishlist</a></li>
                            {{-- <li><a href="#">Track My Order</a></li>
                            <li><a href="#">Help</a></li> --}}
                            @endauth
                        </ul><!-- End .widget-list -->
                    </div><!-- End .widget -->
                </div><!-- End .col-sm-6 col-lg-3 -->
            </div><!-- End .row -->
        </div><!-- End .container -->
    </div><!-- End .footer-middle -->

    <div class="footer-bottom">
        <div class="container">
            <p class="footer-copyright" style="color: rgb(255, 255, 255)">Copyright © 2024 Akitu Star Store. All Rights Reserved.</p><!-- End .footer-copyright -->
            <figure class="footer-payments">
                {{-- <img src="{{ asset('lang/payments.png')}}" alt="Payment methods" width="272" height="20"> --}}
            </figure><!-- End .footer-payments -->
        </div><!-- End .container -->
    </div><!-- End .footer-bottom -->
</footer><!-- End .footer -->