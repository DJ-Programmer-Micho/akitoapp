
@extends('mains.layout.app')
@section('business-content')
<style>
    .product-price {
        color: #343e52;
    }
</style>
<div class="main">
    <x-mains.components.intro-section-one/>
    <div class="mb-7 mb-lg-11"></div>
    <x-mains.components.featured-one />
    <div class="mb-7 mb-lg-11"></div><!-- End .mb-7 -->
    {{-- <x-mains.components.cta-banner-one /> --}}
    {{-- <x-mains.components.deal-time-one /> --}}
    <div class="bg-light py-4">
        <x-mains.components.brand-slider-one/>
    </div>
    {{-- <div class="container">
        <hr class="mt-3 mb-6">
    </div><!-- End .container --> --}}
    <div class="mt-3"></div>
    <x-mains.components.category-product-one :productsData="$productsCat1" :title="$productsCat1Title"/>
    <div class="container">
        <hr class="mt-5 mb-6">
    </div><!-- End .container -->
    <x-mains.components.category-product-one :productsData="$productsCat2" :title="$productsCat2Title"/>
    <div class="container">
        <hr class="mt-5 mb-6">
    </div><!-- End .container -->
    <x-mains.components.category-product-one :productsData="$productsCat3" :title="$productsCat3Title"/>
    <div class="container">
        <hr class="mt-5 mb-6">
    </div><!-- End .container -->
    {{-- <x-mains.components.trending-product-one />
    <div class="container">
        <hr class="mt-5 mb-6">
    </div><!-- End .container --> --}}
    {{-- <x-mains.components.top-selling-one />
    <div class="container">
        <hr class="mt-5 mb-0">
    </div><!-- End .container --> --}}
    <x-mains.components.service-view-one />
    <x-mains.components.cta-coupon-one />
</div>
@endsection