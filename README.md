# Laravel Stisla

Template admin dashboard [Stisla](https://getstisla.com) (v2.3.0) yang sudah dikonversi ke Blade untuk Laravel 13. Seluruh 82 halaman demo Stisla tersedia sebagai view Blade lengkap dengan layout, route, dan asetnya.

## Teknologi

- PHP 8.4 & Laravel 13
- Stisla 2.3.0 (Bootstrap 4, jQuery)
- Pest 5 untuk testing
- Laravel Pint untuk code style

## Instalasi

```bash
git clone <repo-url> laravel-stisla
cd laravel-stisla
composer run setup
```

Perintah `setup` akan menjalankan `composer install`, membuat `.env`, generate app key, migrate database, serta install & build aset frontend.

Jalankan server development:

```bash
composer run dev
```

Lalu buka `/pages/index.html` untuk melihat Ecommerce Dashboard.

## Struktur Project

```
resources/views/
├── layouts/
│   ├── app.blade.php       # Layout dengan navbar + sidebar
│   └── auth.blade.php      # Layout polos (auth, error, utilities)
└── pages/                  # 82 halaman Stisla dalam bentuk Blade

public/
├── assets/                 # CSS, JS, font, dan gambar template Stisla
└── node_modules/           # Library pihak ketiga yang dipakai halaman

stisla/                     # Source asli Stisla (HTML statis) sebagai referensi
```

### Layout

| Layout | Dipakai oleh |
|---|---|
| `layouts.app` | Dashboard, Bootstrap, Components, Forms, Google Maps, Modules, Features, Credits, dll |
| `layouts.auth` | `auth-*`, `errors-*`, `utilities-contact`, `utilities-subscribe` |
| _(standalone)_ | `layout-transparent`, `layout-top-navigation` (memiliki navbar/body class sendiri) |

Menu sidebar pada `layouts.app` otomatis memberi class `active` sesuai route yang sedang dibuka menggunakan `request()->routeIs()`.

## Membuat Halaman Baru

1. Buat view di `resources/views/pages/nama-halaman.blade.php`:

```blade
@extends('layouts.app')

@section('title', 'Judul Halaman')

@push('css')
  <link rel="stylesheet" href="{{ asset('node_modules/library/dist/library.min.css') }}">
@endpush

@push('js')
  <script src="{{ asset('node_modules/library/dist/library.min.js') }}"></script>
@endpush

@push('page-js')
  <script src="{{ asset('assets/js/page/nama-halaman.js') }}"></script>
@endpush

@section('content')
  <div class="section-header">
    <h1>Judul Halaman</h1>
  </div>

  <div class="section-body">
    {{-- konten --}}
  </div>
@endsection
```

2. Daftarkan route di `routes/web.php`:

```php
Route::view('/pages/nama-halaman.html', 'pages.nama-halaman')->name('nama-halaman.html');
```

3. Tambahkan menu di sidebar `resources/views/layouts/app.blade.php`:

```blade
<li @class(['active' => request()->routeIs('nama-halaman.html')])>
  <a class="nav-link" href="{{ route('nama-halaman.html') }}"><i class="far fa-square"></i> <span>Nama Halaman</span></a>
</li>
```

### Stack yang tersedia

| Stack | Fungsi |
|---|---|
| `css` | CSS library tambahan (dimuat sebelum CSS template) |
| `js` | JS library tambahan (dimuat sebelum `scripts.js`) |
| `page-js` | JS khusus halaman (dimuat paling akhir) |

### Menambah library

Library dari `stisla/node_modules` cukup disalin ke `public/node_modules` dengan struktur path yang sama, lalu dipanggil memakai `asset('node_modules/...')`.

## Testing

```bash
php artisan test --compact
```

`tests/Feature/StislaPagesTest.php` memastikan:

- Semua route halaman Stisla me-render dengan status 200.
- Semua aset lokal (`assets/` & `node_modules/`) yang direferensikan halaman benar-benar ada di `public/`.
- Sidebar menandai halaman aktif dengan benar.

Format kode PHP sebelum commit:

```bash
vendor/bin/pint --dirty
```

## Kredit

- [Stisla](https://github.com/stisla/stisla) oleh Muhamad Nauval Azhar — MIT License
- [Laravel](https://laravel.com) — MIT License
