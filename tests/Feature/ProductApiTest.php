<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;

class ProductApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test getting all products as an authenticated user.
     *
     * @return void
     */
    public function test_get_all_products_as_authenticated_user()
    {
        $supplier = User::factory()->create(['role' => 'supplier']);
        Product::factory(10)->create(['supplier_id' => $supplier->id]);

        $response = $this->actingAs($supplier, 'sanctum')->getJson('/api/products');

        $response->assertStatus(200)->assertJsonCount(10);
    }

    /**
     * Test getting a single product as an authenticated user.
     *
     * @return void
     */
    public function test_get_single_product_as_authenticated_user()
    {
        $supplier = User::factory()->create(['role' => 'supplier']);
        $product = Product::factory()->create(['supplier_id' => $supplier->id]);

        $response = $this->actingAs($supplier, 'sanctum')->getJson("/api/products/{$product->id}");

        $response->assertStatus(200)->assertJson([
            'id' => $product->id,
            'name' => $product->name,
            'description' => $product->description,
            'image' => $product->image,
            'stock' => $product->stock,
            'price' => $product->price,
            'supplier_id' => $product->supplier_id,
        ]);
    }

    /**
     * Test creating a product as an authenticated user.
     *
     * @return void
     */
    public function test_create_product_as_authenticated_user()
    {
        $supplier = User::factory()->create(['role' => 'supplier']);

        $data = [
            'name' => 'Test Product',
            'description' => 'Test Description',
            'image' => 'http://example.com/image.jpg',
            'stock' => 10,
            'price' => 99.99,
            'supplier_id' => $supplier->id,
        ];

        $response = $this->actingAs($supplier, 'sanctum')->postJson('/api/products', $data);

        $response->assertStatus(201)->assertJson($data);
    }

    /**
     * Test updating a product as an authenticated user.
     *
     * @return void
     */
    public function test_update_product_as_authenticated_user()
    {
        $supplier = User::factory()->create(['role' => 'supplier']);
        $product = Product::factory()->create(['supplier_id' => $supplier->id]);

        $data = [
            'name' => 'Updated Product',
            'description' => 'Updated Description',
            'stock' => 20,
            'price' => 199.99,
        ];

        $response = $this->actingAs($supplier, 'sanctum')->putJson("/api/products/{$product->id}", $data);

        $response->assertStatus(200)->assertJson($data);
    }

    /**
     * Test deleting a product as an authenticated user.
     *
     * @return void
     */
    public function test_delete_product_as_authenticated_user()
    {
        $supplier = User::factory()->create(['role' => 'supplier']);
        $product = Product::factory()->create(['supplier_id' => $supplier->id]);

        $response = $this->actingAs($supplier, 'sanctum')->deleteJson("/api/products/{$product->id}");

        $response->assertStatus(204);
    }

    /**
     * Test getting all products as an unauthenticated user.
     *
     * @return void
     */
    public function test_get_all_products_as_unauthenticated_user()
    {
        $response = $this->getJson('/api/products');

        $response->assertStatus(401);
    }

    /**
     * Test updating a product as a different supplier.
     */
    public function test_update_product_as_different_supplier()
    {
        $supplier1 = User::factory()->create(['role' => 'supplier']);
        $supplier2 = User::factory()->create(['role' => 'supplier']);
        $product = Product::factory()->create(['supplier_id' => $supplier1->id]);

        $data = [
            'name' => 'Updated Product',
            'description' => 'Updated Description',
            'stock' => 20,
            'price' => 199.99,
        ];

        $response = $this->actingAs($supplier2, 'sanctum')->putJson("/api/products/{$product->id}", $data);

        $response->assertStatus(403);
    }

    /**
     * Test deleting a product as a different supplier.
     */
    public function test_delete_product_as_different_supplier()
    {
        $supplier1 = User::factory()->create(['role' => 'supplier']);
        $supplier2 = User::factory()->create(['role' => 'supplier']);
        $product = Product::factory()->create(['supplier_id' => $supplier1->id]);

        $response = $this->actingAs($supplier2, 'sanctum')->deleteJson("/api/products/{$product->id}");

        $response->assertStatus(403);
    }

    /**
     * Test fetching a supplier's own products.
     */
    public function test_fetch_own_products_as_supplier()
    {
        $supplier1 = User::factory()->create(['role' => 'supplier']);
        $supplier2 = User::factory()->create(['role' => 'supplier']);
        $product = Product::factory(10)->create(['supplier_id' => $supplier1->id]);
        $product2 = Product::factory(10)->create(['supplier_id' => $supplier2->id]);

        $response = $this->actingAs($supplier1, 'sanctum')->getJson('/api/supplier/products');

        $response->assertStatus(200)->assertJsonCount(10);
    }
}
