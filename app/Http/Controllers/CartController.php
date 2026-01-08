<?php

namespace App\Http\Controllers;

use App\Http\Requests\CartRequest;
use App\Models\Cart;
use App\Services\CartService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Controller responsible for managing user cart actions.
 *
 * Handles:
 * - Viewing cart
 * - Adding products to cart
 * - Updating quantity
 * - Removing items
 */
class CartController extends Controller
{
    /**
     * Inject CartService to handle cart logic.
     */
    public function __construct(protected CartService $service) {}

    /**
     * Display the user's cart page.
     *
     * @param Request $request
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        // Fetch user's cart with items and products
        $cart = $request->user()->cart()->with('items.product')->first() ?? new Cart();

        Log::info('Rendering cart page', [
            'user_id' => $request->user()->id,
            'cart_id' => $cart->id ?? null,
        ]);

        return Inertia::render('Cart/Index', [
            'cart' => $cart,
        ]);
    }

    /**
     * Add a product to the cart.
     *
     * @param CartRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(CartRequest $request)
    {
        try {
            $item = $this->service->addItem(
                $request->user()->id,
                $request->product_id,
                $request->quantity
            );

            Log::info('Product added to cart successfully', [
                'user_id'    => $request->user()->id,
                'cart_item_id' => $item->id,
                'product_id' => $request->product_id,
                'quantity'   => $request->quantity,
            ]);

            return back()->with('success', 'Product added to cart successfully.');
        } catch (Throwable $e) {
            // Log full error for debugging
            Log::error('Failed to add product to cart', [
                'user_id' => $request->user()->id,
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors($e->getMessage());
        }
    }

    /**
     * Update the quantity of a cart item.
     *
     * @param Request $request
     * @param int $itemId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateQty(Request $request, int $itemId)
    {
        // Validate quantity input
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        try {
            $item = $this->service->updateItemQuantity(
                $request->user()->id,
                $itemId,
                $request->quantity
            );

            Log::info('Cart item quantity updated', [
                'user_id' => $request->user()->id,
                'cart_item_id' => $item->id,
                'new_quantity' => $request->quantity,
            ]);

            return back()->with('success', 'Cart updated successfully.');
        } catch (Throwable $e) {
            Log::error('Failed to update cart item quantity', [
                'user_id' => $request->user()->id,
                'cart_item_id' => $itemId,
                'requested_quantity' => $request->quantity,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors($e->getMessage());
        }
    }

    /**
     * Remove a product from the cart.
     *
     * @param Request $request
     * @param int $productId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request, int $productId)
    {
        try {
            $deleted = $this->service->removeItem(
                $request->user()->id,
                $productId
            );

            Log::info('Product removed from cart', [
                'user_id' => $request->user()->id,
                'product_id' => $productId,
                'deleted' => $deleted,
            ]);

            $message = $deleted ? 'Product removed from cart.' : 'Product not found in cart.';
            return back()->with('success', $message);
        } catch (Throwable $e) {
            Log::error('Failed to remove product from cart', [
                'user_id' => $request->user()->id,
                'product_id' => $productId,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors($e->getMessage());
        }
    }
}
