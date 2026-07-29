# Audit Independen — TASK-019A (Authentication & RBAC Remediation)

**Repo:** GBU-Project/MasjidCMS · **Branch:** `develop` · **Commit diaudit:** `afb53d5`
**Metodologi:** Clone langsung dari GitHub, checkout persis ke `afb53d5`, verifikasi setiap klaim di kedua dokumen (`TASK-019A-IMPLEMENTATION-SUMMARY.md`, `TASK019A_SECURITY_BLOCKER_REMEDIATION.md`) terhadap source code aktual — bukan terhadap narasi dokumen. Sebagian klaim dieksekusi langsung (PHP CLI di sandbox audit) untuk pembuktian empiris, bukan hanya pembacaan statis.

---

## 1. Kesimpulan Singkat

TASK-019A **berhasil menutup celah broken-access-control utama** (panel admin, rute domain, endpoint financial API semuanya sekarang mewajibkan sesi login + permission). Namun proses "verifikasi end-to-end" yang diklaim di `TASK-019A-IMPLEMENTATION-SUMMARY.md` **tidak sepenuhnya bisa direproduksi dari isi repo**, dan audit ini menemukan **satu bug baru yang kritis dan belum tercatat**: mekanisme bypass Super Admin **rusak total**, yang berarti akun Super Admin — satu-satunya akun yang seharusnya bisa mengelola sistem — akan mendapat **403 di hampir semua aksi admin** begitu deployment ini berjalan dengan data nyata. Temuan §4.1 di dokumen (identity spoofing pada approval keuangan) juga **lebih parah** dari yang digambarkan: bukan cuma spoofing identitas, tapi role-gate persetujuannya sama sekali tidak pernah dieksekusi.

Patch minimal untuk kedua isu ini sudah saya buat dan dibuktikan bekerja (lihat §7 dan file patch terlampir).

---

## 2. Verifikasi Klaim per Item Audit

### 2.1 Authentication & RBAC — Valid, dengan catatan

- `Config/Routes.php`: grup `admin/*` dibungkus `['filter' => ['auth', 'rbac']]` — **terverifikasi**, 56 baris route terdaftar, permission granular (`rbac:financial.manage`, `rbac:admin.manage`) terpasang di rute mutasi data.
- `AuthenticationFilter`: redirect ke `/login` untuk request browser biasa, JSON 401 untuk `api/*`/AJAX — **sesuai kode**.
- `AuthorizationFilter`: `rbac` tanpa argumen = cukup login, Super Admin bypass — **logikanya benar**, tapi lihat §3.1: bypass-nya sendiri tidak pernah terpicu karena bug di layer lain.
- Halaman login (`AuthPageController`, `admin/auth/login.php`): ada, CSRF-protected (`csrf_field()`), rate-limited (5x/5menit via `Config\Services::throttler()`) — **terverifikasi**.

### 2.2 Penghapusan `app/Domains/Authorization` — Bersih, tidak ada dependency tertinggal

`grep -rn "Domains\\Authorization"` di seluruh codebase (di luar direktori itu sendiri) hanya menyisakan referensi ke `App\Filters\AuthorizationFilter` (nama kelas beda, domain baru) dan `App\Core\Exceptions\AuthorizationException` — keduanya bagian dari implementasi baru yang aktif dipakai, bukan sisa domain lama. **Tidak ada dead import atau broken reference.**

### 2.3 Routes/Filters/Controllers/Services/Repository/Middleware — Diverifikasi satu per satu

Semua rute domain (`jamaah.php`, `family.php`, `masjid.php`, `financial.php`) sudah memakai `['filter' => ['auth', 'rbac']]` + permission granular sesuai `permission_code` di `RbacSeeder`. `PermissionRepository`/`RoleRepository` sudah dibetulkan memakai `permission_code`/`role_code` sesuai skema migrasi asli (`2026-07-27-000003_CreateRbacTables.php`) — klaim bug schema-mismatch **benar dan sudah diperbaiki dengan tepat**.

### 2.4 Penyebab redirect ke `/public` di lokal (Item #4)

`app/Config/App.php` men-detect `baseURL` secara dinamis dari `SCRIPT_NAME`:
```php
$dir = rtrim(dirname($scriptName), '/\\');
$this->baseURL = $protocol . '://' . $host . ($dir ? $dir . '/' : '/');
```
Root `.htaccess` proyek melakukan *internal rewrite* `RewriteRule ^(.*)$ public/$1 [L]`. Pada konfigurasi Apache/XAMPP/Laragon yang umum (document root diarahkan ke folder proyek, bukan langsung ke `public/`), `SCRIPT_NAME` yang diterima PHP akan berisi `/public/index.php` — sehingga `dirname()` menghasilkan `.../public`, dan seluruh `baseURL` otomatis mendapat sisipan `/public/`. Setiap `redirect()->to()`, `site_url()`, `base_url()` (termasuk yang baru ditambahkan TASK-019A: `redirect()->to('/login')`, `redirect()->to('/admin/dashboard')`) ikut mengarah ke `.../public/login`, dst.

**Sebelum TASK-019A bug ini tidak pernah terlihat** karena aplikasi tidak pernah benar-benar melakukan redirect (akses selalu 200 mentah). TASK-019A adalah perubahan pertama yang benar-benar mengeksekusi jalur redirect ini secara luas — sehingga bug lama yang laten baru "muncul ke permukaan" sekarang. Ini **kemungkinan penjelasan paling masuk akal** untuk laporan "masih redirect ke /public" di lingkungan lokal.

*Catatan kejujuran metodologis:* saya tidak punya akses ke `packagist.org`/Composer di sandbox audit ini, sehingga tidak bisa menjalankan `php spark serve` sungguhan untuk mengonfirmasi perilaku Apache/mod_rewrite secara live. Kesimpulan ini didasarkan pada pembacaan kode + pengetahuan mekanisme `SCRIPT_NAME`/mod_rewrite CodeIgniter4 yang terdokumentasi luas, bukan pengujian HTTP langsung.

**Rekomendasi:** set `$baseURL` eksplisit lewat `.env` (`app.baseURL`), atau arahkan document root langsung ke folder `public/` sesuai rekomendasi resmi CodeIgniter4 — jangan mengandalkan root `.htaccess` sebagai forwarder di lingkungan produksi/staging.

### 2.5 Kompatibilitas skema database (`sort_order`, `balance`, `permission_code`, `role_code`)

- `permission_code`, `role_code`: **cocok** — dikonfirmasi terhadap migrasi asli, dan repository RBAC (setelah fix) memakainya dengan benar.
- `sort_order`, `balance`: dipakai di modul Financial/Homepage (`FinancialAccount`, `FundBalanceResponse`, homepage ordering) — **di luar cakupan perubahan TASK-019A** (tidak disentuh diff-nya sama sekali), tidak ada regresi yang ditemukan terkait kolom-kolom ini akibat task ini.
- Ditemukan **file legacy `database/seed.sql`** yang memakai skema RBAC yang sudah usang (`role_id`/`permission_id` integer, tabel `roles(id,name,description)` tanpa `role_code`) — **tidak sinkron** dengan migrasi CI4 aktual (UUID, `role_code`, `permission_code`). Berisiko jika tim salah pakai file SQL mentah ini alih-alih `php spark migrate` + `db:seed RbacSeeder` untuk provisioning. Di luar cakupan TASK-019A, tapi layak dibersihkan/dihapus agar tidak menyesatkan.

### 2.6 Regresi / broken dependency dari refactor

- Tidak ditemukan broken reference dari penghapusan `Authorization/*` maupun `Masjid/Routes/routes.php` (dead code lama).
- **Klaim regresi test "153→146" di dokumen tidak bisa direproduksi.** Saya hitung langsung jumlah method `public function test` di kedua commit via `git show` (bukan lewat working tree yang bisa ter-kontaminasi):
  - Baseline (`5b4c090`): **146** test methods (50 file test).
  - Current (`afb53d5`): **146** test methods (50 file test) — persis sama.
  - `diff` daftar file test antara dua commit hanya menunjukkan **2 perubahan**: `RbacFoundationRc1Test.php` (7 method) dihapus, `AdminAccessControlTest.php` (7 method) ditambah — net nol, bukan net turun 7 seperti diklaim ("153 → 146").

  Ini **tidak berarti ada regresi tersembunyi** — total tetap sama — tapi menunjukkan narasi verifikasi "dijalankan sungguhan via `php spark serve` + `curl`, 153→146" di dokumen **tidak konsisten dengan apa yang benar-benar ada di repo**. Rekomendasi: jangan menerima angka verifikasi di dokumen task begitu saja; minta tim menjalankan ulang test suite dan menempelkan output asli (bukan ringkasan naratif) sebagai bukti sebelum merge.

### 2.7 Reasoning sebagai persiapan production deployment

Selain dua bug kritis di §3, saya juga menemukan (di luar cakupan TASK-019A tapi relevan untuk kesiapan deploy):

- **`AdminSeeder::createAdmin()`** (dipakai wizard `install/admin`) **tidak pernah menulis ke database** — hanya mengembalikan array `['success' => true, 'user' => [...]]` lalu controller langsung membuat install-lock dan redirect ke halaman selesai. Artinya **instalasi wizard produksi tidak pernah benar-benar membuat akun admin yang bisa login**. Digabung dengan bug §3.1, tim yang mengikuti instruksi deploy resmi (`docs/milestones/...`: "jalankan installer, pastikan ada akun Super Admin") kemungkinan besar akan berakhir dengan sistem yang terkunci total — tidak ada satupun akun yang bisa login setelah instalasi fresh.
- Duplikasi rute `admin/media` (dua controller berbeda didaftarkan untuk path sama) — sudah didokumentasikan sebagai pre-existing di kedua doc, dikonfirmasi masih ada, di luar cakupan keamanan.

---

## 3. Bug Kritis Baru (Belum Tercatat di Dokumen Task)

### 3.1 🔴 Super Admin bypass rusak total — memblokir akses Super Admin sendiri

**Rantai bug (3 lapis, saling menutupi):**

1. **`AuthenticationRepository::mapToEntity()`** membaca kolom `roles`/`permissions` dari tabel `users` — padahal tabel `users` (migrasi `CreateRbacTables`) **tidak punya kolom itu sama sekali**. Hasilnya: `$user->roles` dan `$user->permissions` **selalu array kosong** untuk setiap user, disimpan ke sesi saat login (`SessionService::login()`), dan tetap kosong sepanjang umur sesi (`SessionService::currentUser()` tidak pernah query ulang ke DB).
2. **`AuthenticatedUser::isSuperAdmin()`** mengecek literal string `'superadmin'`/`'admin'` di `$this->roles` — padahal `RbacSeeder` memakai `role_code` `'SUPER_ADMIN'` (uppercase, underscore). Bahkan seandainya bug #1 tidak ada, string-nya tetap tidak akan pernah cocok.
3. **`RbacSeeder`** tidak pernah memberi baris `role_permissions` apa pun ke role `SUPER_ADMIN` — desainnya sengaja bergantung 100% pada bypass `isSuperAdmin()` di `AuthorizationFilter`.

**Efek gabungan:** `AuthorizationFilter::before()` memanggil `$user->isSuperAdmin()` → selalu `false` untuk Super Admin manapun → jatuh ke `$this->rbacService->authorize($user, $requiredPermission)` untuk rute `rbac:<permission>` → `DatabasePermissionProvider::getPermissions()` query DB by user id → mengembalikan **array kosong** (karena tidak ada `role_permissions` untuk SUPER_ADMIN) → **403 Forbidden**.

**Dampak nyata:** akun Super Admin — akun tunggal yang (secara desain) seharusnya bisa mengelola segalanya — akan mendapat 403 pada **setiap** aksi yang memakai `rbac:<permission>`: simpan/ubah/hapus Master Data, semua aksi Financial (termasuk create/approve/reject/post/void transaksi), simpan CMS, Settings, Menu, Media, Notification. Rute tanpa argumen (`rbac` polos, mis. `admin/dashboard`) tetap bisa diakses karena filter tidak mengecek permission sama sekali di jalur itu — sehingga bug ini **tidak akan terlihat di skenario uji dangkal** ("login lalu buka dashboard: 200 OK, kelihatan berhasil"), tapi meledak begitu Super Admin mencoba melakukan aksi CRUD apa pun.

**Bukti empiris (dieksekusi, bukan dibaca statis):**
```
[BEFORE PATCH] SUPER_ADMIN role -> isSuperAdmin() true            : FAIL (CONFIRMS BUG)
[BEFORE PATCH] SUPER_ADMIN role -> hasPermission(financial.manage): FAIL (CONFIRMS BUG)
```
Dijalankan langsung dengan `php -r` terhadap `AuthenticatedUser.php` versi `afb53d5` — bukan asumsi.

**Kenapa ini lolos dari "verifikasi end-to-end" yang diklaim dokumen:** skenario uji di dokumen (`User role Operator → admin/users: 403 rapi`) hanya menguji role non-Super-Admin yang memang seharusnya 403. Tidak ada skenario yang menguji **Super Admin mencoba aksi ber-permission dan seharusnya lolos** — celah pengujian ini persis yang menyembunyikan bug di atas.

### 3.2 🔴 Financial approval role-gate tidak pernah aktif (lebih parah dari §4.1 di dokumen)

Dokumen menyebut ini sebagai "identity spoofing" (approver_user_id bisa dipalsukan). Audit ini menemukan masalahnya lebih dalam: **`FinancialApiController::approve()`/`reject()`** memanggil `ApproveTransactionApplicationService`/`RejectTransactionApplicationService`, yang langsung memanggil `$transaction->approve($approverUserId)` **tanpa melalui `ApprovalPolicy` sama sekali**.

Ada implementasi yang benar — `ApprovalWorkflowService` — yang memang memanggil `ApprovalPolicy::assertCanApprove()` (membatasi ke role Treasurer/Finance Manager/Chairman/Super Admin), tapi service ini **orphan**: hanya direferensikan dari test RC1 (`FinancialApprovalWorkflowRc1Test.php`, `FinancialModuleIntegrationRc1Test.php`), tidak pernah di-wire ke controller produksi manapun.

**Dampak:** siapa pun dengan permission `financial.manage` (yaitu **seluruh** akun `ADMIN_MASJID`, bukan cuma Treasurer/Finance Manager/Chairman) bisa approve/reject transaksi apa pun. Menambahkan filter `auth`+`rbac:financial.manage` (yang sudah dilakukan TASK-019A) menutup akses anonim, tapi **tidak menutup akses dari akun yang sah tapi tidak berwenang secara bisnis**.

**Kenapa saya tidak mengaktifkan `ApprovalPolicy` langsung sebagai patch:** role yang dicek (`Treasurer`, `Finance Manager`, `Chairman`) **tidak ada** di `RbacSeeder` — hanya `SUPER_ADMIN/ADMIN_MASJID/OPERATOR/VIEWER`. Ini persis item §4.4 yang sudah diidentifikasi dokumen sebagai keputusan terbuka. Memaksa `ApprovalPolicy` aktif sekarang akan mengunci **semua** akun (tidak ada yang punya role tsb) — regresi fungsional besar. Ini keputusan produk/taksonomi role yang butuh pembahasan tim, konsisten dengan keputusan dokumen untuk memisahkannya sebagai TASK-019B.

Yang saya patch minimal (aman, tidak butuh keputusan taksonomi): **identity spoofing murni** — `approver_user_id`/rejecter sekarang diambil paksa dari `SecurityContext::user()->id` (sesi login), bukan dari body JSON client. Ini menutup vektor serangan paling langsung (mengatasnamakan user lain) tanpa menyentuh keputusan role-mapping yang belum diambil tim.

---

## 4. Uji Regresi Sanity Check (angka test)

| | Baseline `5b4c090` | Current `afb53d5` |
|---|---|---|
| Jumlah file test | 50 | 50 |
| Jumlah `public function test` | **146** | **146** |
| File berubah | — | `-RbacFoundationRc1Test.php` (7), `+AdminAccessControlTest.php` (7) |

Klaim dokumen "153 test dijalankan sebelum & sesudah, turun 153→146" **tidak match** dengan isi repo. Lihat §2.6.

---

## 5. Patch Minimal yang Diterapkan

File patch terlampir: `0001-TASK-019A-Audit-Hotfix-SuperAdmin-and-Financial-Approval.patch` (unified diff, siap `git apply`).

| File | Perubahan |
|---|---|
| `app/Domains/System/Entities/AuthenticatedUser.php` | `isSuperAdmin()` sekarang mencocokkan `role_code` sesungguhnya (`SUPER_ADMIN`, case-insensitive), bukan literal lama yang tidak pernah cocok. |
| `app/Domains/System/Repositories/AuthenticationRepository.php` | `mapToEntity()` mengisi `roles`/`permissions` dari tabel RBAC sesungguhnya (via `RoleRepository`/`PermissionRepository`) alih-alih kolom `users.roles`/`users.permissions` yang tidak ada. |
| `app/Database/Seeds/RbacSeeder.php` | Menambahkan `role_permissions` eksplisit untuk `SUPER_ADMIN` sebagai jaring pengaman defense-in-depth — Super Admin tetap berfungsi walau bypass bermasalah lagi di kemudian hari. |
| `app/Controllers/Api/FinancialApiController.php` | `approve()`/`reject()` mengambil identitas approver dari `SecurityContext::user()->id` (sesi login), bukan dari body JSON client — menutup identity spoofing. |
| `tests/unit/SuperAdminBypassRegressionTest.php` (baru) | 3 test unit murni (tanpa DB) mengunci perilaku `isSuperAdmin()`/`hasPermission()` — bisa jalan di environment manapun termasuk tanpa database aktif. |

**Yang sengaja TIDAK dipatch** (butuh keputusan tim, bukan patch minimal): pengaktifan `ApprovalPolicy` role-gate di jalur approve/reject (§3.2) — menunggu taksonomi role Treasurer/Finance Manager/Chairman diselesaikan (TASK-019B), dan bug `AdminSeeder`/instalasi wizard (§2.7) — di luar cakupan TASK-019A, perlu task terpisah.

**Verifikasi patch:**
- `php -l` bersih di semua file yang diubah.
- Perilaku sebelum/sesudah patch dieksekusi langsung (bukan cuma dibaca) — lihat bukti empiris di §3.1.
- Satu-satunya call site `new AuthenticationRepository()` (`DatabaseIdentityProvider.php`) dicek kompatibel dengan constructor baru (semua parameter opsional).

---

## 6. Rekomendasi Sebelum Deploy Produksi

1. **Terapkan patch terlampir** — tanpa ini, Super Admin akan terkunci dari hampir semua aksi begitu data nyata masuk.
2. **Jangan gunakan `AdminSeeder`/wizard installer untuk membuat akun pertama** sampai bug §2.7 diperbaiki — gunakan `php spark db:seed RbacSeeder` + insert manual user pertama dengan role SUPER_ADMIN, atau perbaiki `AdminSeeder::createAdmin()` agar benar-benar menulis ke `users`+`user_roles`.
3. **Set `app.baseURL` eksplisit** di `.env` produksi (jangan andalkan auto-detect), untuk menghindari bug `/public` di §2.4.
4. **Minta tim menjalankan ulang test suite dengan output asli terlampir** sebelum percaya klaim "153→146, zero regresi" — angka itu tidak match dengan isi repo.
5. **Jangan anggap Financial API aman untuk approval nyata** sampai taksonomi role (Treasurer/Finance Manager/Chairman) diselesaikan dan `ApprovalPolicy` benar-benar di-wire ke controller (TASK-019B) — patch saat ini hanya menutup identity spoofing, bukan role-gate bisnisnya.
6. Bersihkan/hapus `database/seed.sql` legacy yang skema-nya tidak sinkron dengan migrasi aktual, agar tidak menyesatkan proses provisioning.
