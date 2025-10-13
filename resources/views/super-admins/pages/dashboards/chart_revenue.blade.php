<div class="row">
    <!-- Left Column (Revenue Chart) -->
    <div class="col-xl-8">
        <div class="card">
            <div class="card-header border-0 align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Revenue</h4>

                <!-- Year & Month Dropdowns -->
                <div class="d-flex gap-2">
                    <!-- Year Dropdown -->
                    <div>
                        <select class="form-select form-select-sm" wire:model="selectedYear" style="min-width: 80px;">
                            @foreach($availableYears as $year)
                                <option value="{{ $year }}">{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>
                    <!-- Month Dropdown -->
                    <div>
                        <select class="form-select form-select-sm" wire:model="selectedMonth" style="min-width: 90px;">
                            <option value="All">All</option>
                            <option value="1">Jan</option>
                            <option value="2">Feb</option>
                            <option value="3">Mar</option>
                            <option value="4">Apr</option>
                            <option value="5">May</option>
                            <option value="6">Jun</option>
                            <option value="7">Jul</option>
                            <option value="8">Aug</option>
                            <option value="9">Sep</option>
                            <option value="10">Oct</option>
                            <option value="11">Nov</option>
                            <option value="12">Dec</option>
                        </select>
                    </div>
                </div>
            </div><!-- end card header -->

            <!-- Stats row inside the card header -->
            <div class="card-header p-0 border-0 bg-light-subtle">
                <div class="row g-0 text-center">
                    <div class="col-6 col-sm-3">
                        <div class="p-3 border border-dashed border-start-0">
                            <!-- Orders from DB -->
                            <h5 class="mb-1">
                                <span class="counter-value" data-target="{{ $ordersCount }}">{{$ordersCount}}</span>
                            </h5>
                            <p class="text-muted mb-0">Orders</p>
                        </div>
                    </div>
                    <!--end col-->
                    <div class="col-6 col-sm-3">
                        <div class="p-3 border border-dashed border-start-0">
                            <!-- E.g., earnings in K -->
                        @php
                            $amount = $totalEarnings ?? 0;

                            if ($amount >= 1_000_000_000) {
                                // Billion
                                $formattedValue = number_format($amount / 1_000_000_000, 3);
                                $suffix = 'B';
                            } elseif ($amount >= 1_000_000) {
                                // Million
                                $formattedValue = number_format($amount / 1_000_000, 3);
                                $suffix = 'M';
                            } elseif ($amount >= 1_000) {
                                // Thousand
                                $formattedValue = number_format($amount / 1_000, 3);
                                $suffix = 'K';
                            } else {
                                // Less than thousand
                                $formattedValue = number_format($amount, 0);
                                $suffix = '';
                            }
                        @endphp
                            <h5 class="mb-1">
                                <span class="counter-value" data-target="{{ $formattedValue }}">{{ $formattedValue }}</span>{{ $suffix }} IQD
                            </h5>
                            <p class="text-muted mb-0">Earnings</p>
                        </div>
                    </div>
                    <!--end col-->
                    <div class="col-6 col-sm-3">
                        <div class="p-3 border border-dashed border-start-0">
                            <!-- Refund count from DB -->
                            <h5 class="mb-1">
                                <span class="counter-value" data-target="{{ $refundCount }}">{{$refundCount}}</span>
                            </h5>
                            <p class="text-muted mb-0">Refunds/Cancelled</p>
                        </div>
                    </div>
                    <!--end col-->
                    <div class="col-6 col-sm-3">
                        <div class="p-3 border border-dashed border-start-0 border-end-0">
                            <!-- Conversion Ratio from DB -->
                            <h5 class="mb-1 text-success">
                                <span class="counter-value" data-target="{{ $conversionRatio }}">{{$conversionRatio}}</span>%
                            </h5>
                            <p class="text-muted mb-0">Conversion Ratio</p>
                        </div>
                    </div>
                    <!--end col-->
                </div>
            </div><!-- end card header -->

            <div class="card-body p-0 pb-2">
                <div class="w-100">
                    <!-- Chart container -->
                    <div id="customer_impression_charts"
                         data-colors='["--vz-success", "--vz-info", "--vz-danger"]'
                         class="apex-charts" dir="ltr"
                         style="height: 370px;">
                    </div>
                </div>
            </div><!-- end card body -->
        </div><!-- end card -->
    </div><!-- end col -->

    <!-- Right Column (Sales by Locations) -->
    <div class="col-xl-4">
        <div class="card card-height-100">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Sales by Locations</h4>
                <div class="flex-shrink-0">
                    {{-- <button type="button" class="btn btn-soft-primary btn-sm">
                        Export Report
                    </button> --}}
                </div>
            </div>
            <!-- end card header -->
    
            <div class="card-body">
                <!-- "topZones" is passed from the Livewire render -->
                <div class="px-2 py-2 mt-1">
                    @php
                        // find the highest "count" so we can do a relative progress bar
                        $maxCount = count($topZones) ? $topZones[0]['count'] : 1;
                    @endphp
            
                    @foreach($topZones as $tz)
                        @php
                            $zoneName = $tz['zone']->name ?? 'Unknown';
                            $zoneCount = $tz['count'];
                            $percentage = $zoneCount ? ($zoneCount / $maxCount) * 100 : 0;
                        @endphp
            
                        <p class="mb-1">
                            {{ $zoneName }}
                            <span class="float-end">{{ $zoneCount }}</span>
                        </p>
                        <div class="progress mt-2 mb-2" style="height: 6px;">
                            <div class="progress-bar progress-bar-striped bg-primary" 
                                 role="progressbar"
                                 style="width: {{ $percentage }}%;">
                            </div>
                        </div>
                        <hr>
                    @endforeach
                </div>
            </div>
            
            <!-- end card body -->
        </div>
        <!-- end card -->
    </div>    
    <!-- end col -->
</div>

@push('dashScript')
<script>
document.addEventListener('livewire:load', function () {

    // We ask the server for chart data now that everything is loaded
    if (! window.__chartDataFetched) {
        window.__chartDataFetched = true;  // set a flag
        Livewire.emit('fetchChartData');
    }

    let linechartcustomerColors = getChartColorsArray("customer_impression_charts");
    let options = {
        series: [],
        chart: {
            height: 370,
            type: "line",
            toolbar: { show: false }
        },
        stroke: {
            curve: "straight",
            dashArray: [0, 0, 8],
            width: [2, 0, 2.2]
        },
        fill: { opacity: [0.1, 0.9, 1] },
        markers: {
            size: [0, 0, 0],
            strokeWidth: 2,
            hover: { size: 4 }
        },
        xaxis: {
            categories: [],
            axisTicks: { show: false },
            axisBorder: { show: false }
        },
        grid: {
            show: true,
            xaxis: { lines: { show: true } },
            yaxis: { lines: { show: false } },
            padding: { top: 0, right: -2, bottom: 15, left: 10 }
        },
        legend: {
            show: true,
            horizontalAlign: "center",
            offsetX: 0,
            offsetY: -5,
            markers: { width: 9, height: 9, radius: 6 },
            itemMargin: { horizontal: 10, vertical: 0 }
        },
        plotOptions: {
            bar: {
                columnWidth: "30%",
                barHeight: "70%"
            }
        },
        colors: linechartcustomerColors,
        tooltip: {
            shared: true,
            y: [
                {
                    formatter: function (val) {
                        return val !== undefined ? val.toFixed(0) : val;
                    }
                },
                {
                    formatter: function (val) {
                        return val !== undefined ? "$" + val.toFixed(2) + "k" : val;
                    }
                },
                {
                    formatter: function (val) {
                        return val !== undefined ? val.toFixed(0) + " R/C" : val;
                    }
                }
            ]
        }
    };
    window.chart = new ApexCharts(
        document.querySelector("#customer_impression_charts"),
        options
    );
    chart.render();
});

// When Livewire emits chartDataUpdated, we update the chart
Livewire.on('chartDataUpdated', (chartData) => {

    if (window.chart) {
        window.chart.updateOptions({
            xaxis: { categories: chartData.categories },
            series: chartData.series
        });
    }
});

// Helper function
function getChartColorsArray(e) {
    let el = document.getElementById(e);
    if (el !== null) {
        let t = el.getAttribute("data-colors");
        if (t) {
            t = JSON.parse(t);
            return t.map(function (color) {
                color = color.replace(" ", "");
                if (color.indexOf(",") === -1) {
                    // single color or css var
                    let cssColor = getComputedStyle(document.documentElement).getPropertyValue(color);
                    return cssColor || color;
                } else {
                    // e.g. ["--vz-info", 0.5]
                    let [baseColor, alpha] = color.split(",");
                    let rgbaColor = getComputedStyle(document.documentElement).getPropertyValue(baseColor.trim());
                    return "rgba(" + rgbaColor + "," + alpha.trim() + ")";
                }
            });
        }
        console.warn("data-colors attribute not found on", e);
    }
    return [];
}
</script>
@endpush
