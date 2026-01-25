<?php

namespace Tests\Feature\Shop;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_creates_order_with_payment_method()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['price' => 100]);

        $cart = [
            $product->id => [
                'product_id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => 1,
                'image' => $product->image_path,
                'size' => 'M',
            ]
        ];

        $response = $this->actingAs($user)
            ->withSession(['cart' => $cart])
            ->post(route('checkout.store'), [
                'firstName' => 'John',
                'lastName' => 'Doe',
                'address' => '123 Main St',
                'city' => 'New York',
                'postalCode' => '10001',
                'paymentMethod' => 'cod',
            ]);

        $response->assertRedirect(route('orders.index'));

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'payment_method' => 'cod',
            'payment_status' => 'pending',
            'total_price' => 100,
        ]);
    }

    public function test_checkout_validation_requires_payment_method()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $cart = [
            $product->id => [
                'product_id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => 1,
                'image' => $product->image_path,
                'size' => 'M',
            ]
        ];

        $response = $this->actingAs($user)
            ->withSession(['cart' => $cart])
            ->post(route('checkout.store'), [
                'firstName' => 'John',
                'lastName' => 'Doe',
                'address' => '123 Main St',
                'city' => 'New York',
                'postalCode' => '10001',
                // paymentMethod missing
            ]);

        $response->assertSessionHasErrors('paymentMethod');
    }

    public function test_checkout_with_credit_card_sets_status_paid()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['price' => 100]);

        $cart = [
            $product->id => [
                'product_id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => 1,
                'image' => $product->image_path,
                'size' => 'M',
            ]
        ];

        $response = $this->actingAs($user)
            ->withSession(['cart' => $cart])
            ->post(route('checkout.store'), [
                'firstName' => 'John',
                'lastName' => 'Doe',
                'address' => '123 Main St',
                'city' => 'New York',
                'postalCode' => '10001',
                'paymentMethod' => 'credit_card',
            ]);

        $response->assertRedirect(route('orders.index'));

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'payment_method' => 'credit_card',
            'payment_status' => 'paid', // As per our logic in OrderService
        ]);
    }
}
