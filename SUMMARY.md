# SUMMARY: Implementasi SaaS Undangan Digital Berbayar (Kawinan.id)

Aplikasi web SaaS "Undangan Digital Berbayar" telah selesai dibangun dari nol hingga MVP fungsional lengkap sesuai spesifikasi arsitektur dan kebutuhan bisnis.

---

## 📌 Ringkasan Implementasi

- **Framework**: Laravel 12 pada PHP 8.2+
- **Frontend**: Blade + Tailwind CSS + Alpine.js (desain mobile-first, animasi cover modal, floating audio player)
- **Database**: MySQL `kawinan` (skema migrasi lengkap dengan foreign keys, JSON casts, dan SQLite in-memory untuk testing)
- **Payment Gateway**: Midtrans Snap Sandbox dengan **Payment Gateway Adapter Pattern** (`PaymentGatewayInterface`, `MidtransPaymentGateway`, `XenditPaymentGateway`) serta webhook verification (SHA-512) dan testing simulator.
- **Template Siap Pakai**: 4 Varian Template Desain Responsif:
  1. `templates.wedding_floral` (Pernikahan Floral Romantis)
  2. `templates.khitan_ceria` (Khitanan Anak Ceria & Penuh Warna)
  3. `templates.general_modern` (Acara Formal/Umum Minimalis Elegan)
  4. `templates.wedding_islami` (Pernikahan Islami Minimalis & Kaligrafi)
- **Scheduler**: Command artisan `php artisan invitations:expire` harian di `routes/console.php`.

---

## 🔑 Kredensial Pengguna (Seed Database)

| Peran (Role) | Email | Password | Keterangan |
|---|---|---|---|
| **Administrator** | `admin@kawinan.test` | `password` | Mengakses rute `/admin`, kelola pengguna, template, paket, transaksi, dan pengaturan situs |
| **Pengguna Demo** | `user@kawinan.test` | `password` | Mengakses dashboard pengguna `/dashboard`, alur checkout, dan editor undangan |

---

## 🌐 Daftar Endpoint & Rute Utama

### 1. Halaman Publik
- `GET /`: Landing page lengkap dengan hero, selector kategori acara, galeri desain template, tabel perbandingan 3 paket, dan fitur unggulan.
- `GET /u/{slug}`: Halaman publik undangan digital interaktif (cover pembuka, floating audio, countdown, maps, profil, galeri, timeline, amplop digital, RSVP, doa).
- `GET /u/{slug}/{personal_token}`: Halaman publik undangan dengan personalisasi tamu spesifik.
- `POST /u/{slug}/rsvp`: Endpoint pengiriman formulir RSVP dan ucapan/doa restu dari tamu.

### 2. Alur Pembayaran & Webhook
- `GET /checkout/select?template_id={id}&plan_code={code}`: Halaman konfirmasi pemilihan template dan paket.
- `POST /checkout/pay`: Inisiasi transaksi dan pembuatan order ID unik.
- `GET /checkout/pay/{transaction_id}`: Halaman pembayaran Midtrans Snap dan tombol simulator pengujian sandbox.
- `POST /payment/midtrans/webhook`: Webhook resmi callback Midtrans (diverifikasi dengan signature SHA-512, dibebaskan dari CSRF).
- `POST /payment/simulator/callback`: Simulator pengujian sandbox lokal tanpa perlu tunneling Ngrok.
- `GET /checkout/success/{transaction_id}`: Halaman notifikasi pembayaran lunas dan tombol langsung menuju editor.

### 3. Dashboard Pengguna & Editor
- `GET /dashboard`: Dashboard pengguna (ringkasan statistik, daftar undangan, status publik/draft/expired, countdown sisa hari, tombol aksi).
- `GET /editor/{invitation}`: Editor undangan berbasis section dengan frame preview smartphone interaktif di samping formulir.
- `PUT /editor/{invitation}`: Simpan perubahan seluruh bagian undangan dan unggahan media.
- `POST /editor/{invitation}/toggle-publish`: Tombol beralih status antara Draft dan Publik.
- `GET /editor/{invitation}/preview`: Pratinjau mobile view dalam iframe.

### 4. Manajemen Tamu & RSVP
- `GET /invitations/{invitation}/guests`: Halaman analitik RSVP, tabel tamu, link personal unik, dan generator pesan WhatsApp.
- `POST /invitations/{invitation}/guests`: Tambah tamu baru.
- `POST /invitations/{invitation}/guests/{guest}/checkin`: Tombol check-in kehadiran tamu di lokasi acara.
- `GET /invitations/{invitation}/guests/export`: Ekspor daftar tamu & kehadiran ke berkas CSV.

### 5. Admin Panel (`/admin`)
- `GET /admin`: Dashboard metrik pendapatan, total transaksi, total undangan, dan statistik pengguna.
- `GET /admin/users`: CRUD pengguna dan pengubahan role (`user` / `admin`).
- `GET /admin/templates`: CRUD template undangan (unggah file `.blade.php` dan gambar thumbnail).
- `GET /admin/plans`: Pengaturan harga paket, durasi hari, batas jumlah foto, batas rangkaian acara, dan fitur JSON.
- `GET /admin/transactions`: Riwayat seluruh transaksi sistem dengan filter status dan rentang tanggal, serta ekspor CSV.
- `GET /admin/settings`: Pengaturan nama situs, logo, favicon, kontak WhatsApp, dan kredensial Midtrans.

---

## 🧪 Langkah Pengujian Manual End-to-End

Untuk memverifikasi fungsionalitas aplikasi di browser secara langsung, ikuti langkah berikut:

### Langkah 1: Jalankan Server Lokal
Pastikan server berjalan:
```bash
php artisan serve
```
Buka browser pada: `http://localhost:8000`

### Langkah 2: Alur Pembelian Paket Baru
1. Di halaman utama (`/`), klik tombol **"Pilih Desain Ini"** pada salah satu template (misal: *Floral Romance* atau *Khitanan Ceria*).
2. Jika belum login, login dengan `user@kawinan.test` / `password` (atau registrasi akun baru).
3. Anda akan diarahkan ke halaman **Review Pilihan Undangan & Paket** (`/checkout/select`).
4. Klik **"Lanjut ke Pembayaran Snap →"**.
5. Di halaman pembayaran (`/checkout/pay/{id}`), klik tombol hijau **"⚡ Simulasi Pembayaran Berhasil (Auto Active)"**.
6. Sistem memproses webhook seketika, status transaksi menjadi **LUNAS**, dan undangan otomatis dibuat berstatus **Draft** dengan masa aktif 90 hari. Anda akan tiba di halaman sukses.

### Langkah 3: Mengisi Formulir Editor & Live Mobile Preview
1. Klik **"Buka Editor Undangan →"**.
2. Anda melihat split-screen: form per tab di sebelah kiri, dan mockup frame smartphone di sebelah kanan.
3. Ubah nama mempelai/anak, tambahkan rangkaian acara, foto galeri, nomor rekening amplop digital, dan teks penutup.
4. Klik **"Simpan Perubahan"**. Frame mobile di sebelah kanan akan memperbarui tampilannya secara langsung.

### Langkah 4: Publikasikan & Cek Halaman Publik
1. Kembali ke Dashboard (`/dashboard`), klik tombol **"🚀 Publikasikan"**.
2. Klik tombol salin link atau buka tautan `/u/{slug}` di tab baru.
3. Klik tombol **"Buka Undangan"** pada cover modal:
   - Cover terangkat ke atas.
   - Musik latar mulai berputar.
   - Countdown timer berjalan menuju hari H acara.
   - Tombol salin nomor rekening berfungsi dan memunculkan notifikasi tersalin.
   - Tombol Google Maps membuka lokasi.

### Langkah 5: Uji Form RSVP & Personalisasi Tamu
1. Isi form RSVP di bagian bawah undangan dengan nama *"Budi Dermawan"*, pilih *"Ya, Saya akan Hadir"*, tuliskan doa restu, lalu klik **"Kirim Konfirmasi"**.
2. Ucapan langsung muncul pada feed doa restu tanpa reload halaman.
3. Buka menu **"Tamu & RSVP"** di dashboard:
   - Tamu terdata secara otomatis pada statistik analitik kehadiran.
   - Tambahkan tamu personal baru, klik **"Salin Link"** untuk mencoba URL personal `/u/{slug}/{personal_token}`.
   - Klik tombol **"Check-in"** untuk mencatat waktu kedatangan tamu di lokasi.
   - Klik **"Ekspor Data (CSV)"** untuk mengunduh rekap spreadsheet kehadiran.

### Langkah 6: Masuk ke Admin Panel
1. Logout, lalu login sebagai `admin@kawinan.test` / `password`.
2. Klik menu **Admin** di navigasi atas (`/admin`).
3. Cek statistik total pendapatan, daftar transaksi, kelola template, sesuaikan harga paket, dan periksa pengaturan situs.

---

## 📊 Hasil Uji Otomatis (PHPUnit)

Jalankan perintah:
```bash
php artisan test
```

Hasil verifikasi:
```text
Tests:    37 passed (108 assertions)
Duration: ~2.6s
Status:   100% GREEN (ALL PASS)
```
Semua modul fitur (Autentikasi Breeze, Alur Checkout & Webhook Auto-Activation, Halaman Publik Undangan & RSVP, Personalisasi Tamu, Editor Publish Toggle, dan Expiration Artisan Command) teruji dengan sempurna.
