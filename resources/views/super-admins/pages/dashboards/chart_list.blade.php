<div class="row">
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Best Selling Products</h4>
                <div class="flex-shrink-0">
                    <a href="{{route('super.product.table',['locale' => app()->getLocale()])}}" type="button" class="btn btn-soft-primary btn-sm">
                        Check All
                    </a>
                    {{-- <div class="dropdown card-header-dropdown">
                        <a class="text-reset dropdown-btn" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="fw-semibold text-uppercase fs-12">Sort by:
                            </span><span class="text-muted">Today<i class="mdi mdi-chevron-down ms-1"></i></span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a class="dropdown-item" href="#">Today</a>
                            <a class="dropdown-item" href="#">Yesterday</a>
                            <a class="dropdown-item" href="#">Last 7 Days</a>
                            <a class="dropdown-item" href="#">Last 30 Days</a>
                            <a class="dropdown-item" href="#">This Month</a>
                            <a class="dropdown-item" href="#">Last Month</a>
                        </div>
                    </div> --}}
                </div>
            </div><!-- end card header -->

            <div class="card-body">
                <div class="table-responsive table-card">
                    <table class="table table-hover table-centered align-middle table-nowrap mb-0">
                        <tbody>
                            @foreach($bestSellingProducts as $item)
                                @php
                                    // The aggregated data
                                    $ordersCount  = $item->orders_count;   // # distinct orders
                                    $unitsSold    = $item->total_units;    // sum of quantity
                                    $totalRevenue = $item->total_revenue;  // sum of total
    
                                    // The related Product
                                    $product      = $item->product;
                                    if (!$product) continue; // safety check
    
                                    // Pull the first translation (assuming we filtered by locale)
                                    $translation  = $product->productTranslation->first();
                                    $productName  = $translation ? $translation->name : 'Unnamed Product';
    
                                    // Variation info (price, stock, images)
                                    $variation    = $product->variation;
                                    if (!$variation) continue; // safety check
                                    $price        = $variation->price;
                                    $stock        = $variation->stock;
    
                                    // Product image
                                    $firstImage   = $variation->images->first();
                                    $imageUrl     = $firstImage
                                        ? app('cloudfront') . $firstImage->image_path
                                        : 'path/to/default/image.jpg';
                                @endphp
    
                                <tr>
                                    <!-- Product Image + Name -->
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-light rounded p-1 me-2">
                                                <img src="{{ $imageUrl }}"
                                                     alt="{{ $productName }}"
                                                     class="img-fluid d-block" />
                                            </div>
                                            <div>
                                                <h5 class="fs-13 my-1">
                                                    <!-- Link to a product details page, if you have one -->
                                                    <a href="#" class="text-reset">
                                                        {{ $productName }}
                                                    </a>
                                                </h5>
                                                <span class="text-muted">
                                                    {{ optional($product->created_at)->format('d M Y') }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>
    
                                    <!-- Price -->
                                    <td>
                                        <h5 class="fs-14 my-1 fw-normal">
                                            {{ number_format($price, 0) }} IQD
                                        </h5>
                                        <span class="text-muted">Price</span>
                                    </td>
    
                                    <!-- Orders or Units Sold? 
                                         - If you want total units, show $unitsSold. 
                                         - If you want distinct # of orders, show $ordersCount. -->
                                    <td>
                                        <h5 class="fs-14 my-1 fw-normal">{{ $unitsSold }}</h5>
                                        <span class="text-muted">Units Sold</span>
                                    </td>
    
                                    <!-- Stock -->
                                    <td>
                                        <h5 class="fs-14 my-1 fw-normal">
                                            @if ($stock <= 0)
                                                <span class="badge bg-danger-subtle text-danger">Out of stock</span>
                                            @else
                                                {{ $stock }}
                                            @endif
                                        </h5>
                                        <span class="text-muted">Stock</span>
                                    </td>
    
                                    <!-- Total Revenue (Amount) -->
                                    <td>
                                        <h5 class="fs-14 my-1 fw-normal">
                                            {{ number_format($totalRevenue, 0) }} IQD
                                        </h5>
                                        <span class="text-muted">&#8776 Amount</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

    <div class="col-xl-6">
        <div class="card card-height-100">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Top Buyers</h4>
                <div class="flex-shrink-0">
                    <a href="{{route('super.product.table',['locale' => app()->getLocale()])}}" type="button" class="btn btn-soft-primary btn-sm">
                        Check All
                    </a>
                    {{-- <div class="dropdown card-header-dropdown">
                        <a class="text-reset dropdown-btn" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="text-muted">Report<i class="mdi mdi-chevron-down ms-1"></i></span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a class="dropdown-item" href="#">Download Report</a>
                            <a class="dropdown-item" href="#">Export</a>
                            <a class="dropdown-item" href="#">Import</a>
                        </div>
                    </div> --}}
                </div>
            </div><!-- end card header -->
    
            <div class="card-body">
                <div class="table-responsive table-card">
                    <table class="table table-centered table-hover align-middle table-nowrap mb-0">
                        <tbody>
                            @php
                                $maxAvg = max(1, $topCustomers->map(function($c){
                                    $cnt = $c->orders_count;
                                    $amt = $c->orders_sum_total_amount_iqd;
                                    return $cnt ? ($amt/$cnt) : 0;
                                })->max());
                            @endphp
    
                            @foreach($topCustomers as $customer)
                                @php
                                    $brandName = $customer->customer_profile->brand_name 
                                                 ?? ($customer->customer_profile->first_name 
                                                     . ' ' 
                                                     . $customer->customer_profile->last_name);
    
                                    $stockOrCount = $customer->orders_count;
                                    $totalSpent = $customer->orders_sum_total_amount_iqd;
                                    $customerType = $customer->customer_profile->business_module;
                                    $avgOrderValue = $stockOrCount 
                                        ? ($totalSpent / $stockOrCount) 
                                        : 1;
                                    $growthPercent = ($avgOrderValue / $maxAvg) * 100;
                                @endphp
    
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0 me-2">
                                                <img src="{{ $customer->customer_profile->avatar 
                                                    ? app('cloudfront').$customer->customer_profile->avatar 
                                                    : app('userImg') }}"
                                                     alt="{{ $customer->customer_profile->first_name 
                                                         . ' ' 
                                                         . $customer->customer_profile->last_name }}"
                                                     class="img-thumbnail rounded-circle"
                                                     style="width:40px;height:40px;object-fit:cover;"
                                                />
                                            </div>
                                            <div>
                                                <h5 class="fs-13 my-1">
                                                    <a href="#" class="text-reset">{{ $brandName }}</a>
                                                </h5>
                                                <span class="text-muted">
                                                    {{ $customer->customer_profile->first_name ?? '' }}
                                                    {{ $customer->customer_profile->last_name ?? '' }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-muted">{{ $customerType }}</span>
                                    </td>
                                    <td>
                                        <p class="mb-0">{{ $stockOrCount }}</p>
                                        <span class="text-muted">Orders</span>
                                    </td>
                                    <td>
                                        <span class="text-muted">
                                            {{ number_format($totalSpent, 0) }} IQD
                                        </span>
                                    </td>
                                    <td>
                                    <h5 class="fs-13 fw-semibold mb-0">
                                        {{ round($growthPercent, 0) }}%
                                        <i class="ri-bar-chart-fill 
                                            {{ $growthPercent >= 80 ? 'text-success' : ($growthPercent >= 50 ? 'text-warning' : 'text-danger') }}
                                            fs-16 align-middle ms-2">
                                        </i>
                                    </h5>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table><!-- end table -->
                </div>
    
            </div> <!-- .card-body-->
        </div> <!-- .card-->
    </div> <!-- .col-->
    
</div> <!-- end row-->