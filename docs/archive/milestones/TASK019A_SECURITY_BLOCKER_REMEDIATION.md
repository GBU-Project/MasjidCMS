# TASK-019A — 🔴 Security Blocker Remediation (Broken Access Control)

**Status:** Implemented (siap review & deploy)
**Priority:** Critical / Blocker
**Sumber temuan:** `Laporan_Audit_IT_MasjidCMS_Independen.md` (29 Jul 2026), §3

---

## Background

Audit IT independen menemukan bahwa **seluruh panel admin MasjidCMS — termasuk modul Keuangan, User/RBAC, dan Settings — dapat diakses tanpa login oleh siapa pun.** Filter keamanan (`AuthenticationFilter`, `AuthorizationFilter`) sudah dirancang dan diimplementasikan dengan benar, tapi tidak pernah "di-wiring" ke rute yang benar-benar aktif. Rute login (`auth/login`) juga tidak terdaftar, dan **tidak ada halaman login HTML sama sekali** — hanya JSON API tanpa antarmuka.

## Root Cause

1. `app/Config/Filters.php` → `public array $filters = [];` — kosong, filter global tidak pernah dipasang.
2. `app/Config/Routes.php` — 60+ rute `admin/*` didaftarkan polos tanpa parameter `filter`.
3. `app/Domains/Masjid/Routes/routes.php` — implementasi filter yang **benar** sempat dibuat, tapi file ini tidak pernah `require`, jadi jadi dead code yang tidak berpengaruh apa pun.
4. `app/Domains/System/Routes/auth.php` — berisi rute `auth/login`/`logout`/`refresh`, juga tidak pernah `require`.
5. Tidak ada view/controller yang merender halaman login HTML — `AuthenticationController::login()` yang ada hanya mengembalikan JSON, tidak bisa diakses lewat browser secara wajar.
6. Rute domain `jamaah/`, `family/`, `masjid/` (API, prefix non-`admin/`) serta `api/financial/*` (approve/reject/post/void transaksi) juga tanpa filter — ditemukan saat verifikasi ulang, memperluas cakupan dari yang disebutkan di laporan audit.

---

## Perubahan

### 1. `app/Config/Routes.php`
- Seluruh rute `admin/*` (Dashboard, Master Data, Financial Workspace, Reporting, CMS, Homepage Manager, Settings, Menu, Media, Notification) dibungkus:
  ```php
  $routes->group('', ['filter' => ['auth', 'rbac']], static function ($routes) { ... });
  ```
  Prefix group sengaja dikosongkan (`''`) karena path masing-masing rute sudah memuat `admin/...` secara eksplisit — menghindari risiko salah tulis ulang 60+ baris path yang sudah ada.
- Ditambahkan 2 rute baru **di luar** grup filter (harus tetap publik): `GET admin/login`, `GET admin/logout`.
- Ditambahkan `require APPPATH . 'Domains/System/Routes/auth.php';` — mengaktifkan `POST auth/login`, `POST auth/logout`, `POST auth/refresh` yang sebelumnya tidak terdaftar.

### 2. Halaman Login Admin (baru — sebelumnya tidak ada sama sekali)
- `app/Controllers/AuthPageController.php` — `login()` merender form, redirect ke dashboard kalau sudah login; `logout()` membersihkan sesi lalu redirect ke halaman login.
- `app/Views/auth/login.php` — form HTML sederhana + `fetch()` ke `POST auth/login` (JSON API yang sudah ada), redirect ke `admin/dashboard` saat sukses, tampilkan pesan error saat gagal.
- `app/Views/layouts/admin.php` — ditambahkan link **"🚪 Keluar"** di header (sebelumnya tidak ada jalur logout sama sekali dari UI).

### 3. `app/Filters/AuthenticationFilter.php`
- Sebelumnya: request tanpa sesi selalu dijawab JSON 401 mentah, termasuk untuk navigasi browser biasa.
- Sekarang: request yang mengharapkan JSON (AJAX/`Accept: application/json`/`api/*`) tetap dapat JSON 401; request browser biasa **di-redirect ke `admin/login`** — pengalaman yang jauh lebih wajar untuk aplikasi berbasis HTML.

### 4. Proteksi rute domain di luar `admin/*`
- `app/Domains/Jamaah/Routes/jamaah.php`, `Family/Routes/family.php`, `Masjid/Routes/masjid.php` — grup rute masing-masing diberi `['filter' => ['auth', 'rbac']]`.
- `app/Domains/Financial/Routes/financial.php` (`api/financial/*` — approve/reject/post/void transaksi) — **temuan tambahan yang belum tercatat di laporan audit**, sekarang juga diberi filter yang sama.

### 5. Pembersihan Dead Code (rekomendasi #3 dan §5.3 laporan audit)
- Dihapus: `app/Domains/Masjid/Routes/routes.php` (implementasi filter yang benar tapi tidak pernah dieksekusi, membingungkan).
- Dihapus: `app/Domains/Authorization/Filters/AuthorizationFilter.php` (duplikat class yang tidak kompatibel dengan pipeline `SecurityContext` yang aktif, dan tidak pernah direferensikan di manapun — diverifikasi lewat `grep` sebelum dihapus).

### 6. Defense-in-Depth Tambahan (rekomendasi §5.2)
- `writable/uploads/.htaccess` baru — menonaktifkan eksekusi PHP di direktori upload, sebagai lapisan kedua di luar validasi MIME type yang sudah ada di `AdminMediaController`.

### 7. Test Regresi Baru
`tests/unit/AdminAccessControlTest.php` (7 test):
- Verifikasi statis: semua rute `admin/*` (kecuali login/logout) berada di dalam grup filter `auth`+`rbac` — divalidasi manual terhadap source code sungguhan (74 rute admin ditemukan, 0 di luar grup proteksi).
- Verifikasi rute login/logout tetap publik.
- Verifikasi `auth.php` benar-benar di-`require`.
- Verifikasi grup rute `jamaah`/`family`/`masjid` dan `api/financial/*` memakai filter.
- Verifikasi dead code `AuthorizationFilter` duplikat sudah tidak ada.
- 1 test HTTP-level (`FeatureTestTrait`) yang memverifikasi request tanpa sesi ke `admin/dashboard` tidak pernah mengembalikan HTTP 200 — di-skip otomatis (bukan gagal) kalau lingkungan test tidak punya database aktif, alih-alih membuat seluruh suite merah karena masalah infrastruktur yang tidak terkait.

---

## Known Limitation / Catatan Verifikasi

1. **Verifikasi dijalankan lewat pembacaan source code + simulasi regex manual** di lingkungan audit (tidak ada MySQL live + `php spark serve` yang benar-benar dijalankan). Test HTTP-level (`testUnauthenticatedRequestToAdminDashboardIsNotServedDirectly`) sudah ditulis mengikuti konvensi `FeatureTestTrait` yang ada di proyek, tapi **belum bisa saya jalankan end-to-end** di sandbox ini — perlu dijalankan tim di lingkungan dengan DB aktif sebelum merge.
2. **Permission granular per-route (`rbac:financial.create`, dst.) belum ditambahkan** — task ini fokus menutup lubang akses (siapapun bisa masuk tanpa login sama sekali). Begitu filter `auth`+`rbac` aktif, Super Admin otomatis bypass semua (`AuthorizationFilter::isSuperAdmin()`), tapi role non-Super-Admin akan diblokir `rbac` filter untuk SEMUA rute admin karena belum ada argumen permission spesifik per-route (`['filter' => 'rbac:xxx']`) seperti pola di contoh dead code sebelumnya. **Tim perlu memetakan permission granular per modul** sebagai task lanjutan (TASK-019B), atau untuk sementara pastikan seluruh akun admin yang dipakai berstatus Super Admin sampai granular RBAC selesai dipetakan.
3. **`cookie.secure = true`** belum di-hardcode ke `Config/Cookie.php` (bisa merusak local dev tanpa HTTPS) — direkomendasikan diset lewat `.env` produksi saja, sesuai saran §5.1 laporan audit.
4. Rute `install/*` sengaja **tidak** diberi filter — ini alur bootstrap sebelum ada user/sesi sama sekali, jadi mewajibkan login di sana akan membuat instalasi awal mustahil dilakukan. Perlu diperhatikan agar `InstallerController` sendiri punya pengaman lain (mis. menolak jalan kalau instalasi sudah pernah selesai) — di luar cakupan TASK-019A ini.
5. Ditemukan (di luar cakupan tapi dicatat untuk referensi): route `admin/media` didaftarkan dua kali dengan controller berbeda (`AdminMediaController::index` dan `AdminSystemWorkspaceController::index`) — bug pre-existing yang tidak diubah karena di luar scope keamanan task ini, namun sebaiknya dibersihkan terpisah.

---

## Kesimpulan

Temuan kritis §3 pada laporan audit independen — **broken access control di seluruh panel admin** — sudah ditutup: setiap rute `admin/*`, ditambah rute domain `jamaah/family/masjid` dan `api/financial/*`, sekarang wajib melalui filter `auth` (verifikasi sesi) dan `rbac` (verifikasi permission, dengan Super Admin bypass). Halaman login admin yang sebelumnya sama sekali tidak ada kini tersedia di `admin/login`, lengkap dengan jalur logout dari UI.

**Sebelum rilis produksi**, tim WAJIB:
1. Menjalankan test suite (termasuk `AdminAccessControlTest`) di lingkungan dengan DB aktif.
2. Melakukan pengecekan manual singkat: buka setiap URL `admin/*` dalam mode incognito/logged-out, pastikan semuanya redirect ke `admin/login`, bukan menampilkan konten.
3. Memastikan setidaknya satu akun Super Admin tersedia dan bisa login, sebelum granular permission per-route (TASK-019B) dikerjakan.
