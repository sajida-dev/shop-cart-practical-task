<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Throwable;

class OrderController extends Controller
{
    public function __construct(
        protected OrderService $service

    ) {}

    public function checkout(CheckoutRequest $request)
    {
        try {
            $user = $request->user();
            $order = $this->service->checkout(
                $user->id,
            );
            Log::info('Order placed successfully', [
                'user_id' => $user->id,
                'order_id' => $order->id,
            ]);
            return redirect()
                ->back()
                ->with('success', 'Order placed successfully');
        } catch (Throwable $e) {
            report($e);
            return back()->withErrors($e->getMessage());
        }
    }

    public function show(int $id)
    {
        $user = request()->user();
        $order = Order::with('items.product')
            ->where('user_id', $user->id)
            ->findOrFail($id);

        return Inertia::render('Orders/Show', [
            'order' => $order,
        ]);
    }
}
