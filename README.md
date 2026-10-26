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

### Workaround runtime PHP untuk preview

Jika log function menunjukkan `ERR_MODULE_NOT_FOUND` untuk `launcher.launcher`, runtime PHP Vercel yang terpasang masih menggunakan metadata handler lama. Buat prebuilt output pada lingkungan Linux yang sudah terautentikasi ke project Vercel yang benar (misalnya WSL/Docker yang berfungsi), lalu patch metadata sebelum upload:

```sh
vercel project inspect --non-interactive
vercel pull --yes --environment preview
vercel build --yes --environment preview
node scripts/patch-vercel-runtime.mjs
vercel deploy --prebuilt --yes
```

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
