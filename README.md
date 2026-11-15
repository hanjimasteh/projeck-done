# PPDB SMAKNIS

Website Penerimaan Peserta Didik Baru (PPDB) berbasis Laravel 12, Bootstrap 5, MySQL, dan Vite.

## Status Pengerjaan

- [x] Fase 1 - Setup Laravel, Bootstrap, Vite, layout utama, navbar, footer
- [x] Fase 2 - Database: migration, model, relasi, seeder
- [x] Fase 3 - Frontend publik
- [x] Fase 4 - Authentication & role
- [x] Fase 5 - Formulir pendaftaran
- [x] Fase 6 - Upload dokumen
- [x] Fase 7 - Dashboard admin, data pendaftar, verifikasi
- [x] Fase 8 - Seleksi & pengumuman
- [x] Fase 9 - Daftar ulang & cetak bukti
- [x] Fase 10 - Laporan, polish, dan penyelesaian project

## Kebutuhan

- PHP 8.2+
- Composer 2
- Node.js 20+ dan npm
- MySQL 8 (atau MariaDB)

## Menjalankan Aplikasi

```bash
composer install
cp .env.example .env
php artisan key:generate
npm install
npm run build
php artisan migrate:fresh --seed
php artisan serve
```

Untuk pengembangan frontend, jalankan Vite di terminal terpisah:

```bash
npm run dev
```

Buka `http://127.0.0.1:8000`.

## Konfigurasi Database

Sesuaikan variabel berikut pada `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tugasppdb
DB_USERNAME=root
DB_PASSWORD=
```

## Akun Demo

Jalankan `php artisan migrate:fresh --seed` untuk membuat akun berikut:

| Role | Username | Email | Password |
|------|----------|-------|----------|
| Administrator | `admin` | `admin@ppdb.test` | `password` |
| Pendaftar | `pendaftar` | `pendaftar@ppdb.test` | `password` |

### Hak Akses Akun Demo

**Admin (`admin`):**
- Bisa mengakses halaman admin di `/admin/dashboard`
- Bisa mengelola jurusan, jalur pendaftaran, program, gelombang
- Bisa verifikasi pendaftaran, mengelola daftar ulang
- Bisa melihat dan mengeksport laporan lengkap

**Pendaftar (`pendaftar`):**
- Bisa mendaftar melalui `/calon/formulir`
- Bisa melengkapi data diri, orang tua, jalur-program, dan dokumen
- Bisa melihat hasil seleksi di `/calon/hasil`
- Bisa melakukan daftar ulang jika status `selected` di `/calon/daftar-ulang`
- Bisa mencetak bukti pendaftaran PDF di `/calon/pendaftaran/cetak-pdf`

## Fitur Utama per Fase

**Fase 1-8:** Setup dasar, pendaftaran, autentikasi, seleksi, pengumuman
**Fase 9:** Fitur daftar ulang untuk siswa yang diterima, konfirmasi oleh admin, dan pencetakan bukti pendaftaran PDF menggunakan Dompdf
**Fase 10:** Fitur laporan admin dengan filter periode, jalur, program, status; ekspor ke CSV/PDF; optimasi performa; responsive UI

## Struktur Layout

- `resources/views/layouts/front.blade.php` - halaman publik
- `resources/views/layouts/calon.blade.php` - area pendaftar
- `resources/views/layouts/backend.blade.php` - area admin

Komponen reusable berada di `resources/views/components/` (navbar, footer, alert).

## Daftar File Baru dan Diubah

### Fase 9
- `app/Http/Controllers/Calon/CetakBuktiController.php` - Controller cetak bukti PDF
- `routes/web.php` - Route daftar ulang dan cetak PDF
- `config/database.php` - Fix PDO constant PHP 8.5
- `composer.json`, `composer.lock` - Tambah dependensi Dompdf

### Fase 10
- `app/Http/Controllers/Admin/LaporanController.php` - Controller laporan dengan filter dan ekspor
- `resources/views/admin/laporan/index.blade.php` - View halaman laporan admin
- `resources/views/admin/laporan/pdf.blade.php` - View cetak PDF laporan
- `routes/web.php` - Route laporan dan ekspor CSV/PDF
- `app/Http/Middleware/EnsureUserHasRole.php` - Sudah ada, ketat otorisasi

## Optimasi Performa

- Semua kueri Eloquent menggunakan `with()` untuk eager loading relasi
- Indeks database pada Foreign Key dan kolom unik sudah terpasang
- Validasi Form Request diterapkan di seluruh formulir
- CSRF protection aktif di semua form
- Middleware otorisasi role:admin dan role:pendaftar ketat

## Lisensi

Hak cipta sekolah. Dikembangkan untuk kebutuhan internal PPDB.