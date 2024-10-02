
@extends('mains.layout.app')
@section('business-content')
<style>
    .product-price {
        color: #343e52;
    }
</style>
<div class="main">
    <x-mains.components.shops.image-header-one />
    <div class="container">
        {{-- <x-mains.components.shops.nav-two /> --}}
        <x-mains.components.contactus.contactus-one/>
    </div>
</div>
@endsection