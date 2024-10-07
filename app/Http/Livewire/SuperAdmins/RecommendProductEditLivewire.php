<?php

namespace App\Http\Livewire\SuperAdmins;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\ProductRecommendation;
use App\Models\Product; // Assuming you have a Product model

class RecommendProductEditLivewire extends Component
{
    public $product;
    public $productId;
    public $recommendedProducts = [];
    public $allProducts; // List of all products for selection
    protected $user_cred; // List of all products for selection
    
    public function mount($id)
    {
        // Load the product data based on the product ID
        $this->productId = $id;
            
        $this->product = Product::with(['productTranslation' => function ($query) {
            $query->where('locale', app()->getLocale());
        }, 'variation.images' => function ($query) {
            $query->where('is_primary', 1); // Fetch only primary images
        }])->findOrFail($id);
        if(!$this->product){
            $this->dispatchBrowserEvent('alert', [
                'type' => 'error',
                'message' => __('Failed to find the Product')
            ]);
        }
        

        // Load currently recommended products
        $this->recommendedProducts = ProductRecommendation::where('product_id', $this->productId)
            ->pluck('recommended_product_id')
            ->toArray();
    }

    // Method to update the recommendations
    public function updateRecommendations()
    {
        // Clear existing recommendations
        ProductRecommendation::where('product_id', $this->productId)->delete();
        
        // Insert new recommendations
        foreach ($this->recommendedProducts as $recommendedProductId) {
            ProductRecommendation::create([
                'product_id' => $this->productId,
                'recommended_product_id' => $recommendedProductId,
            ]);
        }

        // Provide feedback to the admin
        session()->flash('message', 'Recommendations updated successfully.');
    }

    public function submit()
    {
        if(Auth::guard('admin')->check()) {
            $user_cred = Auth::guard('admin')->id();
        } else {
            return redirect()->route('admin.login')->with('error', 'You must be logged in as admin to access this page.');
        }
        // Validate the recommended products
        DB::beginTransaction();

        try {
            $this->validate([
                'recommendedProducts' => 'required|array', // Ensure it's an array and not empty
                'recommendedProducts.*' => 'exists:products,id', // Ensure each selected product exists
            ]);
        
            // Clear existing recommendations for the current product
            ProductRecommendation::where('product_id', $this->productId)->delete();
        
            // Save new recommendations
            foreach ($this->recommendedProducts as $productId) {
                ProductRecommendation::create([
                    'created_by_id' => $user_cred,
                    'updated_by_id' => $user_cred,
                    'product_id' => $this->productId,
                    'recommended_product_id' => $productId,
                ]);
            }
            $this->dispatchBrowserEvent('alert', [
                'type' => 'success',
                'message' => __('Recommendation Products has been updated')
            ]);
            DB::commit();
            return redirect()->route('super.product.recommend', ['locale' => app()->getLocale()]);
        } catch (\Exception $e) {
            // Rollback transaction if an error occurs
            DB::rollBack();

            $this->dispatchBrowserEvent('alert', [
                'type' => 'error',
                'message' => __('Failed to update the Recommendation Product -' . $e->getMessage())
            ]);

            return;
        }

    
        // Optionally, you can reset the recommended products after submission
        // $this->recommendedProducts = [];
    
        // Optionally, add a success message or emit an event

        $this->emit('contentUpdated'); // Emit an event if you need to refresh any data in the front-end
    }

    public function render()
    {
        $this->allProducts = Product::with(['productTranslation' => function ($query) {
            $query->where('locale', app()->getLocale());
        }, 'variation.images' => function ($query) {
            $query->where('is_primary', 1); // Fetch only primary images
        }])
        ->where('status', 1) // Filter by status
        ->where('id', '!=', $this->productId) // Exclude current product
        ->get();
        
        return view('super-admins.pages.recommendproductedit.form', [

        ]);
    }
}