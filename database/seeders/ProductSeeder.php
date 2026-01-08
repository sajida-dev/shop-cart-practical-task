<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::create([
            'name' => 'Laptop',
            'price' => 1200.00,
            'stock_quantity' => 10,
        ]);

        Product::create([
            'name' => 'Headphones',
            'price' => 150.00,
            'stock_quantity' => 5,
        ]);

        Product::create([
            'name' => 'Mouse',
            'price' => 50.00,
            'stock_quantity' => 20,
        ]);
    }
}
