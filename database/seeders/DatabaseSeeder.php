<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Product;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'role' => 'admin',
        ]);

        $supplier = User::factory()->create([
            'name' => 'Supplier User',
            'email' => 'supplier@example.com',
            'role' => 'supplier',
        ]);

        User::factory()->create([
            'name' => 'Normal User',
            'email' => 'user@example.com',
            'role' => 'user',
        ]);

        // Create products associated with the supplier
        Product::factory(10)->create([
            'supplier_id' => $supplier->id,
        ]);
    }
}
