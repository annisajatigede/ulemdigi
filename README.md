# Kawinan.id — SaaS Undangan Digital Berbayar

Platform web SaaS modern untuk pembuatan dan penerbitan undangan digital berbayar (Pernikahan, Khitanan, dan Acara Umum). Model bisnis sekali bayar per undangan dengan aktivasi otomatis melalui Midtrans Snap.

---

## 🚀 Fitur Utama

1. **Landing Page Interaktif**:
   - Filter kategori (Semua, Pernikahan, Khitanan, Acara Umum).
   - Galeri template responsif mobile-first.
   - Tabel perbandingan paket harga (Basic, Standard, Premium).
2. **Autentikasi Pengguna**:
   - Register, Login, Lupa Password, Reset Password, Profil Pengguna (Laravel Breeze).
   - Role pengguna: `user` dan `admin`.
3. **Alur Pembelian Otomatis (Zero Manual Confirmation)**:
   - Pilih template → Pilih paket → Checkout Midtrans Snap.
   - Webhook callback memverifikasi signature SHA-512 dan otomatis mengaktifkan undangan (`draft`, `expires_at = now + duration_days`).
   - Dilengkapi Payment Gateway Adapter Pattern (Midtrans Snap & Xendit adapter ready) serta simulator pengujian lokal sandbox.
4. **Editor Undangan Berbasis Section & Live Mobile Preview**:
   - Preview frame smartphone interaktif di samping form editor.
   - Form per bagian: Dasar & Tema, Pembuka, Tokoh Utama / Mempelai, Rangkaian Acara (multi-acara), Galeri Foto (multi-upload), Cerita/Timeline, Amplop Digital (multi rekening/e-wallet), RSVP & Ucapan, Penutup.
   - Logika dinamis untuk kategori **Khitanan**: label otomatis berubah (Nama Anak, Orang Tua/Wali), kutipan pernikahan disembunyikan dan diganti doa khitan.
   - Validasi upload foto & audio sesuai kuota paket.
5. **Halaman Publik Undangan (`/u/{slug}`)**:
   - Render sesuai template Blade pilihan pengguna.
   - Cover "Buka Undangan" untuk membuka izin pemutaran audio browser.
   - Floating audio player (play/pause berputar).
   - Countdown timer otomatis ke hari H.
   - Salin nomor rekening 1-klik dengan umpan balik toast.
   - Integrasi petunjuk arah Google Maps.
   - Form RSVP kehadiran dan buku tamu / ucapan real-time.
   - Tombol share WhatsApp dengan teks otomatis.
6. **Manajemen Tamu & RSVP Personal**:
   - Dukungan tautan personal tamu unik `/u/{slug}/{personal_token}` dan query `?to=Nama`.
   - Generator teks undangan WhatsApp otomatis.
   - Fitur check-in tamu di lokasi acara.
   - Dashboard analitik RSVP (Hadir, Tidak Hadir, Ragu-ragu, Belum Respon, Total Ucapan) dan ekspor data ke file CSV.
7. **Admin Panel**:
   - Kelola data pengguna & peran.
   - Manajemen template (unggah berkas Blade view baru dan gambar thumbnail).
   - Manajemen paket layanan (harga, durasi hari, batas foto/acara, fitur JSON).
   - Riwayat transaksi pembayaran dengan filter status & tanggal, serta ekspor CSV.
   - Pengaturan situs (nama, logo, favicon, kontak WhatsApp, kunci API Midtrans).
8. **Scheduler Kedaluwarsa**:
   - Artisan command `php artisan invitations:expire` harian untuk otomatis mengubah status menjadi `expired` jika melewati `expires_at`.

---

## 🛠️ Stack Teknologi

- **Backend**: Laravel 12 (PHP 8.2+)
- **Frontend**: Blade + Tailwind CSS + Alpine.js
- **Database**: MySQL 8.0 (dengan opsi SQLite in-memory untuk automated testing)
- **Payment**: Midtrans Snap Sandbox (Payment Gateway Adapter Pattern)
- **Testing**: PHPUnit / Feature Tests (100% lulus)

---

## 💻 Panduan Instalasi Lokal (Laragon / Windows)

### 1. Prasyarat
- PHP >= 8.2 (dengan ekstensi `pdo_mysql`, `pdo_sqlite`, `curl`, `mbstring`, `fileinfo`, `gd`, `zip`, `openssl`)
- Composer >= 2.x
- Node.js >= 18.x & npm
- MySQL Server (sudah termasuk di Laragon)

### 2. Langkah Setup
```bash
# Masuk ke direktori proyek
cd c:\laragon\www\kawinan

# Salin file konfigurasi lingkungan
cp .env.example .env

# Generate Application Key
php artisan key:generate

# Jalankan migrasi dan seeder awal
php artisan migrate:fresh --seed

# Hubungkan penyimpanan public untuk upload
php artisan storage:link

# Build aset frontend (Tailwind & Alpine)
npm install
npm run build

# Jalankan server pengembangan
php artisan serve
```

Aplikasi dapat diakses di browser melalui: `http://localhost:8000`

---

## 🐳 Panduan Menjalankan dengan Docker

```bash
# Build dan jalankan kontainer
docker-compose up -d --build

# Jalankan migrasi dan seeder di dalam kontainer
docker-compose exec app php artisan migrate:fresh --seed
docker-compose exec app php artisan storage:link
```
Buka browser pada: `http://localhost`

---

## 🔑 Kredensial Default (Hasil Seed)

| Role | Email | Password | Hak Akses |
|---|---|---|---|
| **Administrator** | `admin@kawinan.test` | `password` | Akses penuh dashboard admin (`/admin`), kelola template, paket, transaksi, user, pengaturan. |
| **User Biasa (Demo)** | `user@kawinan.test` | `password` | Membeli paket, mengelola undangan, editor, tamu & RSVP. |

---

## 🧪 Menjalankan Automated Tests

Seluruh fitur inti (pembelian, webhook otomatis, public RSVP, personal token, publish toggle, dan expiry scheduler) telah diuji dengan test otomatis:

```bash
php artisan test
```

Hasil: **35 tests passed (100 assertions), 100% Green**.
