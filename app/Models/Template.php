<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Template extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'style',
        'thumbnail_path',
        'blade_view',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function invitations(): HasMany
    {
        return $this->hasMany(Invitation::class);
    }

    /**
     * Get accessible thumbnail URL with automatic fallback (.svg or .jpg).
     */
    public function getThumbnailUrlAttribute(): string
    {
        if (! empty($this->thumbnail_path)) {
            $cleanPath = ltrim($this->thumbnail_path, '/');
            if (file_exists(public_path($cleanPath))) {
                return $this->thumbnail_path;
            }

            // Fallback from .jpg to .svg if exists
            $svgCandidate = preg_replace('/\.jpg$/i', '.svg', $this->thumbnail_path);
            if (file_exists(public_path(ltrim($svgCandidate, '/')))) {
                return $svgCandidate;
            }

            // Fallback from .svg to .jpg if exists
            $jpgCandidate = preg_replace('/\.svg$/i', '.jpg', $this->thumbnail_path);
            if (file_exists(public_path(ltrim($jpgCandidate, '/')))) {
                return $jpgCandidate;
            }

            return $this->thumbnail_path;
        }

        $baseName = str_replace('templates.', '', $this->blade_view);
        if (file_exists(public_path('templates/thumbnails/' . $baseName . '.svg'))) {
            return '/templates/thumbnails/' . $baseName . '.svg';
        }

        return '/templates/thumbnails/' . $baseName . '.jpg';
    }
}
