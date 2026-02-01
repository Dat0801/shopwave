<?php

namespace App\Http\Controllers;

use App\Mail\PaymentSuccessful;
use App\Models\Order;
use App\Models\Payment;
use App\Services\StripePaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Inertia\Response;

class PaymentController extends Controller
{
    public function __construct(private readonly StripePaymentService $stripeService) {}

    /**
     * Create a payment intent for an order
     */
    public function createIntent(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'order_id' => 'required|exists:orders,id',
                'amount' => 'required|integer|min:100',
            ]);

            $order = auth()->user()->orders()->findOrFail($request->input('order_id'));

            $payment = $this->stripeService->createPaymentIntent(
                $order,
                $request->input('amount'),
                'usd'
            );

            return response()->json([
                'success' => true,
                'client_secret' => $this->getPaymentIntent($payment)?->client_secret,
                'payment_id' => $payment->id,
                'public_key' => config('stripe.public_key'),
            ]);
        } catch (\Exception $e) {
            Log::error('Payment intent creation failed: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to create payment intent',
            ], 422);
        }
    }

    /**
     * Display payment processing page
     */
    public function process(Request $request, int $orderId, int $paymentId): Response
    {
        $order = auth()->user()->orders()->with('user')->findOrFail($orderId);
        $payment = Payment::findOrFail($paymentId);

        if ($payment->order_id !== $order->id) {
            abort(403);
        }

        try {
            $paymentIntent = $this->stripeService->getPaymentIntent($payment->stripe_payment_intent_id);
        } catch (\Exception $e) {
            Log::error('Failed to fetch payment intent: '.$e->getMessage());

            return Inertia::render('Payment/ProcessError', [
                'orderId' => $orderId,
                'message' => 'Failed to load payment details',
            ]);
        }

        return Inertia::render('Payment/Process', [
            'order' => $order,
            'payment' => $payment,
            'clientSecret' => $paymentIntent->client_secret,
            'stripePublicKey' => config('stripe.public_key'),
            'amount' => $payment->amount,
            'currency' => $payment->currency,
            'csrfToken' => csrf_token(),
        ]);
    }

    /**
     * Confirm payment after Stripe processing
     */
    public function confirm(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'payment_id' => 'required|exists:payments,id',
            ]);

            $payment = Payment::findOrFail($request->input('payment_id'));

            // Verify payment belongs to authenticated user
            if ($payment->order->user_id !== auth()->id()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }

            $confirmed = $this->stripeService->confirmPayment($payment);

            return response()->json([
                'success' => true,
                'paid' => $payment->isPaid(),
                'message' => $confirmed ? 'Payment confirmed' : 'Payment pending',
            ]);
        } catch (\Exception $e) {
            Log::error('Payment confirmation failed: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Payment confirmation failed',
            ], 422);
        }
    }

    /**
     * Webhook endpoint for Stripe events
     */
    public function webhook(Request $request): JsonResponse
    {
        try {
            $payload = json_decode($request->getContent(), true);
            $signature = $request->header('Stripe-Signature');
            $secret = config('stripe.webhook_secret');

            // Verify webhook signature
            $computedSignature = hash_hmac(
                'sha256',
                $request->getContent(),
                $secret
            );

            if (! hash_equals($signature, $computedSignature)) {
                Log::warning('Invalid Stripe webhook signature');

                return response()->json(['success' => false], 401);
            }

            $event = $payload['type'];

            match ($event) {
                'payment_intent.succeeded' => $this->handlePaymentSucceeded($payload),
                'payment_intent.payment_failed' => $this->handlePaymentFailed($payload),
                'charge.refunded' => $this->handleChargeRefunded($payload),
                default => Log::info("Unhandled webhook event: {$event}"),
            };

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Webhook processing failed: '.$e->getMessage());

            return response()->json(['success' => false], 422);
        }
    }

    /**
     * Handle payment_intent.succeeded event
     */
    private function handlePaymentSucceeded(array $data): void
    {
        $intentId = $data['data']['object']['id'];
        $payment = Payment::where('stripe_payment_intent_id', $intentId)->first();

        if ($payment) {
            $payment->update([
                'status' => 'succeeded',
                'paid_at' => now(),
                'payment_method' => $data['data']['object']['payment_method'] ?? null,
            ]);

            $payment->order->update(['payment_status' => 'paid']);

            // Send payment success email
            Mail::to($payment->order->user->email)->send(new PaymentSuccessful($payment));

            Log::info("Payment succeeded via webhook: {$payment->id}");
        }
    }

    /**
     * Handle payment_intent.payment_failed event
     */
    private function handlePaymentFailed(array $data): void
    {
        $intentId = $data['data']['object']['id'];
        $payment = Payment::where('stripe_payment_intent_id', $intentId)->first();

        if ($payment) {
            $payment->update(['status' => 'failed']);
            $payment->order->update(['payment_status' => 'failed']);

            Log::warning("Payment failed via webhook: {$payment->id}");
        }
    }

    /**
     * Handle charge.refunded event
     */
    private function handleChargeRefunded(array $data): void
    {
        $intentId = $data['data']['object']['payment_intent'];
        $payment = Payment::where('stripe_payment_intent_id', $intentId)->first();

        if ($payment) {
            $payment->update(['status' => 'refunded']);

            Log::info("Payment refunded via webhook: {$payment->id}");
        }
    }

    /**
     * Get payment intent details
     */
    private function getPaymentIntent(Payment $payment): mixed
    {
        try {
            return $this->stripeService->getPaymentIntent($payment->stripe_payment_intent_id);
        } catch (\Exception $e) {
            Log::error('Failed to fetch payment intent: '.$e->getMessage());

            return null;
        }
    }
}
