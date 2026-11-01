<?php

namespace Tests\Feature;

use App\Models\Invitation;
use App\Models\Plan;
use App\Models\Template;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvitationPublishAndLimitsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_owner_can_access_editor_page_successfully(): void
    {
        $user = User::factory()->create();
        $template = Template::first();
        $plan = Plan::first();

        $invitation = Invitation::create([
            'user_id' => $user->id,
            'template_id' => $template->id,
            'plan_id' => $plan->id,
            'slug' => 'test-editor-render',
            'category' => 'wedding',
            'status' => 'draft',
            'expires_at' => Carbon::now()->addDays(30),
            'data' => [],
        ]);

        $response = $this->actingAs($user)->get(route('editor.edit', $invitation));
        $response->assertStatus(200);
        $response->assertSeeText('Editor Undangan');
        $response->assertSeeText('Live Mobile Preview');
    }

    public function test_owner_can_toggle_publish_status(): void
    {
        $user = User::factory()->create();
        $template = Template::first();
        $plan = Plan::first();

        $invitation = Invitation::create([
            'user_id' => $user->id,
            'template_id' => $template->id,
            'plan_id' => $plan->id,
            'slug' => 'test-publish-toggle',
            'category' => 'wedding',
            'status' => 'draft',
            'expires_at' => Carbon::now()->addDays(30),
            'data' => [],
        ]);

        $response = $this->actingAs($user)->post(route('editor.toggle-publish', $invitation));
        $response->assertRedirect();

        $invitation->refresh();
        $this->assertEquals('published', $invitation->status);
        $this->assertNotNull($invitation->published_at);

        // Toggle back to draft
        $response = $this->actingAs($user)->post(route('editor.toggle-publish', $invitation));
        $invitation->refresh();
        $this->assertEquals('draft', $invitation->status);
    }

    public function test_non_owner_cannot_modify_or_publish_another_users_invitation(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $template = Template::first();
        $plan = Plan::first();

        $invitation = Invitation::create([
            'user_id' => $owner->id,
            'template_id' => $template->id,
            'plan_id' => $plan->id,
            'slug' => 'test-forbidden-access',
            'category' => 'wedding',
            'status' => 'draft',
            'expires_at' => Carbon::now()->addDays(30),
            'data' => [],
        ]);

        $response = $this->actingAs($otherUser)->post(route('editor.toggle-publish', $invitation));
        $response->assertStatus(403);

        $response = $this->actingAs($otherUser)->get(route('editor.edit', $invitation));
        $response->assertStatus(403);
    }
}
