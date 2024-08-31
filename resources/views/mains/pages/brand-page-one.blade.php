
@extends('mains.layout.app')
@section('business-content')
<style>
    .product-price {
        color: #343e52;
    }
</style>
<div class="main">
    <div class="container">
        <x-mains.components.shops.nav-two />
        <x-mains.components.brands.grid-one/>
    </div>
</div>
@endsection