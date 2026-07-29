# TASK-019A — 🔴 Security Blocker Remediation (Broken Access Control)

**Status:** ✅ Implemented & Verified End-to-End (siap review & deploy)
**Priority:** Critical / Blocker
**Sumber temuan:** `Laporan_Audit_IT_MasjidCMS_Independen.md` (29 Jul 2026), §3
**Baseline commit:** `5b4c090` (develop, "feat: complete TASK-018 and TASK-019")
**Dikerjakan:** 29 Juli 2026

> Catatan: dokumen `TASK019A_SECURITY_BLOCKER_REMEDIATION.md` yang sempat beredar terpisah menggambarkan pendekatan serupa namun **tidak pernah ter-push ke repo GitHub** (diverifikasi via `git ls-remote` — hanya branch `develop`/`master` yang ada, keduanya tidak mencerminkan dokumen tsb). Sebagian klaimnya (endpoint `api/financial/*` tanpa filter) terbukti benar dan sudah diikutsertakan di sini; satu klaim lain (soal filter `rbac` tanpa argumen memblokir semua non-Super-Admin) terbukti keliru berdasarkan kode `AuthorizationFilter.php` yang sebenarnya.

---

## 1. Root Cause

| # | File | Masalah |
|---|---|---|
| 1 | `app/Config/Filters.php` | `public array $filters = [];` — kosong, filter global tidak pernah dipasang ke rute manapun |
| 2 | `app/Config/Routes.php` | 60+ rute `admin/*` (Dashboard, Master Data, **Financial Workspace**, Reporting, CMS, Settings) terdaftar tanpa parameter `filter` sama sekali |
| 3 | `app/Domains/System/Routes/auth.php` | Berisi rute `auth/login`/`logout`/`refresh`, **tidak pernah `require`** — tidak ada jalur login yang aktif di aplikasi |
| 4 | `app/Domains/Masjid/Routes/routes.php` | Implementasi filter yang **benar** sempat dibuat, tapi file ini tidak pernah `require` — dead code |
| 5 | `app/Domains/{Jamaah,Family}/Routes/*.php` | Data pribadi jamaah/keluarga (termasuk transfer kepala keluarga) publicly writable tanpa login |
| 6 | `app/Domains/Financial/Routes/financial.php` | **Ditemukan saat verifikasi** — endpoint `api/financial/*` (approve/reject/post/void transaksi) juga tanpa filter sama sekali |
| 7 | Tidak ada view/controller login HTML | `AuthenticationController::login()` hanya JSON API, tidak bisa diakses wajar lewat browser |
| 8 | `app/Domains/System/Repositories/{Permission,Role}Repository.php` | **Bug tersembunyi baru ketahuan** — query JOIN memakai nama kolom yang tidak ada di skema (`permission_id` vs `permission_code` sesungguhnya), sehingga begitu filter `rbac` benar-benar dieksekusi untuk user non-Super-Admin, aplikasi **crash 500** alih-alih menolak dengan 403 |

Item #6 dan #8 adalah temuan yang **belum tercatat** di laporan audit awal — baru terungkap saat proses remediasi & pengujian end-to-end.

---

## 2. Perubahan (23 file, +661/-879 baris — mayoritas penghapusan dead code)

### `app/Config/Routes.php`
Seluruh rute `admin/*` dibungkus:
```php
$routes->group('admin', ['filter' => ['auth', 'rbac']], static function (RouteCollection $routes) {
    ...
});
```
Filter `rbac` tanpa argumen = cukup harus login (Super Admin selalu bypass). Untuk aksi sensitif ditambahkan permission spesifik per-rute:
```php
$routes->post('financial/store', '...::store', ['filter' => 'rbac:financial.manage']);
$routes->post('master/store', '...::store', ['filter' => 'rbac:admin.manage']);
```
Ditambahkan pula rute login baru: `GET/POST /login`, `GET /logout`, dan mengaktifkan `require APPPATH . 'Domains/System/Routes/auth.php';` untuk JSON Auth API.

### Halaman Login Admin (baru)
- `app/Controllers/AuthPageController.php` — `showLogin()`, `login()` (form-post, rate-limited 5x/5menit sama seperti API), `logout()`.
- `app/Views/admin/auth/login.php` — form HTML mandiri, konsisten dengan tema visual (`app-theme.css`), CSRF-protected.

### `app/Filters/AuthenticationFilter.php`
Request tanpa sesi: browser biasa → redirect ke `/login`; request `api/*`/AJAX → tetap JSON 401 seperti sebelumnya.

### Proteksi rute domain di luar `admin/*`
`app/Domains/{Masjid,Jamaah,Family}/Routes/*.php` diberi `['filter' => ['auth', 'rbac']]` + permission granular per-aksi (`jamaah.read/create/update/delete`, dst — memakai `permission_code` yang sudah ada di seeder).

### `app/Domains/Financial/Routes/financial.php`
`api/financial/*` (approve/reject/post/void) diberi `['filter' => ['auth', 'rbac:financial.manage']]`. **Lihat §4 untuk catatan penting yang belum sepenuhnya tuntas di sini.**

### `app/Database/Seeds/RbacSeeder.php`
Ditambahkan permission `masjid.read/create/update/delete` dan `financial.manage`, digrant ke role `ADMIN_MASJID` (dan `masjid.read` ke `OPERATOR`/`VIEWER`) — supaya role yang sudah ada tidak terkunci begitu filter aktif.

### `app/Domains/System/Repositories/PermissionRepository.php` & `RoleRepository.php`
Perbaikan bug schema mismatch (`permission_id`→`permission_code`, `slug`→`role_code`) — **wajib**, tanpa ini fitur RBAC granular yang baru diaktifkan akan 500 untuk semua non-Super-Admin.

### Dead Code Cleanup
- `app/Domains/Masjid/Routes/routes.php` dihapus (digantikan oleh `masjid.php` yang sudah diperbaiki).
- **`app/Domains/Authorization/*` dihapus seluruhnya** (9 file: Entities, Guards, Providers, Services, Filters, README) — ternyata ini bukan sekadar satu class duplikat, melainkan **seluruh sistem RBAC paralel dari era RC1** (entity `User`/`Role`/`Permission` sendiri, `GuardResolver` dengan dukungan Bearer Token, `PolicyResolver`, `assignMasjidRole` multi-tenant) yang sepenuhnya digantikan oleh `app/Domains/System/*` (yang benar-benar dipakai `Config/Filters.php` saat ini). Diverifikasi lewat `grep` bahwa **satu-satunya** konsumen namespace ini di seluruh codebase adalah test file khusus miliknya sendiri (`tests/unit/RbacFoundationRc1Test.php`) — yang memberi kesan palsu bahwa RBAC "sudah diuji", padahal jalur yang diuji tidak pernah dilalui request produksi sama sekali. Test file tersebut ikut dihapus. Full test suite diverifikasi turun tepat 153→146 (persis 7 test yang dihapus), 0 kegagalan baru.

### `tests/unit/AdminAccessControlTest.php` (baru — 7 test, 100 assertions)
Memuat ulang `RouteCollection` sungguhan dan memverifikasi tiap rute `admin/*` benar-benar membawa filter `auth`+`rbac`, rute login tetap publik, JSON Auth API aktif, rute domain (jamaah/family/masjid) terproteksi, endpoint financial API terproteksi, dan perbaikan skema repository sudah benar.

---

## 3. Verifikasi End-to-End (dijalankan sungguhan, bukan simulasi)

Semua verifikasi berikut dijalankan dengan meng-install CodeIgniter4 + PHPUnit sungguhan via Composer, migrasi ke SQLite lokal, menjalankan `php spark serve`, dan menembak endpoint nyata dengan `curl`:

| Skenario | Sebelum | Sesudah |
|---|---|---|
| `GET admin/dashboard` tanpa sesi | 200 (terbuka) | **302 → `/login`** |
| `GET admin/financial` tanpa sesi | 200 (terbuka) | **302 → `/login`** |
| `POST api/financial/.../approve` tanpa sesi | 200 (bisa approve!) | **401 JSON** |
| `GET /login` | 404 (tidak ada rute) | **200**, form tampil |
| `GET /` (portal publik) | 200 | **200** (tidak terganggu) |
| Login form (kredensial benar) → redirect | N/A | **303 → `admin/dashboard`** |
| `GET admin/dashboard` dengan sesi valid | N/A | **200**, dashboard ter-render |
| User role Operator → `admin/financial` (index) | N/A | **200** (cukup login) |
| User role Operator → `admin/users` (butuh `admin.manage`) | N/A | **403** rapi (bukan 500) |

**Regresi:** Full test suite proyek (153 test) dijalankan sebelum & sesudah. 5 kegagalan yang muncul **dikonfirmasi identik** pada baseline asli tanpa perubahan apa pun (pre-existing, tidak terkait) — perubahan TASK-019A menambah 7 test baru, semuanya lulus, **zero regresi baru**.

---

## 4. ⚠️ Belum Tuntas — Perlu TASK-019B (Tindak Lanjut Wajib)

### 4.1 Identity Spoofing di `FinancialApiController::approve()`/`reject()`
```php
$req = new ApproveTransactionRequest($uuid, (string) ($json['approver_user_id'] ?? 'user-dkm'));
```
Menambahkan filter `auth` mencegah akses **anonim**, tapi **tidak mencegah** user yang sudah login memalsukan `approver_user_id` milik user lain di body request untuk melewati `ApprovalPolicy` (Treasurer/Finance Manager/Chairman only). `approver_user_id` **harus** diambil dari `SecurityContext::user()->id` hasil sesi login, bukan dari input client. Ini menyentuh Controller + Application Service/DTO (`ApproveTransactionRequest`, `RejectTransactionRequest`) sehingga sengaja dipisah sebagai task tersendiri agar tidak memperbesar/mengaburkan scope perbaikan akses ini.

### 4.2 Cookie `Secure` flag & `.htaccess` upload
Belum di-hardcode (lihat §5.1–5.2 laporan audit) — direkomendasikan diset lewat `.env` produksi terpisah dari task ini agar tidak mengganggu dev lokal non-HTTPS.

### 4.3 Duplikasi rute `admin/media`
Pre-existing bug (dua controller berbeda didaftarkan untuk path yang sama, salah satu ter-shadow) — dibiarkan apa adanya karena di luar cakupan keamanan, direkomendasikan dibersihkan terpisah.

### 4.4 Role `Treasurer`/`Finance Manager`/`Chairman` (disebut README) belum ada di `RbacSeeder`
`ApprovalPolicy` di domain Financial mereferensikan role-role ini by-name, tapi `RbacSeeder` hanya punya `SUPER_ADMIN/ADMIN_MASJID/OPERATOR/VIEWER`. Di luar cakupan TASK-019A, tapi relevan untuk pemetaan RBAC granular finansial berikutnya.

---

## 5. Cara Deploy / Langkah Tim

1. Terapkan patch (`0001-TASK-019A-Security-Blocker-Remediation.patch`) ke branch `develop`.
2. Jalankan `composer install`, lalu `php spark migrate --all` (tidak ada migration baru — hanya perubahan seeder & kode).
3. **Jalankan ulang `RbacSeeder`** di environment staging/produksi (`php spark db:seed RbacSeeder`) agar permission `masjid.*`/`financial.manage` baru tersedia dan ter-assign ke `ADMIN_MASJID` — **tanpa langkah ini, admin non-Super-Admin akan mendapat 403 di modul Financial/Masjid** meski sudah login.
4. Jalankan `vendor/bin/phpunit tests/unit/AdminAccessControlTest.php` untuk konfirmasi di lingkungan masing-masing.
5. Uji manual: buka setiap URL `admin/*` dalam mode incognito, pastikan redirect ke `/login`.
6. Buat TASK-019B untuk item §4.1 (identity spoofing financial approval) **sebelum** modul Financial dianggap aman untuk data produksi riil.

---

## 6. Kesimpulan

Temuan kritis §3 laporan audit — **broken access control di seluruh panel admin** — sudah ditutup dan **diverifikasi bekerja lewat eksekusi HTTP sungguhan**, bukan hanya pembacaan kode. Ditemukan pula dua isu tambahan di luar cakupan laporan audit awal (endpoint approval keuangan tanpa filter, dan bug skema RBAC yang akan menyebabkan crash begitu filter diaktifkan) — keduanya sudah diperbaiki dalam paket yang sama. Satu isu residual (identity spoofing pada approval keuangan) didokumentasikan sebagai TASK-019B dan **wajib** dikerjakan sebelum modul Financial API dianggap sepenuhnya aman.
