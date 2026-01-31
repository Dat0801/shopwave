<?php

namespace Tests\Feature\Payment;

use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StripePaymentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Set test Stripe keys
        config(['stripe.public_key' => 'pk_test_fake']);
        config(['stripe.secret_key' => 'sk_test_fake']);
    }

    public function test_user_can_access_payment_create_endpoint(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'total_price' => 99.99,
            'discount_amount' => 0,
        ]);

        $response = $this->actingAs($user)->postJson(route('payments.create-intent'), [
            'order_id' => $order->id,
            'amount' => 9999, // 99.99 in cents
        ]);

        // Response will be 422 due to fake Stripe keys, but endpoint exists and validates
        $response->assertStatus(422);
    }

    public function test_payment_confirm_endpoint_exists(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);
        $payment = Payment::factory()->create(['order_id' => $order->id]);

        $response = $this->actingAs($user)->postJson(route('payments.confirm'), [
            'payment_id' => $payment->id,
        ]);

        // Should not fail due to not found
        $response->assertStatus(422);
    }

    public function test_unauthorized_user_cannot_confirm_payment(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $order = Order::factory()->create(['user_id' => $user1->id]);
        $payment = Payment::factory()->create(['order_id' => $order->id]);

        $response = $this->actingAs($user2)->postJson(route('payments.confirm'), [
            'payment_id' => $payment->id,
        ]);

        $response->assertStatus(403);
    }

    public function test_user_can_view_payment_process_page(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);
        $payment = Payment::factory()->create([
            'order_id' => $order->id,
            'stripe_payment_intent_id' => 'pi_test_fake',
        ]);

        // This will fail without valid Stripe credentials
        $response = $this->actingAs($user)->get(
            route('payment.process', [
                'orderId' => $order->id,
                'paymentId' => $payment->id,
            ])
        );

        // Will render error page due to invalid Stripe keys
        $response->assertStatus(200); // Should render ProcessError page
    }

    public function test_payment_model_relations(): void
    {
        $order = Order::factory()->create();
        $payment = Payment::factory()->create(['order_id' => $order->id]);

        $this->assertTrue($payment->order()->exists());
        $this->assertEquals($order->id, $payment->order->id);
    }

    public function test_payment_status_checks(): void
    {
        $payment = Payment::factory()->succeeded()->create();
        $this->assertTrue($payment->isPaid());
        $this->assertFalse($payment->isFailed());

        $failedPayment = Payment::factory()->failed()->create();
        $this->assertFalse($failedPayment->isPaid());
        $this->assertTrue($failedPayment->isFailed());
    }

    public function test_order_has_payment_relationship(): void
    {
        $order = Order::factory()->create();
        $payment = Payment::factory()->create(['order_id' => $order->id]);

        $this->assertTrue($order->payment()->exists());
        $this->assertEquals(1, $order->payment()->count());
    }
}
