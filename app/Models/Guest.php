<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Guest extends Model
{
    use HasFactory;

    protected $fillable = [
        'invitation_id',
        'name',
        'phone',
        'personal_token',
        'rsvp_status',
        'guest_count',
        'checked_in_at',
    ];

    protected function casts(): array
    {
        return [
            'guest_count' => 'integer',
            'checked_in_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function ($guest) {
            if (empty($guest->personal_token)) {
                $guest->personal_token = Str::random(12);
            }
        });
    }

    public function invitation(): BelongsTo
    {
        return $this->belongsTo(Invitation::class);
    }

    public function wishes(): HasMany
    {
        return $this->hasMany(Wish::class);
    }

    public function getPersonalUrlAttribute(): string
    {
        return route('invitation.guest', [
            'slug' => $this->invitation->slug,
            'token' => $this->personal_token,
        ]);
    }
}
