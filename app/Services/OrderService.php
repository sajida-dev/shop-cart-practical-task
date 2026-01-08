<?php

namespace App\Services;

use App\Events\ProductStockUpdated;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Throwable;

class OrderService
{
    /**
     * Checkout cart into an order (atomic & safe)
     */
    public function checkout(int $userId, array $addresses): Order
    {
        return DB::transaction(function () use ($userId, $addresses) {

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

                if ($product->stock < $item->quantity) {
                    throw new RuntimeException(
                        "Insufficient stock for {$product->name}"
                    );
                }

                $price = $product->finalPrice();
                $lineTotal = $price * $item->quantity;

                $subtotal += $product->price * $item->quantity;
                $total += $lineTotal;
            }

            $order = Order::create([
                'user_id'          => $userId,
                'order_number'     => Order::generateNumber(),
                'subtotal'         => $subtotal,
                'discount'         => $subtotal - $total,
                'tax'              => 0,
                'total'            => $total,
                'status'           => 'pending',
                'shipping_address' => $addresses['shipping'],
                'billing_address'  => $addresses['billing'],
            ]);

            foreach ($cart->items as $item) {
                $product = Product::lockForUpdate()->find($item->product_id);

                $order->items()->create([
                    'product_id' => $product->id,
                    'quantity'   => $item->quantity,
                    'price'      => $product->finalPrice(),
                ]);

                $product->decrement('stock', $item->quantity);
                $product->refresh();
                ProductStockUpdated::dispatch($product);
            }

            $cart->items()->delete();

            return $order->load('items.product');
        });
    }
}
