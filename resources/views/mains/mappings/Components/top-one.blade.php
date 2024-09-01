{{--file path: resources/views/mains/mappings/Components/top-one.blade.php --}}
<div class="header-top">
    <div class="container">
        <div class="header-left">
            <ul class="top-menu top-link-menu d-none d-md-block">
                <li>
                    <a href="#">Links</a>
                    <ul>
                        <li><a href="tel:009647507747742"><i class="icon-phone"></i>Call: +964 750 774 7742</a></li>
                    </ul>
                </li>
            </ul><!-- End .top-menu -->
        </div><!-- End .header-left -->

        <div class="header-right">
            <div class="social-icons social-icons-color">
                <a href="#" class="social-icon social-facebook" title="Facebook" target="_blank"><i class="icon-facebook-f"></i></a>
                <a href="#" class="social-icon social-twitter" title="Twitter" target="_blank"><i class="icon-twitter"></i></a>
                <a href="#" class="social-icon social-instagram" title="Pinterest" target="_blank"><i class="icon-instagram"></i></a>
                <a href="#" class="social-icon social-pinterest" title="Instagram" target="_blank"><i class="icon-pinterest-p"></i></a>
            </div><!-- End .soial-icons -->
            <ul class="top-menu top-link-menu">
                <li>
                    <a href="#">Links</a>
                    <ul>
                        
                    </ul>
                </li>
            </ul><!-- End .top-menu -->

            {{-- <div class="header-dropdown">
                <a href="#">USD</a>
                <div class="header-menu">
                    <ul>
                        <li><a href="#">Eur</a></li>
                        <li><a href="#">Usd</a></li>
                    </ul>
                </div><!-- End .header-menu -->
            </div><!-- End .header-dropdown --> --}}

            <div class="header-dropdown">
                <div>{{ str_replace('_', '-', app()->getLocale()) }}</div>
                <div class="header-menu">
                    <ul>
                        @foreach (config('app.locales') as $locale)
                        <li>
                            <a class="dropdown-item" onclick="changeLanguage('{{ $locale }}')">
                                <img src="{{ asset('lang/'.$locale.'.png') }}" width="20" alt="{{ $locale }}"> {{ __(strtoupper($locale)) }}
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div><!-- End .header-menu -->
            </div><!-- End .header-dropdown -->
        </div><!-- End .header-right -->
    </div>
</div>