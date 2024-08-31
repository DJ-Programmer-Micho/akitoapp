<div class="megamenu demo">
        <h4 class="py-5">{{__('Brands')}}</h4><!-- End .menu-title -->
        <div class="row">
            @foreach($brands as $brand)
            <div class="col-2 mt-3">
                {{-- {{ route('brand.show', $brand->id) }} --}}
                <a href="{{ route('business.productShop', ['locale' => app()->getLocale(), 'brands[]' => $brand->id]) }}"> 
                    <span class="demo-bg" style="background-image: url('{{ app('cloudfront').$brand->image }}');" alt="{{app('cloudfront').$brand->image }}"></span>
                    <p class="text-center">{{ $brand->brandTranslation->name }}</p>
                </a>
            </div><!-- End .demo-item -->
        @endforeach
        </div><!-- End .demo-list -->
</div><!-- End .megamenu -->