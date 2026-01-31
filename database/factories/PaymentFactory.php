<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'stripe_payment_intent_id' => 'pi_'.fake()->bothify('??##??##??##'),
            'amount' => fake()->numberBetween(1000, 100000), // in cents
            'currency' => 'usd',
            'status' => fake()->randomElement(['pending', 'succeeded', 'failed']),
            'payment_method' => fake()->randomElement(['card', 'ideal', 'sepa_debit']),
            'metadata' => json_encode(['test' => true]),
            'paid_at' => now(),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'paid_at' => null,
        ]);
    }

    public function succeeded(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'succeeded',
            'paid_at' => now(),
        ]);
    }

    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'failed',
            'paid_at' => null,
        ]);
    }
}
