# LMS Al Azhar SMP

Aplikasi LMS SMP Al Azhar berbasis Laravel.

## Data

Data default aplikasi disimpan pada:

- `database/database.sqlite`
- `lmsalazharsmp.sql`

File SQLite digunakan langsung oleh aplikasi lokal dan deployment Vercel. File SQL disediakan sebagai dump data agar data siswa, kelas, kelas Quran, guru, jadwal, mata pelajaran, dan data pendukung dapat dicek atau diimpor ulang.

## Menjalankan Lokal

```bash
composer install
npm install
php artisan migrate --seed
npm run build
php artisan serve
```

## Deployment Vercel

Konfigurasi deployment tersedia di `vercel.json`. Database SQLite ikut dibundel melalui konfigurasi function sehingga data web tersedia saat aplikasi dijalankan di Vercel.
