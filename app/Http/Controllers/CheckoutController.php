<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use App\Models\Plan;
use App\Models\Template;
use App\Models\Transaction;
use App\Services\OrderPaymentService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(
        protected OrderPaymentService $orderPaymentService
    ) {}

    /**
     * Show Checkout Selection & Confirmation page.
     */
    public function select(Request $request): View
    {
        $templateId = $request->query('template_id');
        $planCode = $request->query('plan_code');
        $invitationId = $request->query('invitation_id');

        $template = Template::find($templateId) ?? Template::where('is_active', true)->first();
        $plan = Plan::where('code', $planCode)->first() ?? Plan::where('code', 'standard')->first() ?? Plan::first();

        $existingInvitation = $invitationId ? Invitation::find($invitationId) : null;
        if ($existingInvitation && $existingInvitation->user_id !== $request->user()?->id) {
            $existingInvitation = null;
        }

        $allPlans = Plan::orderBy('price')->get();
        $allTemplates = Template::where('is_active', true)->get();

        return view('checkout.select', compact('template', 'plan', 'allPlans', 'allTemplates', 'existingInvitation'));
    }

    /**
     * Process checkout and initiate payment transaction.
     */
    public function pay(Request $request): RedirectResponse
    {
        $request->validate([
            'template_id' => ['required', 'exists:templates,id'],
            'plan_id' => ['required', 'exists:plans,id'],
            'invitation_id' => ['nullable', 'exists:invitations,id'],
        ]);

        $user = $request->user();
        $template = Template::findOrFail($request->template_id);
        $plan = Plan::findOrFail($request->plan_id);

        $existingInvitation = null;
        if ($request->invitation_id) {
            $existingInvitation = Invitation::where('id', $request->invitation_id)
                ->where('user_id', $user->id)
                ->first();
        }

        $result = $this->orderPaymentService->initiateOrder($user, $template, $plan, $existingInvitation);
        $transaction = $result['transaction'];

        return redirect()->route('checkout.pay.page', ['transaction' => $transaction->id]);
    }

    /**
     * Render Payment Gateway Snap View.
     */
    public function payPage(Transaction $transaction): View|RedirectResponse
    {
        // Must be transaction owner or admin
        if (auth()->id() !== $transaction->user_id && ! auth()->user()?->isAdmin()) {
            abort(403);
        }

        if ($transaction->isPaid()) {
            return redirect()->route('checkout.success', ['transaction' => $transaction->id]);
        }

        $snapToken = $transaction->raw_payload['snap_token'] ?? null;
        $clientKey = config('services.midtrans.client_key', env('MIDTRANS_CLIENT_KEY', ''));
        $isProduction = config('services.midtrans.is_production', false);

        return view('checkout.pay', compact('transaction', 'snapToken', 'clientKey', 'isProduction'));
    }

    /**
     * Handle Midtrans Snap Webhook Notification.
     */
    public function midtransWebhook(Request $request): JsonResponse
    {
        $payload = $request->all();
        $signatureHeader = $request->header('X-Signature', '');

        Log::info('Received Midtrans Webhook Callback', ['payload' => $payload]);

        try {
            $result = $this->orderPaymentService->processWebhook($payload, $signatureHeader);
            return response()->json([
                'status' => 'success',
                'message' => 'Webhook processed successfully.',
                'data' => $result,
            ]);
        } catch (Exception $e) {
            Log::error('Midtrans Webhook Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Sandbox Payment Simulator Callback (for local testing without live Midtrans webhook tunnel).
     */
    public function simulatorCallback(Request $request): JsonResponse
    {
        $request->validate([
            'order_id' => ['required', 'string'],
            'status' => ['nullable', 'in:paid,failed,expired'],
        ]);

        $orderId = $request->input('order_id');
        $status = $request->input('status', 'paid');

        $transaction = Transaction::where('order_id', $orderId)->firstOrFail();

        $midtransStatus = match ($status) {
            'paid' => 'settlement',
            'expired' => 'expire',
            default => 'deny',
        };

        $dummyPayload = [
            'order_id' => $orderId,
            'transaction_status' => $midtransStatus,
            'status_code' => '200',
            'gross_amount' => (string) round($transaction->amount),
            'fraud_status' => 'accept',
            'signature_key' => 'mock-signature-for-simulator',
        ];

        try {
            $result = $this->orderPaymentService->processWebhook($dummyPayload, '', bypassSignatureForTesting: true);
            return response()->json([
                'status' => 'success',
                'message' => 'Simulasi pembayaran berhasil diproses.',
                'result' => $result,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Display Payment Success page.
     */
    public function success(Transaction $transaction): View
    {
        if (auth()->id() !== $transaction->user_id && ! auth()->user()?->isAdmin()) {
            abort(403);
        }

        $invitation = $transaction->invitation;

        return view('checkout.success', compact('transaction', 'invitation'));
    }
}
