<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Stripe\StripeClient;

class PaymentMethodController extends Controller
{
    private StripeClient $stripe;

    public function __construct()
    {
        $this->stripe = new StripeClient(config('stripe.secret_key'));
    }

    public function index()
    {
        $paymentMethods = Auth::user()->paymentMethods()->latest()->get()->map(function ($method) {
            return [
                'id' => $method->id,
                'stripe_payment_method_id' => $method->stripe_payment_method_id,
                'brand' => $method->stripe_card_brand ?? $method->type,
                'last4' => $method->stripe_card_last4 ?? $method->last4,
                'exp_month' => $method->stripe_card_exp_month,
                'exp_year' => $method->stripe_card_exp_year,
                'expiry' => $method->stripe_card_exp_month && $method->stripe_card_exp_year
                    ? str_pad($method->stripe_card_exp_month, 2, '0', STR_PAD_LEFT).'/'.substr($method->stripe_card_exp_year, -2)
                    : $method->expiry_date,
                'holder' => $method->holder_name,
                'is_default' => $method->is_default,
                'label' => $method->label,
            ];
        });

        return Inertia::render('Profile/PaymentMethods', [
            'paymentMethods' => $paymentMethods,
            'stripePublicKey' => config('stripe.public_key'),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'stripe_payment_method_id' => 'required|string',
            'label' => 'nullable|string',
            'is_default' => 'boolean',
        ]);

        try {
            // Retrieve payment method from Stripe to get card details
            $paymentMethod = $this->stripe->paymentMethods->retrieve(
                $request->stripe_payment_method_id
            );

            if ($paymentMethod->type !== 'card') {
                return redirect()->back()->withErrors(['error' => 'Only card payment methods are supported']);
            }

            // Prepare data
            $data = [
                'stripe_payment_method_id' => $request->stripe_payment_method_id,
                'stripe_card_brand' => $paymentMethod->card->brand,
                'stripe_card_last4' => $paymentMethod->card->last4,
                'stripe_card_exp_month' => $paymentMethod->card->exp_month,
                'stripe_card_exp_year' => $paymentMethod->card->exp_year,
                'label' => $request->label ?? 'Personal',
                'is_default' => $request->boolean('is_default', false),
            ];

            // If setting as default, unset other defaults
            if ($data['is_default']) {
                Auth::user()->paymentMethods()->update(['is_default' => false]);
            }

            Auth::user()->paymentMethods()->create($data);

            return redirect()->back()->with('success', 'Payment method added successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Failed to add payment method: '.$e->getMessage()]);
        }
    }

    public function update(Request $request, PaymentMethod $paymentMethod)
    {
        if ($paymentMethod->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'label' => 'nullable|string',
            'is_default' => 'boolean',
        ]);

        $data = [
            'label' => $request->label ?? $paymentMethod->label,
            'is_default' => $request->boolean('is_default', false),
        ];

        // If setting as default, unset other defaults
        if ($data['is_default']) {
            Auth::user()->paymentMethods()->where('id', '!=', $paymentMethod->id)->update(['is_default' => false]);
        }

        $paymentMethod->update($data);

        return redirect()->back()->with('success', 'Payment method updated successfully.');
    }

    public function destroy(PaymentMethod $paymentMethod)
    {
        if ($paymentMethod->user_id !== Auth::id()) {
            abort(403);
        }

        try {
            // Detach from Stripe if needed
            if ($paymentMethod->stripe_payment_method_id) {
                $this->stripe->paymentMethods->detach(
                    $paymentMethod->stripe_payment_method_id
                );
            }

            $paymentMethod->delete();

            return redirect()->back()->with('success', 'Payment method deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Failed to delete payment method: '.$e->getMessage()]);
        }
    }
}
