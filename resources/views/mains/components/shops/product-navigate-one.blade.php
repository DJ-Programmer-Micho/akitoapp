<nav aria-label="Page navigation">
    <ul class="pagination justify-content-center">
        {{-- Previous Page Link --}}
        <li class="page-item {{ $products->currentPage() == 1 ? 'disabled' : '' }}">
            <a class="page-link page-link-prev" href="{{ $products->currentPage() > 1 ? route('business.productShop', array_merge(request()->query(), ['page' => $products->currentPage() - 1, 'locale' => app()->getLocale()])) : '#' }}" aria-label="Previous" tabindex="-1" aria-disabled="{{ $products->currentPage() == 1 ? 'true' : 'false' }}">
                <span aria-hidden="true">
                    <i class="fa fa-chevron-{{ app()->getLocale() === 'ar' || app()->getLocale() === 'ku' ? 'right' : 'left' }}"></i>
                </span>
                <span class="nav-txt">
                {{__('Prev')}}
                </span>
            </a>
        </li>

        {{-- Page Number Links --}}
        @php
            $currentPage = $products->currentPage();
            $lastPage = $products->lastPage();
            $startPage = max(1, $currentPage - 2);
            $endPage = min($lastPage, $currentPage + 2);
        @endphp

        {{-- First Page Link --}}
        @if ($startPage > 1)
            <li class="page-item">
                <a class="page-link" href="{{ route('business.productShop', array_merge(request()->query(), ['page' => 1, 'locale' => app()->getLocale()])) }}">1</a>
            </li>
            @if ($startPage > 2)
                <li class="page-item disabled"><span class="page-link">...</span></li>
            @endif
        @endif

        {{-- Middle Pages Links --}}
        @for ($i = $startPage; $i <= $endPage; $i++)
            <li class="page-item {{ $currentPage == $i ? 'active' : '' }}">
                <a class="page-link" href="{{ route('business.productShop', array_merge(request()->query(), ['page' => $i, 'locale' => app()->getLocale()])) }}">{{ $i }}</a>
            </li>
        @endfor

        {{-- Last Page Link --}}
        @if ($endPage < $lastPage)
            @if ($endPage < $lastPage - 1)
                <li class="page-item disabled"><span class="page-link">...</span></li>
            @endif
            <li class="page-item">
                <a class="page-link" href="{{ route('business.productShop', array_merge(request()->query(), ['page' => $lastPage, 'locale' => app()->getLocale()])) }}">{{ $lastPage }}</a>
            </li>
        @endif

        {{-- Total Pages --}}
        <li class="page-item-total">{{__('of')}} {{ $products->lastPage() }}</li>

        {{-- Next Page Link --}}
        <li class="page-item {{ $products->currentPage() == $products->lastPage() ? 'disabled' : '' }}">
            <a class="page-link page-link-next" href="{{ $products->currentPage() < $products->lastPage() ? route('business.productShop', array_merge(request()->query(), ['page' => $products->currentPage() + 1, 'locale' => app()->getLocale()])) : '#' }}" aria-label="Next">
                <span class="nav-txt">
                    {{__('Next')}}
                </span>
                <span aria-hidden="true">
                    <i class="fa fa-chevron-{{ app()->getLocale() === 'ar' || app()->getLocale() === 'ku' ? 'left' : 'right' }}"></i>
                </span>
            </a>
        </li>
    </ul>
</nav>
