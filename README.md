# Laravel Spark Admin

Template admin dashboard [Spark Admin](https://themewagon.com/themes/spark-admin/) berbasis Bootstrap 5 yang sudah dikonversi ke Blade untuk Laravel 13. Seluruh halaman demo Spark Admin tersedia sebagai view Blade lengkap dengan layout modular, partials, route, dan aset lokalnya.

## Teknologi

- PHP 8.4 & Laravel 13
- Spark Admin (Bootstrap 5, Bootstrap Icons, ApexCharts, Flatpickr)
- Pest 5 untuk testing
- Laravel Pint untuk code style

## Instalasi

```bash
git clone <repo-url> laravel-spark-admin
cd laravel-spark-admin
composer run setup
```

Perintah `setup` akan menjalankan `composer install`, membuat `.env`, generate app key, migrate database, serta install & build aset frontend.

Pastikan pada file `.env` konfigurasi template admin diatur ke `spark`:

```env
ADMIN_TEMPLATE=spark
```

Jalankan server development:

```bash
composer run dev
```

Lalu buka `/` atau `/pages-spark-admin/dashboard` di browser Anda.

## Struktur Project

```text
resources/views/spark-admin/
├── layouts/
│   └── app-spark-admin.blade.php   # Layout utama (sidebar + header + page-header + footer)
├── partials/
│   ├── meta.blade.php              # Meta tags SEO
│   ├── sidebar.blade.php           # Navigasi sidebar dengan active state
│   ├── header.blade.php            # Top navbar (search, notifications, profile)
│   └── footer.blade.php            # Footer komponen
├── index.blade.php                 # Halaman Dashboard utama
├── tables-basic.blade.php          # Halaman Basic Tables
├── ui-forms.blade.php              # Halaman Forms & Input Elements
├── ui-buttons.blade.php            # Halaman Buttons & Alerts
├── page-blank.blade.php            # Halaman Blank starter template
├── page-login.blade.php            # Halaman Login (standalone)
└── page-404.blade.php              # Halaman Error 404 (standalone)

public/modules/admin/spark-admin/
└── assets/                         # CSS, JS, images, dan third-party libs

routes/
└── spark-admin-web.php             # Route khusus halaman Spark Admin

spark-admin/                        # Source asli Spark Admin (HTML statis) sebagai referensi
```

## Daftar Route Spark Admin

Seluruh route berada di bawah prefix `/pages-spark-admin` dengan nama `pages.spark-admin.*`:

| URL | Route Name | View | Layout |
|---|---|---|---|
| `/pages-spark-admin/dashboard` | `pages.spark-admin.index` | `spark-admin.index` | `app-spark-admin` |
| `/pages-spark-admin/tables-basic` | `pages.spark-admin.tables-basic` | `spark-admin.tables-basic` | `app-spark-admin` |
| `/pages-spark-admin/ui-forms` | `pages.spark-admin.ui-forms` | `spark-admin.ui-forms` | `app-spark-admin` |
| `/pages-spark-admin/ui-buttons` | `pages.spark-admin.ui-buttons` | `spark-admin.ui-buttons` | `app-spark-admin` |
| `/pages-spark-admin/page-blank` | `pages.spark-admin.page-blank` | `spark-admin.page-blank` | `app-spark-admin` |
| `/pages-spark-admin/page-login` | `pages.spark-admin.page-login` | `spark-admin.page-login` | _(standalone)_ |
| `/pages-spark-admin/page-404` | `pages.spark-admin.page-404` | `spark-admin.page-404` | _(standalone)_ |

> Root route `/` ditangani oleh `App\Http\Controllers\HomeController` yang membaca `config('app.admin_template')` (`ADMIN_TEMPLATE` di `.env`): jika `spark` diarahkan ke `/pages-spark-admin/dashboard`, jika `stisla` diarahkan ke `/pages/index.html`.

## Membuat Halaman Baru

1. Buat view di `resources/views/spark-admin/nama-halaman.blade.php`:

```blade
@extends('spark-admin.layouts.app-spark-admin')

@section('title', 'Judul Halaman - Spark Admin')

@section('page-header')
    <div>
        <h1 class="page-title">Judul Halaman</h1>
        <p class="page-subtitle">Deskripsi singkat halaman.</p>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('pages.spark-admin.index') }}" class="text-decoration-none text-muted-green">Home</a></li>
            <li class="breadcrumb-item active text-main" aria-current="page">Judul Halaman</li>
        </ol>
    </nav>
@endsection

@section('content')
    <div class="card p-4 border-light shadow-sm">
        {{-- konten --}}
    </div>
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('modules/admin/spark-admin/assets/libs/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('modules/admin/spark-admin/assets/libs/bootstrap-icons/bootstrap-icons.css') }}">
@endsection

@section('js')
    <script src="{{ asset('modules/admin/spark-admin/assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
@endsection
```

2. Daftarkan route di `routes/spark-admin-web.php`:

```php
Route::view('/nama-halaman', 'spark-admin.nama-halaman')->name('nama-halaman');
```

3. Tambahkan menu pada `resources/views/spark-admin/partials/sidebar.blade.php`:

```blade
<li class="sidebar-menu-item">
    <a href="{{ route('pages.spark-admin.nama-halaman') }}"
       class="sidebar-menu-link {{ request()->routeIs('pages.spark-admin.nama-halaman') ? 'active' : '' }}">
        <i class="bi bi-file-earmark"></i>
        <span>Nama Halaman</span>
    </a>
</li>
```

## Code Style

Format kode PHP sebelum commit menggunakan Laravel Pint:

```bash
vendor/bin/pint --dirty
```

## Kredit

- [Spark Admin](https://themewagon.com/themes/spark-admin/) oleh Spark Admin Dev & didistribusikan oleh ThemeWagon — MIT License
- [Laravel](https://laravel.com) — MIT License
