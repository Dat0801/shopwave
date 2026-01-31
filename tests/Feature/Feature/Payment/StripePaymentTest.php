<?php

namespace Tests\Feature\Feature\Payment;

use Tests\TestCase;

class StripePaymentTest extends TestCase
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
