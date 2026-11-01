# PLAN: SaaS Undangan Digital Berbayar (Kawinan)

## 1. Arsitektur & Spesifikasi Sistem
- **Framework Backend**: Laravel 12 (PHP 8.2+)
- **Frontend**: Blade + Tailwind CSS + Alpine.js (desain mobile-first, animasi scroll, audio player)
- **Database**: MySQL (default `kawinan`) + SQLite (dev/testing in-memory)
- **Payment Gateway**: Midtrans Snap Sandbox (Payment Gateway Adapter Pattern: `PaymentGatewayInterface`, `MidtransPaymentGateway`, `XenditPaymentGateway`)
- **Auth**: Laravel Breeze (Email/Password, Register, Login, Reset Password, Role: `user`, `admin`)
- **Storage**: Local public disk (dev), S3-compatible ready (production)

## 2. Struktur Data & Relasi
- `users`: id, name, email, password, role (`user`, `admin`), timestamps
- `templates`: id, name, category (`wedding`, `khitan`, `general`), style, thumbnail_path, blade_view, is_active, timestamps
- `plans`: id, code (`basic`, `standard`, `premium`), name, price, duration_days, max_photos, max_events, features (JSON), timestamps
- `invitations`: id, user_id, template_id, plan_id, slug (unique), category, status (`draft`, `published`, `expired`), published_at, expires_at, data (JSON), timestamps
- `invitation_events`: id, invitation_id, title, start_at, end_at, timezone, venue_name, address, maps_url, timestamps
- `guests`: id, invitation_id, name, phone, personal_token (unique), rsvp_status (`attended`, `not_attended`, `maybe`, `pending`), guest_count, checked_in_at, timestamps
- `wishes`: id, invitation_id, guest_id (nullable), name, message, timestamps
- `transactions`: id, user_id, invitation_id, plan_id, order_id (unique), amount, status (`pending`, `paid`, `failed`, `expired`), gateway, paid_at, raw_payload (JSON), timestamps
- `settings`: id, key, value, timestamps

## 3. Tahapan Eksekusi (Checklist)

### Fase 1: Inisialisasi & Scaffolding Proyek [COMPLETED]
- [x] 1.1 Inisialisasi project Laravel 12 di `c:\laragon\www\kawinan`
- [x] 1.2 Konfigurasi database MySQL (`kawinan`) dan fallback SQLite untuk unit/feature test
- [x] 1.3 Install Laravel Breeze (Blade + Alpine.js + Tailwind CSS) & build assets
- [x] 1.4 Inisialisasi Git repository dan commit awal

### Fase 2: Database, Migrations, Models, & Seeders [COMPLETED]
- [x] 2.1 Migrations untuk users (role), templates, plans, invitations, invitation_events, guests, wishes, transactions, settings
- [x] 2.2 Models dengan casts (JSON), fillable, relasi Eloquent lengkap (User, Template, Plan, Invitation, InvitationEvent, Guest, Wish, Transaction, Setting)
- [x] 2.3 Seeders:
  - 1 User Admin (`admin@kawinan.test`) & 1 User Demo (`user@kawinan.test`)
  - 3 Plans (`basic` Rp 49.000, `standard` Rp 99.000, `premium` Rp 149.000)
  - 3 Templates (`wedding_floral`, `khitan_ceria`, `general_modern`)
  - Default Site Settings

### Fase 3: Payment Gateway & Alur Pembelian [COMPLETED]
- [x] 3.1 Kontrak & Adapter Pembayaran: `PaymentGatewayInterface`, `MidtransPaymentGateway`, `XenditPaymentGateway`
- [x] 3.2 Service `OrderPaymentService` untuk inisiasi checkout & pemrosesan webhook callback
- [x] 3.3 Flow Checkout: Pilih Template -> Pilih Paket -> Generate Snap Token -> Halaman Bayar
- [x] 3.4 Webhook Handler: verifikasi SHA-512 signature Midtrans, update transaksi `paid`, aktifkan undangan (`draft`, `expires_at = now + duration_days`) secara otomatis tanpa verifikasi manual
- [x] 3.5 Simulator/Mock Webhook untuk kemudahan pengujian sandbox/lokal

### Fase 4: Editor Undangan Berbasis Section & Live Mobile Preview [COMPLETED]
- [x] 4.1 Layout Editor responsif (split-screen: form section di kiri/desktop, iframe live mobile preview di kanan)
- [x] 4.2 Sections: Opening, Pihak 1, Pihak 2, Acara, Cerita, Galeri Foto, Amplop Digital, RSVP, Penutup
- [x] 4.3 Logika Dinamis Khitanan: Label otomatis berganti (Anak & Orang Tua), doa khitan, tema ceria
- [x] 4.4 Validasi Form Request & Policy Otorisasi User

### Fase 5: Halaman Publik Undangan (`/u/{slug}`) & Personalisasi Tamu [COMPLETED]
- [x] 5.1 Route publik `/u/{slug}` dan `/u/{slug}/{personal_token}` serta query `?to=Nama+Tamu`
- [x] 5.2 Render 3 varian template Blade:
  - `templates.wedding_floral` (elegan floral romantis)
  - `templates.khitan_ceria` (ceria anak-anak, balon/awan)
  - `templates.general_modern` (minimalis modern elegan)
- [x] 5.3 Komponen Interaktif: Cover modal, floating audio player, countdown timer, salin rekening, maps, RSVP form, wishes feed, tombol WhatsApp

### Fase 6: Manajemen Tamu, Undangan Personal, & RSVP Analytics [COMPLETED]
- [x] 6.1 CRUD Tamu & Generate Link Personal (`personal_token`)
- [x] 6.2 Generator Teks Undangan WhatsApp personal otomatis
- [x] 6.3 Dashboard Analitik RSVP: ringkasan hadir/tidak/ragu/belum respon + progress bar + ekspor daftar tamu CSV
- [x] 6.4 Check-in kehadiran tamu di lokasi acara

### Fase 7: User Dashboard & Admin Panel [COMPLETED]
- [x] 7.1 User Dashboard: Daftar undangan, status, sisa hari aktif, quick actions
- [x] 7.2 Admin Panel:
  - Manajemen Pengguna (CRUD & role)
  - Manajemen Template (CRUD, upload blade view & thumbnail)
  - Manajemen Paket/Plan (Harga, durasi, kuota fitur)
  - Daftar Transaksi (Filter tanggal/status, Ekspor CSV)
  - Pengaturan Web (Nama SaaS, logo, favicon, kontak, SEO meta)

### Fase 8: Background Task, Scheduler, & Artisan Command [COMPLETED]
- [x] 8.1 Command `invitations:expire`: mengecek dan mengubah status undangan menjadi `expired` jika melewati `expires_at`
- [x] 8.2 Registrasi scheduler harian di `routes/console.php`

### Fase 9: Pengujian Otomatis & Verifikasi End-to-End [COMPLETED]
- [x] 9.1 Feature Test: Alur Checkout -> Webhook Midtrans -> Aktivasi Undangan
- [x] 9.2 Feature Test: Form RSVP Publik & Kirim Ucapan
- [x] 9.3 Feature Test: Publish / Unpublish Undangan & Batasan Paket
- [x] 9.4 Feature Test: Command `invitations:expire`
- [x] 9.5 Verifikasi 35 tests passed (100 assertions), 100% Green

### Fase 10: Dokumentasi, Docker & Finalisasi [COMPLETED]
- [x] 10.1 `.env.example` lengkap dengan Midtrans, DB, dan app config
- [x] 10.2 `docker-compose.yml` (App + Nginx + MySQL) dan `docker/nginx/default.conf`
- [x] 10.3 `DECISIONS.md` catatan arsitektur & kompromi desain
- [x] 10.4 `README.md` panduan instalasi lokal, panduan deployment, & kredensial demo
- [x] 10.5 `SUMMARY.md` ringkasan hasil implementasi dan checklist uji
