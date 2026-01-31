<?php

namespace Tests\Feature\Shop;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_show_product_returns_correct_data(): void
    {
        // Arrange
        $category = Category::factory()->create([
            'status' => true,
        ]);

        $product = Product::factory()->create([
            'category_id' => $category->id,
            'status' => true,
            'stock' => 10,
        ]);

        // Create variants with colors and sizes
        ProductVariant::factory()->create([
            'product_id' => $product->id,
            'active' => true,
            'options' => ['Color' => 'Black', 'Size' => 'M'],
        ]);

        ProductVariant::factory()->create([
            'product_id' => $product->id,
            'active' => true,
            'options' => ['Color' => 'Blue', 'Size' => 'L'],
        ]);

        // Create approved reviews
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        Review::factory()->create([
            'product_id' => $product->id,
            'user_id' => $user1->id,
            'rating' => 5,
            'status' => 'approved',
        ]);

        Review::factory()->create([
            'product_id' => $product->id,
            'user_id' => $user2->id,
            'rating' => 4,
            'status' => 'approved',
        ]);

        // Create a pending review (should not be included)
        Review::factory()->create([
            'product_id' => $product->id,
            'user_id' => $user1->id,
            'rating' => 3,
            'status' => 'pending',
        ]);

        // Create related products
        $relatedProduct = Product::factory()->create([
            'category_id' => $category->id,
            'status' => true,
        ]);

        // Act
        $response = $this->get(route('shop.show', $product->slug));

        // Assert
        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Shop/Show')
            ->has('product')
            ->where('product.id', $product->id)
            ->where('product.name', $product->name)
            ->where('product.category.id', $category->id)
            ->has('product.variants', 2)
            ->has('product.reviews', 2)
            ->has('reviewStats')
            ->where('reviewStats.total', 2)
            ->where('reviewStats.average', 4.5)
            ->where('reviewStats.distribution.5', 1)
            ->where('reviewStats.distribution.4', 1)
            ->where('reviewStats.distribution.3', 0)
            ->has('availableOptions')
            ->has('availableOptions.colors', 2)
            ->has('availableOptions.sizes', 2)
            ->has('relatedProducts')
        );
    }

    public function test_show_product_with_no_reviews(): void
    {
        // Arrange
        $category = Category::factory()->create(['status' => true]);
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'status' => true,
        ]);

        // Act
        $response = $this->get(route('shop.show', $product->slug));

        // Assert
        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Shop/Show')
            ->where('reviewStats.total', 0)
            ->where('reviewStats.average', 0)
        );
    }

    public function test_show_product_with_no_variants(): void
    {
        // Arrange
        $category = Category::factory()->create(['status' => true]);
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'status' => true,
        ]);

        // Act
        $response = $this->get(route('shop.show', $product->slug));

        // Assert
        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Shop/Show')
            ->has('availableOptions')
            ->has('availableOptions.colors', 0)
            ->has('availableOptions.sizes', 0)
        );
    }

    public function test_related_products_excludes_current_product(): void
    {
        // Arrange
        $category = Category::factory()->create(['status' => true]);
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'status' => true,
        ]);

        $relatedProduct = Product::factory()->create([
            'category_id' => $category->id,
            'status' => true,
        ]);

        // Act
        $response = $this->get(route('shop.show', $product->slug));

        // Assert
        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Shop/Show')
            ->has('relatedProducts', 1)
            ->where('relatedProducts.0.id', $relatedProduct->id)
        );
    }
}
