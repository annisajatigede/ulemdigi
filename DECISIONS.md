# Catatan Keputusan Desain & Arsitektur (DECISIONS.md)

Dokumen ini mencatat keputusan desain teknis dan kompromi yang diambil dalam pengembangan SaaS Undangan Digital Berbayar (Kawinan.id):

## 1. Arsitektur Pola Pembayaran (Payment Gateway Adapter Pattern)
- **Keputusan**: Dibuat kontrak `PaymentGatewayInterface` yang diimplementasikan oleh `MidtransPaymentGateway` (mode Snap Sandbox & Production) dan `XenditPaymentGateway` (adapter ready).
- **Alasan**: Persyaratan mewajibkan kemudahan migrasi jika sewaktu-waktu beralih dari Midtrans ke Xendit. Pergantian dapat dilakukan cukup dengan mengganti `PAYMENT_GATEWAY=xendit` di `.env` tanpa mengubah kode aplikasi lainnya.

## 2. Pengujian Sandbox Lokal & Webhook Simulator
- **Keputusan**: Selain mendukung webhook resmi Midtrans dengan verifikasi SHA-512 signature (`POST /payment/midtrans/webhook`), disediakan juga endpoint simulasi dev/sandbox (`POST /payment/simulator/callback`) dan tombol satu-klik "Simulasi Pembayaran Berhasil" di halaman checkout.
- **Alasan**: Dalam pengembangan lokal (tanpa tunneling publik seperti Ngrok), callback webhook dari server Midtrans di internet tidak dapat menjangkau `localhost`. Simulator ini memastikan alur aktivasi instan tanpa konfirmasi manual dapat diuji 100% secara lokal dan otomatis dalam unit/feature test.

## 3. Struktur Kolom `invitations.data` (JSON)
- **Keputusan**: Disimpan dalam bentuk JSON fleksibel dengan struktur baku: `opening`, `party1`, `party2`, `gallery[]`, `story[]`, `gifts[]`, `closing`, `theme_color`, `music_path`, `rsvp_enabled`, `wishes_enabled`.
- **Alasan**: Mencegah overhead pembuatan relasi tabel mikro untuk data konten presentasi yang bervariasi antar kategori acara. Model `Invitation` dilengkapi method helper `getSafeData()` yang menjamin tersedianya nilai fallback default bila ada field yang kosong.

## 4. Adaptasi Dinamis Kategori Khitanan
- **Keputusan**: Pada kategori `khitan`, label form otomatis menyesuaikan menjadi "Nama Anak yang Dikhitan", "Nama Orang Tua / Wali", serta menghilangkan kutipan ayat pernikahan (Ar-Rum 21) dan menggantikannya dengan doa syukuran walimatul khitan.
- **Alasan**: Khitanan adalah acara anak-anak sehingga tidak relevan menggunakan atribut mempelai atau doa pernikahan. Template `khitan_ceria` dirancang khusus dengan warna cerah dan tipografi ramah anak.

## 5. Personalisasi Tamu & Tautan Unik
- **Keputusan**: Setiap tamu dalam daftar tamu memiliki kolom `personal_token` unik 10-12 karakter acak. URL `/u/{slug}/{personal_token}` secara otomatis mengisi nama tamu dan status konfirmasi mereka. Tautan umum `/u/{slug}?to=Nama` juga tetap didukung.
- **Alasan**: Memberikan pengalaman personal bagi tamu dan memudahkan pengirim membagikan tautan langsung lewat WhatsApp dengan template pesan yang sudah diformat otomatis.

## 6. Kebijakan Autoplay Musik di Browser Modern
- **Keputusan**: Musik tidak diputar secara otomatis saat pertama kali halaman dimuat, melainkan dipicu saat tamu menekan tombol "Buka Undangan" pada cover layar pembuka.
- **Alasan**: Kebijakan keamanan browser modern (Chrome, Safari, Firefox, Edge) memblokir pemutaran audio otomatis tanpa adanya interaksi pengguna (user gesture). Pola cover modal "Buka Undangan" adalah praktik standar industri undangan online.

## 7. Kebijakan Kedaluwarsa (Expiration)
- **Keputusan**: Command `invitations:expire` dijalankan setiap hari pada pukul 00:05. Undangan yang kedaluwarsa akan menampilkan halaman status masa aktif berakhir, dan pemilik undangan diberikan tombol perpanjangan paket instan via checkout.
