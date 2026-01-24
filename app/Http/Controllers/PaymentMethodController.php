<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class PaymentMethodController extends Controller
{
    public function index()
    {
        return Inertia::render('Profile/PaymentMethods', [
            'paymentMethods' => [
                [
                    'id' => 1,
                    'type' => 'mastercard',
                    'last4' => '4242',
                    'holder' => 'JANE DOE',
                    'expiry' => '12/26',
                    'is_default' => true,
                    'is_personal' => true,
                ],
                [
                    'id' => 2,
                    'type' => 'visa',
                    'last4' => '8888',
                    'holder' => 'JOHN SMITH',
                    'expiry' => '05/25',
                    'is_business' => true,
                ],
                [
                    'id' => 3,
                    'type' => 'mastercard',
                    'last4' => '9012',
                    'holder' => 'JANE DOE',
                    'expiry' => '09/27',
                    'is_secondary' => true,
                ],
            ]
        ]);
    }
}
