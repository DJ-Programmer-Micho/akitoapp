
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
                    <h4 class="alert-heading text-dark text-center">{{__('Payment Cancelled')}}</h4>
                    <div class="text-center">
                        <lord-icon
                            src="https://cdn.lordicon.com/fkelfmfi.json"
                            trigger="loop"
                            delay="2000"
                            state="hover-oscillate"
                            colors="primary:#c71f16,secondary:#e86830"
                            style="width:150px;height:150px">
                        </lord-icon>
                    </div>
                    <p class="text-dark text-center"><b>{{__('You Have Cancelled The Order or Time-Out Paying')}}</b></p>
                    <hr>
                    <div class="text-center mb-1">
                        <a href="{{ route('business.account', ['locale' => app()->getLocale()]) }}" class="btn btn-primary px-1"><b>{{__('Back to Orders')}}</b></a>
                    </div>
                  </div>
            </div>
            <div class="col-4"></div>
        </div>
    </div>
</div>
@endsection