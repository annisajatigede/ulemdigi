<?php

namespace Tests\Feature;

use App\Models\Plan;
use App\Models\Template;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutAndActivationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_user_can_access_checkout_selection_page(): void
    {
        $user = User::factory()->create();
        $template = Template::first();
        $plan = Plan::where('code', 'standard')->first();

        $response = $this->actingAs($user)->get(route('checkout.select', [
            'template_id' => $template->id,
            'plan_code' => $plan->code,
        ]));

        $response->assertStatus(200);
        $response->assertSeeText($template->name);
        $response->assertSeeText($plan->name);
    }

    public function test_user_can_initiate_payment_and_creates_pending_transaction(): void
    {
        $user = User::factory()->create();
        $template = Template::first();
        $plan = Plan::where('code', 'standard')->first();

        $response = $this->actingAs($user)->post(route('checkout.pay'), [
            'template_id' => $template->id,
            'plan_id' => $plan->id,
        ]);

        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'status' => 'pending',
            'amount' => $plan->price,
        ]);

        $transaction = Transaction::where('user_id', $user->id)->first();
        $response->assertRedirect(route('checkout.pay.page', ['transaction' => $transaction->id]));
    }

    public function test_payment_webhook_automatically_activates_invitation_without_manual_confirmation(): void
    {
        $user = User::factory()->create();
        $template = Template::where('category', 'wedding')->first();
        $plan = Plan::where('code', 'standard')->first();

        $transaction = Transaction::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'order_id' => 'INV-TEST-WEBHOOK-1234',
            'amount' => $plan->price,
            'status' => 'pending',
            'gateway' => 'midtrans',
            'raw_payload' => [
                'template_id' => $template->id,
                'plan_id' => $plan->id,
                'category' => $template->category,
            ],
        ]);

        // Trigger simulator webhook callback
        $response = $this->postJson(route('payment.simulator.callback'), [
            'order_id' => $transaction->order_id,
            'status' => 'paid',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);

        // Check transaction updated to paid
        $transaction->refresh();
        $this->assertEquals('paid', $transaction->status);
        $this->assertNotNull($transaction->paid_at);
        $this->assertNotNull($transaction->invitation_id);

        // Check invitation automatically created with draft status and correct expiry
        $invitation = $transaction->invitation;
        $this->assertNotNull($invitation);
        $this->assertEquals($user->id, $invitation->user_id);
        $this->assertEquals('draft', $invitation->status);
        $this->assertEquals('wedding', $invitation->category);
        $this->assertNotNull($invitation->expires_at);

        // Expiry should be approx now + 90 days (standard plan duration)
        $expectedDays = $plan->duration_days;
        $this->assertGreaterThanOrEqual($expectedDays - 1, Carbon::now()->diffInDays($invitation->expires_at));
    }
}
