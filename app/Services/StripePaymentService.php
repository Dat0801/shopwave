<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;

class StripePaymentService
{
    private StripeClient $stripe;

    public function __construct()
    {
        $this->stripe = new StripeClient(config('stripe.secret_key'));
    }

    /**
     * Create a Stripe Payment Intent for an order
     */
    public function createPaymentIntent(Order $order, int $amountInCents, string $currency = 'usd'): ?Payment
    {
        try {
            $paymentIntent = $this->stripe->paymentIntents->create([
                'amount' => $amountInCents,
                'currency' => $currency,
                'metadata' => [
                    'order_id' => $order->id,
                    'customer_id' => $order->user_id,
                ],
                'description' => "Order #{$order->id} for {$order->user->email}",
            ]);

            // Create or update payment record
            $payment = Payment::updateOrCreate(
                ['stripe_payment_intent_id' => $paymentIntent->id],
                [
                    'order_id' => $order->id,
                    'amount' => $amountInCents,
                    'currency' => $currency,
                    'status' => 'pending',
                    'metadata' => json_encode($paymentIntent->metadata),
                ]
            );

            Log::info("Payment Intent created: {$paymentIntent->id} for Order #{$order->id}");

            return $payment;
        } catch (ApiErrorException $e) {
            Log::error("Stripe API Error: {$e->getMessage()}", ['order_id' => $order->id]);
            throw $e;
        }
    }

    /**
     * Get Stripe Payment Intent details
     */
    public function getPaymentIntent(string $intentId): mixed
    {
        try {
            return $this->stripe->paymentIntents->retrieve($intentId);
        } catch (ApiErrorException $e) {
            Log::error("Failed to retrieve Payment Intent: {$e->getMessage()}");
            throw $e;
        }
    }

    /**
     * Confirm payment and update order status
     */
    public function confirmPayment(Payment $payment): bool
    {
        try {
            $paymentIntent = $this->getPaymentIntent($payment->stripe_payment_intent_id);

            if ($paymentIntent->status === 'succeeded') {
                $payment->update([
                    'status' => 'succeeded',
                    'paid_at' => now(),
                    'payment_method' => $paymentIntent->payment_method,
                ]);

                // Update order payment status
                $payment->order->update([
                    'payment_status' => 'paid',
                    'transaction_id' => $payment->stripe_payment_intent_id,
                ]);

                Log::info("Payment confirmed: {$payment->id} for Order #{$payment->order_id}");

                return true;
            }

            if ($paymentIntent->status === 'requires_action') {
                // Payment requires additional action (3D Secure, etc.)
                $payment->update(['status' => 'pending']);

                return false;
            }

            return false;
        } catch (ApiErrorException $e) {
            Log::error("Payment confirmation failed: {$e->getMessage()}", [
                'payment_id' => $payment->id,
                'intent_id' => $payment->stripe_payment_intent_id,
            ]);
            throw $e;
        }
    }

    /**
     * Handle webhook events
     */
    public function handleWebhookEvent(array $data): bool
    {
        try {
            $signature = request()->header('Stripe-Signature');
            $webhookSecret = config('stripe.webhook_secret');

            $event = $this->stripe->webhookEndpoints->verifyBody(
                json_encode($data),
                $signature,
                $webhookSecret
            );

            // This will be handled by a separate WebhookController
            // Just verify the signature here
            return true;
        } catch (ApiErrorException $e) {
            Log::error("Webhook verification failed: {$e->getMessage()}");

            return false;
        }
    }

    /**
     * Refund a payment
     */
    public function refundPayment(Payment $payment, ?int $amountInCents = null): bool
    {
        try {
            $refundParams = [
                'payment_intent' => $payment->stripe_payment_intent_id,
            ];

            if ($amountInCents) {
                $refundParams['amount'] = $amountInCents;
            }

            $refund = $this->stripe->refunds->create($refundParams);

            if ($refund->status === 'succeeded') {
                $payment->update([
                    'status' => 'refunded',
                ]);

                Log::info("Payment refunded: {$payment->id}", [
                    'refund_id' => $refund->id,
                    'amount' => $refund->amount,
                ]);

                return true;
            }

            return false;
        } catch (ApiErrorException $e) {
            Log::error("Refund failed: {$e->getMessage()}", ['payment_id' => $payment->id]);
            throw $e;
        }
    }

    /**
     * Get Stripe client
     */
    public function getStripeClient(): StripeClient
    {
        return $this->stripe;
    }
}
