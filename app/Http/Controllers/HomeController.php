<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Inertia\Inertia;
use Laravel\Fortify\Features;

class HomeController extends Controller
{
    public function index()
    {
        $products = Product::all()->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name ?? '',
                'price' => $product->price ?? 0,
                'stock_quantity' => $product->stock_quantity ?? 0,
                'inStock' => $product->stock_quantity > 5 ? true : false,
            ];
        });

        return Inertia::render('Welcome', [
            'canRegister' => Features::enabled(Features::registration()),
            'products' => $products,

        ]);
    }
}
