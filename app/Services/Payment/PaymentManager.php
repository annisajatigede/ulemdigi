<?php

namespace App\Services\Payment;

class PaymentManager
{
    public static function create(?string $driver = null): PaymentGatewayInterface
    {
        $driver = $driver ?: config('services.payment.gateway', env('PAYMENT_GATEWAY', 'midtrans'));

        return match (strtolower($driver)) {
            'xendit' => new XenditPaymentGateway(),
            default => new MidtransPaymentGateway(),
        };
    }
}
