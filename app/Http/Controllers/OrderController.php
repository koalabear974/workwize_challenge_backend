<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class OrderController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the orders.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            // Fetch all orders if the user is an admin, including order items and product details
            $orders = Order::with('orderItems.product')->get();
        } elseif ($user->role === 'supplier') {
            // Fetch orders where the supplier is involved, including order items and product details
            $orders = Order::whereHas('orderItems.product', function($query) use ($user) {
                $query->where('supplier_id', $user->id);
            })->with('orderItems.product')->get();
        } else {
            // Fetch only the user's own orders, including order items and product details
            $orders = Order::where('user_id', $user->id)->with('orderItems.product')->get();
        }

        return response()->json($orders);
    }

    /**
     * Store a newly created order in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'cart' => 'required|array',
            'cart.*.id' => 'required|exists:products,id',
            'cart.*.quantity' => 'required|integer|min:1',
        ]);

        $order = DB::transaction(function () use ($request) {
            $user = Auth::user();
            $cart = $request->input('cart');

            $order = Order::create([
                'user_id' => $user->id,
                'status' => 'pending',
            ]);

            if (!$order) {
                Log::error('Order creation failed');
                throw new \Exception('Order creation failed');
            }

            foreach ($cart as $cartItem) {
                $orderItem = OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem['id'],
                    'quantity' => $cartItem['quantity'],
                ]);

                if (!$orderItem) {
                    Log::error('OrderItem creation failed', ['order_id' => $order->id, 'product_id' => $cartItem['id']]);
                    throw new \Exception('OrderItem creation failed for product_id: ' . $cartItem['id']);
                }
            }

            return $order;
        });

        if ($order) {
            // Load the related order items
            $order->load('orderItems');
            return response()->json($order, 201);
        } else {
            return response()->json(['message' => 'Order creation failed'], 500);
        }
    }

    /**
     * Update the status of the specified order.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|string',
        ]);

        // Check if the user is authorized to update the order
        $this->authorize('update', $order);

        // Update the order status
        $order->status = $request->status;
        $order->save();

        return response()->json($order, 200);
    }

    /**
     * Remove the specified order from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $this->authorize('delete', $order);
        $order->delete();
        return response()->json(null, 204);
    }
}
