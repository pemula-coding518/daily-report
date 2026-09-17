# Daily Report — Sistem Pelaporan Pekerjaan Harian Kantor

Aplikasi web internal kantor berbasis **Laravel 11** yang dirancang untuk mempermudah karyawan dalam mengisi laporan pekerjaan harian secara cepat tanpa perlu login, serta menyediakan dashboard monitoring kepatuhan dan manajemen data terpusat bagi tim **Admin & HRD**.

---

## 📌 Daftar Isi

- [Fitur Utama](#-fitur-utama)
  - [1. Portal Karyawan (Tanpa Login)](#1-portal-karyawan-tanpa-login)
  - [2. Dashboard Monitoring Admin & HRD](#2-dashboard-monitoring-admin--hrd)
  - [3. Manajemen & Moderasi Laporan](#3-manajemen--moderasi-laporan)
  - [4. Manajemen Karyawan & Absensi Khusus](#4-manajemen-karyawan--absensi-khusus)
  - [5. Sistem Autentikasi & Hak Akses (Role-Based)](#5-sistem-autentikasi--hak-akses-role-based)
- [Teknologi yang Digunakan](#-teknologi-yang-digunakan)
- [Struktur Database Utama](#-struktur-database-utama)
- [Panduan Instalasi & Setup](#-panduan-instalasi--setup)
- [Akun Default (Development)](#-akun-default-development)
- [Menjalankan Pengujian (Testing)](#-menjalankan-pengujian-testing)
- [Format & Standar Kode](#-format--standar-kode)
- [Keamanan & Praktik Terbaik](#-keamanan--praktik-terbaik)

---

## ✨ Fitur Utama

### 1. Portal Karyawan (Tanpa Login)
- **Akses Langsung**: Karyawan membuka portal publik (`/`) tanpa perlu proses autentikasi/login yang merepotkan.
- **Pemilihan Tanggal Fleksibel**: Tanggal laporan dapat disesuaikan dengan kebutuhan operasional pekerjaan.
- **Dropdown Reaktif**: Pilihan divisi memuat daftar nama karyawan aktif secara dinamis via AJAX.
- **Formulir Spesifik 6 Divisi**:
  1. **Teknisi**: Multi-select jenis pekerjaan, opsi dinamis *"Yang lain"*, status pengerjaan (*Selesai/Progres/Pending*), kendala, dan rencana besok.
  2. **Admin Sales**: Pekerjaan hari ini, rencana besok, metrik angka (*customer dihubungi, quotation, closing*), dan kendala.
  3. **Admin Project**: Nama proyek, persentase progres (0–100%), pekerjaan hari ini, kendala, rencana besok, dan dokumen yang diproses (*dengan aturan mutually exclusive untuk opsi "Tidak ada"*).
  4. **Admin Procurement**: Jumlah PO dibuat, pekerjaan hari ini, vendor yang dihubungi, barang diterima/dikirim, kendala, dan rencana besok.
  5. **System Informasi**: Pekerjaan hari ini, status pengerjaan, kendala, dan rencana besok.
  6. **Finance**: Pekerjaan hari ini, invoice dibuat, pembayaran/pencairan dana, rekap kas/bank harian, kendala, dan rencana besok.
- **Integritas Data & Snapshot**:
  - Validasi ketat `UNIQUE(employee_id, report_date)` mencegah pengiriman ganda pada tanggal yang sama.
  - Snapshot nama karyawan, nama divisi, kode divisi, dan versi formulir disimpan bersama laporan agar riwayat historis tetap valid meskipun data master berubah di masa depan.

### 2. Dashboard Monitoring Admin & HRD
- **Filter Tanggal & Divisi**: Mengubah tanggal atau divisi secara instan memperbarui seluruh indikator performa.
- **Kartu Metrik**: Total Karyawan Aktif, Karyawan Wajib Lapor, Jumlah Laporan Masuk, dan Tingkat Kepatuhan (%).
- **Tabel Karyawan Belum Melapor**:
  - Menampilkan daftar nama karyawan yang belum mengirimkan laporan pada tanggal terpilih.
  - **Pengecualian Otomatis**: Karyawan berstatus Cuti, Sakit, Izin, Libur, atau karyawan yang baru bergabung setelah tanggal terpilih secara cerdas dikecualikan dari perhitungan wajib lapor.
  - Aksi cepat untuk menandai status Cuti/Sakit langsung dari dashboard.
- **Progress Bar Kepatuhan Per Divisi**: Visualisasi rasio pengisian laporan per unit kerja.

### 3. Manajemen & Moderasi Laporan
- **Filter Komprehensif**: Pencarian berdasarkan rentang tanggal (`start_date` – `end_date`), divisi, status laporan (*Aktif/Dibatalkan*), dan pencarian nama.
- **Tampilan Detail Laporan**: Format rapi untuk data payload JSON spesifik tiap divisi.
- **Koreksi & Edit**: Staf Admin/HRD dapat membantu mengedit konten laporan jika ada kesalahan input dari karyawan.
- **Pembatalan & Pemulihan**: Status laporan dapat diubah menjadi `cancelled` atau `active` (soft-status) agar integritas pengiriman tetap terjaga.
- **Ekspor CSV / Excel**: Fitur unduh laporan berformat CSV dengan *UTF-8 Byte Order Mark (BOM)* agar langsung terbaca rapi di Microsoft Excel tanpa masalah karakter khusus.

### 4. Manajemen Karyawan & Absensi Khusus
- **Master Karyawan**: Penambahan, pengubahan data, dan penugasan divisi.
- **Toggle Status Aktif**: Menonaktifkan karyawan yang keluar/cuti panjang tanpa menghapus data historis pelaporan.
- **Pencatatan Kehadiran Khusus**: Input izin tidak masuk kerja (*Cuti, Sakit, Izin, Libur Kantor*) dengan keterangan pendukung.

### 5. Sistem Autentikasi & Hak Akses (Role-Based)
- **Custom Authentication Guard**: Menggunakan guard `admin_hrd` berbasis tabel internal `admin_hrd_users`.
- **Tanpa Registrasi Publik**: Hanya akun yang didaftarkan oleh Administrator yang dapat mengakses dashboard.
- **Login Khusus Kantor**: Form login di `/admin/login` dengan label *"Email Kantor"* & *"Password"*, opsi remember-me, dan pesan kegagalan aman.
- **2 Tingkatan Peran (Role)**:
  - **Administrator (`admin`)**: Akses penuh ke seluruh dashboard, laporan, karyawan, absensi, ekspor, serta **Kelola Akun Staf Admin/HRD**.
  - **HRD Team (`hrd`)**: Akses operasional ke dashboard, moderasi laporan, manajemen karyawan, absensi, dan ekspor data (dibatasi dari menu kelola akun).
- **Keamanan Akun**:
  - Middleware aktifasi akun: Akun non-aktif ditolak saat login, dan sesi langsung diputus jika akun dinonaktifkan di tengah sesi.
  - Perlindungan *self-deactivation*: Pengguna tidak dapat menonaktifkan akun miliknya sendiri.

---

## 🛠 Teknologi yang Digunakan

- **Backend**: [PHP 8.3+](https://www.php.net/) & [Laravel 11](https://laravel.com/)
- **Frontend**: [Blade Templates](https://laravel.com/docs/blade), [Tailwind CSS 3](https://tailwindcss.com/), dan [Alpine.js](https://alpinejs.dev/)
- **Database**: SQLite (Development) / MySQL / PostgreSQL (Production)
- **Asset Bundler**: [Vite](https://vitejs.dev/)
- **Testing**: [PHPUnit](https://phpunit.de/)
- **Code Style**: [Laravel Pint](https://laravel.com/docs/pint)

---

## 🗄 Struktur Database Utama

```
├── divisions                # Master data divisi kantor
├── employees                # Master data karyawan (relasi ke divisions)
├── daily_reports            # Header laporan, snapshot histori, dan form_data (JSON)
├── employee_attendances     # Catatan kehadiran/pengecualian (Cuti, Sakit, Izin, Libur)
└── admin_hrd_users          # Akun pengguna terotentikasi (role: admin / hrd)
```

---

## 🚀 Panduan Instalasi & Setup

### Prasyarat
- PHP >= 8.3 (dengan ekstensi `pdo`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `curl`, `bcmath`)
- [Composer](https://getcomposer.org/)
- [Node.js](https://nodejs.org/) & NPM

### Langkah-langkah

1. **Clone Repository**:
   ```bash
   git clone <URL_REPOSITORY_ANDA> daily-report
   cd daily-report
   ```

2. **Install Dependensi PHP**:
   ```bash
   composer install
   ```

3. **Install Dependensi Frontend**:
   ```bash
   npm install
   ```

4. **Konfigurasi Environment**:
   Salin file `.env.example` menjadi `.env`:
   ```bash
   cp .env.example .env
   ```
   *Sesuaikan konfigurasi database dan variabel aplikasi di dalam file `.env`.*

5. **Generate Application Key**:
   ```bash
   php artisan key:generate
   ```

6. **Jalankan Migrasi & Seeder Database**:
   ```bash
   php artisan migrate --seed
   ```

7. **Build Aset Frontend**:
   ```bash
   npm run build
   ```
   *(Atau gunakan `npm run dev` untuk mode pengembangan dengan hot module replacement).*

8. **Jalankan Web Server**:
   ```bash
   php artisan serve
   ```
   Aplikasi akan berjalan di `http://127.0.0.1:8000`.

---

## 👤 Akun Default (Development)

> **PENTING**: Akun di bawah ini dihasilkan dari database seeder untuk keperluan pengujian lokal. **Ubah password atau hapus akun demo sebelum menerapkan ke server produksi.**

| Role | Email Kantor | Password Default | Hak Akses |
| :--- | :--- | :--- | :--- |
| **Administrator** | `admin@kantor.com` | `password` | Penuh + Kelola Akun Admin/HRD |
| **HRD Team** | `hrd@kantor.com` | `password` | Monitoring, Laporan, Karyawan, Cuti |

---

## 🧪 Menjalankan Pengujian (Testing)

Proyek ini dilengkapi dengan rangkaian pengujian otomatis fitur dan unit menggunakan PHPUnit:

```bash
php artisan test
```

Cakupan pengujian mencakup:
- Pengiriman formulir publik & validasi input dinamis per divisi.
- Proteksi pencegahan laporan ganda per karyawan per tanggal.
- Integritas snapshot data pada laporan.
- Autentikasi guard `admin_hrd` & hak akses berbasis role (`admin` vs `hrd`).
- Perhitungan kepatuhan dashboard & pengecualian otomatis karyawan cuti/sakit.
- Pembatalan, pemulihan, pengeditan, dan ekspor laporan ke format CSV.

---

## 🎨 Format & Standar Kode

Untuk memastikan kepatuhan standar penulisan kode PHP:

```bash
vendor/bin/pint
```

---

## 🔒 Keamanan & Praktik Terbaik

- **Tanpa Registrasi Terbuka**: Sistem autentikasi hanya dapat diakses melalui penambahan manual oleh Administrator.
- **Enkripsi Password**: Seluruh password disimpan menggunakan hashing aman bawaan Laravel (*Bcrypt / Argon2id*).
- **Proteksi CSRF**: Seluruh formulir web terlindungi dari serangan *Cross-Site Request Forgery*.
- **Rate Limiting**: Endpoint login dibatasi (`throttle:10,1`) untuk mencegah percobaan brute-force.
- **Isolasi Sesi**: Akun yang dinonaktifkan akan langsung kehilangan hak akses pada request berikutnya.

---

## 📄 Lisensi

Aplikasi ini bersifat internal dan berlisensi komersial/privat untuk kebutuhan operasional kantor.
