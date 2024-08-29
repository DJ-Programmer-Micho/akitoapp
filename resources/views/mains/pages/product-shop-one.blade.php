@extends('mains.layout.app')
@section('business-content')
<div class="main">
    <x-mains.components.shops.image-header-one />
    <x-mains.components.shops.nav-one />
    <div class="page-content">
        <div class="container">
            <div class="row">
                <div class="col-lg-9">
                    <x-mains.components.shops.toolbox-one :products="$products"/>
                    <x-mains.components.shops.product-show-one :products="$products" />
                    <x-mains.components.shops.product-navigate-one />
                </div>
                <aside class="col-lg-3 order-lg-first">
                    <x-mains.components.shops.aside-filter-one :brands="$brands" :categories="$categories" :sub-category="$subCategory" :sizes="$sizes" :colors="$colors" :capacities="$capacities" :materials="$materials" :minPrice="$minPrice" :maxPrice="$maxPrice"/>
                </aside>
            </div>
        </div>
    </div>

</div>
@endsection