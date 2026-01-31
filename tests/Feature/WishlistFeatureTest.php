<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WishlistFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->product = Product::factory()->create();
    }

    public function test_user_can_view_wishlist_page(): void
    {
        $this->actingAs($this->user)
            ->get(route('wishlist.index'))
            ->assertStatus(200)
            ->assertInertia(fn ($assert) => $assert
                ->component('Profile/Wishlist')
                ->has('wishlistItems')
                ->has('filters')
            );
    }

    public function test_unauthenticated_user_cannot_view_wishlist(): void
    {
        $this->get(route('wishlist.index'))
            ->assertRedirect(route('login'));
    }

    public function test_user_can_add_product_to_wishlist(): void
    {
        $this->actingAs($this->user)
            ->post(route('wishlist.store', $this->product))
            ->assertRedirect();

        $this->assertTrue(
            $this->user->wishlist()->where('product_id', $this->product->id)->exists()
        );
    }

    public function test_user_cannot_add_same_product_to_wishlist_twice(): void
    {
        $this->actingAs($this->user)
            ->post(route('wishlist.store', $this->product))
            ->assertRedirect();

        $this->actingAs($this->user)
            ->post(route('wishlist.store', $this->product))
            ->assertRedirect();

        $this->assertEquals(
            1,
            $this->user->wishlist()->where('product_id', $this->product->id)->count()
        );
    }

    public function test_unauthenticated_user_cannot_add_to_wishlist(): void
    {
        $this->post(route('wishlist.store', $this->product))
            ->assertRedirect(route('login'));
    }

    public function test_user_can_remove_product_from_wishlist(): void
    {
        // Add to wishlist first
        $this->actingAs($this->user)
            ->post(route('wishlist.store', $this->product));

        // Verify it's added
        $this->assertTrue(
            $this->user->wishlist()->where('product_id', $this->product->id)->exists()
        );

        // Remove from wishlist
        $this->actingAs($this->user)
            ->delete(route('wishlist.destroy', $this->product))
            ->assertRedirect();

        // Verify it's removed
        $this->assertFalse(
            $this->user->wishlist()->where('product_id', $this->product->id)->exists()
        );
    }

    public function test_unauthenticated_user_cannot_remove_from_wishlist(): void
    {
        $this->delete(route('wishlist.destroy', $this->product))
            ->assertRedirect(route('login'));
    }

    public function test_wishlist_shows_only_non_deleted_products(): void
    {
        $product1 = Product::factory()->create();
        $product2 = Product::factory()->create();

        $this->actingAs($this->user)->post(route('wishlist.store', $product1));
        $this->actingAs($this->user)->post(route('wishlist.store', $product2));

        // Soft delete one product
        $product2->delete();

        $response = $this->actingAs($this->user)->get(route('wishlist.index'));

        $response->assertInertia(fn ($assert) => $assert
            ->where('wishlistItems.total', 1)
            ->where('wishlistItems.data.0.product.id', $product1->id)
        );
    }

    public function test_wishlist_can_be_filtered_by_search(): void
    {
        $product1 = Product::factory()->create(['name' => 'Blue Shirt']);
        $product2 = Product::factory()->create(['name' => 'Red Pants']);

        $this->actingAs($this->user)->post(route('wishlist.store', $product1));
        $this->actingAs($this->user)->post(route('wishlist.store', $product2));

        $response = $this->actingAs($this->user)->get(route('wishlist.index', ['search' => 'Blue']));

        $response->assertInertia(fn ($assert) => $assert
            ->where('wishlistItems.total', 1)
            ->where('wishlistItems.data.0.product.name', 'Blue Shirt')
            ->where('filters.search', 'Blue')
        );
    }

    public function test_wishlist_can_be_filtered_by_category(): void
    {
        $category1 = \App\Models\Category::factory()->create(['name' => 'Clothing']);
        $category2 = \App\Models\Category::factory()->create(['name' => 'Accessories']);

        $product1 = Product::factory()->create(['category_id' => $category1->id]);
        $product2 = Product::factory()->create(['category_id' => $category2->id]);

        $this->actingAs($this->user)->post(route('wishlist.store', $product1));
        $this->actingAs($this->user)->post(route('wishlist.store', $product2));

        $response = $this->actingAs($this->user)->get(route('wishlist.index', ['category' => 'Clothing']));

        $response->assertInertia(fn ($assert) => $assert
            ->where('wishlistItems.total', 1)
            ->where('wishlistItems.data.0.product.category.name', 'Clothing')
            ->where('filters.category', 'Clothing')
        );
    }

    public function test_wishlist_items_are_paginated(): void
    {
        // Create 15 products
        $products = Product::factory(15)->create();

        foreach ($products as $product) {
            $this->actingAs($this->user)->post(route('wishlist.store', $product));
        }

        $response = $this->actingAs($this->user)->get(route('wishlist.index'));

        $response->assertInertia(fn ($assert) => $assert
            ->where('wishlistItems.total', 15)
            ->where('wishlistItems.per_page', 12)
            ->has('wishlistItems.data', 12)
        );
    }

    public function test_wishlist_preserves_product_relationship(): void
    {
        $category = \App\Models\Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);

        $this->actingAs($this->user)->post(route('wishlist.store', $product));

        $response = $this->actingAs($this->user)->get(route('wishlist.index'));

        $response->assertInertia(fn ($assert) => $assert
            ->has('wishlistItems.data.0.product')
            ->where('wishlistItems.data.0.product.id', $product->id)
            ->has('wishlistItems.data.0.product.category')
            ->where('wishlistItems.data.0.product.category.name', $category->name)
        );
    }

    public function test_product_show_page_displays_wishlist_status_when_in_wishlist(): void
    {
        $this->actingAs($this->user)
            ->post(route('wishlist.store', $this->product));

        $response = $this->actingAs($this->user)
            ->get(route('shop.show', $this->product->slug))
            ->assertInertia(fn ($assert) => $assert
                ->where('isInWishlist', true)
            );
    }

    public function test_product_show_page_displays_wishlist_status_when_not_in_wishlist(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('shop.show', $this->product->slug))
            ->assertInertia(fn ($assert) => $assert
                ->where('isInWishlist', false)
            );
    }

    public function test_wishlist_cascade_deletes_when_product_deleted(): void
    {
        $this->actingAs($this->user)->post(route('wishlist.store', $this->product));

        $this->assertTrue(
            $this->user->wishlist()->where('product_id', $this->product->id)->exists()
        );

        // Force delete to test cascade
        $this->product->forceDelete();

        $this->assertFalse(
            Wishlist::where('product_id', $this->product->id)->exists()
        );
    }

    public function test_wishlist_cascade_deletes_when_user_deleted(): void
    {
        $this->actingAs($this->user)->post(route('wishlist.store', $this->product));

        $userId = $this->user->id;

        $this->user->delete();

        $this->assertFalse(
            Wishlist::where('user_id', $userId)->exists()
        );
    }
}
