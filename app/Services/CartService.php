<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * Service class responsible for handling user cart operations.
 *
 * This includes:
 * - Retrieving cart items
 * - Adding products to cart
 * - Updating quantity
 * - Removing items
 * - Clearing the cart
 *
 * All operations that modify the database are wrapped in transactions
 * for atomicity and use proper row locking to prevent race conditions.
 */
class CartService
{
    /**
     * Retrieve all cart items for a given user.
     *
     * @param int $userId
     * @return array
     */
    public function getAll(int $userId): array
    {
        // Fetch all CartItems where the parent Cart belongs to the user
        $cartItems = CartItem::with(['product'])
            ->whereHas('cart', fn($q) => $q->where('user_id', $userId))
            ->get()
            ->map(function (CartItem $item) {
                $product = $item->product;
                return [
                    'id'       => $item->id,
                    'name'     => $product->name ?? 'Unknown',
                    'price'    => $product->price ?? 0,
                    'quantity' => $item->quantity,
                ];
            });

        Log::info("Fetched cart items for user {$userId}", ['count' => $cartItems->count()]);

        return $cartItems->toArray();
    }

    /**
     * Add a product to a user's cart.
     *
     * If the product is already in the cart (even soft-deleted), it will update the quantity.
     *
     * @param int $userId
     * @param int $productId
     * @param int $quantity
     * @return CartItem
     * @throws RuntimeException
     */
    public function addItem(int $userId, int $productId, int $quantity): CartItem
    {
        return DB::transaction(function () use ($userId, $productId, $quantity) {

            // Lock the product row to prevent race conditions (multiple requests)
            $product = Product::lockForUpdate()->findOrFail($productId);

            Log::info("Attempting to add product to cart", [
                'user_id'          => $userId,
                'product_id'       => $productId,
                'product_stock'    => $product->stock_quantity,
                'requested_qty'    => $quantity,
            ]);

            // Check stock availability
            if ($product->stock_quantity < $quantity) {
                Log::warning("Insufficient stock for product", [
                    'product_id' => $productId,
                    'stock'      => $product->stock_quantity,
                    'requested'  => $quantity,
                ]);
                throw new RuntimeException("Insufficient stock for {$product->name}");
            }

            // Retrieve or create the user's cart
            $cart = Cart::firstOrCreate(['user_id' => $userId]);
            Log::info("Cart retrieved/created", ['cart_id' => $cart->id]);

            // Add or update CartItem, restoring soft-deleted items
            $item = CartItem::withTrashed()->updateOrCreate(
                ['cart_id' => $cart->id, 'product_id' => $productId],
                ['quantity' => $quantity, 'deleted_at' => null]
            );

            Log::info("CartItem added or updated", [
                'cart_item_id' => $item->id,
                'cart_id'      => $cart->id,
                'product_id'   => $productId,
                'quantity'     => $quantity,
                'was_trashed'  => $item->trashed(),
            ]);

            return $item;
        });
    }

    /**
     * Update the quantity of a cart item.
     *
     * @param int $userId
     * @param int $itemId
     * @param int $quantity
     * @return CartItem
     * @throws RuntimeException
     */
    public function updateItemQuantity(int $userId, int $itemId, int $quantity): CartItem
    {
        return DB::transaction(function () use ($userId, $itemId, $quantity) {

            // Fetch the CartItem belonging to the user's cart
            $item = CartItem::whereHas('cart', fn($q) => $q->where('user_id', $userId))
                ->findOrFail($itemId);

            $product = Product::lockForUpdate()->findOrFail($item->product_id);

            Log::info("Updating cart item quantity", [
                'cart_item_id'  => $item->id,
                'user_id'       => $userId,
                'product_id'    => $product->id,
                'old_quantity'  => $item->quantity,
                'new_quantity'  => $quantity,
                'stock_available' => $product->stock_quantity,
            ]);

            // Stock validation
            if ($product->stock_quantity < $quantity) {
                Log::warning("Insufficient stock to update quantity", [
                    'product_id' => $product->id,
                    'stock'      => $product->stock_quantity,
                    'requested'  => $quantity,
                ]);
                throw new RuntimeException("Insufficient stock for {$product->name}");
            }

            $item->quantity = $quantity;
            $item->save();

            Log::info("Cart item quantity updated successfully", [
                'cart_item_id' => $item->id,
                'quantity'     => $item->quantity,
            ]);

            return $item;
        });
    }

    /**
     * Remove a product from the user's cart.
     *
     * @param int $userId
     * @param int $productId
     * @return bool
     */
    public function removeItem(int $userId, int $productId): bool
    {
        $cart = Cart::where('user_id', $userId)->firstOrFail();

        $deleted = $cart->items()->where('product_id', $productId)->delete() > 0;

        Log::info("Removed product from cart", [
            'user_id'    => $userId,
            'cart_id'    => $cart->id,
            'product_id' => $productId,
            'deleted'    => $deleted,
        ]);

        return $deleted;
    }

    /**
     * Clear all items from the user's cart.
     *
     * @param int $userId
     * @return bool
     */
    public function clear(int $userId): bool
    {
        $cart = Cart::where('user_id', $userId)->first();

        if (!$cart) {
            Log::info("Clear cart: no cart found for user", ['user_id' => $userId]);
            return false;
        }

        $deleted = $cart->items()->delete() > 0;

        Log::info("Cleared cart items", [
            'user_id' => $userId,
            'cart_id' => $cart->id,
            'deleted_count' => $deleted,
        ]);

        return $deleted;
    }
}
