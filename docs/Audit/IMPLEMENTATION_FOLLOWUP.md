# MasjidCMS Audit — Implementation Follow-up

Dikerjakan sesuai urutan: **verifikasi dulu → Critical → High → Medium**, tanpa mengubah arsitektur/workflow berdasarkan dugaan.

## Batasan lingkungan (penting)
Sandbox ini **tidak memiliki PHP, Composer, atau MySQL**, dan `packagist.org` tidak ada di daftar domain yang diizinkan — sehingga `composer install` dan `phpunit` **tidak bisa dijalankan di sini**. Karena itu, semua perubahan pada iterasi ini sengaja dibatasi pada **penambahan file (tidak menyentuh `app/` atau `tests/`)**, sehingga risiko regresi terhadap kode aplikasi = nihil, dan tidak melanggar aturan "jalankan test setelah tiap perubahan" secara substansi (tidak ada kode yang berubah untuk diuji). Perubahan yang butuh eksekusi PHPUnit sungguhan (lihat bagian "Belum dikerjakan") **belum** dilakukan — itu harus dikerjakan di environment dengan PHP+Composer+DB (mis. lewat Claude Code lokal atau CI yang baru ditambahkan ini sendiri).

---

## 1. Verifikasi temuan [Unverified] — hasil

| Temuan audit sebelumnya | Status setelah verifikasi source-level | Bukti |
|---|---|---|
| **Critical #1**: Auth API Financial belum tentu independen dari CSRF exemption | ✅ **Sudah aman — tidak perlu perbaikan.** Route group `api/financial` memakai filter `['auth', 'rbac:financial.manage']` (`app/Domains/Financial/Routes/financial.php`), dan `AuthenticationFilter` mengembalikan 401 untuk request `api/*` yang tidak terautentikasi. | Dibaca langsung dari `AuthenticationFilter.php` dan `financial.php` |
| **Critical #2**: Rate-limiting login diklaim di ROADMAP tapi tidak ketemu grep "Throttler" | ✅ **Sudah diimplementasikan — false positive audit sebelumnya** (grep sebelumnya salah pola). `Throttler` dipakai di `AuthPageController::login()` **dan** `App\Domains\System\Controllers\AuthenticationController`, keduanya membatasi 5 percobaan / 5 menit per IP+username. | `AuthPageController.php:62-66`, `AuthenticationController.php:39-46` |
| **High**: kemungkinan SQL Injection via raw query | ✅ **Aman.** Seluruh pemanggilan `->query()` di codebase memakai placeholder `?` (parameterized), tidak ada string concatenation. | grep menyeluruh `app/**/*.php` |
| **Medium**: kemungkinan secret (`.env`) pernah ter-commit | ✅ **Tidak ditemukan** di riwayat git branch `develop`. | `git log --all -- .env` kosong |
| **High**: tidak ada CI (`.github/workflows`) | ❌ **Konfirmasi: benar-benar tidak ada.** → **Diperbaiki** (lihat §2). |  |
| **Medium**: tidak ada `CONTRIBUTING.md` / issue-PR template | ❌ **Konfirmasi: benar-benar tidak ada.** → **Diperbaiki** (lihat §2). |  |
| **High**: controller >600 baris (4 file) | ❌ **Konfirmasi: benar, ukurannya sesuai temuan awal** (739/677/674/606 baris). → **Sengaja TIDAK di-refactor** pada iterasi ini (lihat §3 — alasan aturan #3 & #4). |  |
| **High**: inkonsistensi lokasi controller domain Financial | ❌ **Konfirmasi: benar**, `Financial` tidak punya `Controllers/` sendiri seperti domain lain. → **Sengaja TIDAK dipindahkan** pada iterasi ini (lihat §3). |  |
| **Medium**: constraint DB untuk double-entry hanya di level aplikasi? | ⚠️ **Belum bisa diverifikasi** tanpa membaca `database/schema.sql` baris-demi-baris terhadap ERD dan tanpa DB live untuk uji constraint aktual — di luar cakupan aman untuk diputuskan lewat asumsi. Tidak diubah. |  |
| **Low**: bundling/minifikasi asset (3.0MB) | ⚠️ **Belum bisa diverifikasi** — perlu konfirmasi apakah ada build tool (Vite/Webpack) yang tidak ter-commit confignya, atau memang asset disajikan mentah. Tidak diubah. |  |
| **Accessibility (ARIA, contrast, focus)** | ⚠️ **Tidak bisa diverifikasi secara statis** — perlu instance live + axe/Lighthouse. Tidak diubah. |  |

---

## 2. Perubahan yang diimplementasikan (aman, tanpa risiko regresi kode)

Semua item ini adalah **penambahan file baru**, tidak menyentuh `app/`, `tests/`, atau perilaku runtime apa pun:

1. **`.github/workflows/ci.yml`** — menjalankan `composer validate`, `composer install`, dan `composer test` (phpunit) pada setiap push/PR ke `main`/`develop`. Sengaja **tidak** menebak perintah migrasi database (`spark migrate ...`) yang tepat karena itu belum diverifikasi — dikomentari eksplisit di file, merujuk ke `INSTALLATION.md` sebagai sumber kebenaran. Maintainer perlu mengisi bagian setup DB di CI sesuai proses instalasi resmi.
2. **`CONTRIBUTING.md`** — panduan kontribusi, mewajibkan test lulus sebelum merge, melarang perubahan arsitektur/workflow spekulatif (menegaskan pola yang sama seperti aturan yang Anda berikan), dan mendokumentasikan pola auth untuk endpoint API baru (`auth` + `rbac:<permission>`).
3. **`.github/ISSUE_TEMPLATE/bug_report.md`** dan **`feature_request.md`**, serta **`.github/PULL_REQUEST_TEMPLATE.md`** — checklist eksplisit termasuk "tidak ada perubahan arsitektur/workflow yang tidak terkait" dan "dokumentasi diperbarui jika ada perubahan arsitektur/keamanan/instalasi".
4. **`SECURITY.md`** diperbarui — menambahkan bagian "Verified Controls" yang mendokumentasikan hasil verifikasi CSRF, auth API, rate-limiting, dan SQLi di atas, sehingga status "Unverified" dari audit sebelumnya sekarang tercatat resmi sebagai terverifikasi (bukan klaim, tapi hasil pembacaan source).

### Mengapa tidak ada "run test" di sini
Karena keempat perubahan di atas murni penambahan dokumentasi/CI config dan tidak mengubah satu pun file di `app/` atau `tests/`, tidak ada perilaku aplikasi yang bisa regresi. Begitu PR ini dibuka di GitHub, workflow CI yang baru ditambahkan akan otomatis menjalankan `composer test` — ini adalah cara paling jujur untuk memenuhi aturan #4 di lingkungan yang tidak punya PHP runtime.

---

## 3. Yang SENGAJA belum dikerjakan (sesuai aturan #3)

- **Split controller >600 baris** (`AdminFinancialWorkspaceController`, `PublicPortalController`, `AdminMasterDataController`, `AdminCmsWorkspaceController`) — ini murni benar sebagai smell, tapi memecahnya adalah **refactor perilaku-sensitif** (routing, DI, kemungkinan state tersembunyi) yang **wajib** diverifikasi dengan test suite berjalan sebelum dan sesudah. Tidak bisa dilakukan bertanggung jawab tanpa PHP/PHPUnit di sini — mengerjakannya sekarang berarti menebak. **Rekomendasi:** kerjakan satu controller per PR di environment dengan CI aktif (yang baru saja ditambahkan), mulai dari `AdminFinancialWorkspaceController` (paling berisiko tinggi karena finansial).
- **Memindahkan controller Financial ke `app/Domains/Financial/Controllers/`** — sama alasannya: perubahan namespace/route berisiko regresi routing dan harus diuji, bukan diasumsikan aman.
- **Constraint DB tambahan untuk double-entry** — memerlukan pembacaan detail `schema.sql` vs `ERD.md` dan idealnya migrasi teruji di DB nyata; di luar cakupan yang bisa dipastikan aman di iterasi ini.

---

## 4. Rekomendasi langkah selanjutnya

1. Buka PR dari patch/file terlampir → biarkan CI yang baru berjalan sekali untuk mengonfirmasi `composer test` benar-benar hijau di baseline saat ini (sebelum refactor apa pun).
2. Baru setelah CI hijau dan stabil, kerjakan split controller satu per satu, masing-masing sebagai PR terpisah dengan test sebelum/sesudah dibandingkan.
3. Untuk item accessibility, asset bundling, dan constraint DB — butuh instance live atau pembacaan mendalam tambahan; sarankan sesi kerja terpisah karena masing-masing perlu tooling (axe/Lighthouse) atau akses DB yang tidak tersedia di audit statis ini.

---

## File yang disertakan
- `masjidcms-audit-followup.patch` — git diff berisi seluruh perubahan di atas, siap di-apply (`git apply`) ke branch `develop`.
- `CONTRIBUTING.md`, `SECURITY.md` (versi diperbarui), dan folder `github-additions/` (isi `.github/`) sebagai file lepas untuk ditinjau langsung.
