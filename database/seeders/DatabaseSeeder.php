<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@shopwave.test'],
            [
                'name' => 'Admin',
                'password' => bcrypt('password'),
                'role' => 'admin',
            ],
        );

        $categories = [];

        $categoryDefinitions = [
            [
                'name' => 'T-Shirts',
                'slug' => 't-shirts',
                'status' => true,
            ],
            [
                'name' => 'Hoodies',
                'slug' => 'hoodies',
                'status' => true,
            ],
            [
                'name' => 'Accessories',
                'slug' => 'accessories',
                'status' => true,
            ],
        ];

        foreach ($categoryDefinitions as $data) {
            $category = Category::withTrashed()->updateOrCreate(
                ['slug' => $data['slug']],
                [
                    'name' => $data['name'],
                    'status' => $data['status'],
                    'parent_id' => null,
                ],
            );

            $category->restore();

            $categories[$data['slug']] = $category;
        }

        $productDefinitions = [
            [
                'name' => 'Classic Cotton Tee',
                'slug' => 'classic-cotton-tee',
                'price' => '19.00',
                'sale_price' => null,
                'stock' => 100,
                'description' => 'Soft and breathable cotton tee for everyday wear.',
                'category_slug' => 't-shirts',
                'status' => true,
                'image_path' => null,
            ],
            [
                'name' => 'Premium Hoodie',
                'slug' => 'premium-hoodie',
                'price' => '49.00',
                'sale_price' => '39.00',
                'stock' => 50,
                'description' => 'Cozy fleece hoodie with a relaxed fit.',
                'category_slug' => 'hoodies',
                'status' => true,
                'image_path' => null,
            ],
            [
                'name' => 'Canvas Tote Bag',
                'slug' => 'canvas-tote-bag',
                'price' => '15.00',
                'sale_price' => null,
                'stock' => 200,
                'description' => 'Durable tote bag perfect for daily errands.',
                'category_slug' => 'accessories',
                'status' => true,
                'image_path' => null,
            ],
        ];

        foreach ($productDefinitions as $data) {
            $category = $categories[$data['category_slug']] ?? null;

            if (! $category) {
                continue;
            }

            $product = Product::withTrashed()->updateOrCreate(
                ['slug' => $data['slug']],
                [
                    'name' => $data['name'],
                    'price' => $data['price'],
                    'sale_price' => $data['sale_price'],
                    'stock' => $data['stock'],
                    'description' => $data['description'],
                    'category_id' => $category->id,
                    'status' => $data['status'],
                    'image_path' => $data['image_path'],
                ],
            );

            $product->restore();
        }
    }
}
