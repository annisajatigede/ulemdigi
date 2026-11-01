<?php

namespace Tests\Feature;

use App\Models\Invitation;
use App\Models\Plan;
use App\Models\Template;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvitationExpirationCommandTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_invitations_expire_command_updates_past_invitations_to_expired(): void
    {
        $user = User::factory()->create();
        $template = Template::first();
        $plan = Plan::first();

        // 1. Past invitation (expired)
        $pastInvitation = Invitation::create([
            'user_id' => $user->id,
            'template_id' => $template->id,
            'plan_id' => $plan->id,
            'slug' => 'past-invitation',
            'category' => 'wedding',
            'status' => 'published',
            'published_at' => Carbon::now()->subDays(60),
            'expires_at' => Carbon::now()->subDays(1), // already expired yesterday
            'data' => [],
        ]);

        // 2. Active future invitation
        $activeInvitation = Invitation::create([
            'user_id' => $user->id,
            'template_id' => $template->id,
            'plan_id' => $plan->id,
            'slug' => 'active-future-invitation',
            'category' => 'wedding',
            'status' => 'published',
            'published_at' => Carbon::now()->subDays(5),
            'expires_at' => Carbon::now()->addDays(25), // still active
            'data' => [],
        ]);

        // Run artisan command
        $this->artisan('invitations:expire')
            ->expectsOutputToContain("Berhasil memperbarui 1 undangan yang telah melewati masa aktif menjadi 'expired'.")
            ->assertExitCode(0);

        $pastInvitation->refresh();
        $activeInvitation->refresh();

        $this->assertEquals('expired', $pastInvitation->status);
        $this->assertEquals('published', $activeInvitation->status);
    }
}
