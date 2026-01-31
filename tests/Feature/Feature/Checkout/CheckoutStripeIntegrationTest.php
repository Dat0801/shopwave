<?php

namespace Tests\Feature\Feature\Checkout;

use Tests\TestCase;

class CheckoutStripeIntegrationTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
