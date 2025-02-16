<div>
<div class="page-content-dash">
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Dashboard</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Ecommerce</a></li>
                            <li class="breadcrumb-item active">Dashboard</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="container-fluid">
    <div class="row">
        <div class="col">
            <div class="h-100">
                <div class="row mb-3 pb-1">
                    <div class="col-12">
                        <div class="d-flex align-items-lg-center flex-lg-row flex-column">
                            <div class="flex-grow-1">
                                <h4 class="fs-16 mb-1">Welcome, {{auth()->guard('admin')->user()->profile->first_name}}!</h4>
                                <p class="text-muted mb-0">Here's what's happening with your store
                                    today.</p>
                            </div>
                            <div class="mt-3 mt-lg-0">
                                <form action="javascript:void(0);">
                                    <div class="row g-3 mb-0 align-items-center">
                                        {{-- <div class="col-sm-auto">
                                            <div class="input-group">
                                                <input type="text" class="form-control border-0 dash-filter-picker shadow" data-provider="flatpickr" data-range-date="true" data-date-format="d M, Y" data-deafult-date="01 Jan 2022 to 31 Jan 2022">
                                                <div class="input-group-text bg-primary border-primary text-white">
                                                    <i class="ri-calendar-2-line"></i>
                                                </div>
                                            </div>
                                        </div> --}}
                                        <!--end col-->
                                        <div class="col-auto">
                                            <a href="{{route('super.product.create',['locale' => app()->getLocale()])}}" type="button" class="btn btn-soft-success"><i class="ri-add-circle-line align-middle me-1"></i>
                                                Add Product</a>
                                        </div>
                                        <!--end col-->
                                        <div class="col-auto">
                                            <button type="button" class="btn btn-soft-info btn-icon waves-effect waves-light layout-rightside-btn"><i class="ri-pulse-line"></i></button>
                                        </div>
                                        <!--end col-->
                                    </div>
                                    <!--end row-->
                                </form>
                            </div>
                        </div><!-- end card header -->
                    </div>
                    <!--end col-->
                </div>
                <!--end row-->

                @include('super-admins.pages.dashboards.summary_cards', [
                    'totalEarnings' => $totalEarningsCard,
                    'ordersCount'   => $ordersCountCard,
                    'quantitySells' => $quantitySellsCard,
                    'customersCount'=> $customersCountCard,
                ])

                @include('super-admins.pages.dashboards.chart_revenue',[
                    'availableYears'=> $availableYears,
                    'refundCount'=> $refundCount,
                    'conversionRatio'=> $conversionRatio,
                ])
                @include('super-admins.pages.dashboards.chart_list',[
                    'bestSellingProducts' => $bestSellingProducts,
                ])

                @livewire('super-admins.dashboard.dash-order-livewire')
            </div> <!-- end .h-100-->

        </div> <!-- end col -->

        @livewire('super-admins.dashboard.dash-activity-livewire')
    </div>

</div>
<!-- container-fluid -->
</div>