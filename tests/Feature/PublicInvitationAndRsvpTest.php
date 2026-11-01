<?php

namespace Tests\Feature;

use App\Models\Guest;
use App\Models\Invitation;
use App\Models\Plan;
use App\Models\Template;
use App\Models\User;
use App\Models\Wish;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicInvitationAndRsvpTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_draft_invitation_shows_not_published_for_general_public(): void
    {
        $user = User::factory()->create();
        $template = Template::first();
        $plan = Plan::first();

        $invitation = Invitation::create([
            'user_id' => $user->id,
            'template_id' => $template->id,
            'plan_id' => $plan->id,
            'slug' => 'test-draft-momen',
            'category' => 'wedding',
            'status' => 'draft',
            'expires_at' => Carbon::now()->addDays(30),
            'data' => [],
        ]);

        $response = $this->get(route('invitation.show', ['slug' => $invitation->slug]));
        $response->assertStatus(200);
        $response->assertSeeText('Undangan Belum Dipublikasikan');
    }

    public function test_published_invitation_renders_successfully(): void
    {
        $user = User::factory()->create();
        $template = Template::first();
        $plan = Plan::first();

        $invitation = Invitation::create([
            'user_id' => $user->id,
            'template_id' => $template->id,
            'plan_id' => $plan->id,
            'slug' => 'test-publik-momen',
            'category' => 'wedding',
            'status' => 'published',
            'published_at' => Carbon::now(),
            'expires_at' => Carbon::now()->addDays(30),
            'data' => [
                'party1' => ['name' => 'Fulanah'],
                'party2' => ['name' => 'Fulan'],
            ],
        ]);

        $response = $this->get(route('invitation.show', ['slug' => $invitation->slug]));
        $response->assertStatus(200);
        $response->assertSeeText('Fulanah');
        $response->assertSeeText('Fulan');
    }

    public function test_guest_can_submit_rsvp_and_congratulatory_wish(): void
    {
        $user = User::factory()->create();
        $template = Template::first();
        $plan = Plan::first();

        $invitation = Invitation::create([
            'user_id' => $user->id,
            'template_id' => $template->id,
            'plan_id' => $plan->id,
            'slug' => 'test-rsvp-momen',
            'category' => 'wedding',
            'status' => 'published',
            'expires_at' => Carbon::now()->addDays(30),
            'data' => [],
        ]);

        $response = $this->postJson(route('invitation.rsvp', ['slug' => $invitation->slug]), [
            'name' => 'Bapak Joko Widodo',
            'phone' => '081299998888',
            'rsvp_status' => 'attended',
            'guest_count' => 2,
            'message' => 'Selamat menempuh hidup baru, semoga sakinah mawaddah warahmah!',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('guests', [
            'invitation_id' => $invitation->id,
            'name' => 'Bapak Joko Widodo',
            'phone' => '081299998888',
            'rsvp_status' => 'attended',
            'guest_count' => 2,
        ]);

        $this->assertDatabaseHas('wishes', [
            'invitation_id' => $invitation->id,
            'name' => 'Bapak Joko Widodo',
            'message' => 'Selamat menempuh hidup baru, semoga sakinah mawaddah warahmah!',
        ]);
    }

    public function test_personalized_guest_link_displays_guest_name(): void
    {
        $user = User::factory()->create();
        $template = Template::first();
        $plan = Plan::first();

        $invitation = Invitation::create([
            'user_id' => $user->id,
            'template_id' => $template->id,
            'plan_id' => $plan->id,
            'slug' => 'test-personal-link',
            'category' => 'wedding',
            'status' => 'published',
            'expires_at' => Carbon::now()->addDays(30),
            'data' => [],
        ]);

        $guest = Guest::create([
            'invitation_id' => $invitation->id,
            'name' => 'Dr. H. Raden Santoso',
            'personal_token' => 'raden12345',
            'rsvp_status' => 'pending',
            'guest_count' => 1,
        ]);

        $response = $this->get(route('invitation.guest', [
            'slug' => $invitation->slug,
            'token' => $guest->personal_token,
        ]));

        $response->assertStatus(200);
        $response->assertSeeText('Dr. H. Raden Santoso');
    }
}
