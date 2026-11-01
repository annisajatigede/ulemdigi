<?php

namespace App\Services;

use App\Models\Invitation;
use App\Models\InvitationEvent;
use App\Models\Plan;
use App\Models\Template;
use App\Models\Transaction;
use App\Models\User;
use App\Services\Payment\PaymentManager;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Str;

class OrderPaymentService
{
    /**
     * Initiate an order transaction and retrieve payment tokens.
     */
    public function initiateOrder(User $user, Template $template, Plan $plan, ?Invitation $existingInvitation = null): array
    {
        $orderId = 'INV-' . date('YmdHis') . '-' . strtoupper(Str::random(4));

        $transaction = Transaction::create([
            'user_id' => $user->id,
            'invitation_id' => $existingInvitation?->id,
            'plan_id' => $plan->id,
            'order_id' => $orderId,
            'amount' => $plan->price,
            'status' => 'pending',
            'gateway' => config('services.payment.gateway', 'midtrans'),
            'raw_payload' => [
                'template_id' => $template->id,
                'plan_id' => $plan->id,
                'category' => $template->category,
                'is_renewal' => (bool) $existingInvitation,
            ],
        ]);

        $gateway = PaymentManager::create($transaction->gateway);
        $paymentData = $gateway->createTransaction($transaction, [
            'first_name' => $user->name,
            'email' => $user->email,
        ]);

        // Save token in payload for client script
        $payload = $transaction->raw_payload ?? [];
        $payload['snap_token'] = $paymentData['snap_token'] ?? null;
        $payload['redirect_url'] = $paymentData['redirect_url'] ?? null;
        $transaction->update(['raw_payload' => $payload]);

        return [
            'transaction' => $transaction,
            'snap_token' => $paymentData['snap_token'] ?? null,
            'redirect_url' => $paymentData['redirect_url'] ?? null,
        ];
    }

    /**
     * Process incoming webhook callback from payment gateway.
     */
    public function processWebhook(array $payload, string $signatureHeader = '', bool $bypassSignatureForTesting = false): array
    {
        $gatewayName = config('services.payment.gateway', 'midtrans');
        $gateway = PaymentManager::create($gatewayName);

        // Verify signature if not explicitly testing in local environment
        if (! $bypassSignatureForTesting) {
            $isValid = $gateway->verifyWebhook($payload, $signatureHeader);
            if (! $isValid) {
                throw new Exception('Invalid webhook signature verification.');
            }
        }

        $parsed = $gateway->parseWebhookPayload($payload);
        $orderId = $parsed['order_id'];
        $newStatus = $parsed['status'];

        $transaction = Transaction::where('order_id', $orderId)->first();
        if (! $transaction) {
            throw new Exception("Transaction with order_id [{$orderId}] not found.");
        }

        // Check if already paid to prevent double activation
        if ($transaction->status === 'paid' && $newStatus === 'paid') {
            return [
                'success' => true,
                'message' => 'Transaction already processed.',
                'order_id' => $orderId,
                'status' => 'paid',
                'invitation_id' => $transaction->invitation_id,
            ];
        }

        $plan = $transaction->plan;
        $invitation = $transaction->invitation;

        if ($newStatus === 'paid') {
            $transaction->status = 'paid';
            $transaction->paid_at = Carbon::now();

            if (! $invitation) {
                // First-time purchase: create invitation in draft status
                $templateId = $transaction->raw_payload['template_id'] ?? 1;
                $template = Template::find($templateId) ?? Template::first();
                $category = $template ? $template->category : 'wedding';

                $slug = $this->generateUniqueSlug($category);
                $duration = $plan ? $plan->duration_days : 30;

                $initialData = $this->buildInitialInvitationData($category, $user = $transaction->user);

                $invitation = Invitation::create([
                    'user_id' => $transaction->user_id,
                    'template_id' => $template ? $template->id : 1,
                    'plan_id' => $transaction->plan_id,
                    'slug' => $slug,
                    'category' => $category,
                    'status' => 'draft',
                    'published_at' => null,
                    'expires_at' => Carbon::now()->addDays($duration),
                    'data' => $initialData,
                ]);

                // Create default event (Akad Nikah / Walimatul Khitan / Acara Utama)
                InvitationEvent::create([
                    'invitation_id' => $invitation->id,
                    'title' => $category === 'khitan' ? 'Walimatul Khitan' : ($category === 'general' ? 'Acara Utama' : 'Akad Nikah'),
                    'start_at' => Carbon::now()->addDays(14)->setTime(9, 0),
                    'end_at' => Carbon::now()->addDays(14)->setTime(11, 0),
                    'timezone' => 'WIB',
                    'venue_name' => 'Kediaman Mempelai / Gedung Serbaguna',
                    'address' => 'Jl. Mawar No. 123, Jakarta Selatan',
                    'maps_url' => 'https://maps.google.com',
                ]);

                $transaction->invitation_id = $invitation->id;
            } else {
                // Renewal / Upgrade
                $duration = $plan ? $plan->duration_days : 30;
                $baseDate = ($invitation->expires_at && $invitation->expires_at->isFuture())
                    ? $invitation->expires_at
                    : Carbon::now();

                $invitation->expires_at = $baseDate->copy()->addDays($duration);
                $invitation->plan_id = $transaction->plan_id;

                if ($invitation->status === 'expired') {
                    $invitation->status = 'draft';
                }
                $invitation->save();
            }
        } elseif (in_array($newStatus, ['failed', 'expired'])) {
            $transaction->status = $newStatus;
        }

        // Store webhook history in raw_payload
        $payloadData = $transaction->raw_payload ?? [];
        $payloadData['webhooks'][] = [
            'received_at' => Carbon::now()->toIso8601String(),
            'payload' => $payload,
        ];
        $transaction->raw_payload = $payloadData;
        $transaction->save();

        return [
            'success' => true,
            'order_id' => $orderId,
            'status' => $transaction->status,
            'invitation_id' => $transaction->invitation_id,
        ];
    }

    /**
     * Generate unique slug for invitation.
     */
    protected function generateUniqueSlug(string $category): string
    {
        $prefix = match ($category) {
            'khitan' => 'khitan',
            'general' => 'acara',
            default => 'momen',
        };

        do {
            $slug = $prefix . '-' . Str::lower(Str::random(7));
        } while (Invitation::where('slug', $slug)->exists());

        return $slug;
    }

    /**
     * Default initial data customized by category.
     */
    protected function buildInitialInvitationData(string $category, ?User $user): array
    {
        if ($category === 'khitan') {
            return [
                'theme_color' => '#0ea5e9', // ceria sky blue
                'music_path' => null,
                'opening' => [
                    'title' => 'Walimatul Khitan',
                    'quote' => 'Dan semoga anak kami menjadi anak yang sholeh, berbakti kepada orang tua, agama, nusa dan bangsa.',
                    'quote_source' => 'Doa Syukuran Khitan',
                    'greeting' => 'Assalamu’alaikum Warahmatullahi Wabarakatuh',
                ],
                'party1' => [
                    'name' => 'Muhammad Rayyan',
                    'full_name' => 'Muhammad Rayyan Al-Ghifari',
                    'role_label' => 'Anak yang Dikhitan',
                    'child_order' => 'Putra tercinta dari',
                    'parents' => ($user ? $user->name : 'Bpk. Ahmad') . ' & Ibu Fatimah',
                    'instagram' => '@rayyan.family',
                    'photo_path' => null,
                ],
                'party2' => [
                    'name' => 'Keluarga Besar',
                    'full_name' => 'Keluarga Bpk. ' . ($user ? $user->name : 'Ahmad'),
                    'role_label' => 'Orang Tua / Wali',
                    'child_order' => '',
                    'parents' => 'Kediaman Keluarga Besar',
                    'instagram' => '',
                    'photo_path' => null,
                ],
                'gallery' => [],
                'story' => [
                    [
                        'year' => 'Momen Syukuran',
                        'title' => 'Langkah Menuju Kedewasaan',
                        'description' => 'Syukuran khitanan putra kami tersayang sebagai wujud ketaatan syariat dan rasa syukur kepada Allah SWT.',
                    ],
                ],
                'gifts' => [
                    [
                        'bank_name' => 'BCA',
                        'account_number' => '1234567890',
                        'account_holder' => ($user ? $user->name : 'Ahmad Santoso'),
                    ],
                ],
                'rsvp_enabled' => true,
                'wishes_enabled' => true,
                'closing' => [
                    'text' => 'Merupakan suatu kehormatan dan kebahagiaan bagi kami sekeluarga apabila Bapak/Ibu/Saudara/i berkenan hadir serta memberikan doa restu.',
                    'signature' => 'Keluarga Besar Bpk. ' . ($user ? $user->name : 'Ahmad'),
                ],
            ];
        }

        if ($category === 'general') {
            return [
                'theme_color' => '#10b981', // emerald green
                'music_path' => null,
                'opening' => [
                    'title' => 'Special Celebration',
                    'quote' => 'Kebahagiaan terasa sempurna ketika dapat berbagi momen istimewa bersama orang-orang tercinta.',
                    'quote_source' => '',
                    'greeting' => 'Salam Sejahtera / Assalamu’alaikum',
                ],
                'party1' => [
                    'name' => 'Nama Tuan Rumah / Acara',
                    'full_name' => 'Penyelenggara Acara',
                    'role_label' => 'Penyelenggara',
                    'child_order' => '',
                    'parents' => '',
                    'instagram' => '@acara.kita',
                    'photo_path' => null,
                ],
                'party2' => [
                    'name' => '',
                    'full_name' => '',
                    'role_label' => '',
                    'child_order' => '',
                    'parents' => '',
                    'instagram' => '',
                    'photo_path' => null,
                ],
                'gallery' => [],
                'story' => [],
                'gifts' => [],
                'rsvp_enabled' => true,
                'wishes_enabled' => true,
                'closing' => [
                    'text' => 'Kehadiran dan doa restu Anda merupakan suatu kehormatan yang tak ternilai bagi kami.',
                    'signature' => 'Panitia / Penyelenggara',
                ],
            ];
        }

        // Default Wedding
        return [
            'theme_color' => '#e11d48', // romantic rose
            'music_path' => null,
            'opening' => [
                'title' => 'The Wedding of',
                'quote' => 'Dan di antara tanda-tanda kebesaran-Nya ialah Dia menciptakan pasangan-pasangan untukmu dari jenismu sendiri, agar kamu cenderung dan merasa tenteram kepadanya...',
                'quote_source' => 'QS. Ar-Rum: 21',
                'greeting' => 'Assalamu’alaikum Warahmatullahi Wabarakatuh',
            ],
            'party1' => [
                'name' => 'Fulanah',
                'full_name' => 'Siti Fulanah, S.Kom',
                'role_label' => 'Mempelai Wanita',
                'child_order' => 'Putri pertama dari',
                'parents' => 'Bpk. Ahmad & Ibu Fatimah',
                'instagram' => '@fulanah',
                'photo_path' => null,
            ],
            'party2' => [
                'name' => 'Fulan',
                'full_name' => 'Muhammad Fulan, S.T',
                'role_label' => 'Mempelai Pria',
                'child_order' => 'Putra kedua dari',
                'parents' => 'Bpk. Hasan & Ibu Aminah',
                'instagram' => '@fulan',
                'photo_path' => null,
            ],
            'gallery' => [],
            'story' => [
                [
                    'year' => '2021',
                    'title' => 'Awal Bertemu',
                    'description' => 'Pertemuan pertama kami yang tak disengaja di sebuah seminar nasional.',
                ],
                [
                    'year' => '2023',
                    'title' => 'Komitmen Bersama',
                    'description' => 'Dengan restu kedua orang tua, kami mantap melangkah ke jenjang yang lebih serius.',
                ],
            ],
            'gifts' => [
                [
                    'bank_name' => 'BCA',
                    'account_number' => '8880123456',
                    'account_holder' => 'Muhammad Fulan',
                ],
                [
                    'bank_name' => 'Bank Mandiri',
                    'account_number' => '1370012345678',
                    'account_holder' => 'Siti Fulanah',
                ],
            ],
            'rsvp_enabled' => true,
            'wishes_enabled' => true,
            'closing' => [
                'text' => 'Merupakan suatu kehormatan dan kebahagiaan bagi kami apabila Bapak/Ibu/Saudara/i berkenan hadir untuk memberikan doa restu.',
                'signature' => 'Fulan & Fulanah Beserta Keluarga Besar',
            ],
        ];
    }
}
