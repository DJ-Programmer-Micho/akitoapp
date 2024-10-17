
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
                    <h4 class="alert-heading text-danger text-center">{{__('Operation Failed')}}</h4>
                    <p class="text-dark text-center">{{__('Something Went Wrong')}}</p>
                    <div class="text-center">
                        <lord-icon
                            src="https://cdn.lordicon.com/lltgvngb.json"
                            trigger="loop"
                            delay="2000"
                            state="hover-oscillate"
                            colors="primary:#c71f16,secondary:#e86830"
                            style="width:150px;height:150px">
                        </lord-icon>
                    </div>
                    <p class="text-dark text-center">{{__('Please Contact Support Team')}}</p>
                    <hr>
                    <div class="d-flex justify-content-around">
                        <div class="text-center">
                            <a href="mailto:support@akitu-co.com" class="btn btn-primary">{{__('Email Us')}}</a>
                        </div>
                        <div class="text-center">
                            <a href="tel:009647507747742" class="btn btn-primary">{{__('Call Us')}}</a>
                        </div>
                    </div>
                  </div>
                <h1></h1>
            </div>
            <div class="col-4"></div>
        </div>

    </div>
</div>
@endsection