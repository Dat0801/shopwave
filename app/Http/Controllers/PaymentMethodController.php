<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class PaymentMethodController extends Controller
{
    public function index()
    {
        $paymentMethods = Auth::user()->paymentMethods()->latest()->get()->map(function ($method) {
            return [
                'id' => $method->id,
                'type' => $method->type,
                'last4' => $method->last4,
                'holder' => $method->holder_name,
                'expiry' => $method->expiry_date,
                'is_default' => $method->is_default,
                'label' => $method->label,
                'is_personal' => $method->label === 'Personal',
                'is_business' => $method->label === 'Business',
                'is_secondary' => $method->label === 'Secondary',
            ];
        });

        return Inertia::render('Profile/PaymentMethods', [
            'paymentMethods' => $paymentMethods,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|string',
            'last4' => 'required|string|size:4',
            'holder_name' => 'required|string',
            'expiry_date' => 'required|string', // MM/YY
            'is_default' => 'boolean',
            'label' => 'nullable|string',
        ]);

        if ($validated['is_default'] ?? false) {
            Auth::user()->paymentMethods()->update(['is_default' => false]);
        }

        Auth::user()->paymentMethods()->create($validated);

        return redirect()->back()->with('success', 'Payment method added successfully.');
    }

    public function update(Request $request, PaymentMethod $paymentMethod)
    {
        if ($paymentMethod->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'type' => 'required|string',
            'last4' => 'required|string|size:4',
            'holder_name' => 'required|string',
            'expiry_date' => 'required|string',
            'is_default' => 'boolean',
            'label' => 'nullable|string',
        ]);

        if ($validated['is_default'] ?? false) {
            Auth::user()->paymentMethods()->where('id', '!=', $paymentMethod->id)->update(['is_default' => false]);
        }

        $paymentMethod->update($validated);

        return redirect()->back()->with('success', 'Payment method updated successfully.');
    }

    public function destroy(PaymentMethod $paymentMethod)
    {
        if ($paymentMethod->user_id !== Auth::id()) {
            abort(403);
        }

        $paymentMethod->delete();

        return redirect()->back()->with('success', 'Payment method deleted successfully.');
    }
}
