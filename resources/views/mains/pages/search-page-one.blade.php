
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
        <x-mains.components.search.search-one :products="$products" :searchQuery="$searchQuery"/>
    </div>
</div>
@endsection