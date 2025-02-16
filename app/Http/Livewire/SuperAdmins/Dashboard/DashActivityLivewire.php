<?php

namespace App\Http\Livewire\SuperAdmins\Dashboard;

use App\Models\Order;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class DashActivityLivewire extends Component
{
    public $latestOrders;
    public $topCategoryList;
    protected $listeners = [
        'echo:AdminChannel,EventOrderStatusUpdated' => 'reloadTable',
        'echo:AdminChannel,EventCustomerOrderCheckout' => 'reloadTable',
        'echo:AdminChannel,EventOrderPaymentStatusUpdated' => 'reloadTable',
    ];

    public function reloadTable(){ 
        $this->render();
    }

    public function getTopCategoriesByOrders($limit = 10)
    {
        // 1) Query the raw pivot from order_items -> products -> product_category -> categories
        //    Summing total ordered quantity per category.
        $topCategories = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('product_category', 'product_category.product_id', '=', 'products.id')
            ->join('categories', 'categories.id', '=', 'product_category.category_id')
            ->select('categories.id', DB::raw('SUM(order_items.quantity) as total_qty'))
            ->groupBy('categories.id')
            ->orderByDesc('total_qty')
            ->take($limit)
            ->get();

        // 2) Extract the category IDs from the result
        $catIds = $topCategories->pluck('id');

        // 3) Load Category models (with translations) for those IDs
        //    so we can get the proper name from 'categoryTranslation'
        $catModels = \App\Models\Category::with(['categoryTranslation' => function($query){
                // if your CategoryTranslation is per-locale:
                $query->where('locale', app()->getLocale());
            }])
            ->whereIn('id', $catIds)
            ->get()
            ->keyBy('id');

        // 4) Merge them into a final array: [ 'id'=>.., 'name'=>.., 'quantity'=>.. ]
        //    We map each raw row into a nicer structure
        $final = $topCategories->map(function($row) use($catModels){
            $cat = $catModels->get($row->id);
            $translation = optional($cat->categoryTranslation); // hasOne
            // If your categoryTranslation has a 'name' column:
            $name = $translation->name ?? 'N/A';  
            
            return [
                'category_id' => $row->id,
                'name'        => $name,
                'total_qty'   => $row->total_qty,
            ];
        });

        // Return as a collection or array
        return $final;
    }


    public function render(){
        $notificationsQuery = auth('admin')->user()->unreadNotifications();
        $this->topCategoryList = $this->getTopCategoriesByOrders(10);
        $notifications = $notificationsQuery->get();
        return view('super-admins.pages.dashboards.activity_side', [
            'activities' => $notifications,
            'topCategories' => $this->topCategoryList,
        ]);
    }
}