<div class="heading text-center mb-4">
    <h2 class="title">{{ __('Coming Soon Brands') }}</h2>
    <p class="title-desc">{{ __('With Italian Coffee Company all new brands with us') }}</p>
</div>

<section class="slide-option">
    <div id="infinite-2">
        <div class="slider-container">
            <ul id="soon-list" class="highway-lane">
                @foreach($soons as $soon)
                <li class="highway-car">
                    <a href="{{ route('business.soon', ['locale' => app()->getLocale()]) }}">
                        <img loading="lazy" src="{{ app('cloudfront') . $soon->image }}" alt="{{ $brand->coming_soon_translation->name ?? 'Unknown Brand' }}" style="max-width: 100%; height: 120px;">
                    </a>
                </li>
                @endforeach
            </ul>
        </div>
    </div>
</section>

@push('styles-password')
<style>
#infinite-2 {
    overflow: hidden;
    position: relative;
    height: 150px;
}
</style>
@endpush

@push("productView")
<script>
document.addEventListener('DOMContentLoaded', function () {
    const brandList = document.getElementById('soon-list');
    const brands = Array.from(brandList.children);
    const brandCount = brands.length;
    
    // Clone all brands and append them to create a seamless loop
    brands.forEach(brand => {
        const clone = brand.cloneNode(true);
        brandList.appendChild(clone);
    });
    
    // Calculate total slider width before animation
    const sliderWidth = brandList.scrollWidth / 2;
    
    // Set up animation
    let position = 0;
    const speed = 1; // Pixels per frame - adjust for speed
    
    function moveSlider() {
        position -= speed;
        
        // Reset position when we've scrolled through the first set of brands
        if (position <= -sliderWidth) {
            position = 0;
        }
        
        brandList.style.transform = `translateX(${position}px)`;
        requestAnimationFrame(moveSlider);
    }
    
    // Start the animation
    requestAnimationFrame(moveSlider);
    
    // Pause on hover (optional)
    const infinite = document.getElementById('infinite');
    infinite.addEventListener('mouseenter', () => {
        brandList.style.animationPlayState = 'paused';
    });
    
    infinite.addEventListener('mouseleave', () => {
        brandList.style.animationPlayState = 'running';
    });
});
</script>
@endpush