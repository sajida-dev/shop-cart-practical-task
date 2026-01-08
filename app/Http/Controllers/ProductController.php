<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
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
        dd($products);
        return Inertia::render('Products/Index', [
            'products' => $products,
        ]);
    }


    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        $productData = [
            'id' => $product->id,
            'name' => $product->name ?? '',
            'price' => $product->price ?? 0,
            'stock_quantity' => $product->stock_quantity ?? 0,
            'inStock' => $product->stock_quantity > 5,
        ];

        return Inertia::render('Products/Show', [
            'product' => $productData,
        ]);
    }
}
