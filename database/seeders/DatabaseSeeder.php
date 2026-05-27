<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Admin User
        $admin = User::create([
            'name' => 'Admin Parfum Store',
            'email' => 'admin@parfumstore.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        // 2. Create 5 Perfume Categories
        $categories = Category::factory(5)->create();

        // 3. Create 30 Perfume Products (distributed across categories)
        $products = Product::factory(30)->create();

        // 4. Create sample transactions
        $sampleUser = User::factory()->create([
            'name' => 'Customer Demo',
            'email' => 'customer@example.com',
            'password' => Hash::make('password'),
        ]);

        // Create 5 sample transactions
        foreach ($products->random(5) as $product) {
            $quantity = fake()->numberBetween(1, 3);
            Transaction::create([
                'user_id' => $sampleUser->id,
                'product_id' => $product->id,
                'quantity' => $quantity,
                'total_price' => $product->price * $quantity,
                'status' => fake()->randomElement(['pending', 'processing', 'completed']),
                'payment_method' => fake()->randomElement(['transfer_bank', 'e_wallet', 'cod']),
                'shipping_address' => fake()->address(),
                'notes' => fake()->optional()->sentence(),
            ]);
        }
    }
}
