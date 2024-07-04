<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrderPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can update the order.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Order  $order
     * @return mixed
     */
    public function update(User $user, Order $order)
    {
        return $user->role === 'admin' || $order->orderItems()->whereHas('product', function($query) use ($user) {
                $query->where('supplier_id', $user->id);
            })->exists();
    }


    /**
     * Determine whether the user can delete the order.
     */
    public function delete(User $user, Order $order)
    {
        return $user->role === 'admin';
    }
}
