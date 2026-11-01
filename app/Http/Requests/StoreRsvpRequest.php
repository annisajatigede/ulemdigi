<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRsvpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:30'],
            'rsvp_status' => ['required', 'in:attended,not_attended,maybe'],
            'guest_count' => ['nullable', 'integer', 'min:1', 'max:10'],
            'message' => ['nullable', 'string', 'max:1000'],
            'personal_token' => ['nullable', 'string', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama tamu wajib diisi.',
            'rsvp_status.required' => 'Konfirmasi kehadiran wajib dipilih.',
            'rsvp_status.in' => 'Pilihan status kehadiran tidak valid.',
        ];
    }
}
