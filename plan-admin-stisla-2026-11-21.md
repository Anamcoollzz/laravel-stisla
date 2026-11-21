# Roadmap Maturasi Admin Template Stisla Menuju Laravel Package (`plan-admin-stisla-2026-11-21`)

Dokumen ini berfokus **100% pada Core Admin Template Stisla** (tanpa mencampur modul bisnis/vertikal seperti E-Commerce, SPP, News, atau Landing Page). Tujuannya adalah mematangkan arsitektur, komponen, performa, keamanan, dan *developer experience* agar siap diekstrak menjadi package Laravel mandiri (misalnya `composer require anamkun/laravel-stisla`).

---

## 1. Audit Kondisi Core Admin Stisla Saat Ini

### Modul Core Admin yang Sudah Ada (`routes/stisla-web.php`)
1. **Authentication & Account**: Login, Register + Email Activation (`signed` URL), Forgot/Reset Password, Google OAuth, Profile & Update Password.
2. **Access Control (RBAC)**: Users, Roles, Permissions (Spatie Permission), Dynamic Menus (`menus` table dengan dukungan `header`, `item`, `dropdown` + pencarian menu di navbar).
3. **System Configuration**: Dynamic Settings per grup (`config/settings.php` + tabel `settings`).
4. **Communication & Support**: Internal Chat (`ChatController`), App Notifications (`AppNotificationController`), Announcements (`AnnouncementController`), Helpdesk Tickets (`TicketController`).
5. **DevOps, Observability & Audit**:
   - Activity Log (`ActivityLogController` — *Before vs After* audit trail).
   - HTTP Request Log (`LogRequestController` + `LogRequestMiddleware`).
   - Laravel Log Viewer (`LaravelLogController`).
   - Database Backup (`BackupController`).
   - Database Table Explorer (`DatabaseTableController` — inspeksi & edit tabel/kolom/baris).
6. **Standardisasi Fitur Tabel & lokalisasi**:
   - Action *Duplicate*, *Export CSV/Excel*, *Export PDF*, *Download Template*, *Import*, serta lokalisasi penuh (`lang/id.json` & `lang/en.json`) dan proteksi Demo Mode (`PreventInDemoMode`).

### Temuan Teknis yang Perlu Dibenahi Sebelum Menjadi Package
- **Coupling di `AppServiceProvider.php`**: Logic Core Admin (`layouts.app`, `Menu`, `ChatMessage`, `AppNotification`, `Announcement`, `Gate::before`) masih bercampur dengan query bisnis (`CompanyProfile`, `ProductCategory`, `ProductCart`, `FooterProductCategory`). Terdapat juga duplikasi kode listener `Event::listen(Login::class)` (baris 84–94) dan pengecekan `last_logged_in` (baris 99–105).
- **Hardcoded Prefix & Model**: `Route::prefix('app')` di `routes/stisla-web.php` serta referensi `App\Models\User` masih hardcoded, belum bisa dikustomisasi oleh aplikasi host lewat config.
- **Query Berulang Tanpa Cache di Setiap Request**: `Menu` sidebar dan `Setting` masih di-query langsung ke database pada setiap halaman admin.
- **Duplikasi Markup Blade & Boilerplate Controller**: Setiap modul di `resources/views/modules/*` menulis ulang HTML yang sama untuk *Section Header*, *Stat Card*, *Filter Box*, *SweetAlert Delete*, dan *Import Modal*.
- **Ketergantungan CDN & Lokasi Asset Campuran**: `resources/views/layouts/app.blade.php` masih memuat Bootstrap 4.3.1 & FontAwesome 5.7.2 dari CDN eksternal, sementara plugin JS dimuat dari `public/node_modules/` dan `public/assets/`.

---

## 2. Arsitektur Package & Decoupling (`StislaServiceProvider`)

Agar siap menjadi package Composer (`src/`, `config/`, `resources/`, `database/`, `routes/`):

- [ ] **Buat `StislaServiceProvider` Khusus Core Admin**:
  - Pisahkan seluruh bootstrapping Core Admin dari `AppServiceProvider` ke `StislaServiceProvider`.
  - Gunakan namespace view `stisla::` (misal `stisla::layouts.app`, `stisla::modules.users.index`) dengan fallback otomatis ke `resources/views/vendor/stisla` milik aplikasi host.
  - Daftarkan *publishable tags*:
    - `stisla-config` (`config/stisla.php`, `config/settings.php`)
    - `stisla-assets` (`public/vendor/stisla/...`)
    - `stisla-views` (`resources/views/vendor/stisla/...`)
    - `stisla-migrations` (migrasi core: `menus`, `settings`, `activity_logs`, `log_requests`, `announcements`, `app_notifications`, `chat_messages`, `tickets`)
    - `stisla-lang` (`lang/id.json`, `lang/en.json`)
- [ ] **Perkaya Konfigurasi `config/stisla.php`**:
  - `route_prefix` (default: `'app'`)
  - `middleware` (default: `['web']`)
  - `auth_guard` (default: `'web'`)
  - `super_admin_role` (default: `'admin'`)
  - `models` (mapping class model agar bisa di-override aplikasi host: `user`, `menu`, `setting`, `activity_log`, `log_request`, `ticket`, `announcement`, `notification`, `chat_message`)
  - `features` (toggle on/off modul bawaan):
    ```php
    'features' => [
        'registration' => true,
        'google_oauth' => true,
        'chat' => true,
        'notifications' => true,
        'announcements' => true,
        'tickets' => true,
        'backups' => true,
        'database_explorer' => true,
        'log_requests' => true,
        'activity_logs' => true,
        'laravel_logs' => true,
    ],
    ```
  - Jika sebuah fitur di-set `false`, route, menu sidebar, dan icon navbar terkait otomatis disembunyikan.

---

## 3. Standarisasi Blade Components (`<x-stisla::*>`)

Mengubah markup Stisla yang berulang di `resources/views/modules/*` menjadi komponen Blade yang ringkas dan konsisten:

- [ ] **`<x-stisla::section-header :title="..." :breadcrumbs="[...]" :back-url="..." />`**:
  Menyeragamkan header halaman, tombol kembali, dan breadcrumb.
- [ ] **`<x-stisla::stat-card icon="..." color="primary" :label="..." :value="..." />`**:
  Menyeragamkan widget `card-statistic-1`.
- [ ] **`<x-stisla::card :title="..." :icon="...">` + slot `actions` & `footer`**:
  Menyeragamkan pembungkus `.card`, `.card-header`, `.card-header-action`, `.card-body`, `.card-footer`.
- [ ] **`<x-stisla::crud-toolbar route-prefix="users" permission-prefix="users" :has-pdf="true" :has-excel="true" :has-import="true" />`**:
  Menyeragamkan tombol *Add New*, *Export Excel*, *Export PDF*, *Download Template*, dan tombol *Import Modal* di kanan atas tabel.
- [ ] **`<x-stisla::action-buttons :model="$item" route-prefix="users" permission-prefix="users" />`**:
  Menyeragamkan tombol *Show*, *Edit*, *Duplicate*, dan *Delete* (termasuk integrasi SweetAlert konfirmasi hapus terpusat di layout utama sehingga tidak perlu menulis `@push('page-js')` SweetAlert di setiap file Blade).
- [ ] **Form Components (`<x-stisla::form.input>`, `<x-stisla::form.select>`, `<x-stisla::form.textarea>`, `<x-stisla::form.switch>`, `<x-stisla::form.image-picker>`)**:
  Otomatis menangani `old()`, `@error`, class `is-invalid`, `<div class="invalid-feedback">`, dan atribut `required`.

---

## 4. ReusableTraits & Base Classes untuk Backend

Mengurangi kode duplikat di controller dan model Core Admin:

- [ ] **Eloquent Trait `App\Traits\LogsActivity` (Auto Audit Trail)**:
  - Saat ini `ActivityLog::record()` dipanggil manual di controller.
  - Dengan trait `LogsActivity` pada model (misal `User`, `Role`, `Permission`, `Menu`, `Setting`, `Ticket`), perubahan `created`, `updated`, dan `deleted` otomatis tercatat beserta `getOriginal()` vs `getChanges()` (`before_values` & `after_values`), dengan konfigurasi `$activityModule`, `$activitySubjectLabel`, dan `$activityExcludedAttributes`.
- [ ] **Controller Trait `HasDuplicateRecord`**:
  - Menstandarkan aksi `duplicate()` untuk model apa pun (otomatis menangani penamaan `-copy`, `-copy-2`, penyalinan relasi opsional, dan pencatatan `ActivityLog`).
- [ ] **Controller Trait `HasCsvImportExport` & `HasPdfExport`**:
  - Menstandarkan pembuatan response stream CSV/Excel, download template CSV, validasi baris saat import, serta rendering PDF dengan header/footer standar dari `resources/views/layouts/pdf.blade.php`.

---

## 5. Performa & Caching Layer Core Admin

- [x] **Cache Sidebar Menu (`Menu`)**:
  - Simpan hasil query tree `Menu` ke cache (`Cache::rememberForever("stisla.menus.{$menuApp}", ...)`) dan otomatis hapus cache melalui Model Observer `MenuObserver` saat menu ditambah, diubah, atau dihapus.
- [x] **Cache Konfigurasi `Setting`**:
  - Simpan seluruh key-value `Setting` ke dalam 1 cache key (`stisla.settings.all`) sehingga pemanggilan `Setting::getValue()` dan `Setting::valuesForGroup()` tidak melakukan query SQL berulang di setiap request, dan di-flush otomatis saat `SettingController@update` / `reset`.
- [x] **Optimasi Query Navbar (`ChatMessage` & `AppNotification`)**:
  - Gabungkan atau cache ringan (TTL 15–30 detik per user) perhitungan unread count di `layouts.app` View Composer, serta bersihkan kode duplikat di `AppServiceProvider`.

---

## 6. CLI Tooling & Generator (`php artisan stisla:*`)

Fitur wajib agar package ini disukai developer Laravel:

- [ ] **`php artisan stisla:install`**:
  - Mem-publish config, asset, menjalankan migrasi core, menjalankan `RolePermissionSeeder` & `MenuSeeder`, serta membuat akun Super Admin interaktif di terminal.
- [ ] **`php artisan stisla:make-crud {ModelName} --fields="name:string,is_active:boolean"`**:
  - Men-generate secara otomatis:
    1. Migration & Model (dengan trait `LogsActivity`).
    2. FormRequest (`Store{Model}Request` & `Update{Model}Request`).
    3. Controller lengkap (`index`, `create`, `store`, `show`, `edit`, `update`, `destroy`, `duplicate`, `export`, `exportPdf`, `template`, `import`).
    4. Blade Views (`index`, `create`, `edit`, `show`) berbasis komponen `<x-stisla::*>`.
    5. Pendaftaran route resource, permission (`{module}.view/create/edit/delete`), item menu di tabel `menus`, dan key terjemahan di `lang/id.json` & `lang/en.json`.
- [x] **`php artisan stisla:sync-permissions`**:
  - Memindai daftar module di config dan menyinkronkan permission baru ke database tanpa merusak role yang sudah ada.

---

## 7. Keamanan Enterprise & Hardening Modul Sistem

- [ ] **Manajemen Sesi Aktif & Two-Factor Authentication (2FA) di Profile**:
  - Tampilkan daftar perangkat/browser yang sedang login dari tabel `sessions` di halaman `profile.edit` (lengkap dengan IP, perangkat, waktu aktif terakhir, dan tombol *Logout Other Browser Sessions*).
  - Tambahkan opsi 2FA (TOTP Google Authenticator atau Email OTP) yang bisa diaktifkan per user atau diwajibkan untuk role `admin`.
- [ ] **Hardening `DatabaseTableController` & `BackupController`**:
  - Tambahkan opsi config `'allow_database_mutation_in_production' => env('STISLA_DB_MUTATION_IN_PROD', false)` agar di environment `production`, fitur *Database Tables* otomatis berjalan dalam mode **Read-Only** (tidak bisa drop kolom/hapus row langsung dari UI kecuali diizinkan eksplisit).
  - Tambahkan pencatatan `ActivityLog` setiap kali seseorang membuat/menghapus backup database, membersihkan log, atau mengubah struktur/isi tabel lewat `DatabaseTableController`.
- [ ] **Kebijakan Password & Lockout**:
  - Tambahkan rate-limiting jelas pada login admin, riwayat login gagal di `ActivityLog`, serta aturan kompleksitas password yang bisa diatur dari menu `Settings -> Security`.

---

## 8. Modernisasi Asset & UI/UX Admin Stisla

- [ ] **Self-Hosted & Bundled Core Assets (`public/vendor/stisla`)**:
  - Pindahkan Bootstrap, FontAwesome, jQuery, NiceScroll, SweetAlert, Chart.js, dan Summernote dari CDN / `public/node_modules/` ke satu direktori terstruktur `public/vendor/stisla/` agar template tetap berjalan 100% di jaringan lokal/intranet (offline-ready) dan mudah di-publish oleh package.
- [ ] **Global Toast & SweetAlert Handler Terpusat di `layouts/app.blade.php`**:
  - Pindahkan script flash message `session('success')`, `session('error')`, dan handler `.btn-delete` ke file JS utama (`stisla-crud.js`) yang dimuat di `layouts/app.blade.php`, sehingga setiap view modul tidak perlu menduplikasi script SweetAlert.
- [ ] **Dark Mode Admin Stisla**:
  - Tambahkan toggle *Light / Dark Mode* di navbar admin (disimpan di `localStorage` / profil user) menggunakan CSS variables di `public/assets/css/custom.css`.
- [ ] **Global Command Palette (`Ctrl+K` / `Cmd+K`)**:
  - Tingkatkan fitur pencarian menu di navbar (`#navbar-menu-search`) agar mendukung shortcut keyboard `Ctrl+K` / `Cmd+K` dan navigasi panah atas/bawah + `Enter`.

---

## Urutan Prioritas Eksekusi yang Disarankan

1. **Tahap 1 (Clean Up & Performa)**: Bersihkan duplikasi di `AppServiceProvider`, buat `StislaServiceProvider`, tambahkan caching pada `Menu` & `Setting`, dan pusatkan handler SweetAlert di layout utama.
2. **Tahap 2 (Blade Components & Traits)**: Buat komponen `<x-stisla::*>` dan trait `LogsActivity` + `HasDuplicateRecord`.
3. **Tahap 3 (Security & Enterprise Features)**: Tambahkan Active Sessions di Profile, Audit Log pada `BackupController` & `DatabaseTableController`, serta mode Read-Only DB Explorer di production.
4. **Tahap 4 (CLI Generator & Packaging)**: Buat command `stisla:make-crud`, `stisla:install`, dan struktur konfigurasi `config/stisla.php` yang siap dipaketkan ke Composer.
