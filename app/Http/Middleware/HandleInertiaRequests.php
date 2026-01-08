<?php

namespace App\Http\Middleware;

use App\Models\Cart;
use Illuminate\Foundation\Inspiring;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        [$message, $author] = str(Inspiring::quotes()->random())->explode('-');
        $cartItems = collect();
        if ($request->user()) {
            $cart = Cart::with('items.product')->firstOrCreate(['user_id' => $request->user()->id]);

            $cartItems = $cart->items->map(fn($item) => [
                'id' => $item->id,
                'productId' => $item->product_id,
                'name' => $item->product->name ?? '',
                'price' => $item->product->price ?? 0,
                'quantity' => $item->quantity,
            ]);
        }
        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'quote' => ['message' => trim($message), 'author' => trim($author)],
            'auth' => [
                'user' => $request->user(),
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
            'shop' => [
                'cart' => fn() => $cartItems,
                'cartCount' => fn() => $cartItems->sum('quantity'),
            ],
        ];
    }
}
