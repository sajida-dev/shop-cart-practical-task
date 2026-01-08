<?php

namespace App\Http\Controllers;

use App\Http\Requests\CartRequest;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Throwable;

class CartControllerCopy extends Controller
{
    /**
     * Display the current user's cart.
     */
    public function index(Request $request)
    {
        $cart = Cart::with('items.product')
            ->firstOrCreate(['user_id' => $request->user()->id]);

        $cartItems = $cart->items->map(fn(CartItem $item) => [
            'id' => $item->id,
            'productId' => $item->product_id,
            'name' => $item->product->name ?? '',
            'price' => $item->product->price ?? 0,
            'quantity' => $item->quantity,
        ]);

        return Inertia::render('Cart/Index', [
            'cartItems' => $cartItems,
        ]);
    }

    /**
     * Add a product to the cart.
     */
    public function store(CartRequest $request)
    {


        $userId = $request->user()->id;
        $productId = $request->product_id;
        $quantity = $request->quantity ?? 1;

        try {
            $product = Product::lockForUpdate()->findOrFail($productId);

            if ($product->stock_quantity < $quantity) {
                return back()->withErrors("Insufficient stock for {$product->name}");
            }

            $cart = Cart::firstOrCreate(['user_id' => $userId]);

            $item = $cart->items()->firstOrCreate(
                ['product_id' => $productId],
                ['quantity' => 0]
            );

            $item->increment('quantity', $quantity);

            return back()->with('success', 'Product added to cart');
        } catch (Throwable $e) {
            report($e);
            return back()->withErrors($e->getMessage());
        }
    }

    /**
     * Update the quantity of a cart item.
     */
    public function updateQty(Request $request, int $itemId)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        try {
            $item = CartItem::whereHas('cart', fn($q) => $q->where('user_id', $request->user()->id))
                ->findOrFail($itemId);

            $product = Product::lockForUpdate()->findOrFail($item->product_id);

            if ($product->stock_quantity < $request->quantity) {
                return back()->withErrors("Insufficient stock for {$product->name}");
            }

            $item->quantity = $request->quantity;
            $item->save();

            return back()->with('success', 'Cart updated successfully');
        } catch (Throwable $e) {
            report($e);
            return back()->withErrors($e->getMessage());
        }
    }

    /**
     * Remove a product from the cart.
     */
    public function destroy(Request $request, int $productId)
    {
        try {
            $cart = Cart::where('user_id', $request->user()->id)->firstOrFail();
            $cart->items()->where('product_id', $productId)->delete();

            return back()->with('success', 'Product removed from cart');
        } catch (Throwable $e) {
            report($e);
            return back()->withErrors($e->getMessage());
        }
    }

    /**
     * Clear the user's cart.
     */
    public function clear(Request $request)
    {
        try {
            $cart = Cart::where('user_id', $request->user()->id)->first();
            if ($cart) {
                $cart->items()->delete();
            }

            return back()->with('success', 'Cart cleared');
        } catch (Throwable $e) {
            report($e);
            return back()->withErrors($e->getMessage());
        }
    }
}
