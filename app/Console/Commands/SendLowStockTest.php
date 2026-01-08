<?php

namespace App\Console\Commands;

use App\Jobs\LowStockJob;
use App\Models\Product;
use Illuminate\Console\Command;

class SendLowStockTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stock:low-test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $product = Product::first();
        dispatch(new LowStockJob($product));
        $this->info('Low stock alert dispatched for ' . $product->name);
    }
}
