<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invitation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'template_id',
        'plan_id',
        'slug',
        'category',
        'status',
        'published_at',
        'expires_at',
        'data',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'expires_at' => 'datetime',
            'data' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(Template::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(InvitationEvent::class)->orderBy('start_at');
    }

    public function guests(): HasMany
    {
        return $this->hasMany(Guest::class);
    }

    public function wishes(): HasMany
    {
        return $this->hasMany(Wish::class)->latest();
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function isPublished(): bool
    {
        return $this->status === 'published' && ! $this->isExpired();
    }

    public function isExpired(): bool
    {
        if ($this->status === 'expired') {
            return true;
        }

        if ($this->expires_at && Carbon::now()->greaterThan($this->expires_at)) {
            return true;
        }

        return false;
    }

    public function daysRemaining(): int
    {
        if (! $this->expires_at) {
            return 0;
        }

        $now = Carbon::now();
        if ($now->greaterThan($this->expires_at)) {
            return 0;
        }

        return (int) ceil($now->diffInHours($this->expires_at) / 24);
    }

    /**
     * Get default structured data if empty.
     */
    public function getSafeData(): array
    {
        $defaults = [
            'theme_color' => '#8b5cf6',
            'music_path' => null,
            'music_title' => null,
            'music_source' => 'preset',
            'opening' => [
                'title' => 'The Wedding of',
                'quote' => 'Dan di antara tanda-tanda kebesaran-Nya ialah Dia menciptakan pasangan-pasangan untukmu dari jenismu sendiri...',
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
            'story' => [],
            'gifts' => [],
            'rsvp_enabled' => true,
            'wishes_enabled' => true,
            'closing' => [
                'text' => 'Merupakan suatu kehormatan dan kebahagiaan bagi kami apabila Bapak/Ibu/Saudara/i berkenan hadir untuk memberikan doa restu.',
                'signature' => 'Keluarga Besar Kami',
            ],
        ];

        $data = array_replace_recursive($defaults, $this->data ?? []);

        if (! empty($data['music_path'])) {
            $path = $data['music_path'];
            $data['music_url'] = (str_starts_with($path, 'http://') || str_starts_with($path, 'https://'))
                ? $path
                : asset(ltrim($path, '/'));
        } else {
            $data['music_url'] = null;
        }

        return $data;
    }
}
