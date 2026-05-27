<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $perfumeNames = [
            'Midnight Oud', 'Rose Imperiale', 'Velvet Noir', 'Golden Amber',
            'Ocean Breeze', 'Mystic Jasmine', 'Royal Sandalwood', 'Desert Rose',
            'Blue Lagoon', 'White Musk', 'Smoky Vetiver', 'Cherry Blossom',
            'Lavender Dreams', 'Citrus Burst', 'Vanilla Orchid', 'Spicy Cardamom',
            'Fresh Bergamot', 'Woody Cedar', 'Floral Fantasy', 'Amber Nights',
            'Patchouli Essence', 'Lotus Bloom', 'Cedarwood Mist', 'Tropical Paradise',
            'Emerald Garden', 'Saffron Gold', 'Iris Velvet', 'Tonka Absolute',
            'Pink Pepper', 'Aqua Marine', 'Neroli Sunrise', 'Mango Tango',
            'Dark Leather', 'Sweet Vanilla', 'Forest Pine', 'Honey Blossom',
        ];

        $name = fake()->unique()->randomElement($perfumeNames);

        return [
            'category_id' => Category::inRandomOrder()->first()->id ?? Category::factory(),
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => fake()->paragraph(3),
            'price' => fake()->randomFloat(2, 150000, 2500000),
            'stock' => fake()->numberBetween(5, 100),
            'image' => null,
        ];
    }
}
