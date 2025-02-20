<?php

namespace App\Http\Controllers\Api\V1\Main;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\CartItem;
use App\Models\WishlistItem;
use App\Models\DiscountRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartControllerApi extends Controller
{
    /**
     * Get all wishlist items
     */
    public function getWishlist()
    {
        $customerId = Auth::guard('sanctum')->user()->id ?? null;

        $wishlistItems = WishlistItem::where('customer_id', $customerId)
            ->with(['product.variation', 'product.variation.images'])
            ->get()
            ->map(fn($item) => $this->formatProduct($item->product));

        return response()->json(['wishlist' => $wishlistItems]);
    }

    /**
     * Add product to wishlist
     */
    public function addToWishlist(Request $request)
    {
        $customerId = Auth::guard('sanctum')->user()->id ?? null;
        $productId = $request->input('product_id');

        $exists = WishlistItem::where('customer_id', $customerId)->where('product_id', $productId)->exists();

        if (!$exists) {
            WishlistItem::create([
                'customer_id' => $customerId,
                'product_id' => $productId,
            ]);
        }

        return response()->json(['message' => 'Item added to wishlist successfully.']);
    }

    /**
     * Remove product from wishlist
     */
    public function removeFromWishlist($productId)
    {
        $customerId = Auth::guard('sanctum')->user()->id ?? null;

        WishlistItem::where('customer_id', $customerId)->where('product_id', $productId)->delete();

        return response()->json(['message' => 'Item removed from wishlist successfully.']);
    }

    /**
     * Move product from wishlist to cart
     */
    public function moveToCart(Request $request)
    {
        $customerId = Auth::guard('sanctum')->user()->id ?? null;
        $productId = $request->input('product_id');

        $product = Product::find($productId);
        
        if (!$product || $product->variation->stock <= 0) {
            return response()->json(['message' => 'Item is out of stock.'], 400);
        }

        // Add to cart
        CartItem::updateOrCreate(
            ['customer_id' => $customerId, 'product_id' => $productId],
            ['quantity' => 1]
        );

        // Remove from wishlist
        WishlistItem::where('customer_id', $customerId)->where('product_id', $productId)->delete();

        return response()->json(['message' => 'Item moved to cart successfully.']);
    }

    public function moveAllToCart()
    {
        $customerId = Auth::guard('sanctum')->user()->id ?? null;

        $wishlistItems = WishlistItem::where('customer_id', $customerId)->get();

        foreach ($wishlistItems as $item) {
            $product = Product::find($item->product_id);
            
            if ($product && $product->variation->stock > 0) {
                CartItem::updateOrCreate(
                    ['customer_id' => $customerId, 'product_id' => $product->id],
                    ['quantity' => 1]
                );
                $item->delete();
            }
        }

        return response()->json(['message' => 'All wishlist items moved to cart successfully.']);
    }

    private function formatProduct(Product $product): array
    {
        $customerId = Auth::guard('sanctum')->user()->id ?? null;
        $finalPrices = $this->calculateFinalPrice($product, $customerId);

        return [
            'id'                     => $product->id,
            'name'                   => $product->productTranslation->first()->name ?? '',
            'slug'                   => $product->productTranslation->first()->slug ?? '',
            'brand'                  => $product->brand->brandTranslation->name ?? '',
            'category'               => $product->categories->first()->categoryTranslation->name ?? '',
            'image'                  => app('cloudfront').$product->variation->images->first()->image_path ?? 'https://d1h4q8vrlfl3k9.cloudfront.net/web-setting/logo/icon_2024021117305762286939.png',
            'price'                  => $finalPrices['base_price'],
            'discount_price'         => $finalPrices['customer_discount_price'] ?? $finalPrices['discount_price'],
        ];
    }

    public function getCart()
    {
        $customerId = Auth::guard('sanctum')->user()->id ?? null;

        $cartItems = CartItem::where('customer_id', $customerId)
            ->with(['product.variation', 'product.variation.images'])
            ->get()
            ->map(fn($item) => [
                'id' => $item->id,
                'product' => $this->formatProduct($item->product),
                'quantity' => $item->quantity,
            ]);

        return response()->json(['cart' => $cartItems]);
    }

    public function addToCart(Request $request)
    {
        $customerId = Auth::guard('sanctum')->user()->id ?? null;
        $productId = $request->input('product_id');

        $product = Product::find($productId);

        if (!$product || $product->variation->stock <= 0) {
            return response()->json(['message' => 'Item is out of stock.'], 400);
        }

        CartItem::updateOrCreate(
            ['customer_id' => $customerId, 'product_id' => $productId],
            ['quantity' => 1]
        );

        return response()->json(['message' => 'Item added to cart successfully.']);
    }

    public function updateCartQuantity(Request $request)
    {
        $customerId = Auth::guard('sanctum')->user()->id ?? null;
        $cartItemId = $request->input('cart_item_id');
        $quantity = $request->input('qty');
// return [$cartItemId,$quantity];
        $cartItem = CartItem::where('customer_id', $customerId)->where('id', $cartItemId)->first();

        if (!$cartItem) {
            return response()->json(['message' => 'Cart item not found.'], 404);
        }

        if ($quantity <= 0) {
            $cartItem->delete();
            return response()->json(['message' => 'Item removed from cart.']);
        }

        $availableStock = $cartItem->product->variation->stock ?? 0;
        if ($quantity > $availableStock) {
            return response()->json(['message' => 'Insufficient stock.'], 400);
        }

        $cartItem->update(['quantity' => $quantity]);

        return response()->json(['message' => 'Cart item quantity updated.']);
    }

    public function removeFromCart($cartItemId)
    {
        $customerId = Auth::guard('sanctum')->user()->id ?? null;

        CartItem::where('customer_id', $customerId)->where('id', $cartItemId)->delete();

        return response()->json(['message' => 'Item removed from cart successfully.']);
    }
    // HELPER FUNCTIONS
    private function calculateFinalPrice(Product $product, ?int $customerId): array
    {
        $basePrice = $product->variation->price;
        $discountPrice = $product->variation->discount ?? null; // If no discount, keep it null
        $customerDiscountPrice = $discountPrice ?? $basePrice; // If no discount, use base price
        $totalDiscountPercentage = 0;

        if ($customerId) {
            $discountRules = DiscountRule::where('customer_id', $customerId)
                ->where(fn($query) => $query->where('product_id', $product->id)
                    ->orWhere('category_id', optional($product->categories->first())->id)
                    ->orWhere('sub_category_id', optional($product->subCategories->first())->id)
                    ->orWhere('brand_id', $product->brand_id))
                ->get();

            foreach ($discountRules as $rule) {
                $totalDiscountPercentage += $rule->discount_percentage;
            }

            $totalDiscountPercentage = min($totalDiscountPercentage, 100);

            // Apply customer discount if applicable
            if ($totalDiscountPercentage > 0) {
                $customerDiscountPrice = $customerDiscountPrice * (1 - ($totalDiscountPercentage / 100));
            }
        }

        return [
            'base_price'              => number_format($basePrice, 2, '.', ''),
            'discount_price'          => $discountPrice !== null ? number_format($discountPrice, 2, '.', '') : null,
            'customer_discount_price' => $customerDiscountPrice != $basePrice ? number_format($customerDiscountPrice, 2, '.', '') : null,
        ];
    }
}
