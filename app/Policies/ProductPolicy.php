<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    /**
     * Determine whether the user can view any products (product listing).
     */
    public function viewAny(User $user): bool
    {
        return true; // Everyone can see the product catalog
    }

    /**
     * Determine whether the user can view a specific product.
     */
    public function view(User $user, Product $product): bool
    {
        return true; // All users can view individual products
    }

    /**
     * Determine whether the user can create a product.
     */
    public function create(User $user): bool
    {
        return $user->role === 'admin'; // Only admin can add products
    }

    /**
     * Determine whether the user can update a product.
     */
    public function update(User $user, Product $product): bool
    {
        return $user->role === 'admin'; // Only admin can edit products
    }

    /**
     * Determine whether the user can delete a product.
     */
    public function delete(User $user, Product $product): bool
    {
        return $user->role === 'admin'; // Only admin can delete products
    }

    /**
     * Determine whether the user can restore a product (if soft deleted).
     */
    public function restore(User $user, Product $product): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can permanently delete a product.
     */
    public function forceDelete(User $user, Product $product): bool
    {
        return $user->role === 'admin';
    }
}
