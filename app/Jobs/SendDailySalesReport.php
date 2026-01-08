<?php

namespace App\Jobs;

use App\Mail\DailySalesReport;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendDailySalesReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 120;


    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $date = Carbon::yesterday()->toDateString();

        // Get all orders from yesterday
        $orders = Order::whereDate('created_at', $date)->with('items.product')->get();

        $soldProducts = $orders->flatMap(function ($order) {
            return $order->items->map(function ($item) {
                return [
                    'name' => $item->product->name,
                    'quantity' => $item->quantity,
                    'total' => $item->quantity * $item->price,
                ];
            });
        })->groupBy('name')->map(function ($items, $name) {
            $quantity = collect($items)->sum('quantity');
            $total = collect($items)->sum('total');
            return [
                'name' => $name,
                'quantity' => $quantity,
                'total' => $total,
            ];
        })->values();

        $adminEmail = 'admin@example.com';
        Mail::to($adminEmail)->queue(new DailySalesReport($date, $soldProducts));
    }
}
