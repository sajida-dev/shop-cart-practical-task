<?php

namespace App\Http\Controllers;

use App\Http\Requests\CartRequest;
use App\Models\Cart;
use App\Services\CartService;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Throwable;

class CartController extends Controller
{
    public function __construct(protected CartService $service) {}

    public function index(Request $request)
    {
        $cart = $request->user()->cart()->with('items.product')->first() ?? new Cart();
        return Inertia::render('Cart/Index', ['cart' => $cart]);
    }

    public function store(CartRequest $request)
    {
        try {
            $this->service->addItem(
                $request->user()->id,
                $request->product_id,
                $request->quantity
            );
            return back()->with('success', 'Product added to cart');
        } catch (Throwable $e) {
            report($e);
            return back()->withErrors($e->getMessage());
        }
    }
    public function updateQty(Request $request, int $itemId)
    {
        $request->validate(['quantity' => 'required|integer|min:1']);

        try {
            $this->service->updateItemQuantity($request->user()->id, $itemId, $request->quantity);
            return back()->with('success', 'Cart updated successfully');
        } catch (\Throwable $e) {
            report($e);
            return back()->withErrors($e->getMessage());
        }
    }


    public function destroy(Request $request, int $productId)
    {
        try {
            $this->service->removeItem($request->user()->id, $productId);
            return back()->with('success', 'Product removed from cart');
        } catch (Throwable $e) {
            report($e);
            return back()->withErrors($e->getMessage());
        }
    }
}
