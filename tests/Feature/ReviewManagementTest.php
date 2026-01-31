<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ReviewManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_submit_review_once(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('products.reviews.store', $product->id), [
                'rating' => 5,
                'comment' => 'Great product.',
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('reviews', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'rating' => 5,
            'status' => 'pending',
        ]);

        $this->actingAs($user)
            ->post(route('products.reviews.store', $product->id), [
                'rating' => 4,
                'comment' => 'Second review attempt.',
            ])
            ->assertRedirect();

        $this->assertDatabaseCount('reviews', 1);
    }

    public function test_review_auto_approved_for_verified_buyer_with_high_rating(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        // Create an order for the user to mark them as verified buyer
        $order = Order::factory()->create(['user_id' => $user->id]);
        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
        ]);

        $response = $this->actingAs($user)
            ->post(route('products.reviews.store', $product->id), [
                'rating' => 5,
                'comment' => 'Excellent product, highly recommend!',
            ]);

        $response->assertRedirect();

        // Review should be auto-approved
        $this->assertDatabaseHas('reviews', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'rating' => 5,
            'status' => 'approved',
        ]);
    }

    public function test_review_pending_for_non_verified_buyer(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        // User has no orders, so not a verified buyer
        $response = $this->actingAs($user)
            ->post(route('products.reviews.store', $product->id), [
                'rating' => 5,
                'comment' => 'Great product.',
            ]);

        $response->assertRedirect();

        // Review should stay pending
        $this->assertDatabaseHas('reviews', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'status' => 'pending',
        ]);
    }

    public function test_review_pending_for_low_rating(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        // Create an order for the user
        $order = Order::factory()->create(['user_id' => $user->id]);
        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
        ]);

        $response = $this->actingAs($user)
            ->post(route('products.reviews.store', $product->id), [
                'rating' => 3,
                'comment' => 'Average product.',
            ]);

        $response->assertRedirect();

        // Review should stay pending (rating < 4)
        $this->assertDatabaseHas('reviews', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'rating' => 3,
            'status' => 'pending',
        ]);
    }

    public function test_review_pending_if_contains_banned_keywords(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        // Create an order for the user
        $order = Order::factory()->create(['user_id' => $user->id]);
        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
        ]);

        $response = $this->actingAs($user)
            ->post(route('products.reviews.store', $product->id), [
                'rating' => 5,
                'comment' => 'Great product but spam keyword is bad.',
            ]);

        $response->assertRedirect();

        // Review should stay pending (contains banned keyword "spam")
        $this->assertDatabaseHas('reviews', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'status' => 'pending',
        ]);
    }

    public function test_admin_can_update_review_status(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $review = Review::factory()->create([
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)
            ->patch(route('admin.reviews.update-status', $review), [
                'status' => 'approved',
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('reviews', [
            'id' => $review->id,
            'status' => 'approved',
        ]);
    }

    public function test_product_show_only_returns_approved_reviews(): void
    {
        $product = Product::factory()->create();

        Review::factory()->create([
            'product_id' => $product->id,
            'status' => 'approved',
        ]);

        Review::factory()->create([
            'product_id' => $product->id,
            'status' => 'rejected',
        ]);

        $response = $this->get(route('shop.show', $product->slug));

        $response->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->component('Shop/Show')
                ->has('product.reviews', 1)
                ->where('product.reviews.0.status', 'approved')
            );
    }
}
