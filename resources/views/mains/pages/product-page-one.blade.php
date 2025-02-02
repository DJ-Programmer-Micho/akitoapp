@extends('mains.layout.app')
@section('business-content')
<div class="main">
    <x-mains.components.products.nav-one />
    <div class="page-content">
        <div class="container">
            <x-mains.components.products.product-view-one :product=$product/>
            <x-mains.components.products.product-information-one :product=$product/>
            <x-mains.components.products.product-reco-one :locale=$locale :id="$product->id"/>
        </div>
    </div>
</div>
@endsection