<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;

class OrderApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test fetching all orders as an admin.
     */
    public function test_fetch_all_orders_as_admin()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Order::factory(10)->create();

        $response = $this->actingAs($admin, 'sanctum')->getJson('/api/orders');

        $response->assertStatus(200)->assertJsonCount(10);
    }

    /**
     * Test fetching user's own orders.
     */
    public function test_fetch_own_orders_as_user()
    {
        $user = User::factory()->create(['role' => 'user']);
        Order::factory(10)->create();
        Order::factory(3)->create(['user_id' => $user->id]);

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/orders');

        $response->assertStatus(200)->assertJsonCount(3);
    }

    /**
     * Test fetching orders related to a supplier.
     */
    public function test_fetch_orders_as_supplier()
    {
        $supplier = User::factory()->create(['role' => 'supplier']);
        $product = Product::factory()->create(['supplier_id' => $supplier->id]);
        Order::factory(10)->create();
        $order = Order::factory()->create();
        OrderItem::factory()->create(['order_id' => $order->id, 'product_id' => $product->id]);

        $response = $this->actingAs($supplier, 'sanctum')->getJson('/api/orders');

        $response->assertStatus(200)->assertJsonCount(1);
    }
}
