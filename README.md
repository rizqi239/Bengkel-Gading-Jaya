<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

In addition, [Laracasts](https://laracasts.com) contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

You can also watch bite-sized lessons with real-world projects on [Laravel Learn](https://laravel.com/learn), where you will be guided through building a Laravel application from scratch while learning PHP fundamentals.

## Deploy ke Vercel

Repository ini sudah memiliki konfigurasi Vercel menggunakan runtime PHP `vercel-php`. Hubungkan repository ini ke Vercel dengan root directory di root repository; proses build menjalankan `npm run build`.

Database Neon Postgres terhubung melalui Vercel Marketplace dan menyediakan `DATABASE_URL` untuk environment yang terhubung. Aplikasi di Vercel menggunakan PostgreSQL (`DB_CONNECTION=pgsql`); jangan mengganti `DATABASE_URL` dengan kredensial MySQL lokal. Jangan unggah atau commit file `.env`.

- `APP_KEY`: hasilkan dengan `php artisan key:generate --show`, lalu simpan hanya di Vercel.
- `APP_URL`: URL deployment, misalnya `https://nama-proyek.vercel.app`.
- `DATABASE_URL`: dibuat dan dikelola oleh integrasi Neon di Vercel.
- `GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET`, `GOOGLE_REDIRECT_URI` jika login Google digunakan. Tambahkan URL callback Vercel ke konfigurasi OAuth Google.
- Untuk foto stok, siapkan bucket S3 atau layanan yang kompatibel, lalu tambahkan variabel berikut di pengaturan Environment Variables Vercel:
  - `FILESYSTEM_DISK=s3`
  - `AWS_ACCESS_KEY_ID`, `AWS_SECRET_ACCESS_KEY`, `AWS_DEFAULT_REGION`, dan `AWS_BUCKET`
  - `AWS_URL` sebagai URL publik bucket/CDN jika penyedia membutuhkannya.
  - `AWS_ENDPOINT` dan `AWS_USE_PATH_STYLE_ENDPOINT` jika penyedia S3-compatible memerlukannya (misalnya Cloudflare R2).

Database Neon untuk project ini sudah berisi skema aplikasi dan salinan data MySQL lokal. Saat membuat resource Neon baru, jalankan migrasi Laravel dan pindahkan data MySQL sebelum aplikasi menerima trafik.

Upload dan tampilan foto stok menggunakan disk `public`, yang tetap lokal secara default untuk development. Di Vercel, atur disk tersebut ke `s3` dan berikan kredensial bucket yang memiliki akses baca publik agar upload tidak bergantung pada filesystem function yang tidak persisten. Foto yang sudah tersimpan secara lokal tidak otomatis dipindahkan ke bucket.

### Deploy produksi tanpa WSL di laptop

Jika function PHP di Vercel gagal dijalankan, workflow GitHub Actions dapat membangun dan men-deploy output dengan patch runtime PHP yang diperlukan. Runner Ubuntu berjalan di GitHub, bukan di laptop; Docker dan WSL lokal tidak diperlukan.

1. Buat Vercel access token, lalu simpan sebagai repository secret `VERCEL_TOKEN` di **Settings → Secrets and variables → Actions** pada GitHub. Workflow juga menerima nama `GADING_JAYA` jika itu nama secret yang sudah disiapkan.
2. Buka tab **Actions**, pilih **Deploy Vercel production**, lalu klik **Run workflow**.
3. Pilih `deploy` pada konfirmasi produksi dan jalankan workflow dari branch yang ingin di-deploy.

Workflow memverifikasi project Vercel yang terhubung, mengambil environment produksi, membangun output, memperbaiki metadata runtime PHP, lalu men-deploy hasil prebuilt ke produksi. Proses berhenti jika langkah mana pun gagal atau project yang terhubung bukan project aplikasi ini. Workflow hanya berjalan saat dipicu manual dan meminta konfirmasi sebelum deploy.

Untuk menjalankan deploy dari komputer lokal, diperlukan Linux; build runtime PHP tidak andal di Windows.

Patcher menerapkan metadata `NodejsLambda` dari workaround upstream: file handler `launcher.js` dan AWS handler `launcher.launcher`, tanpa helper/source-map injection. Patcher juga menyesuaikan fallback `LAMBDA_TASK_ROOT` ke `/var/task` dan normalisasi body/header request. Patcher hanya menyentuh fungsi PHP yang cocok di `.vercel/output`; jika output tidak memiliki bentuk yang diharapkan, proses berhenti dengan error. Build harus dilakukan di Linux karena builder PHP gagal menghasilkan executable runtime yang valid di Windows.

## Agentic Development

Laravel's predictable structure and conventions make it ideal for AI coding agents like Claude Code, Cursor, and GitHub Copilot. Install [Laravel Boost](https://laravel.com/docs/ai) to supercharge your AI workflow:

```bash
composer require laravel/boost --dev

php artisan boost:install
```

Boost provides your agent 15+ tools and skills that help agents build Laravel applications while following best practices.

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
