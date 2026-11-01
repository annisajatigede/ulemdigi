# PLAN: SaaS Undangan Digital Berbayar (Kawinan)

## 1. Arsitektur & Spesifikasi Sistem
- **Framework Backend**: Laravel 11 (PHP 8.2+)
- **Frontend**: Blade + Tailwind CSS + Alpine.js (desain mobile-first, animasi scroll, audio player)
- **Database**: MySQL (default) + SQLite (dev/testing fallback)
- **Payment Gateway**: Midtrans Snap Sandbox (Payment Gateway Adapter Pattern: `PaymentGatewayInterface`, `MidtransPaymentGateway`, `XenditPaymentGateway` stub)
- **Auth**: Laravel Breeze (Email/Password, Register, Login, Reset Password, Role: user, admin)
- **Storage**: Local public disk (dev), S3-compatible ready (production)

## 2. Struktur Data & Relasi
- `users`: id, name, email, password, role (`user`, `admin`), timestamps
- `templates`: id, name, category (`wedding`, `khitan`, `general`), style, thumbnail_path, blade_view, is_active, timestamps
- `plans`: id, code (`basic`, `standard`, `premium`), name, price, duration_days, max_photos, max_events, features (JSON), timestamps
- `invitations`: id, user_id, template_id, plan_id, slug (unique), category, status (`draft`, `published`, `expired`), published_at, expires_at, data (JSON: opening, party1, party2, gallery[], story[], gifts[], closing, theme_color, music_path), timestamps
- `invitation_events`: id, invitation_id, title, start_at, end_at, timezone, venue_name, address, maps_url, timestamps
- `guests`: id, invitation_id, name, phone, personal_token (unique), rsvp_status (`attended`, `not_attended`, `maybe`, `pending`), guest_count, checked_in_at, timestamps
- `wishes`: id, invitation_id, guest_id (nullable), name, message, timestamps
- `transactions`: id, user_id, invitation_id, plan_id, order_id (unique), amount, status (`pending`, `paid`, `failed`, `expired`), gateway, paid_at, raw_payload (JSON), timestamps
- `settings`: id, key, value, timestamps

## 3. Tahapan Eksekusi (Checklist)

### Fase 1: Inisialisasi & Scaffolding Proyek [CURRENT]
- [ ] 1.1 Inisialisasi project Laravel 11 di `c:\laragon\www\kawinan`
- [ ] 1.2 Konfigurasi database MySQL (`kawinan`) dan fallback SQLite untuk unit/feature test
- [ ] 1.3 Install Laravel Breeze (Blade + Alpine.js + Tailwind CSS) & build assets
- [ ] 1.4 Inisialisasi Git repository dan commit awal

### Fase 2: Database, Migrations, Models, & Seeders
- [ ] 2.1 Migrations untuk users (role), templates, plans, invitations, invitation_events, guests, wishes, transactions, settings
- [ ] 2.2 Models dengan casts (JSON), fillable, relasi Eloquent lengkap (User, Template, Plan, Invitation, InvitationEvent, Guest, Wish, Transaction, Setting)
- [ ] 2.3 Seeders:
  - 1 User Admin (`admin@kawinan.test`) & 1 User Demo (`user@kawinan.test`)
  - 3 Plans (`basic` Rp 49.000, `standard` Rp 99.000, `premium` Rp 149.000)
  - 3 Templates (`wedding_floral`, `khitan_ceria`, `general_modern`)
  - Default Site Settings

### Fase 3: Payment Gateway & Alur Pembelian
- [ ] 3.1 Kontrak & Adapter Pembayaran: `PaymentGatewayInterface`, `MidtransPaymentGateway`, `XenditPaymentGateway`
- [ ] 3.2 Service `OrderPaymentService` untuk inisiasi checkout & pemrosesan webhook callback
- [ ] 3.3 Flow Checkout: Pilih Template -> Pilih Paket -> Generate Snap Token -> Halaman Bayar
- [ ] 3.4 Webhook Handler: verifikasi SHA-512 signature Midtrans, update transaksi `paid`, aktifkan undangan (`draft`, `expires_at = now + duration_days`) secara otomatis tanpa verifikasi manual
- [ ] 3.5 Simulator/Mock Webhook untuk kemudahan pengujian sandbox/lokal

### Fase 4: Editor Undangan Berbasis Section & Live Mobile Preview
- [ ] 4.1 Layout Editor responsif (split-screen: form section di kiri/desktop, iframe live mobile preview di kanan)
- [ ] 4.2 Sections:
  - Opening (Salam, Judul, Kutipan/Ayat)
  - Pihak 1 (Mempelai Wanita / Anak Khitan)
  - Pihak 2 (Mempelai Pria / Orang Tua)
  - Informasi Acara (Multi-acara: Akad, Resepsi, Syukuran dengan waktu, zona waktu, lokasi, google maps)
  - Cerita Cinta / Kisah Perjalanan (Timeline milestones)
  - Galeri Foto (Multi-upload dengan validasi kuota paket)
  - Amplop Digital / Hadiah (Multi-rekening & e-wallet)
  - Pengaturan RSVP & Ucapan
  - Musik Latar (Pilihan preset & custom upload audio)
  - Tema Warna & Penutup
- [ ] 4.3 Logika Dinamis Khitanan: Label otomatis berganti (Anak & Orang Tua), ayat nikah dihilangkan, tema ceria
- [ ] 4.4 Validasi Form Request & Policy Otorisasi User

### Fase 5: Halaman Publik Undangan (`/u/{slug}`) & Personalisasi Tamu
- [ ] 5.1 Route publik `/u/{slug}` dan `/u/{slug}/{personal_token}` serta query `?to=Nama+Tamu`
- [ ] 5.2 Render 3 varian template Blade:
  - `templates.wedding_floral` (elegan floral romantis)
  - `templates.khitan_ceria` (ceria anak-anak, balon/awan)
  - `templates.general_modern` (minimalis modern elegan)
- [ ] 5.3 Komponen Interaktif:
  - Cover Screen / Buka Undangan (membuka kunci audio autoplay sesuai kebijakan browser)
  - Floating Music Player (play, pause, rotasi ikon disk)
  - Countdown Timer ke hari H
  - Tombol Salin Rekening Bank (1-klik copy feedback toast)
  - Google Maps link & embed
  - Form RSVP & Kirim Ucapan
  - Feed ucapan terbaru
  - Tombol Bagikan ke WhatsApp

### Fase 6: Manajemen Tamu, Undangan Personal, & RSVP Analytics
- [ ] 6.1 CRUD Tamu & Generate Link Personal (`personal_token`)
- [ ] 6.2 Generator Teks Undangan WhatsApp personal otomatis (dengan variabel nama tamu & link personal)
- [ ] 6.3 Dashboard Analitik RSVP: ringkasan hadir/tidak/ragu/belum respon + pie chart/progress bar + ekspor daftar tamu
- [ ] 6.4 QR Code Check-in / Tombol Check-in kehadiran tamu di lokasi acara

### Fase 7: User Dashboard & Admin Panel
- [ ] 7.1 User Dashboard: Daftar undangan, status, sisa hari aktif, quick actions (edit, preview, publish/unpublish, salin link)
- [ ] 7.2 Admin Panel:
  - Manajemen Pengguna (CRUD & role)
  - Manajemen Template (CRUD, upload blade view & thumbnail)
  - Manajemen Paket/Plan (Harga, durasi, kuota fitur)
  - Daftar Transaksi (Filter tanggal/status, Ekspor CSV)
  - Pengaturan Web (Nama SaaS, logo, favicon, kontak, SEO meta)

### Fase 8: Background Task, Scheduler, & Artisan Command
- [ ] 8.1 Command `invitations:expire`: mengecek dan mengubah status undangan menjadi `expired` jika melewati `expires_at`
- [ ] 8.2 Registrasi scheduler harian di `routes/console.php`

### Fase 9: Pengujian Otomatis & Verifikasi End-to-End
- [ ] 9.1 Feature Test: Alur Checkout -> Webhook Midtrans -> Aktivasi Undangan
- [ ] 9.2 Feature Test: Form RSVP Publik & Kirim Ucapan
- [ ] 9.3 Feature Test: Publish / Unpublish Undangan & Batasan Paket
- [ ] 9.4 Feature Test: Command `invitations:expire`
- [ ] 9.5 Verifikasi E2E manual & verifikasi responsivitas mobile (Lighthouse ready)

### Fase 10: Dokumentasi, Docker & Finalisasi
- [ ] 10.1 `.env.example` lengkap dengan Midtrans, DB, dan app config
- [ ] 10.2 `docker-compose.yml` (Nginx/App + MySQL + phpMyAdmin/Adminer opsional)
- [ ] 10.3 `DECISIONS.md` catatan arsitektur & kompromi desain
- [ ] 10.4 `README.md` panduan instalasi lokal, panduan deployment, & kredensial demo
- [ ] 10.5 `SUMMARY.md` ringkasan hasil implementasi dan checklist uji
