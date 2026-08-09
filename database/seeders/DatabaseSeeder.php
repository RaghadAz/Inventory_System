<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{

    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            RolesAndPermissionsSeeder::class,
        ]);

        $user = User::first() ?? User::factory()->create();

        $category = Category::first() ?? Category::create(['name' => 'General']);
        $supplier = Supplier::first() ?? Supplier::create([
            'name' => 'Main Supplier',
            'phone' => '0912345678',
            'email' => 'supplier@example.com', // 👈 أضيفتي هذا السطر المفقود
        ]);

        $category = Category::first() ?? Category::create(['name' => 'General']);

        $product = Product::first() ?? Product::create([
            'supplier_id' => $supplier->id,
            'category_id' => $category->id,
            'name' => 'Experimental Product',
            'quantity' => 100,
            'price' => 1000,
        ]);

        $user = User::first() ?? User::factory()->create();

        StockMovement::create([
            'product_id' => $product->id,
            'user_id' => $user->id,
            'quantity' => 50,
            'type' => 'in',
            'date' => now(),
        ]);
    }
}
