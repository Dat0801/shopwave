<?php

namespace Tests\Feature\Checkout;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutStripeIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['stripe.public_key' => 'pk_test_fake']);
        config(['stripe.secret_key' => 'sk_test_fake']);
    }

    public function test_checkout_page_passes_stripe_public_key(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['price' => 50]);

        // Add product to cart
        $this->actingAs($user)->post(route('cart.store', $product), [
            'quantity' => 1,
        ]);

        $response = $this->actingAs($user)->get(route('checkout.index'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Shop/Checkout')
            ->has('stripePublicKey')
            ->where('stripePublicKey', 'pk_test_fake')
        );
    }

    public function test_checkout_page_includes_cart_items(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create([
            'name' => 'Test Product',
            'price' => 99.99,
        ]);

        // Add to cart
        $this->actingAs($user)->post(route('cart.store', $product), [
            'quantity' => 2,
        ]);

        $response = $this->actingAs($user)->get(route('checkout.index'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Shop/Checkout')
            ->has('items')
            ->where('items.0.name', 'Test Product')
            ->where('items.0.quantity', 2)
        );
    }

    public function test_checkout_calculates_totals_correctly(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['price' => 100]);

        $this->actingAs($user)->post(route('cart.store', $product), [
            'quantity' => 1,
        ]);

        $response = $this->actingAs($user)->get(route('checkout.index'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->has('total')
            ->has('discount')
            ->has('totalAfterDiscount')
        );
    }

    public function test_guest_cannot_access_checkout(): void
    {
        $response = $this->get(route('checkout.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_checkout_with_empty_cart_redirects(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('checkout.store'), [
            'firstName' => 'John',
            'lastName' => 'Doe',
            'email' => 'john@example.com',
            'address' => '123 Street',
            'city' => 'City',
            'postalCode' => '12345',
            'paymentMethod' => 'credit_card',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_checkout_requires_valid_data(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['price' => 50]);

        $this->actingAs($user)->post(route('cart.store', $product), [
            'quantity' => 1,
        ]);

        $response = $this->actingAs($user)->post(route('checkout.store'), [
            'firstName' => '',
            'lastName' => '',
            'address' => '',
            'city' => '',
            'postalCode' => '',
            'paymentMethod' => 'invalid_method',
        ]);

        $response->assertSessionHasErrors(['firstName', 'lastName', 'address', 'city', 'postalCode', 'paymentMethod']);
    }
}
