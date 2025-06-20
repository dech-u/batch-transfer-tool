<?php

namespace App\Services\Payment;

interface PaymentInterface {
    public function createPaymentIntent($amount, $customMetaData);

    public function createAndFormatPaymentIntent($amount, $customMetaData): array;

    public function retrievePaymentIntent($paymentId): array;

    public function minimumAmountValidation($currency, $amount);

    public function formatPaymentIntent($id, $amount, $currency, $status, $metadata, $paymentIntent): array;

}
