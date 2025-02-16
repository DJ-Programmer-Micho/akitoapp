
@extends('mains.layout.app')
@section('business-content')
<style>
    .product-price {
        color: #343e52;
    }
</style>
<div class="main">
    <div class="container">
        <div class="row">
            <div class="col-4"></div>
            <div class="col-12 col-md-4 my-5">
                <script src="https://cdn.lordicon.com/lordicon.js"></script>

                <div class="alert alert-secondary" style="border: 1px solid #343e52" role="alert">
                    <h4 class="alert-heading text-dark text-center">{{__('Something Went Wrong')}}</h4>
                    <div class="text-center">
                        <lord-icon
                            src="https://cdn.lordicon.com/lltgvngb.json"
                            trigger="loop"
                            delay="2000"
                            colors="primary:#c71f16,secondary:#e86830"
                            style="width:150px;height:150px">
                        </lord-icon>
                    </div>
                    <div class="description">
                        <p style="line-height: 19px;"><b>{{__("Reasons of Failed Payment:")}}</b>
                        </p>
                        <p class="subsT"><i class="fa-solid fa-triangle-exclamation fa-beat" style="color: #cc0022;"></i> <b>{{__("Faulty internet connection")}}</p></b>
                        <p class="subsT"><i class="fa-solid fa-triangle-exclamation fa-beat" style="color: #cc0022;"></i> <b>{{__("Entering incorrect payment details")}}</p></b>
                        <p class="subsT"><i class="fa-solid fa-triangle-exclamation fa-beat" style="color: #cc0022;"></i> <b>{{__("There is not enough Balance")}}</p></b>
                        <p class="subsT"><i class="fa-solid fa-triangle-exclamation fa-beat" style="color: #cc0022;"></i> <b>{{__("Payment method not supported")}}</p></b>
                        <p class="subsT"><i class="fa-solid fa-triangle-exclamation fa-beat" style="color: #cc0022;"></i> <b>{{__("Downtime and/or maintenance")}}</p></b>
                        <p class="subsT"><i class="fa-solid fa-triangle-exclamation fa-beat" style="color: #cc0022;"></i> <b>{{__("Check Your BanK")}}</p></b>
                    </div>
                    <hr>
                    <div class="text-center mb-1">
                        <a href="{{ route('business.home', ['locale' => app()->getLocale()]) }}" class="btn btn-primary px-1"><b>{{__('Home')}}</b></a>
                    </div>
                  </div>
            </div>
            <div class="col-4"></div>
        </div>
    </div>
</div>
@endsection