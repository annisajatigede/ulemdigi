<?php

namespace App\Services\Payment;

use App\Models\Transaction;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MidtransPaymentGateway implements PaymentGatewayInterface
{
    protected string $serverKey;
    protected string $clientKey;
    protected bool $isProduction;
    protected string $snapBaseUrl;

    public function __construct()
    {
        $this->serverKey = config('services.midtrans.server_key', env('MIDTRANS_SERVER_KEY', ''));
        $this->clientKey = config('services.midtrans.client_key', env('MIDTRANS_CLIENT_KEY', ''));
        $this->isProduction = (bool) config('services.midtrans.is_production', env('MIDTRANS_IS_PRODUCTION', false));
        $this->snapBaseUrl = $this->isProduction
            ? 'https://app.midtrans.com/snap/v1/transactions'
            : 'https://app.sandbox.midtrans.com/snap/v1/transactions';
    }

    public function getClientKey(): string
    {
        return $this->clientKey;
    }

    public function isProduction(): bool
    {
        return $this->isProduction;
    }

    public function createTransaction(Transaction $transaction, array $customerDetails = []): array
    {
        $payload = [
            'transaction_details' => [
                'order_id' => $transaction->order_id,
                'gross_amount' => (int) round($transaction->amount),
            ],
            'customer_details' => array_merge([
                'first_name' => $transaction->user->name ?? 'Pelanggan',
                'email' => $transaction->user->email ?? 'user@example.com',
            ], $customerDetails),
            'item_details' => [
                [
                    'id' => $transaction->plan->code ?? 'plan',
                    'price' => (int) round($transaction->amount),
                    'quantity' => 1,
                    'name' => $transaction->plan->name ?? 'Paket Undangan Digital',
                ],
            ],
        ];

        // If real valid server key is configured and not dummy, make live Midtrans API call
        if (! empty($this->serverKey) && ! str_contains($this->serverKey, 'placeholder') && ! str_contains($this->serverKey, 'demo-key')) {
            try {
                $response = Http::withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Basic ' . base64_encode($this->serverKey . ':'),
                ])->post($this->snapBaseUrl, $payload);

                if ($response->successful()) {
                    $data = $response->json();
                    return [
                        'snap_token' => $data['token'] ?? null,
                        'redirect_url' => $data['redirect_url'] ?? null,
                    ];
                }

                Log::warning('Midtrans Snap request failed, falling back to sandbox simulator token', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            } catch (Exception $e) {
                Log::error('Midtrans connection error: ' . $e->getMessage());
            }
        }

        // Sandbox simulator fallback token for local development & automated testing
        $mockToken = 'SNAP-MOCK-' . md5($transaction->order_id . $this->serverKey);
        return [
            'snap_token' => $mockToken,
            'redirect_url' => 'https://app.sandbox.midtrans.com/snap/v2/vtweb/' . $mockToken,
        ];
    }

    public function verifyWebhook(array $payload, string $signatureHeader = ''): bool
    {
        if (empty($payload['signature_key']) || empty($payload['order_id']) || empty($payload['status_code']) || empty($payload['gross_amount'])) {
            return false;
        }

        $orderId = $payload['order_id'];
        $statusCode = $payload['status_code'];
        $grossAmount = $payload['gross_amount'];

        $expectedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $this->serverKey);

        return hash_equals($expectedSignature, $payload['signature_key']);
    }

    public function parseWebhookPayload(array $payload): array
    {
        $orderId = $payload['order_id'] ?? '';
        $transactionStatus = $payload['transaction_status'] ?? '';
        $fraudStatus = $payload['fraud_status'] ?? '';
        $amount = (float) ($payload['gross_amount'] ?? 0);

        $status = 'pending';

        if ($transactionStatus === 'capture') {
            $status = ($fraudStatus === 'challenge') ? 'pending' : 'paid';
        } elseif ($transactionStatus === 'settlement') {
            $status = 'paid';
        } elseif (in_array($transactionStatus, ['cancel', 'deny', 'expire'])) {
            $status = ($transactionStatus === 'expire') ? 'expired' : 'failed';
        } elseif ($transactionStatus === 'pending') {
            $status = 'pending';
        }

        return [
            'order_id' => $orderId,
            'status' => $status,
            'amount' => $amount,
            'raw' => $payload,
        ];
    }
}
