<!-- Actual Slides -->
<div class="heading text-center mb-4">
    <h2 class="title">{{ __('Brands') }}</h2>
    <p class="title-desc">{{ __('Today’s deal and more') }}</p>
</div>

<section class="slide-option">
    <div id="infinite" class="highway-slider">
        <div class="highway-barrier">
            <ul id="brand-list" class="highway-lane">
                @foreach($brands as $brand)
                <li class="highway-car">
                    <a href="{{ route('business.productShop', ['locale' => app()->getLocale(), 'brands[]' => $brand->id]) }}">
                        <img src="{{ app('cloudfront') . $brand->image }}" alt="{{ $brand->brandtranslation->name ?? 'Unknown Brand' }}" style="max-width: 100%; height: 120px;">
                    </a>
                </li>
                @endforeach
                <!-- Duplicate the items for seamless looping -->
                @foreach($brands as $brand)
                <li class="highway-car">
                    <a href="{{ route('business.productShop', ['locale' => app()->getLocale(), 'brands[]' => $brand->id]) }}">
                        <img src="{{ app('cloudfront') . $brand->image }}" alt="{{ $brand->brandtranslation->name ?? 'Unknown Brand' }}" style="max-width: 100%; height: 120px;">
                    </a>
                </li>
                @endforeach
            </ul>
        </div>
    </div>
</section>

@push('styles-password')
<style>
#infinite {
    overflow: hidden;
    position: relative;
    height: 150px; /* Adjust as necessary */
}

.highway-lane {
    display: flex;
    animation: scroll 10s linear infinite; /* Changed to 10 seconds for faster speed */
}

.highway-car {
    min-width: 180px; /* Set a fixed width for each brand */
    display: flex;
    justify-content: center;
    align-items: center;
}

@keyframes scroll {
    0% {
        transform: translateX(0);
    }
    100% {
        transform: translateX(-50%); /* Scroll half the width of the list */
    }
}
</style>
@endpush

@push("productView")
<script>
document.addEventListener('DOMContentLoaded', function () {
    const brandList = document.getElementById('brand-list');
    const totalBrands = brandList.children.length; // Count total brands
    const brandWidth = 180; // Set this to match your .highway-car width

    // Duplicate the brand items to maintain a continuous effect
    for (let i = 0; i < totalBrands; i++) {
        const clone = brandList.children[i].cloneNode(true);
        brandList.appendChild(clone);
    }

    // Adjust the animation duration based on total brands
    const totalWidth = brandWidth * totalBrands;
    document.querySelector('.highway-lane').style.animationDuration = `${totalWidth / 100}s`; // Adjusted for faster speed
});
</script>
@endpush
