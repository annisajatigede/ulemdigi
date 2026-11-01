# Kawinan.id — SaaS Undangan Digital Berbayar (Node.js + Vue 3)

Aplikasi web SaaS "Undangan Digital Berbayar" (pernikahan, khitanan, acara umum) yang telah dimigrasi secara penuh dari Laravel 12 ke arsitektur modern **Node.js (Express + Prisma ORM)** dan **Vue 3 (Vite + Tailwind CSS + Pinia + Vue Router)** dengan database **MySQL** yang identik tanpa perubahan struktur schema maupun data.

---

## 🏗️ Tech Stack

- **Backend**: Node.js v22 + Express 4 + Prisma ORM 6
- **Database**: MySQL (`kawinan` on `127.0.0.1:3306`)
- **Frontend**: Vue 3 (Composition API `<script setup>`) + Vite + Tailwind CSS + Pinia + Vue Router
- **Payment Gateway**: Midtrans Snap Sandbox (dengan simulator callback untuk pengujian lokal)
- **Audio & Media**: HTML5 Audio Player + Pustaka Musik Admin (Wedding, Khitan, General) + Upload Berkas Kustom

---

## 📁 Struktur Direktori

```
kawinan/
├── backend/                  # Server Node.js + Express + Prisma
│   ├── prisma/
│   │   └── schema.prisma     # Hasil introspeksi schema MySQL
│   ├── public/               # Asset statis publik (audio, templates, uploads)
│   ├── src/
│   │   ├── config/           # Prisma client & BigInt JSON serializer
│   │   ├── controllers/      # Controller logika bisnis (Auth, Home, Checkout, Dashboard, Editor, Guests, Admin)
│   │   ├── middlewares/      # Auth JWT, Admin role, Multer uploads
│   │   ├── routes/           # Routing API Express (/api/...)
│   │   ├── services/         # Helper data undangan & Payment adapter Midtrans
│   │   └── server.js         # Entrypoint server Express
│   └── test_suite.js         # Automated end-to-end verification script
│
├── frontend/                 # Client Vue 3 SPA + Tailwind CSS
│   ├── src/
│   │   ├── api/              # Axios client dengan interceptors & CSRF/Token handler
│   │   ├── components/       # Komponen global (Navbar, etc.)
│   │   ├── stores/           # Pinia store otentikasi & session pengguna
│   │   ├── views/
│   │   │   ├── admin/        # Dashboard, Users, Templates, Musics, Plans, Transactions, Settings
│   │   │   ├── auth/         # Login, Register, Forgot Password
│   │   │   ├── checkout/     # Select Plan, Pay (Snap/Simulasi), Success
│   │   │   ├── dashboard/    # User Dashboard (Kelola Undangan & Masa Aktif)
│   │   │   ├── editor/       # Section-based editor (8 tab + live mobile mockup)
│   │   │   ├── guests/       # Kelola Tamu, Check-in, RSVP Analytics, Ekspor CSV
│   │   │   └── public/       # Dispatcher & 4 Desain Template Undangan Publik
│   │   └── router/           # Vue Router dengan auth guards
│   ├── vite.config.js        # Konfigurasi Vite & proxy backend
│   └── tailwind.config.js    # Konfigurasi tema Tailwind CSS
│
└── package.json              # Script orkestrasi proyek
```

---

## 🚀 Menjalankan Aplikasi Secara Lokal

### 1. Menjalankan Backend (Port 3000)
```bash
cd backend
npm install
npm run dev
```
Backend berjalan di: `http://localhost:3000`

### 2. Menjalankan Frontend (Port 5173)
```bash
cd frontend
npm install
npm run dev
```
Frontend berjalan di: `http://localhost:5173`

*(Catatan: Anda juga dapat langsung mengakses `http://localhost:3000` di peramban karena build frontend `frontend/dist` disajikan langsung oleh server Express dengan SPA fallback).*

---

## 🧪 Menjalankan Verifikasi End-to-End
Jalankan skrip pengujian otomatis komprehensif:
```bash
npm run test:e2e
```
Skrip ini memverifikasi:
- Status API & server Vite
- Landing page data & filter kategori
- Otentikasi User (Budi Santoso) & Dashboard Undangan
- Section Editor (8 Tab formulir & sinkronisasi data JSON)
- Kelola Tamu, Analisis Kehadiran RSVP, & Ekspor CSV
- Otentikasi Admin & panel-panel admin (Users, Templates, Musics, Plans, Transactions, Settings)
- Render 4 Desain Template Undangan Publik (`/u/:slug`)
- Pengiriman RSVP & Buku Tamu interaktif
- Alur Checkout & Pembayaran Midtrans Snap / Simulator

---

## 🔑 Akun Uji Coba Tersedia
- **Super Admin**: `admin@kawinan.test` / `password`
- **User Pengguna**: `user@kawinan.test` / `password`
