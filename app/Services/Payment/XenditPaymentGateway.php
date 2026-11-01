<?php

namespace App\Services\Payment;

use App\Models\Transaction;

class XenditPaymentGateway implements PaymentGatewayInterface
{
    protected string $secretKey;
    protected string $publicKey;

    public function __construct()
    {
        $this->secretKey = config('services.xendit.secret_key', env('XENDIT_SECRET_KEY', ''));
        $this->publicKey = config('services.xendit.public_key', env('XENDIT_PUBLIC_KEY', ''));
    }

    public function createTransaction(Transaction $transaction, array $customerDetails = []): array
    {
        // Adapter implementation for Xendit Invoices API
        $mockInvoiceUrl = 'https://checkout-staging.xendit.co/web/' . $transaction->order_id;
        return [
            'snap_token' => 'XND-' . $transaction->order_id,
            'redirect_url' => $mockInvoiceUrl,
        ];
    }

    public function verifyWebhook(array $payload, string $signatureHeader = ''): bool
    {
        $token = config('services.xendit.webhook_token', env('XENDIT_WEBHOOK_TOKEN', ''));
        if (empty($token)) {
            return true;
        }

        return hash_equals($token, $signatureHeader);
    }

    public function parseWebhookPayload(array $payload): array
    {
        $orderId = $payload['external_id'] ?? ($payload['order_id'] ?? '');
        $xenditStatus = $payload['status'] ?? '';
        $amount = (float) ($payload['amount'] ?? 0);

        $status = 'pending';
        if ($xenditStatus === 'PAID' || $xenditStatus === 'SETTLED') {
            $status = 'paid';
        } elseif ($xenditStatus === 'EXPIRED') {
            $status = 'expired';
        }

        return [
            'order_id' => $orderId,
            'status' => $status,
            'amount' => $amount,
            'raw' => $payload,
        ];
    }
}
