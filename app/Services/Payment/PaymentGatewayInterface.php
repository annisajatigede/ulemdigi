<?php

namespace App\Services\Payment;

use App\Models\Transaction;

interface PaymentGatewayInterface
{
    /**
     * Create checkout transaction token / redirect URL.
     *
     * @return array{snap_token?: string, redirect_url?: string, token?: string}
     */
    public function createTransaction(Transaction $transaction, array $customerDetails = []): array;

    /**
     * Verify authenticity of incoming webhook notification.
     */
    public function verifyWebhook(array $payload, string $signatureHeader = ''): bool;

    /**
     * Parse webhook payload into normalized status.
     *
     * @return array{order_id: string, status: string, amount: float, raw: array}
     */
    public function parseWebhookPayload(array $payload): array;
}
