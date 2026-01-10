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
use Illuminate\Support\Facades\Log;
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
        $date = Carbon::today()->toDateString();
        // Get all orders from yesterday
        $orders = Order::whereDate('created_at', $date)->with('items.product')->get();
        Log::info('Sending daily sales report for date {date}', ['date' => $date]);

        // Group items by product name
        $soldProducts = $orders->flatMap(function ($order) {
            return $order->items->map(function ($item) {
                return [
                    'name' => $item->product->name,
                    'price' => $item->product->price,
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

        $adminEmail = 'sajidajavaid640@gmail.com';
        Log::info('Sending daily sales report to {email}', ['email' => $adminEmail]);
        Mail::to($adminEmail)->queue(new DailySalesReport($date, $soldProducts));
        Log::info('Daily sales report sent successfully');
    }
}
