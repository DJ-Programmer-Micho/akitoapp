<div class="megamenu demo">

        <h4 class="py-5">{{__('Categories')}}</h4><!-- End .menu-title -->
        <div class="row">
            @foreach($categories as $category)
            <div class="col-2 mt-3">
                {{-- {{ route('brand.show', $brand->id) }} --}}
                <a href="{{ route('business.productShop', ['locale' => app()->getLocale(), 'categories[]' => $category->id]) }}"> 
                    <span class="demo-bg" style="background-image: url('{{ app('cloudfront').$category->image }}');" alt="{{app('cloudfront').$category->image }}"></span>
                    <p class="text-center">{{ $category->categoryTranslation->name }}</p>
                </a>
            </div><!-- End .demo-item -->
        @endforeach
        </div><!-- End .demo-list -->
</div><!-- End .megamenu -->