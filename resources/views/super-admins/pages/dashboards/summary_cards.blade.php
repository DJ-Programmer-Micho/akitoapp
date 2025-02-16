<div class="row">
    <!-- Total Earnings Card -->
    <div class="col-xl-3 col-md-6">
        <div class="card card-animate">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1 overflow-hidden">
                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0">
                            Total Earnings</p>
                    </div>
                    <div class="flex-shrink-0">
                        <!-- Static percentage for now -->
                        {{-- <h5 class="text-success fs-14 mb-0">
                            <i class="ri-arrow-right-up-line fs-13 align-middle"></i>
                            +16.24 %
                        </h5> --}}
                    </div>
                </div>
                <div class="d-flex align-items-end justify-content-between mt-4">
                    <div>
                        @php
                            // Format total earnings as thousands (k)
                            $earningsInK = number_format($totalEarnings / 1000, 2);
                        @endphp
                        <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                            $<span class="counter-value" data-target="{{ $earningsInK }}">{{ $earningsInK }}</span>k
                        </h4>
                        <a href="#" class="text-decoration-underline text-muted">View net earnings</a>
                    </div>
                    <div class="avatar-sm flex-shrink-0">
                        <span class="avatar-title bg-success-subtle rounded fs-3">
                            <i class="bx bx-dollar-circle text-success"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div><!-- end col -->

    <!-- Orders Card -->
    <div class="col-xl-3 col-md-6">
        <div class="card card-animate">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1 overflow-hidden">
                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Orders</p>
                    </div>
                    <div class="flex-shrink-0">
                        <!-- Static percentage for now -->
                        {{-- <h5 class="text-danger fs-14 mb-0">
                            <i class="ri-arrow-right-down-line fs-13 align-middle"></i>
                            -3.57 %
                        </h5> --}}
                    </div>
                </div>
                <div class="d-flex align-items-end justify-content-between mt-4">
                    <div>
                        <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                            <span class="counter-value" data-target="{{ $ordersCount }}">{{ $ordersCount }}</span>
                        </h4>
                        <a href="#" class="text-decoration-underline text-muted">View all orders</a>
                    </div>
                    <div class="avatar-sm flex-shrink-0">
                        <span class="avatar-title bg-info-subtle rounded fs-3">
                            <i class="bx bx-shopping-bag text-info"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div><!-- end col -->

    <!-- Quantity Sells Card -->
    <div class="col-xl-3 col-md-6">
        <div class="card card-animate">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1 overflow-hidden">
                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Quantity Sells</p>
                    </div>
                    {{-- <div class="flex-shrink-0">
                        <h5 class="text-muted fs-14 mb-0">+0.00 %</h5>
                    </div> --}}
                </div>
                <div class="d-flex align-items-end justify-content-between mt-4">
                    <div>
                        <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                            <span class="counter-value" data-target="{{ $quantitySells }}">{{ $quantitySells }}</span>
                        </h4>
                        <a href="#" class="text-decoration-underline text-muted">Order Managements</a>
                    </div>
                    <div class="avatar-sm flex-shrink-0">
                        <span class="avatar-title bg-primary-subtle rounded fs-3">
                            <i class="bx bx-wallet text-primary"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div><!-- end col -->

    <!-- Customers Card -->
    <div class="col-xl-3 col-md-6">
        <div class="card card-animate">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1 overflow-hidden">
                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Customers</p>
                    </div>
                    <div class="flex-shrink-0">
                        {{-- <h5 class="text-success fs-14 mb-0">
                            <i class="ri-arrow-right-up-line fs-13 align-middle"></i>
                            +29.08 %
                        </h5> --}}
                    </div>
                </div>
                <div class="d-flex align-items-end justify-content-between mt-4">
                    <div>
                        <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                            <span class="counter-value" data-target="{{ $customersCount }}">{{ $customersCount }}</span>
                        </h4>
                        <a href="#" class="text-decoration-underline text-muted">See details</a>
                    </div>
                    <div class="avatar-sm flex-shrink-0">
                        <span class="avatar-title bg-warning-subtle rounded fs-3">
                            <i class="bx bx-user-circle text-warning"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div><!-- end col -->
</div> <!-- end row -->
