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

        if (!User::where('email', 'admin@example.com')->exists()) {
            $admin = User::create([
                'name' => 'Admin',
                'email' => 'admin@example.com',
                'password' => Hash::make('123456'),
            ]);

            $admin->assignRole('admin');
        }


        $user = User::first();

        $category = Category::first() ?? Category::create(['name' => 'General']);
        $supplier = Supplier::first() ?? Supplier::create([
            'name' => 'Main Supplier',
            'phone' => '0912345678',
            'email' => 'supplier@example.com',
        ]);


        $product = Product::first() ?? Product::create([
            'supplier_id' => $supplier->id,
            'category_id' => $category->id,
            'name' => 'Test Product',
            'quantity' => 100,
            'price' => 1000,
        ]);


        StockMovement::create([
            'product_id' => $product->id,
            'user_id' => $user->id,
            'quantity' => 50,
            'type' => 'in',
            'date' => now(),
        ]);
    }
}
