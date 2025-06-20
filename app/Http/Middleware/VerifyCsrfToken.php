<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        'webhook/*',
        'subscription/webhook/*',
        'subscription/webhook/razorpay',
        'subscription/webhook/stripe',
        'subscription/webhook/paystack',
        'subscription/webhook/flutterwave',
        'payment/status',
        'payment/cancel'
    ];
}
