<?php

namespace Database\Seeders;

use App\Models\Music;
use App\Models\Plan;
use App\Models\Setting;
use App\Models\Template;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Admin & Demo User
        User::updateOrCreate(
            ['email' => 'admin@kawinan.test'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'user@kawinan.test'],
            [
                'name' => 'Budi Santoso',
                'password' => Hash::make('password'),
                'role' => 'user',
                'email_verified_at' => now(),
            ]
        );

        // 2. Plans (Basic, Standard, Premium)
        $plans = [
            [
                'code' => 'basic',
                'name' => 'Paket Basic',
                'price' => 49000,
                'duration_days' => 30,
                'max_photos' => 5,
                'max_events' => 1,
                'features' => [
                    'Aktif 30 Hari',
                    'Maksimal 1 Acara (Akad/Syukuran)',
                    'Galeri hingga 5 Foto',
                    'RSVP & Buku Tamu / Ucapan',
                    'Amplop Digital (Bank / E-Wallet)',
                    'Navigasi Peta Google Maps',
                    'Animasi Pembuka & Musik Latar Standar',
                ],
            ],
            [
                'code' => 'standard',
                'name' => 'Paket Standard',
                'price' => 99000,
                'duration_days' => 90,
                'max_photos' => 15,
                'max_events' => 3,
                'features' => [
                    'Aktif 90 Hari (3 Bulan)',
                    'Hingga 3 Rangkaian Acara (Akad, Resepsi, dll)',
                    'Galeri hingga 15 Foto HD',
                    'Cerita Kisah Cinta / Love Story Timeline',
                    'Personalisasi Nama Tamu (?to=Nama)',
                    'Tautan Tamu Personal (/u/slug/token)',
                    'Amplop Digital Multi-Rekening',
                    'RSVP & Ucapan Real-time',
                    'Pilihan Musik Latar Beragam',
                    'Share Otomatis ke WhatsApp',
                ],
            ],
            [
                'code' => 'premium',
                'name' => 'Paket Premium',
                'price' => 149000,
                'duration_days' => 365,
                'max_photos' => 50,
                'max_events' => 5,
                'features' => [
                    'Aktif 365 Hari (1 Tahun Penuh)',
                    'Hingga 5 Rangkaian Acara Lengkap',
                    'Galeri hingga 50 Foto HD',
                    'Cerita Perjalanan / Timeline Lengkap',
                    'Personalisasi Tamu Tanpa Batas',
                    'Link Tamu Personal & QR Code Check-in',
                    'Upload Musik Sendiri (Custom MP3)',
                    'Kelola Tamu & Ekspor Data RSVP CSV',
                    'Kustomisasi Warna Tema & Tipografi',
                    'Prioritas Dukungan VIP & Bebas Watermark',
                ],
            ],
        ];

        foreach ($plans as $planData) {
            Plan::updateOrCreate(
                ['code' => $planData['code']],
                $planData
            );
        }

        // 3. Templates (Wedding Floral, Khitan Ceria, General Modern, Wedding Islami)
        $templates = [
            [
                'name' => 'Floral Romance',
                'category' => 'wedding',
                'style' => 'romantic',
                'thumbnail_path' => '/templates/thumbnails/wedding_floral.svg',
                'blade_view' => 'templates.wedding_floral',
                'is_active' => true,
            ],
            [
                'name' => 'Khitanan Ceria',
                'category' => 'khitan',
                'style' => 'playful',
                'thumbnail_path' => '/templates/thumbnails/khitan_ceria.svg',
                'blade_view' => 'templates.khitan_ceria',
                'is_active' => true,
            ],
            [
                'name' => 'Modern Minimalist',
                'category' => 'general',
                'style' => 'modern',
                'thumbnail_path' => '/templates/thumbnails/general_modern.svg',
                'blade_view' => 'templates.general_modern',
                'is_active' => true,
            ],
            [
                'name' => 'Islami Minimalis',
                'category' => 'wedding',
                'style' => 'islami',
                'thumbnail_path' => '/templates/thumbnails/wedding_islami.svg',
                'blade_view' => 'templates.wedding_islami',
                'is_active' => true,
            ],
        ];

        foreach ($templates as $tmpl) {
            Template::updateOrCreate(
                ['blade_view' => $tmpl['blade_view']],
                $tmpl
            );
        }

        // 4. Preset Musics (Provided by Admin)
        $musics = [
            [
                'title' => 'Acoustic Romantic Waltz',
                'artist' => 'Kawinan Music Studio',
                'category' => 'wedding',
                'file_path' => '/audio/wedding_romantic.mp3',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'title' => 'Sholawat & Barakah Syahdu',
                'artist' => 'Kawinan Islamic Melodies',
                'category' => 'wedding',
                'file_path' => '/audio/wedding_islami.mp3',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'title' => 'Cheerful Joyful Kids Playground',
                'artist' => 'Kawinan Kids Production',
                'category' => 'khitan',
                'file_path' => '/audio/khitan_ceria.mp3',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'title' => 'Serene Piano Harmony',
                'artist' => 'Kawinan Classical Ensemble',
                'category' => 'general',
                'file_path' => '/audio/general_piano.mp3',
                'is_active' => true,
                'sort_order' => 4,
            ],
        ];

        foreach ($musics as $music) {
            Music::updateOrCreate(
                ['file_path' => $music['file_path']],
                $music
            );
        }

        // 4. Default Settings
        $defaultSettings = [
            'site_name' => 'Kawinan.id',
            'site_tagline' => 'Platform Undangan Digital Berbayar Modern & Elegan',
            'site_description' => 'Solusi undangan online terbaik untuk pernikahan, khitanan, dan acara spesial. Desain estetik, cepat, mudah digunakan, dan siap disebarkan.',
            'contact_whatsapp' => '6281234567890',
            'contact_email' => 'support@kawinan.id',
            'midtrans_mode' => 'sandbox',
        ];

        foreach ($defaultSettings as $key => $val) {
            Setting::updateOrCreate(['key' => $key], ['value' => $val]);
        }
    }
}
