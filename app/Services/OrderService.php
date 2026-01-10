<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class OrderService
{
    /**
     * Checkout cart into an order (atomic & safe)
     */
    public function checkout(int $userId): Order
    {
        return DB::transaction(function () use ($userId) {

            $cart = Cart::with('items.product')
                ->where('user_id', $userId)
                ->lockForUpdate()
                ->firstOrFail();

            if ($cart->items->isEmpty()) {
                throw new RuntimeException('Cart is empty');
            }

            $subtotal = 0;
            $total = 0;

            foreach ($cart->items as $item) {
                $product = Product::lockForUpdate()->findOrFail($item->product_id);

                if ($product->stock_quantity < $item->quantity) {
                    throw new RuntimeException(
                        "Insufficient stock for {$product->name}"
                    );
                }

                $price = $product->price;
                $lineTotal = $price * $item->quantity;

                $subtotal += $product->price * $item->quantity;
                $total += $lineTotal;
            }

            $order = Order::create([
                'user_id'          => $userId,
                'total'            => $total,
            ]);

            foreach ($cart->items as $item) {
                $product = Product::lockForUpdate()->find($item->product_id);

                $order->items()->create([
                    'product_id' => $product->id,
                    'quantity'   => $item->quantity,
                    'price'      => $product->price,
                ]);

                $product->stock_quantity -= $item->quantity;
                $product->save();
            }

            $cart->items()->delete();

            return $order->load('items.product');
        });
    }
}
