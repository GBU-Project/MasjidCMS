# Dokumentasi Perbaikan — Installer, Login, & Hardcoded Path

**Konteks:** Ditemukan saat proses instalasi ulang di lingkungan lokal (XAMPP, subfolder `/masjidgbu/`) setelah deploy hasil TASK-020 RC0 Stabilization.
**Commit terkait:** `5010db7`, `642f432`

---

## 1. Login Gagal Setelah Install (Root Cause)

### Gejala
Setelah menjalankan installer wizard (`/install`), login ke `/admin/login` gagal.

### Diagnosis Awal (dari pelapor)
- Hash password di `seed.sql` adalah hash palsu.
- Database dibuat dari `database/schema.sql` (skema lama), sedangkan kode aplikasi (`PermissionRepository::findByUserId()`) mengasumsikan skema baru dari migration `CreateRbacTables`.

Perbedaan skema:

| Tabel | Skema Lama (`schema.sql`) | Skema Baru (Migration) |
|---|---|---|
| `users.id` | `BIGINT` | `VARCHAR(36)` |
| `roles` | kolom `name` saja | `role_code` + `name` |
| `permissions` | kolom `name` saja | `permission_code` + `module_name` |
| `role_permissions` | `(role_id, permission_id)` | `(role_id, permission_code)` |

Query `PermissionRepository::findByUserId()` melakukan JOIN pada `role_permissions.permission_code = permissions.permission_code` — kolom itu tidak ada di skema lama, sehingga MySQL melempar error dan login gagal.

**Status: diagnosis di atas dikonfirmasi benar** melalui pemeriksaan langsung terhadap `database/schema.sql`, migration `2026-07-27-000003_CreateRbacTables.php`, dan `PermissionRepository.php`.

### Root Cause Sebenarnya (Tambahan)

Ditemukan bug kedua yang lebih mendasar di **installer wizard aplikasi**, sumber dari mengapa skema lama yang terpakai:

1. **`InstallerController::database()`** memanggil `DatabaseInstaller::importSchema()`, yang mengeksekusi `database/schema.sql` + `database/seed.sql` secara langsung — bukan menjalankan migration CodeIgniter. File `schema.sql` ini:
   - Predates migration `CreateRbacTables` (tidak punya `role_code`/`permission_code`/`module_name`)
   - Hanya berisi 34 tabel, jauh tertinggal dari migration terbaru (tidak ada tabel `agenda`, `homepage_manager_settings`, modul bisnis masjid, dll)

2. **`InstallerController::admin()`** — form "Buat Admin" pada wizard memvalidasi password dan membuat hash lewat `AdminSeeder::createAdmin()`, **tapi hasilnya tidak pernah disimpan ke database sama sekali**. Setelah wizard selesai, tidak ada user admin yang benar-benar tersimpan — terlepas dari masalah skema di atas.

### Perbaikan

**`app/Services/Installer/DatabaseInstaller.php`**
- Tambah method baru `migrateAndSeedCore()`: menjalankan migration CodeIgniter asli (`Services::migrations()->latest()`) + `RbacSeeder` (katalog roles/permissions yang sudah benar skemanya).
- Method lama `importSchema()` (schema.sql/seed.sql) tetap ada di kode untuk referensi, ditandai `@deprecated`, tapi **tidak lagi dipanggil**.

**`app/Controllers/InstallerController.php`**
- `database()`: tidak lagi mengeksekusi `schema.sql`/`seed.sql`. Hanya test koneksi + simpan kredensial ke session.
- `admin()`: 
  - Memanggil `migrateAndSeedCore()` di awal — ini request pertama setelah `.env` benar-benar ditulis oleh `application()` (penting: PHP hanya membaca `.env` sekali saat boot, jadi migration harus dijalankan di request *berikutnya*, bukan di request yang sama saat `.env` ditulis).
  - Setelah validasi berhasil, **benar-benar menyimpan** user admin ke tabel `users` (kolom `id` VARCHAR(36), `status` — bukan `is_active`/`full_name` yang ternyata tidak ada di skema baru) dan menetapkan role `SUPER_ADMIN` via `user_roles`.
- Tambah method baru `persistAdminUser()` untuk logika penyimpanan ini, dengan pengecekan username duplikat.

### Catatan
Fix ini tidak dijalankan/dites di runtime PHP nyata (sandbox pengerjaan tidak memiliki interpreter PHP). Setiap nama kolom sudah dicocokkan manual terhadap migration `CreateRbacTables` dan `RbacSeeder` yang sudah terbukti bekerja. **Tetap wajib di-smoke-test** alur install lengkap (welcome → database → application → admin → finish) di lingkungan nyata sebelum dipakai produksi.

---

## 2. Hardcoded Absolute Path `/admin/...` (Not Found di Subfolder Install)

### Gejala
Diminta memeriksa apakah CRUD Berita, Agenda, dan modul lain berpotensi menghasilkan "Not Found".

### Root Cause
Banyak link aksi (`Edit`, `Hapus`, navigasi tab, form action) di seluruh aplikasi ditulis sebagai path absolut hardcoded, contoh:

```php
'<a href="/admin/cms/edit/posts/' . $p['id'] . '">Edit</a>'
```

Selama aplikasi diinstal tepat di **domain root** (`http://domain.com/`), path ini kebetulan tetap benar. Tapi begitu aplikasi diinstal di **subfolder** (mis. `http://localhost/masjidgbu/`), path hardcoded ini tetap mengarah ke `http://localhost/admin/...` — **kehilangan prefix `/masjidgbu/`** — menghasilkan halaman **Not Found** untuk fitur yang sebenarnya berfungsi normal secara logika.

Audit dilakukan dengan pencarian menyeluruh (`grep -rn 'href="/admin'`) di seluruh `Controllers/` dan `Views/`, dikonfirmasi dengan script Python yang mencocokkan **setiap** referensi `Controller::method` di `Config/Routes.php` terhadap method yang benar-benar ada (0 route/method yang hilang — masalahnya murni di path link, bukan di routing itu sendiri).

### File yang Diperbaiki (hardcoded → `site_url()` / `base_url()`)

| File | Bagian yang diperbaiki |
|---|---|
| `app/Controllers/AdminCmsWorkspaceController.php` | Link Edit/Hapus untuk **Berita, Kajian, Agenda, Pages** |
| `app/Views/admin/reporting/index.php` | Tab nav, tombol "Generate Preview" (6 jenis laporan), form filter |
| `app/Views/admin/reporting/preview.php` | Tombol "Clear Filter" |
| `app/Views/admin/financial/detail.php` | Tombol "Kembali" |
| `app/Views/errors/html/error_404.php` | Link CSS + tombol "Kembali ke Dashboard" |
| `app/Views/errors/html/error_403.php` | Link CSS + tombol "Kembali ke Dashboard" |
| `app/Views/errors/html/error_500.php` | Link CSS + tombol "Kembali ke Dashboard" |

### Catatan Khusus: Halaman Error

Halaman error (404/403/500) dirender oleh exception handler CodeIgniter, **bukan** lewat Controller biasa — sehingga helper `url` (yang menyediakan `site_url()`/`base_url()`) tidak otomatis ter-load seperti pada halaman lain (`BaseController` biasanya meng-autoload `$helpers = ['url', 'form', 'text']`, tapi ini tidak berlaku untuk exception page). Untuk mencegah fatal error baru ("Call to undefined function"), ditambahkan pemanggilan eksplisit di baris pertama ketiga file:

```php
<?php helper('url'); ?>
```

### Verifikasi Setelah Perbaikan
- `grep -rn 'href="/admin'` di seluruh `Controllers/` dan `Views/` → **0 hasil**
- Audit ulang route ↔ method (script Python) → **0 broken target** (tidak ada regresi dari perubahan ini)

---

## Ringkasan Commit

| Commit | Isi |
|---|---|
| `5010db7` | Root cause login gagal: installer pakai `schema.sql` lama + admin user tidak pernah disimpan ke DB |
| `642f432` | Hardcoded `/admin/...` → `site_url()` di CMS row actions, Reporting, Financial detail, halaman error |

## Rekomendasi Sebelum Produksi
1. Jalankan `composer install` di server sebelum mengakses `/install` (folder `vendor/` tidak ikut di paket kode).
2. Uji alur install penuh dari nol di lingkungan yang representatif (termasuk kalau memang diinstal di subfolder).
3. Uji login dengan akun admin yang dibuat lewat wizard, bukan hanya akun `superadmin` bawaan `RbacSeeder`.
4. Klik seluruh tombol Edit/Hapus/Generate Preview di Berita, Kajian, Agenda, Financial, dan Reporting untuk memastikan tidak ada lagi "Not Found" — khususnya jika instalasi tidak berada di domain root.
