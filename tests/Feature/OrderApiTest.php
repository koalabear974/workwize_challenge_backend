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

    /**
     * Test cart creation
     */
    public function test_an_authenticated_user_can_create_an_order()
    {
        // Create a user and authenticate
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        // Create products
        $supplier = User::factory()->create(['role' => 'supplier']);
        $product1 = Product::factory()->create(['price' => 100, 'supplier_id' => $supplier->id]);
        $product2 = Product::factory()->create(['price' => 200, 'supplier_id' => $supplier->id]);

        // Prepare cart data
        $cart = [
            ['id' => $product1->id, 'quantity' => 1],
            ['id' => $product2->id, 'quantity' => 2],
        ];

        // Make the request to create an order
        $response = $this->postJson('/api/orders', ['cart' => $cart]);

        // Assert the response status and structure
        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'user_id',
                'status',
                'created_at',
                'updated_at',
                'order_items' => [
                    '*' => [
                        'id',
                        'order_id',
                        'product_id',
                        'quantity',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ]);

        // Assert the order was created in the database
        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'status' => 'pending',
        ]);

        // Assert the order items were created in the database
        $this->assertDatabaseHas('order_items', [
            'order_id' => $response->json('id'),
            'product_id' => $product1->id,
            'quantity' => 1,
        ]);

        $this->assertDatabaseHas('order_items', [
            'order_id' => $response->json('id'),
            'product_id' => $product2->id,
            'quantity' => 2,
        ]);
    }

    /**
     * Test if a supplier can update an order status
     */
    public function test_only_a_supplier_or_admin_can_update_an_order_status()
    {
        // Create users
        $supplier = User::factory()->create(['role' => 'supplier']);
        $admin = User::factory()->create(['role' => 'admin']);
        $otherUser = User::factory()->create(['role' => 'user']);

        // Create a product associated with the supplier
        $product = Product::factory()->create(['supplier_id' => $supplier->id]);

        // Create an order with an order item for the product
        $order = Order::factory()->create(['user_id' => $otherUser->id, 'status' => 'pending']);
        OrderItem::factory()->create(['order_id' => $order->id, 'product_id' => $product->id]);

        // Attempt to update the order status as the supplier
        $this->actingAs($supplier, 'sanctum');
        $response = $this->patchJson("/api/orders/{$order->id}/status", ['status' => 'completed']);
        $response->assertStatus(200);
        $this->assertEquals('completed', $order->fresh()->status);

        // Attempt to update the order status as the admin
        $this->actingAs($admin, 'sanctum');
        $response = $this->patchJson("/api/orders/{$order->id}/status", ['status' => 'cancelled']);
        $response->assertStatus(200);
        $this->assertEquals('cancelled', $order->fresh()->status);

        // Attempt to update the order status as another user
        $this->actingAs($otherUser, 'sanctum');
        $response = $this->patchJson("/api/orders/{$order->id}/status", ['status' => 'pending']);
        $response->assertStatus(403); // Forbidden
        $this->assertNotEquals('pending', $order->fresh()->status);
    }

    /**
     * Test if admin can delete orders
     */
    public function test_only_admin_can_destroy_an_order()
    {
        // Create users
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'user']);

        // Create an order
        $order = Order::factory()->create();

        // Attempt to delete the order as a regular user
        $this->actingAs($user, 'sanctum');
        $response = $this->deleteJson("/api/orders/{$order->id}");
        $response->assertStatus(403);
        $this->assertDatabaseHas('orders', ['id' => $order->id]);

        // Attempt to delete the order as an admin
        $this->actingAs($admin, 'sanctum');
        $response = $this->deleteJson("/api/orders/{$order->id}");
        $response->assertStatus(204);
        $this->assertDatabaseMissing('orders', ['id' => $order->id]);
    }
}


