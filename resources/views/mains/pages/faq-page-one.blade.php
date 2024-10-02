
@extends('mains.layout.app')
@section('business-content')
<style>
    .product-price {
        color: #343e52;
    }
</style>
<div class="main">
    <x-mains.components.shops.image-header-one />
    <x-mains.components.shops.nav-two />
    <div class="container">
        <x-mains.components.faq.faq-one/>
    </div>
    <x-mains.components.faq.cta-one/>
</div>
@endsection