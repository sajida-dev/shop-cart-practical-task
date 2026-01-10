<?php

namespace App\Observers;

use App\Jobs\LowStockJob;
use App\Models\Product;
use Illuminate\Support\Facades\Log;

class ProductObserver
{
    /**
     * Handle the Product "created" event.
     */
    public function created(Product $product): void
    {

        $this->checkLowStock($product, null);
    }

    /**
     * Handle the Product "updated" event.
     */
    public function updated(Product $product): void
    {
        // Only if stock changed
        if (! $product->wasChanged('stock_quantity')) {
            return;
        }
        Log::info('Product stock changed for product_id {product_id} in ProductObserver', [
            'product_id' => $product->id,
            'old' => $product->getOriginal('stock_quantity'),
            'new' => $product->stock_quantity,
        ]);
        $this->checkLowStock(
            $product,
            $product->getOriginal('stock_quantity')
        );
    }

    /**
     * Handle the Product "deleted" event.
     */
    public function deleted(Product $product): void
    {
        //
    }

    /**
     * Handle the Product "restored" event.
     */
    public function restored(Product $product): void
    {
        //
    }

    /**
     * Handle the Product "force deleted" event.
     */
    public function forceDeleted(Product $product): void
    {
        //
    }

    /**
     * Shared low-stock logic
     */
    protected function checkLowStock(Product $product, ?int $oldStock): void
    {
        $threshold =  5;

        /*
        * If this is a newly created product, there is no
        * previous stock value to compare against.
        *
        * In that case, notify only if the product starts
        * life already in a low-stock state.
        */
        if ($oldStock === null) {
            if ($product->stock_quantity <= $threshold) {
                LowStockJob::dispatch($product);
            }

            return;
        }

        /*
        * Notify only when the stock level crosses the
        * threshold boundary.
        *
        * Example:
        *  - old stock: 6
        *  - new stock: 5
        *  - threshold: 5
        *
        * This ensures the notification is sent once and
        * prevents duplicate emails for subsequent reductions.
        */
        if (
            $oldStock > $threshold &&
            $product->stock_quantity <= $threshold &&
            $oldStock > $threshold
        ) {
            LowStockJob::dispatch($product);
        }
    }
}
