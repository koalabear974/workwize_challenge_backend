<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Display a listing of the orders.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            // Fetch all orders if the user is an admin
            $orders = Order::all();
        } elseif ($user->role === 'supplier') {
            // Fetch orders where the supplier is involved
            $orders = Order::whereHas('orderItems.product', function($query) use ($user) {
                $query->where('supplier_id', $user->id);
            })->get();
        } else {
            // Fetch only the user's own orders
            $orders = Order::where('user_id', $user->id)->get();
        }

        return response()->json($orders);
    }
}
