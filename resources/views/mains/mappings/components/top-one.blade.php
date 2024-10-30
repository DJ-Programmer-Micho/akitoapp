{{--file path: resources/views/mains/mappings/Components/top-one.blade.php --}}
<div class="header-top">
    <div class="container">
        <div class="header-left">
            <ul class="top-menu top-link-menu d-none d-md-block">
                <li>
                    <a href="#">Links</a>
                    <ul>
                        <li ><a href="tel:{{app('phoneNumber')}}"><i class="fa-solid fa-phone mx-1"></i> Call: {{app('phoneNumber')}}</a></li>
                    </ul>
                </li>
            </ul><!-- End .top-menu -->
        </div><!-- End .header-left -->

        <div class="header-right">
            <div class="social-icons social-icons-color mr-0">
                <a href="{{app('facebookUrl')}}" class="social-icon social-facebook" title="Facebook" target="_blank"><i class="fa-brands fa-facebook-f"></i></a>
                <a href="{{app('instagramUrl')}}" class="social-icon social-instagram" title="Instagram" target="_blank"><i class="fa-brands fa-instagram"></i></a>
                <a href="{{app('tiktokUrl')}}" class="social-icon social-tiktok" title="TikTok" target="_blank"><i class="fa-brands fa-tiktok"></i></a>
                {{-- <a href="#" class="social-icon social-pinterest" title="Instagram" target="_blank"><i class="icon-pinterest-p"></i></a> --}}
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
                <div class="lang dropdown-item p-0" style="display: flex">
                    {{-- <img 
                    class="mr-2 p-0 my-auto" 
                    src="{{ asset('lang/' . str_replace('_', '-', app()->getLocale()) . '.png') }}" 
                    width="30" 
                    alt="{{ str_replace('_', '-', app()->getLocale()) }}" 
                    style="border: 1px solid #fff; border-radius: 5px; object-fit: cover; height: 20px; align-items: center"> --}}
                    <span style="cursor: pointer">{{ __(str_replace('_', '-', app()->getLocale())) }}</span>
                </div>
                <div class="header-menu px-3" style="z-index: 10000">
                    <ul>
                        @foreach (config('app.locales') as $locale)
                        <li>
                            <a class="dropdown-item" onclick="changeLanguage('{{ $locale }}')">
                                {{-- <img class="mr-2" src="{{ asset('lang/'.$locale.'.png') }}" width="20" alt="{{ $locale }}">  --}}
                                <span class="w-100 mx-1 text">{{ __(strtoupper($locale)) }}</span>
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div><!-- End .header-menu -->
            </div><!-- End .header-dropdown -->
        </div><!-- End .header-right -->
    </div>
</div>