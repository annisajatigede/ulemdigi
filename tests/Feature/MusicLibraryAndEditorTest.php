<?php

namespace Tests\Feature;

use App\Models\Invitation;
use App\Models\Music;
use App\Models\Plan;
use App\Models\Template;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MusicLibraryAndEditorTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_admin_can_view_music_management_page(): void
    {
        $admin = User::where('role', 'admin')->first();

        $response = $this->actingAs($admin)->get(route('admin.musics.index'));
        $response->assertStatus(200);
        $response->assertSeeText('Pustaka Musik Latar Undangan');
        $response->assertSeeText('Acoustic Romantic Waltz');
    }

    public function test_non_admin_cannot_access_music_management(): void
    {
        $user = User::where('role', 'user')->first();

        $response = $this->actingAs($user)->get(route('admin.musics.index'));
        $response->assertStatus(403);
    }

    public function test_admin_can_add_new_preset_music_with_url(): void
    {
        $admin = User::where('role', 'admin')->first();

        $response = $this->actingAs($admin)->post(route('admin.musics.store'), [
            'title' => 'Canon in D Piano Classical',
            'artist' => 'Pachelbel Strings',
            'category' => 'wedding',
            'file_url' => 'https://assets.mixkit.co/music/preview/canon-in-d.mp3',
            'sort_order' => 5,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status');

        $this->assertDatabaseHas('musics', [
            'title' => 'Canon in D Piano Classical',
            'category' => 'wedding',
            'file_path' => 'https://assets.mixkit.co/music/preview/canon-in-d.mp3',
        ]);
    }

    public function test_admin_can_add_new_preset_music_with_file_upload(): void
    {
        $admin = User::where('role', 'admin')->first();
        $fakeAudio = UploadedFile::fake()->create('romantic_waltz.mp3', 1024, 'audio/mpeg');

        $response = $this->actingAs($admin)->post(route('admin.musics.store'), [
            'title' => 'Uploaded Romantic Waltz',
            'artist' => 'Local Artist',
            'category' => 'wedding',
            'music_file' => $fakeAudio,
            'sort_order' => 6,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status');

        $this->assertDatabaseHas('musics', [
            'title' => 'Uploaded Romantic Waltz',
            'artist' => 'Local Artist',
            'category' => 'wedding',
        ]);

        $music = Music::where('title', 'Uploaded Romantic Waltz')->first();
        $this->assertStringStartsWith('/audio/preset_', $music->file_path);

        // Clean up created test file
        $filePath = public_path(ltrim($music->file_path, '/'));
        if (file_exists($filePath)) {
            @unlink($filePath);
        }
    }

    public function test_admin_can_toggle_music_active_status(): void
    {
        $admin = User::where('role', 'admin')->first();
        $music = Music::first();

        $initialStatus = $music->is_active;

        $response = $this->actingAs($admin)->post(route('admin.musics.toggle', $music));
        $response->assertRedirect();

        $music->refresh();
        $this->assertNotEquals($initialStatus, $music->is_active);
    }

    public function test_admin_can_delete_preset_music(): void
    {
        $admin = User::where('role', 'admin')->first();
        $music = Music::create([
            'title' => 'Music to be deleted',
            'artist' => 'Test Artist',
            'category' => 'general',
            'file_path' => '/audio/test-delete.mp3',
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->delete(route('admin.musics.destroy', $music));
        $response->assertRedirect();

        $this->assertDatabaseMissing('musics', [
            'id' => $music->id,
        ]);
    }

    public function test_user_can_select_admin_preset_music_in_editor(): void
    {
        $user = User::where('role', 'user')->first();
        $template = Template::first();
        $plan = Plan::first();
        $music = Music::first();

        $invitation = Invitation::create([
            'user_id' => $user->id,
            'template_id' => $template->id,
            'plan_id' => $plan->id,
            'slug' => 'test-preset-music-selection',
            'category' => 'wedding',
            'status' => 'draft',
            'expires_at' => Carbon::now()->addDays(30),
            'data' => [],
        ]);

        $response = $this->actingAs($user)->put(route('editor.update', $invitation), [
            'slug' => 'test-preset-music-selection',
            'template_id' => $template->id,
            'theme_color' => '#e11d48',
            'music_source' => 'preset',
            'preset_music_id' => $music->id,
            'opening' => ['title' => 'The Wedding of Budi & Ani'],
            'party1' => ['name' => 'Ani', 'full_name' => 'Ani Wijaya'],
            'party2' => ['name' => 'Budi', 'full_name' => 'Budi Santoso'],
            'events' => [
                [
                    'title' => 'Akad Nikah',
                    'start_at' => now()->addDays(7)->format('Y-m-d\TH:i'),
                    'timezone' => 'WIB',
                    'venue_name' => 'Masjid Raya',
                ],
            ],
        ]);

        $response->assertRedirect();
        $invitation->refresh();

        $safeData = $invitation->getSafeData();
        $this->assertEquals($music->file_path, $safeData['music_path']);
        $this->assertEquals('preset', $safeData['music_source']);
        $this->assertStringContainsString($music->title, $safeData['music_title']);
    }

    public function test_user_can_upload_custom_music_in_editor(): void
    {
        Storage::fake('public');

        $user = User::where('role', 'user')->first();
        $template = Template::first();
        $plan = Plan::first();

        $invitation = Invitation::create([
            'user_id' => $user->id,
            'template_id' => $template->id,
            'plan_id' => $plan->id,
            'slug' => 'test-custom-music-upload',
            'category' => 'wedding',
            'status' => 'draft',
            'expires_at' => Carbon::now()->addDays(30),
            'data' => [],
        ]);

        $fakeAudio = UploadedFile::fake()->create('lagu_favorit_kami.mp3', 2048, 'audio/mpeg');

        $response = $this->actingAs($user)->put(route('editor.update', $invitation), [
            'slug' => 'test-custom-music-upload',
            'template_id' => $template->id,
            'music_source' => 'custom',
            'custom_music' => $fakeAudio,
            'opening' => ['title' => 'The Wedding of Budi & Ani'],
            'party1' => ['name' => 'Ani', 'full_name' => 'Ani Wijaya'],
            'party2' => ['name' => 'Budi', 'full_name' => 'Budi Santoso'],
            'events' => [
                [
                    'title' => 'Akad Nikah',
                    'start_at' => now()->addDays(7)->format('Y-m-d\TH:i'),
                    'timezone' => 'WIB',
                    'venue_name' => 'Masjid Raya',
                ],
            ],
        ]);

        $response->assertRedirect();
        $invitation->refresh();

        $safeData = $invitation->getSafeData();
        $this->assertStringStartsWith('/storage/invitations/' . $invitation->id . '/music/', $safeData['music_path']);
        $this->assertEquals('custom', $safeData['music_source']);
        $this->assertEquals('lagu_favorit_kami.mp3', $safeData['music_title']);
    }

    public function test_user_can_select_no_music_in_editor(): void
    {
        $user = User::where('role', 'user')->first();
        $template = Template::first();
        $plan = Plan::first();

        $invitation = Invitation::create([
            'user_id' => $user->id,
            'template_id' => $template->id,
            'plan_id' => $plan->id,
            'slug' => 'test-no-music-mode',
            'category' => 'wedding',
            'status' => 'draft',
            'expires_at' => Carbon::now()->addDays(30),
            'data' => [
                'music_path' => '/audio/wedding_romantic.mp3',
            ],
        ]);

        $response = $this->actingAs($user)->put(route('editor.update', $invitation), [
            'slug' => 'test-no-music-mode',
            'template_id' => $template->id,
            'music_source' => 'none',
            'opening' => ['title' => 'The Wedding of Budi & Ani'],
            'party1' => ['name' => 'Ani', 'full_name' => 'Ani Wijaya'],
            'party2' => ['name' => 'Budi', 'full_name' => 'Budi Santoso'],
            'events' => [
                [
                    'title' => 'Akad Nikah',
                    'start_at' => now()->addDays(7)->format('Y-m-d\TH:i'),
                    'timezone' => 'WIB',
                    'venue_name' => 'Masjid Raya',
                ],
            ],
        ]);

        $response->assertRedirect();
        $invitation->refresh();

        $safeData = $invitation->getSafeData();
        $this->assertNull($safeData['music_path']);
        $this->assertEquals('none', $safeData['music_source']);
    }

    public function test_public_invitation_renders_music_properly_or_omits_when_none(): void
    {
        $user = User::where('role', 'user')->first();
        $template = Template::first();
        $plan = Plan::first();

        // 1. Invitation with active music
        $invitationWithMusic = Invitation::create([
            'user_id' => $user->id,
            'template_id' => $template->id,
            'plan_id' => $plan->id,
            'slug' => 'undangan-dengan-lagu',
            'category' => 'wedding',
            'status' => 'published',
            'published_at' => now(),
            'expires_at' => Carbon::now()->addDays(30),
            'data' => [
                'music_path' => '/audio/wedding_romantic.mp3',
            ],
        ]);

        $response = $this->get(route('invitation.show', $invitationWithMusic->slug));
        $response->assertStatus(200);
        $response->assertSee('wedding_romantic.mp3', false);
        $response->assertSee('x-ref="audioPlayer"', false);

        // 2. Invitation without music
        $invitationNoMusic = Invitation::create([
            'user_id' => $user->id,
            'template_id' => $template->id,
            'plan_id' => $plan->id,
            'slug' => 'undangan-tanpa-lagu',
            'category' => 'wedding',
            'status' => 'published',
            'published_at' => now(),
            'expires_at' => Carbon::now()->addDays(30),
            'data' => [
                'music_path' => null,
            ],
        ]);

        $responseNo = $this->get(route('invitation.show', $invitationNoMusic->slug));
        $responseNo->assertStatus(200);
        $responseNo->assertDontSee('x-ref="audioPlayer"', false);
    }
}
