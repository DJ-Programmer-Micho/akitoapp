<?php

namespace App\Http\Livewire\SuperAdmins;

use App\Models\Product;
use Livewire\Component;
use Livewire\WithFileUploads;

class RecommendProductLivewire extends Component
{
    use WithFileUploads;

    public $activeCount;
    public $nonActiveCount;
    public $glang;
    public $filteredLocales;
    public $search = '';
    public $statusFilter = 'all';
    public $items = 10;
    // On Load
    public function mount(){
        $this->glang = app()->getLocale();
        $this->filteredLocales = app('glocales');
    }
     // Render
     public function render(){ 
                // Calculate active and non-active counts
                $this->activeCount = Product::
                whereHas('recommendations')
                ->count();
            
            $this->nonActiveCount = Product::
            whereHas('recommendations')
                ->count();
                // Build the product query
                $productsQuery = Product::query()
                ->distinct()
                ->join('product_translations', 'products.id', '=', 'product_translations.product_id')
                ->select('products.*', 'product_translations.name as product_name')
                ->where('product_translations.locale', $this->glang)
            
            // Apply search filter
                ->when($this->search, function ($query) {
                    $query->where('product_translations.name', 'like', '%' . $this->search . '%');
                })
            
            // Apply status filter
                ->when($this->statusFilter === 'active', function ($query) {
                    $query->where('status', 1);
                })
                ->when($this->statusFilter === 'non-active', function ($query) {
                    $query->where('status', 0);
                })
            
            // Eager load recommendations
                ->with('recommendations') // Assuming 'recommendations' is the relationship method
                ->paginate($this->items)->withQueryString();
            
            // dd($productsQuery);

        return view('super-admins.pages.recommendproduct.table', [
            'tableData' => $productsQuery,
        ]);
    }

}