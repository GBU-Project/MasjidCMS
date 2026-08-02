# LAPORAN AUDIT TEKNIS (IT AUDIT REPORT)
### MasjidCMS — Aplikasi Manajemen Masjid berbasis CodeIgniter 4
*Review Arsitektur, Keamanan, dan Kualitas Kode*

**Tanggal Audit:** 27 Juli 2026
**Disusun oleh:** Peninjau Teknis (IT Audit Assistant)

---

## 1. Ringkasan Eksekutif

Audit ini dilakukan terhadap source code proyek MasjidCMS, sebuah aplikasi manajemen masjid yang dibangun di atas framework CodeIgniter 4 (PHP 8.2+) dengan pendekatan Domain-Driven Design (DDD). Cakupan review meliputi: arsitektur perangkat lunak, keamanan aplikasi (autentikasi, otorisasi, proteksi CSRF, penanganan file, penyimpanan kredensial), integritas modul finansial (double-entry ledger), serta kelengkapan pengujian otomatis (unit/integration test).

Secara umum, kualitas rekayasa perangkat lunak pada proyek ini berada di atas rata-rata untuk ukuran proyek serupa: pemisahan lapisan domain yang konsisten (Controller → Service → Repository → Entity), penggunaan Query Builder secara menyeluruh (tidak ditemukan satu pun raw SQL query rawan injeksi), hashing password menggunakan bcrypt yang benar, serta dokumentasi arsitektur (`docs/`) yang jauh lebih lengkap dibanding proyek CI4 pada umumnya.

Namun demikian, ditemukan sejumlah celah yang perlu mendapat perhatian sebelum aplikasi ini dinyatakan siap produksi (*production-ready*), terutama terkait proteksi CSRF yang dinonaktifkan secara global, tidak adanya rate-limiting pada endpoint login, adanya class duplikat pada lapisan otorisasi yang berisiko menimbulkan kebingungan/inkonsistensi keamanan di masa depan, dan potensi race condition pada mesin posting keuangan.

### Ringkasan Skor Temuan

| Kategori | Kritis/Tinggi | Sedang | Rendah/Info |
|---|---|---|---|
| Keamanan (Security) | **3** | 1 | 1 |
| Integritas Data / Finansial | **1** | 0 | 0 |
| Kualitas Kode & Maintainability | 0 | 2 | 1 |
| Testing & QA | 0 | 1 | 0 |

---

## 2. Ruang Lingkup & Metodologi

### 2.1 Ruang Lingkup

- Static code review terhadap seluruh source code aplikasi (folder `app/`), tanpa akses ke server produksi, database live, atau environment `.env` sesungguhnya.
- Review dilakukan terhadap struktur folder `app/Core`, `app/Domains` (Masjid, Jamaah, Family, Financial, System, Authorization), `app/Config`, konfigurasi build (`composer.json`), serta dokumentasi teknis di folder `docs/`.
- Tidak termasuk: penetration testing aktif (dynamic testing), review infrastruktur server/hosting, review dependency CVE secara mendalam (`composer.lock` tidak di-scan dengan tool khusus seperti Composer Audit/Snyk dalam sesi ini).

### 2.2 Metodologi

- Ekstraksi dan inventarisasi struktur proyek (290+ file PHP, 6 bounded context domain, 14 migration, 19 file test).
- Pencarian pola berisiko: raw SQL query, penggunaan superglobal langsung, output tanpa escaping, hardcoded credential, konfigurasi default framework yang belum di-harden.
- Penelusuran alur autentikasi dan otorisasi end-to-end (Filter → Service → Repository).
- Penelusuran alur bisnis kritikal (posting transaksi keuangan / double-entry) untuk memastikan atomicity dan konsistensi data.
- Perbandingan cakupan pengujian otomatis terhadap modul-modul inti.

---

## 3. Aspek Positif (Kekuatan Proyek)

Sebelum masuk ke temuan, penting untuk mengapresiasi sejumlah praktik rekayasa yang sudah diterapkan dengan baik:

- **Arsitektur Domain-Driven Design yang konsisten** — setiap domain bisnis (Masjid, Jamaah, Family, Financial, System, Authorization) memiliki lapisan Controller, Service, Repository, Entity, DTO, dan Policy sendiri yang terpisah rapi.
- Tidak ditemukan satu pun penggunaan raw SQL query (`->query()`) di seluruh codebase — seluruhnya menggunakan Query Builder CodeIgniter, sehingga risiko SQL Injection klasik sangat rendah.
- Password disimpan menggunakan `password_hash()`/`password_verify()` (bcrypt) dengan cost yang dapat dikonfigurasi, dilengkapi mekanisme `needsRehash()` untuk migrasi cost/algoritma di masa depan.
- Modul finansial menerapkan pola Double-Entry Bookkeeping yang benar (`JournalBuilder`, `PostingValidator`, `PostingPolicy`) dengan Unit of Work untuk membungkus operasi dalam transaksi database (`transBegin`/`transCommit`/`transRollback`).
- Event-driven Audit Log Engine (`app/Core/Audit`) yang didesain terpisah dari domain bisnis — pencatatan audit terjadi setelah commit transaksi berhasil, mengurangi risiko log palsu.
- Dokumentasi arsitektur (`docs/`) sangat lengkap — mencakup spesifikasi domain, model data finansial, matriks permission, dan arsitektur software — jarang ditemukan pada proyek dengan skala serupa.
- File `.env` template (`env`) tidak berisi kredensial sungguhan yang bocor ke repository.

---

## 4. Temuan Detail

### 4.1. Proteksi CSRF Dinonaktifkan Secara Global

| | |
|---|---|
| **Tingkat Risiko** | 🔴 **TINGGI** |
| **Area** | Keamanan — `app/Config/Filters.php` |

Filter `csrf` terdaftar sebagai alias pada `Config\Filters`, namun pada array `$globals` (before/after) baris tersebut dikomentari (dinonaktifkan), begitu pula `secureheaders`. Hasil pencarian menyeluruh terhadap seluruh file Routes (termasuk domain Masjid, Jamaah, Family, Financial, System) juga tidak menemukan satu pun pemasangan filter `csrf` secara eksplisit per-route. Sementara itu, autentikasi aplikasi terkonfirmasi berbasis session/cookie (`AuthenticationService` → `SessionService`), bukan token bearer stateless.

**Bukti / Lokasi Kode:**
```
app/Config/Filters.php — $globals['before'] = [ // 'csrf', ... ] (dikomentari)
app/Domains/Masjid/Routes/routes.php — filter yang dipasang hanya ['auth','rbac'], tanpa 'csrf'
```

**Dampak:** Karena autentikasi mengandalkan cookie sesi, endpoint yang mengubah data (POST/PUT/DELETE, mis. create/update/delete Masjid, transaksi keuangan) berpotensi dieksploitasi melalui Cross-Site Request Forgery apabila diakses dari browser yang memiliki sesi aktif dan halaman pemicu berada di domain pihak ketiga yang berbahaya.

**Rekomendasi:**
- Aktifkan filter `csrf` secara global pada `$globals['before']`, atau pasang secara eksplisit pada seluruh route yang menggunakan verb POST/PUT/DELETE/PATCH.
- Jika aplikasi memang didesain sebagai pure JSON API yang HANYA diakses via header custom (mis. `Authorization: Bearer`) dan bukan cookie, migrasikan autentikasi ke token-based (JWT/Sanctum-like) agar CSRF secara arsitektural tidak relevan — namun ini perlu keputusan sadar, bukan celah yang tidak disengaja.
- Aktifkan kembali filter `secureheaders` untuk menambahkan header keamanan standar (X-Frame-Options, X-Content-Type-Options, dll).

### 4.2. Tidak Ada Rate Limiting / Lockout pada Endpoint Login

| | |
|---|---|
| **Tingkat Risiko** | 🔴 **TINGGI** |
| **Area** | Keamanan — `AuthenticationController::login()` |

`AuthenticationController::login()` memproses percobaan login tanpa batasan jumlah percobaan (no throttling), tanpa mekanisme account lockout, dan tanpa delay progresif. Pencarian kata kunci `throttle`, `RateLimit`, `rate_limit` di seluruh folder `app/` tidak menemukan hasil.

**Bukti / Lokasi Kode:**
```
app/Domains/System/Controllers/AuthenticationController.php — method login()
```

**Dampak:** Aplikasi rentan terhadap serangan brute-force dan credential stuffing terhadap akun pengguna, termasuk akun dengan hak Super Admin yang dapat melakukan bypass otorisasi penuh (lihat temuan 4.3 di bawah, terkait `isSuperAdmin()`).

**Rekomendasi:**
- Terapkan rate limiting berbasis IP dan/atau username menggunakan Throttler bawaan CodeIgniter 4 (`service('throttler')`) pada endpoint login.
- Tambahkan mekanisme lockout sementara setelah N kali percobaan gagal berturut-turut, dan catat percobaan gagal ke Audit Log (`AuthenticationEvent` sudah tersedia sebagai titik integrasi).
- Pertimbangkan CAPTCHA setelah beberapa kali percobaan gagal untuk endpoint yang bersifat public-facing.

### 4.3. Duplikasi Class AuthorizationFilter yang Berpotensi Membingungkan

| | |
|---|---|
| **Tingkat Risiko** | 🟡 **SEDANG** |
| **Area** | Kualitas Kode — Lapisan Otorisasi |

Ditemukan dua implementasi class bernama sama (`AuthorizationFilter`) dengan logika yang berbeda: satu di `app/Filters/AuthorizationFilter.php` (yang benar-benar didaftarkan sebagai alias `rbac` di `Config\Filters`, membaca dari `SecurityContext`, dan memiliki bypass khusus untuk Super Admin), dan satu lagi di `app/Domains/Authorization/Filters/AuthorizationFilter.php` (menggunakan `GuardResolver` & `PermissionProviderInterface`, tidak terdaftar di `Config\Filters` manapun sehingga tampak tidak terpakai/dead code).

**Bukti / Lokasi Kode:**
```
app/Filters/AuthorizationFilter.php (aktif, terdaftar di Config\Filters sebagai 'rbac')
app/Domains/Authorization/Filters/AuthorizationFilter.php (tidak ditemukan referensi pemakaian
  di Config\Filters atau Routes manapun)
```

**Dampak:** Duplikasi ini meningkatkan risiko maintenance error — pengembang di masa depan dapat secara tidak sengaja mengedit atau memanggil implementasi yang salah, atau mengira logika otorisasi telah konsisten di kedua tempat padahal berbeda (mis. bypass Super Admin hanya ada di satu implementasi).

**Rekomendasi:**
- Tentukan satu implementasi resmi sebagai single source of truth, hapus atau pindahkan implementasi yang tidak terpakai ke luar direktori `app/` (atau branch terpisah) agar tidak membingungkan.
- Tambahkan unit test yang secara eksplisit memverifikasi filter mana yang benar-benar dieksekusi pada pipeline route.

### 4.4. Modul Upload Pipeline Dibangun namun Tidak Terintegrasi (Dead Code) dan Validasi Bersifat Opsional

| | |
|---|---|
| **Tingkat Risiko** | 🟡 **SEDANG** |
| **Area** | Kualitas Kode / Keamanan — `app/Core/Upload` |

`UploadPipeline`, `UploadContext`, dan generator terkait sudah diimplementasikan lengkap (validasi MIME, ekstensi, ukuran, checksum SHA-256, nama file UUID), namun pencarian referensi `new UploadContext`/`UploadPipeline` di seluruh `app/` dan `tests/` tidak menemukan satu pun pemanggil — modul ini belum digunakan oleh Controller/Service manapun dan tidak memiliki test. Selain itu, validasi MIME/ekstensi pada `UploadPipeline::validateMime()`/`validateExtension()` hanya dijalankan JIKA `allowedMimeTypes`/`allowedExtensions` diisi (`!empty(...) && ...`) — jika pemanggil di masa depan lupa mengisi array whitelist, validasi akan otomatis ter-skip tanpa peringatan.

**Bukti / Lokasi Kode:**
```
app/Core/Upload/UploadPipeline.php — validateMime() & validateExtension()
grep 'new UploadContext' → 0 hasil di app/ maupun tests/
```

**Dampak:** Fitur unggah berkas (kemungkinan untuk foto profil jamaah, dokumen masjid, atau lampiran transaksi keuangan) belum siap pakai. Ketika nanti diintegrasikan, default "fail-open" pada validasi (bukan "fail-closed") berisiko meloloskan file berbahaya jika whitelist lupa diisi.

**Rekomendasi:**
- Jika fitur upload memang belum menjadi prioritas rilis saat ini, tandai secara eksplisit di `ROADMAP.md` sebagai "belum terintegrasi" agar tidak dianggap selesai.
- Ubah default menjadi fail-closed: lempar exception jika `allowedMimeTypes`/`allowedExtensions` kosong, alih-alih melewati validasi secara diam-diam.
- Tambahkan pengecekan MIME type berbasis konten file (`fileinfo`/`finfo`) di sisi server, bukan hanya mempercayai `mimeType` yang dikirim dari context/uploader.

### 4.5. Potensi Race Condition pada Financial Posting Engine

| | |
|---|---|
| **Tingkat Risiko** | 🔴 **KRITIS** |
| **Area** | Integritas Data — `FinancialPostingEngine::postTransaction()` |

Pada `FinancialPostingEngine::postTransaction()`, pembacaan saldo akun (`fundRepo->findById()`, `finAccountRepo->findById()`) dan perhitungan saldo ter-cache (`ledgerPostingService->updateCachedAccountBalance()`) dilakukan SEBELUM transaksi database dimulai (`uow->begin()` baru dipanggil setelahnya di langkah 7). Tidak ditemukan indikasi penguncian baris (`SELECT ... FOR UPDATE`) atau optimistic locking (kolom version/timestamp) pada repository akun/dana.

**Bukti / Lokasi Kode:**
```
app/Domains/Financial/Services/Posting/FinancialPostingEngine.php
  — urutan: findById() (baris ~57-58) dieksekusi sebelum uow->begin() (baris ~73)
```

**Dampak:** Apabila dua transaksi keuangan terhadap akun/dana yang sama diproses secara konkuren (mis. dua panitia menginput donasi pada waktu bersamaan, atau melalui API yang dipanggil paralel), keduanya dapat membaca saldo lama yang sama, menghitung saldo baru berdasarkan nilai basi (stale read), dan transaksi yang commit terakhir akan menimpa (overwrite) perubahan transaksi sebelumnya — dikenal sebagai *lost update*. Untuk sistem yang mengelola dana keuangan masjid, kesalahan saldo seperti ini berdampak langsung pada integritas laporan keuangan dan kepercayaan jamaah/donatur.

**Rekomendasi:**
- Pindahkan pembacaan saldo akun/dana ke DALAM boundary transaksi (setelah `uow->begin()`), dan gunakan penguncian baris pesimistik (`SELECT ... FOR UPDATE`) pada `FinancialAccount`/`Fund` saat proses posting.
- Sebagai alternatif/lapisan tambahan, terapkan optimistic locking dengan kolom `version` pada tabel akun keuangan, dan tolak (retry) jika terjadi konflik versi saat commit.
- Tambahkan test konkurensi (mis. simulasi dua request paralel terhadap akun yang sama) untuk memverifikasi bahwa saldo akhir tetap konsisten.

### 4.6. Konfigurasi Keamanan Framework Masih Nilai Default

| | |
|---|---|
| **Tingkat Risiko** | 🟢 **RENDAH** |
| **Area** | Keamanan — `app/Config/Security.php` & `Session.php` |

`Config\Security` masih menggunakan nilai bawaan CodeIgniter starter: `tokenRandomize = false`, `tokenName = 'csrf_test_name'` (nama default yang dikenal luas), `csrfProtection = 'cookie'`. Belum ada indikasi penyesuaian untuk kebutuhan produksi (mis. pengacakan token, penamaan cookie yang lebih spesifik untuk menghindari fingerprinting framework).

**Dampak:** Meskipun bukan celah kritis dengan sendirinya, penggunaan nama token/cookie default memudahkan penyerang mengenali stack teknologi yang dipakai (fingerprinting), dan `tokenRandomize=false` membuat token CSRF statis selama masa hidup sesi (lebih mudah dianalisis jika bocor lewat log/referrer).

**Rekomendasi:**
- Set `tokenRandomize = true`, dan ganti `tokenName`/`cookieName` ke nilai kustom yang tidak generik.
- Pastikan seluruh konfigurasi produksi (`baseURL`, `forceGlobalSecureRequests`, `CSPEnabled`) benar-benar diaktifkan pada file `.env` produksi — bukan hanya template `env` yang di-commit.

### 4.7. Cakupan Automated Testing Belum Merata di Seluruh Domain

| | |
|---|---|
| **Tingkat Risiko** | 🟡 **SEDANG** |
| **Area** | Quality Assurance — folder `tests/` |

Dari 19 file test yang ada, mayoritas terfokus pada modul Financial (9 file) dan cukup baik. Namun tidak ditemukan test untuk `AuthenticationController`/`AuthenticationFilter`/`AuthorizationFilter` secara langsung (alur login, logout, refresh, bypass Super Admin), tidak ada test untuk `App/Core/Upload`, dan domain Family/Masjid/Jamaah hanya memiliki 1 file test generik masing-masing (`DomainTest`/`ModuleRc1Test`) yang kemungkinan tidak mencakup seluruh skenario CRUD dan otorisasi per-permission.

**Bukti / Lokasi Kode:**
```
tests/ — 19 file, 9 di antaranya Financial*Rc1Test.php
```

**Dampak:** Modul keamanan inti (autentikasi & otorisasi) — yang justru paling kritikal untuk diuji secara regresi — belum memiliki jaring pengaman otomatis. Perubahan kode di masa depan pada filter auth/rbac berisiko lolos tanpa terdeteksi jika terjadi regresi (mis. bypass tidak sengaja).

**Rekomendasi:**
- Tambahkan test khusus untuk `AuthenticationFilter` dan `AuthorizationFilter` mencakup skenario: user tidak login, user login tanpa permission, user dengan permission benar, dan bypass Super Admin.
- Tambahkan test brute-force/rate-limit begitu fitur pada temuan 4.2 diimplementasikan.
- Targetkan code coverage minimum (mis. 70–80%) khusus untuk lapisan Filters, Services otorisasi, dan Financial Posting Engine sebagai modul paling sensitif.

---

## 5. Matriks Prioritas Perbaikan

| No | Temuan | Prioritas | Estimasi Effort |
|---|---|---|---|
| 1 | Race condition Financial Posting Engine (4.5) | 🔴 **Segera (sebelum go-live)** | Sedang — refactor urutan lock + test konkurensi |
| 2 | Aktifkan proteksi CSRF (4.1) | 🔴 **Segera** | Kecil — konfigurasi + regresi test frontend/API client |
| 3 | Rate limiting login (4.2) | 🔴 **Segera** | Kecil–Sedang |
| 4 | Bersihkan duplikasi AuthorizationFilter (4.3) | 🟠 Sebelum rilis berikutnya | Kecil |
| 5 | Fail-closed validation Upload Pipeline (4.4) | 🟠 Sebelum fitur upload dirilis | Kecil |
| 6 | Tambah test Auth/RBAC (4.7) | 🟡 Iteratif | Sedang |
| 7 | Harden konfigurasi default (4.6) | 🟢 Nice-to-have | Kecil |

---

## 6. Kesimpulan

MasjidCMS dibangun di atas fondasi arsitektur yang solid dengan penerapan prinsip Domain-Driven Design, pemisahan tanggung jawab yang jelas, dan bebas dari kerentanan SQL Injection klasik maupun praktik penyimpanan password yang buruk — sebuah pencapaian yang tidak umum ditemui bahkan pada proyek komersial berskala serupa.

Meskipun demikian, terdapat gap keamanan yang bersifat konfigurasi (CSRF, rate limiting) yang relatif mudah diperbaiki namun berdampak signifikan jika dibiarkan, serta satu isu integritas data pada modul finansial (race condition) yang sebaiknya ditangani sebelum aplikasi menangani transaksi keuangan riil dalam skala/konkurensi yang lebih tinggi. Direkomendasikan agar kelima temuan berprioritas "Segera" pada Bab 5 diselesaikan terlebih dahulu sebelum rilis produksi (go-live), diikuti dengan penguatan cakupan pengujian otomatis pada lapisan keamanan.

*Laporan ini disusun berdasarkan static code review; disarankan untuk melengkapi dengan dynamic application security testing (DAST) dan dependency vulnerability scanning (mis. `composer audit`) sebagai langkah lanjutan sebelum deployment produksi.*
