<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can update the product.
     */
    public function update(User $user, Product $product)
    {
        return $user->role === 'admin' || $user->id === $product->supplier_id;
    }

    /**
     * Determine whether the user can delete the product.
     */
    public function delete(User $user, Product $product)
    {
        return $user->role === 'admin' || $user->id === $product->supplier_id;
    }
}
