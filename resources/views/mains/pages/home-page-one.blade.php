
@extends('mains.layout.app')
@section('business-content')
<style>
    .product-price {
        color: #343e52;
    }
</style>
<div class="main">
    <x-mains.components.intro-section-one :sliders="$sliders"/>
    <div class="bg-light" style="padding-top: 2rem; padding-bottom: 2rem">
    <x-mains.components.category-anime-one :categoiresData="$categoriesData"/>
    </div>
    <div class="mb-1 mb-lg-3"></div>
    {{-- <x-mains.components.featured-one /> --}}
    <x-mains.components.category-product-general-one :productsData="$featured_products['products']" :title="$featured_products['title']"/>
    <x-mains.components.category-product-general-one :productsData="$on_sale_products['products']" :title="$on_sale_products['title']"/>
    <div class="mb-1 mb-lg-3"></div>
    {{-- <x-mains.components.cta-banner-one /> --}}
    {{-- <x-mains.components.deal-time-one /> --}}
    {{-- <div class="container">
        <hr class="mt-3 mb-6">
    </div><!-- End .container --> --}}
    <div class="mt-3"></div>
    {{-- @php
        dd($productsCat1);
    @endphp --}}
    @if(isset($soons) && $soons->count() > 4)
    <div class="bg-light py-4">
        <x-mains.components.brand-slider-one/>
    </div>
    @endif
    @foreach($categoryProducts as $index => $productData)
    <x-mains.components.banner-width-one :image="$imageBanner[$index]['image']" />
    <x-mains.components.category-product-one :productsData="$productData['products']" :title="$productData['title']" />
    
    <div class="container">
        <hr class="mt-5 mb-6">
    </div><!-- End .container -->
    @endforeach


    @if(isset($soons) && $soons->count() > 4)
    <div class="bg-light py-4">
        <x-mains.components.soon-slider-one :soons="$soons"/>
    </div>
    @else
    <div class="bg-light py-4">
        <x-mains.components.brand-slider-one/>
    </div>
    @endif
    {{-- <x-mains.components.trending-product-one />
    <div class="container">
        <hr class="mt-5 mb-6">
    </div><!-- End .container --> --}}
    {{-- <x-mains.components.top-selling-one />
    <div class="container">
        <hr class="mt-5 mb-0">
    </div><!-- End .container --> --}}
    <x-mains.components.service-view-one />
    {{-- <div class="bg-light" style="padding-top: 2rem; padding-bottom: 2rem"> --}}
    {{-- <x-mains.components.cta-coupon-one /> --}}
    {{-- </div> --}}
</div>
@endsection