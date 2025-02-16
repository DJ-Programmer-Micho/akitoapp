<div class="col-auto layout-rightside-col" wire:ignore>
    <div class="overlay"></div>
    <div class="layout-rightside">
        <div class="card h-100 rounded-0">
            <div class="card-body p-0">
                <div class="p-3">
                    <h6 class="text-muted mb-0 text-uppercase">Recent Activity</h6>
                </div>
                <div data-simplebar style="max-height: 510px;" class="p-3 pt-0">
                    <div class="acitivity-timeline acitivity-main">
                        @forelse ($activities as $notification)
                            @php
                                // 1) Extract data
                                $data = $notification->data;
                                $msg  = $data['message'] ?? 'No message';
                                $time = $notification->created_at->diffForHumans();
                    
                                // 2) Decide on an icon & color class based on the event type
                                //    For instance, if the message includes keywords or status.
                                
                                $icon = 'ri-bell-line'; // default
                                $bgClass = 'bg-secondary-subtle text-secondary'; // default styling
                    
                                if(str_contains($msg, 'Transferred to Driver')) {
                                    $icon = 'ri-truck-line';
                                    $bgClass = 'bg-info-subtle text-info';
                                }
                                elseif(str_contains($msg, 'New Order')) {
                                    $icon = 'ri-shopping-cart-2-line';
                                    $bgClass = 'bg-success-subtle text-success';
                                }
                                elseif(str_contains($msg, 'Payment has been updated to successful')) {
                                    $icon = 'ri-bank-card-fill';
                                    $bgClass = 'bg-success-subtle text-success';
                                }
                                elseif(str_contains($msg, 'shipping')) {
                                    $icon = 'ri-ship-2-line';
                                    $bgClass = 'bg-warning-subtle text-warning';
                                }
                                elseif(str_contains($msg, 'refunded')) {
                                    $icon = 'ri-refund-line';
                                    $bgClass = 'bg-danger-subtle text-danger';
                                }
                                // etc. Add more conditions as needed
                    
                                // 3) Maybe you also want to parse out e.g. "tracking_number"
                                $tracking = $data['tracking_number'] ?? null;
                                $orderNum = $data['orderNumber'] ?? null;
                            @endphp
                    
                            <div class="acitivity-item d-flex py-3">
                                <div class="flex-shrink-0 avatar-xs acitivity-avatar">
                                    <div class="avatar-title rounded-circle {{ $bgClass }}">
                                        <i class="{{ $icon }}"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    {{-- Title or short heading --}}
                                    <h6 class="mb-1 lh-base">
                                        {!! $msg !!}
                                    </h6>
                                    {{-- Possibly show order/tracking link or details --}}
                                    @if($tracking)
                                        <p class="text-muted mb-1">Tracking: <span class="fw-semibold">#{{ $tracking }}</span></p>
                                    @endif
                                    {{-- If you have an order ID route, you can link to it... --}}
                                    @if($orderNum)
                                        <a href="{{ route('super.orderManagementsViewer', [app()->getLocale(), 'id' => $orderNum]) }}" 
                                           class="btn btn-sm btn-soft-secondary">
                                           View Order
                                        </a>
                                    @endif
                                    {{-- time-ago text --}}
                                    <small class="mb-0 text-muted">{{ $time }}</small>
                                </div>
                            </div>
                        @empty
                            <div class="py-3 text-center text-muted">
                                {{ __('No Notifications') }}
                            </div>
                        @endforelse
                    </div>
                    
                </div>

                <div class="p-3 mt-2">
                    <h6 class="text-muted mb-3 text-uppercase">Top 10 Categories</h6>
                    <ol class="ps-3 text-muted">
                        @forelse($topCategories as $cat)
                            <li class="py-1">
                                <a href="#" class="text-muted">
                                    {{ $cat['name'] }}
                                    <span class="float-end">
                                        ({{ $cat['total_qty'] }})
                                    </span>
                                </a>
                            </li>
                        @empty
                            <li class="py-1">{{ __('No categories found.') }}</li>
                        @endforelse
                    </ol>
                    {{-- <div class="mt-3 text-center">
                        <a href="javascript:void(0);" class="text-muted text-decoration-underline">View all Categories</a>
                    </div> --}}
                </div>
                

                {{-- <div class="card sidebar-alert bg-light border-0 text-center mx-4 mb-0 mt-3">
                    <div class="card-body">
                        <img src="assets/images/giftbox.png" alt="">
                        <div class="mt-4">
                            <h5>Invite New Seller</h5>
                            <p class="text-muted lh-base">Refer a new seller to us and earn $100
                                per refer.</p>
                            <button type="button" class="btn btn-primary btn-label rounded-pill"><i class="ri-mail-fill label-icon align-middle rounded-pill fs-16 me-2"></i>
                                Invite Now</button>
                        </div>
                    </div>
                </div> --}}

            </div>
        </div> <!-- end card-->
    </div> <!-- end .rightbar-->

<script>
    document.addEventListener('livewire:load', function () {
layoutRightSideBtn = document.querySelector(".layout-rightside-btn");
layoutRightSideBtn && (Array.from(document.querySelectorAll(".layout-rightside-btn")).forEach(function (e) {
    var t = document.querySelector(".layout-rightside-col");
    e.addEventListener("click", function () {
        t.classList.contains("d-block") ? (t.classList.remove("d-block"), t.classList.add("d-none")) : (t.classList.remove("d-none"), t.classList.add("d-block"))
    })
}), window.addEventListener("resize", function () {
    var e = document.querySelector(".layout-rightside-col");
    e && Array.from(document.querySelectorAll(".layout-rightside-btn")).forEach(function () {
        window.outerWidth < 1699 || 3440 < window.outerWidth ? e.classList.remove("d-block") : 1699 < window.outerWidth && e.classList.add("d-block")
    }), "semibox" == document.documentElement.getAttribute("data-layout") && (e.classList.remove("d-block"), e.classList.add("d-none"))
}), overlay = document.querySelector(".overlay")) && document.querySelector(".overlay").addEventListener("click", function () {
    1 == document.querySelector(".layout-rightside-col").classList.contains("d-block") && document.querySelector(".layout-rightside-col").classList.remove("d-block")
}), window.addEventListener("load", function () {
    var e = document.querySelector(".layout-rightside-col");
    e && Array.from(document.querySelectorAll(".layout-rightside-btn")).forEach(function () {
        window.outerWidth < 1699 || 3440 < window.outerWidth ? e.classList.remove("d-block") : 1699 < window.outerWidth && e.classList.add("d-block")
    }), "semibox" == document.documentElement.getAttribute("data-layout") && 1699 < window.outerWidth && (e.classList.remove("d-block"), e.classList.add("d-none"))
});
});
</script>

</div> <!-- end col -->
