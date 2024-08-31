<nav aria-label="Page navigation">
    <ul class="pagination justify-content-center">
        {{-- Previous Page Link --}}
        <li class="page-item {{ $products->currentPage() == 1 ? 'disabled' : '' }}">
            <a class="page-link page-link-prev" href="{{ $products->currentPage() > 1 ? route('business.productShop', array_merge(request()->query(), ['page' => $products->currentPage() - 1, 'locale' => app()->getLocale()])) : '#' }}" aria-label="Previous" tabindex="-1" aria-disabled="{{ $products->currentPage() == 1 ? 'true' : 'false' }}">
                <span aria-hidden="true"><i class="icon-long-arrow-left"></i></span>Prev
            </a>
        </li>

        {{-- Page Number Links --}}
        @for ($i = 1; $i <= $products->lastPage(); $i++)
            <li class="page-item {{ $products->currentPage() == $i ? 'active' : '' }}" aria-current="{{ $products->currentPage() == $i ? 'page' : '' }}">
                <a class="page-link" href="{{ route('business.productShop', array_merge(request()->query(), ['page' => $i, 'locale' => app()->getLocale()])) }}">{{ $i }}</a>
            </li>
        @endfor

        {{-- Total Pages --}}
        <li class="page-item-total">of {{ $products->lastPage() }}</li>

        {{-- Next Page Link --}}
        <li class="page-item {{ $products->currentPage() == $products->lastPage() ? 'disabled' : '' }}">
            <a class="page-link page-link-next" href="{{ $products->currentPage() < $products->lastPage() ? route('business.productShop', array_merge(request()->query(), ['page' => $products->currentPage() + 1, 'locale' => app()->getLocale()])) : '#' }}" aria-label="Next">
                Next <span aria-hidden="true"><i class="icon-long-arrow-right"></i></span>
            </a>
        </li>
    </ul>
</nav>
