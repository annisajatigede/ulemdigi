<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateInvitationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $invitation = $this->route('invitation');
        return $this->user() && $this->user()->can('update', $invitation);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $invitation = $this->route('invitation');
        $maxPhotos = $invitation->plan->max_photos ?? 15;

        return [
            'slug' => [
                'required',
                'string',
                'max:80',
                'regex:/^[a-z0-9\-]+$/',
                Rule::unique('invitations', 'slug')->ignore($invitation->id),
            ],
            'template_id' => ['required', 'exists:templates,id'],
            'theme_color' => ['nullable', 'string', 'max:20'],
            'opening.title' => ['required', 'string', 'max:150'],
            'opening.quote' => ['nullable', 'string', 'max:1000'],
            'opening.quote_source' => ['nullable', 'string', 'max:150'],
            'opening.greeting' => ['nullable', 'string', 'max:255'],

            'party1.name' => ['required', 'string', 'max:100'],
            'party1.full_name' => ['required', 'string', 'max:150'],
            'party1.role_label' => ['nullable', 'string', 'max:100'],
            'party1.child_order' => ['nullable', 'string', 'max:100'],
            'party1.parents' => ['nullable', 'string', 'max:255'],
            'party1.instagram' => ['nullable', 'string', 'max:100'],
            'party1_photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:3072'],

            'party2.name' => ['nullable', 'string', 'max:100'],
            'party2.full_name' => ['nullable', 'string', 'max:150'],
            'party2.role_label' => ['nullable', 'string', 'max:100'],
            'party2.child_order' => ['nullable', 'string', 'max:100'],
            'party2.parents' => ['nullable', 'string', 'max:255'],
            'party2.instagram' => ['nullable', 'string', 'max:100'],
            'party2_photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:3072'],

            'gallery_photos.*' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:3072'],
            'custom_music' => ['nullable', 'file', 'mimes:mp3,wav,ogg,m4a', 'max:8192'],
            'preset_music' => ['nullable', 'string'],

            'events' => ['required', 'array', 'min:1'],
            'events.*.title' => ['required', 'string', 'max:120'],
            'events.*.start_at' => ['required', 'string'],
            'events.*.end_at' => ['nullable', 'string'],
            'events.*.timezone' => ['required', 'string', 'in:WIB,WITA,WIT'],
            'events.*.venue_name' => ['required', 'string', 'max:150'],
            'events.*.address' => ['nullable', 'string', 'max:500'],
            'events.*.maps_url' => ['nullable', 'string', 'max:500'],

            'story' => ['nullable', 'array'],
            'story.*.year' => ['nullable', 'string', 'max:50'],
            'story.*.title' => ['nullable', 'string', 'max:150'],
            'story.*.description' => ['nullable', 'string', 'max:1000'],

            'gifts' => ['nullable', 'array'],
            'gifts.*.bank_name' => ['nullable', 'string', 'max:100'],
            'gifts.*.account_number' => ['nullable', 'string', 'max:100'],
            'gifts.*.account_holder' => ['nullable', 'string', 'max:150'],

            'closing.text' => ['nullable', 'string', 'max:1000'],
            'closing.signature' => ['nullable', 'string', 'max:150'],

            'rsvp_enabled' => ['nullable', 'boolean'],
            'wishes_enabled' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'slug.required' => 'Tautan / Slug undangan wajib diisi.',
            'slug.unique' => 'Slug sudah digunakan, pilih slug lain.',
            'slug.regex' => 'Slug hanya boleh berisi huruf kecil, angka, dan tanda hubung (-).',
            'opening.title.required' => 'Judul pembuka wajib diisi.',
            'party1.name.required' => 'Nama panggilan pihak 1 wajib diisi.',
            'party1.full_name.required' => 'Nama lengkap pihak 1 wajib diisi.',
            'events.required' => 'Minimal harus ada 1 rangkaian acara.',
            'events.*.title.required' => 'Nama acara wajib diisi.',
            'events.*.start_at.required' => 'Waktu mulai acara wajib diisi.',
            'events.*.venue_name.required' => 'Lokasi / Gedung acara wajib diisi.',
        ];
    }
}
