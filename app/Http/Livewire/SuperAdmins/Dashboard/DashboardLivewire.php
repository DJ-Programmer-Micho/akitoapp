<?php

namespace App\Http\Livewire\SuperAdmins\Dashboard;

use App\Models\Zone;
use App\Models\Order;
use Livewire\Component;
use App\Models\Customer;
use App\Models\OrderItem;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardLivewire extends Component
{
    public $glang;
    public $filteredLocales;

    // Summary cards / mini-stats row
    public $totalEarnings;
    public $ordersCount;
    public $quantitySells;
    public $customersCount;
    public $refundCount;
    public $conversionRatio;

    public $totalEarningsCard;
    public $ordersCountCard;
    public $quantitySellsCard;
    public $customersCountCard;

    public $topZones;
    public $bestSellingProducts = [];
    public bool $chartAlreadyFetched = false;
    public $topCustomers;
    // Chart filters
    public $availableYears = [];
    public $selectedYear;
    public $selectedMonth = 'All';

    // Let the client call fetchChartData after livewire:load
    protected $listeners = [
        'fetchChartData' => 'fetchChartData',
    ];

    public function mount()
    {
        $user = auth('admin')->user();

        if (!$user) {
            abort(403, 'Unauthorized action.');
        }
        // Basic locale info
        $this->glang = app()->getLocale();
        $this->filteredLocales = app('glocales');

        // Default "all-time" stats for initial page load (optional)
        $this->totalEarningsCard = Order::sum('total_amount');
        $this->ordersCountCard   = Order::count();
        $this->quantitySellsCard = OrderItem::sum('quantity');
        $this->customersCountCard = Customer::count();

        $this->totalEarnings = $this->totalEarningsCard;
        $this->ordersCount   = $this->ordersCountCard;
        $this->quantitySells = $this->quantitySellsCard;
        $this->customersCount = $this->customersCountCard;

        $this->refundCount = Order::where('status','refunded')->count();
        $deliveredCount = Order::where('status','delivered')->count();
        $this->conversionRatio = ($this->ordersCount > 0)
            ? round(($deliveredCount / $this->ordersCount) * 100, 2)
            : 0;

        // Distinct years from orders
        $this->availableYears = Order::selectRaw('DISTINCT YEAR(created_at) as year')
            ->orderBy('year', 'DESC')
            ->pluck('year')
            ->toArray();

        // Default selected year
        $currentYear = now()->year;
        $this->selectedYear = in_array($currentYear, $this->availableYears)
            ? $currentYear
            : ($this->availableYears[0] ?? $currentYear);
    }

    public function fetchChartData()
    {
        // We run exactly once on page load to populate the chart
        if ($this->chartAlreadyFetched) {
            return; // Don’t do anything; we already have chart data
        }
        $this->chartAlreadyFetched = true;
        
        $this->emitChartData();
    }

    public function updatedSelectedYear()
    {
        $this->updateFilterStats($this->selectedYear, $this->selectedMonth);
        $this->emitChartData();
    }

    public function updatedSelectedMonth()
    {
        $this->updateFilterStats($this->selectedYear, $this->selectedMonth);
        $this->emitChartData();
    }


    private function emitChartData()
    {
        $year = $this->selectedYear;

        if ($this->selectedMonth === 'All') {
            $chartData = $this->getAllMonthsData($year);
        } else {
            $month = (int) $this->selectedMonth;
            $chartData = $this->getSingleMonthData($year, $month);
        }

        // Send arrays to the front-end for ApexCharts
        $this->emit('chartDataUpdated', $chartData);
    }

    private function updateFilterStats($year, $month)
    {
        // Determine the date range
        if ($month === 'All') {
            $start = Carbon::create($year, 1, 1)->startOfYear();
            $end   = Carbon::create($year, 12, 31)->endOfYear();
        } else {
            $m = (int) $month;
            $start = Carbon::create($year, $m, 1)->startOfMonth();
            $end   = Carbon::create($year, $m, 1)->endOfMonth();
        }

        // Orders in this date range
        $this->ordersCount = Order::whereBetween('created_at', [$start, $end])->count();
        $this->totalEarnings = Order::whereBetween('created_at', [$start, $end])
            ->sum('total_amount');
        $this->refundCount = Order::whereBetween('created_at', [$start, $end])
            ->where('status','refunded')
            ->count();

        // For conversion ratio, we need delivered
        $deliveredCount = Order::whereBetween('created_at', [$start, $end])
            ->where('status','delivered')
            ->count();
        $this->conversionRatio = ($this->ordersCount > 0)
            ? round(($deliveredCount / $this->ordersCount) * 100, 2)
            : 0;

        // If you want quantity + customers also to be filtered:
        $this->quantitySells = OrderItem::join('orders','order_items.order_id','=','orders.id')
            ->whereBetween('orders.created_at', [$start, $end])
            ->sum('order_items.quantity');
        $this->customersCount = Customer::whereBetween('created_at', [$start, $end])
            ->count();
    }

    // Chart methods remain unchanged:
    private function getAllMonthsData($year)
    {
        // 1) Single query: group by month, compute orders, earnings, refunds in one pass
        $monthlyData = Order::selectRaw('
                MONTH(created_at) as month_num,
                COUNT(*) as total_orders,
                SUM(total_amount) as total_earnings,
                SUM(CASE WHEN status = "refunded" THEN 1 ELSE 0 END) as total_refunds
            ')
            ->whereYear('created_at', $year)
            ->groupBy('month_num')
            ->orderBy('month_num')
            ->get();
    
        // 2) We want arrays for each of the 12 months (Jan..Dec). Initialize them with 0
        $ordersArray   = array_fill(1, 12, 0);
        $earningsArray = array_fill(1, 12, 0);
        $refundsArray  = array_fill(1, 12, 0);
    
        // 3) Fill data from the query results
        foreach ($monthlyData as $row) {
            $m = (int) $row->month_num; // 1..12
            $ordersArray[$m]   = (int) $row->total_orders;
            // earnings in "k"
            $earningsArray[$m] = round($row->total_earnings / 1000, 2);
            $refundsArray[$m]  = (int) $row->total_refunds;
        }
    
        $ordersArray   = array_values($ordersArray);
        $earningsArray = array_values($earningsArray);
        $refundsArray  = array_values($refundsArray);
    
        $categories = ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];
    
        return [
            'categories' => $categories,
            'series' => [
                ['name' => 'Orders',   'type' => 'area', 'data' => $ordersArray],
                ['name' => 'Earnings', 'type' => 'bar',  'data' => $earningsArray],
                ['name' => 'Refunds',  'type' => 'line', 'data' => $refundsArray],
            ],
        ];
    }
    

    private function getSingleMonthData($year, $month)
    {
        // Single query: group by day
        $dailyData = Order::selectRaw('
                DAY(created_at) as day_num,
                COUNT(*) as total_orders,
                SUM(total_amount) as total_earnings,
                SUM(CASE WHEN status = "refunded" THEN 1 ELSE 0 END) as total_refunds
            ')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->groupBy('day_num')
            ->orderBy('day_num')
            ->get();

        // Figure out how many days in month
        $start = Carbon::create($year, $month, 1)->startOfMonth();
        $daysInMonth = $start->daysInMonth;

        // Prepare array for each day (1..$daysInMonth)
        $ordersArray   = array_fill(1, $daysInMonth, 0);
        $earningsArray = array_fill(1, $daysInMonth, 0);
        $refundsArray  = array_fill(1, $daysInMonth, 0);

        // Fill data from the single query
        foreach ($dailyData as $row) {
            $d = (int) $row->day_num;
            $ordersArray[$d]   = (int) $row->total_orders;
            $earningsArray[$d] = round($row->total_earnings / 1000, 2);
            $refundsArray[$d]  = (int) $row->total_refunds;
        }

        // Reindex arrays from 0..($daysInMonth-1) if needed
        $ordersArray   = array_values($ordersArray);
        $earningsArray = array_values($earningsArray);
        $refundsArray  = array_values($refundsArray);

        // Build categories array: "Day 1", "Day 2", ...
        $categories = [];
        for ($d=1; $d<=$daysInMonth; $d++) {
            $categories[] = "Day $d";
        }

        return [
            'categories' => $categories,
            'series' => [
                ['name' => 'Orders',   'type' => 'area', 'data' => $ordersArray],
                ['name' => 'Earnings', 'type' => 'bar',  'data' => $earningsArray],
                ['name' => 'Refunds',  'type' => 'line', 'data' => $refundsArray],
            ],
        ];
    }


    public function pointInPolygon($pointLat, $pointLng, array $polygonCoordinates)
    {
        $inside = false;
        $numPoints = count($polygonCoordinates);
        $j = $numPoints - 1;

        for ($i = 0; $i < $numPoints; $i++) {
            // polygonCoordinates[$i] is [lat, lng]
            $xi = $polygonCoordinates[$i][0];
            $yi = $polygonCoordinates[$i][1];

            $xj = $polygonCoordinates[$j][0];
            $yj = $polygonCoordinates[$j][1];

            // Ray-casting
            $intersect = (
                ($yi > $pointLng) != ($yj > $pointLng)
            ) && (
                $pointLat < ($xj - $xi) * ($pointLng - $yi) / ($yj - $yi) + $xi
            );

            if ($intersect) {
                $inside = !$inside;
            }
            $j = $i;
        }

        return $inside;
    }

    public function getTopZonesNaive()
    {
        // 1) Get all enabled zones
        $zones = Zone::where('status','!=','disabled')->get();

        // 2) Prepare counters
        $zoneCounts = [];
        foreach ($zones as $z) {
            $zoneCounts[$z->id] = 0;
        }

        // 3) Load all orders (be mindful of performance if this is large)
        $orders = Order::all();

        // 4) Check each order's (lat,lng) against each zone
        foreach ($orders as $o) {
            $lat = (float) $o->latitude;
            $lng = (float) $o->longitude;

            foreach ($zones as $z) {
                // Decode JSON if it's stored as a JSON string
                $polygonJson = is_string($z->coordinates)
                    ? json_decode($z->coordinates, true)
                    : $z->coordinates;

                // Convert from [{lat:..., lng:...}, ...]
                // to [[lat, lng], [lat, lng], ...]
                $polygonArray = array_map(function($pt) {
                    return [(float)$pt['lat'], (float)$pt['lng']];
                }, $polygonJson);

                if ($this->pointInPolygon($lat, $lng, $polygonArray)) {
                    $zoneCounts[$z->id]++;
                    // Once we find which zone the point belongs to, we can break
                    break;
                }
            }
        }

        // 5) Sort by count descending
        $result = [];
        foreach ($zones as $z) {
            $result[] = [
                'zone'  => $z,
                'count' => $zoneCounts[$z->id],
            ];
        }
        usort($result, function($a, $b) {
            return $b['count'] <=> $a['count'];
        });

        // 6) Return top 7
        return array_slice($result, 0, 5);
    }

    public function getTopCustomers($limit = 5)
    {
        return Customer::with(['customer_profile'])
            ->withCount('orders')
            ->withSum('orders', 'total_amount')
            ->orderBy('orders_sum_total_amount', 'desc')
            ->take($limit)
            ->get();
    }

    public function render()
    {
        $this->bestSellingProducts = OrderItem::select(
            'product_id',
            DB::raw('COUNT(DISTINCT order_id) as orders_count'),
            DB::raw('SUM(quantity) as total_units'),
            DB::raw('SUM(total) as total_revenue')
        )
        ->groupBy('product_id')
        ->orderByDesc(DB::raw('SUM(quantity)'))  // or orderByDesc('total_units')
        ->take(5)
        // Important: eager-load your relationships
        ->with([
            // We'll need the product, its variation (for price, stock), and images
            'product.variation.images',
            // We'll also need the product translations, filtered by current locale (if you wish)
            'product.productTranslation' => function($query) {
                $query->where('locale', app()->getLocale());
            }
        ])
        ->get();

        $this->topZones = $this->getTopZonesNaive();
        $this->topCustomers = $this->getTopCustomers(5);
        return view('super-admins.pages.dashboards.container', [
            'totalEarnings'  => $this->totalEarnings,
            'ordersCount'    => $this->ordersCount,
            'quantitySells'  => $this->quantitySells,
            'customersCount' => $this->customersCount,
            'refundCount'    => $this->refundCount,
            'totalEarningsCard'=> $this->totalEarningsCard,
            'ordersCountCard'=> $this->ordersCountCard,
            'quantitySellsCard'=> $this->quantitySellsCard,
            'customersCountCard'=> $this->customersCountCard,
            'availableYears' => $this->availableYears,
            'conversionRatio'=> $this->conversionRatio,
            'topZones' => $this->topZones,
            'bestSellingProducts' => $this->bestSellingProducts,
        ]);
    }
}
