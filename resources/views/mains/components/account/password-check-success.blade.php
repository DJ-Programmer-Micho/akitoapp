
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
                    <h4 class="alert-heading text-dark text-center">{{__('All Done')}}</h4>
                    <div class="text-center">
                        <lord-icon
                            src="https://cdn.lordicon.com/sjoccsdj.json"
                            trigger="loop"
                            delay="2000"
                            state="hover-loading"
                            colors="primary:#109121"
                            style="width:150px;height:150px">
                        </lord-icon>
                    </div>
                    <p class="text-dark text-center">{{__('Your Password Has Been Reset Succesfully')}}</p>
                    <hr>
                    <div class="text-center">
                        <a href="{{ route('business.home', ['locale' => app()->getLocale()]) }}" class="btn btn-primary">{{__('Home')}}</a>
                    </div>
                  </div>
                <h1></h1>
            </div>
            <div class="col-4"></div>
        </div>

    </div>
</div>
@endsection