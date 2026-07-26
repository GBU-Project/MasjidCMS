# MasjidCMS — Software Architecture Document (SAD)

**Versi:** 1.1 (revisi dari v1.0, hasil Architecture Review)
**Status:** Architecture Lock — dokumen tertinggi, menjadi acuan seluruh pengembangan berikutnya
**Tanggal:** 26 Juli 2026
**Riwayat revisi:**
- v1.0 — Draft awal Architecture Phase.
- v1.1 — Finalisasi Repository Pattern, penambahan domain Asset & Communication, bab Engineering Standards, Git Workflow, Release Strategy, Architecture Decision Records, dan status lifecycle keputusan.

> Setiap keputusan besar di dokumen ini diberi label status: **APPROVED** (final, siap jadi acuan implementasi), **PROPOSED** (usulan, masih perlu konfirmasi Lead Tech/tim), **FUTURE** (disadari perlu, sengaja belum dikerjakan), **DEPRECATED** (pernah berlaku, sudah tidak dipakai).

---

## 1. Vision

**Tujuan MasjidCMS:** menyediakan sistem manajemen konten dan operasional masjid yang (a) mudah dioperasikan oleh pengurus non-teknis, (b) dibangun di atas fondasi yang relevan untuk 3–5 tahun ke depan, dan (c) siap menjadi basis layanan API bagi aplikasi mobile di masa depan tanpa perombakan ulang.

**Target pengguna:**
- Pengurus masjid non-teknis (Admin Konten, Admin Modul Masjid) — prioritas: antarmuka sederhana, minim istilah teknis.
- Super Admin/developer — prioritas: struktur kode yang predictable dan mudah di-maintain oleh tim kecil.
- Jamaah/publik — prioritas: informasi jadwal, kajian, dan transparansi donasi yang cepat diakses tanpa hambatan.

**Filosofi sistem:**
1. **Boring is good.** Pilih pola yang predictable dan terbukti, hindari abstraksi berlebihan yang tidak dibutuhkan skala proyek ini.
2. **Fondasi sebelum fitur.** Tidak ada modul domain dibangun sebelum Core teruji lewat implementasi nyata (checkpoint MVP).
3. **Dapat diwariskan.** Developer yang masuk kemudian harus bisa memahami struktur hanya dengan membaca dokumen ini.

---

## 2. Architecture Principles

*Status: **APPROVED***

| Prinsip | Makna Praktis |
|---|---|
| **Blueprint First** | Tidak ada kode ditulis sebelum desain (struktur folder, skema data, kontrak antar-layer) disepakati tertulis. |
| **Core Before Modules** | Domain `System` harus stabil dan tervalidasi sebelum domain bisnis lain dibangun. |
| **Everything Configurable** | Perilaku yang mungkin berubah tanpa deploy ulang (judul situs, tema aktif, kontak, rekening donasi) disimpan di database. |
| **API First** | Setiap fitur publik dirancang agar bisa diakses lewat REST API sejak awal. |
| **Convention over Configuration** | Penamaan file, folder, dan kelas mengikuti pola baku (Bagian 7). |
| **Low Coupling** | Domain tidak boleh bergantung pada detail internal domain lain (Bagian 6). |
| **High Cohesion** | Satu domain hanya bertanggung jawab atas satu area bisnis yang jelas batasnya. |
| **Open for Extension** | Struktur harus menerima domain/modul baru tanpa mengubah domain yang sudah ada. |

---

## 3. High Level Architecture

*Status: **APPROVED** — Repository Pattern final dikunci pada revisi ini (Revision 1).*

```
 Request
    │
    ▼
 Filter          (AuthFilter, RbacFilter, RateLimitFilter, CorsFilter)
    │
    ▼
 Controller      (menerima request, validasi input, memanggil Service, membentuk response)
    │
    ▼
 Service         (logic bisnis, orkestrasi, aturan domain, otorisasi)
    │
    ▼
 Repository      (query, join, pagination, search, data mapping — TANPA logic bisnis)
    │
    ▼
 Model           (representasi tabel/entity, tanpa logic bisnis)
    │
    ▼
 Database
    │
    ▼
 Response        (View untuk web, JSON envelope untuk API)
```

### Keputusan final Repository Pattern

**Repository bertanggung jawab HANYA untuk:**
- Query dasar dan kompleks ke database.
- Join antar tabel.
- Pagination.
- Search/filtering data.
- Data mapping (mengubah hasil query jadi bentuk yang mudah dipakai Service — array/objek entitas ringan).

**Repository TIDAK BOLEH melakukan:**
- Validation (itu tanggung jawab Service, sebelum data sampai ke Repository).
- Business rule (aturan bisnis apa pun — kapan status berubah, siapa boleh apa — ada di Service).
- Authorization (pengecekan hak akses ada di Filter/Service, bukan di Repository).
- Response formatting (pembentukan JSON envelope atau tampilan ada di Controller/Core, bukan di Repository).

Batasan ini menjaga Repository tetap "bodoh" (dumb) — hanya tahu cara bicara dengan database, tidak tahu *mengapa* data itu diminta. Ini yang membuat Repository mudah diuji dan mudah diganti tanpa merusak logic bisnis.

**Catatan konsistensi dengan keputusan sebelumnya:** Repository tetap tanpa interface/contract di v1 (`{Entity}Repository.php` konkret langsung), sesuai keputusan v1.0 — *Status: **APPROVED**, interface ditunda ke **FUTURE*** (lihat Bagian 14) dan hanya ditambahkan jika ada kebutuhan nyata (mis. caching decorator, dukungan multi-database).

---

## 4. Directory Standard

*Status: **APPROVED***

```
app/
├── Core/            # Fondasi teknis lintas-domain (BUKAN logic bisnis)
├── Domains/         # Seluruh kapabilitas bisnis, dikelompokkan per domain
├── Config/          # Konfigurasi CI4 native
├── Filters/         # Filter global lintas domain
├── Libraries/       # Pembungkus teknis pihak ketiga / util non-domain-specific
├── Helpers/         # Fungsi bantuan prosedural ringan
├── Views/           # View global (layout admin, layout publik, komponen bersama)
public/              # Document root
themes/              # Paket tema publik yang dapat diganti
plugins/             # Ekstensi/modul opsional di luar Domains inti
storage/             # File upload/hasil generate (di luar `writable/` bawaan CI4)
database/            # Dokumentasi skema, diagram ERD, contoh seed data mentah
docs/                # Seluruh dokumen arsitektur, roadmap, permission matrix, UI guideline, ADR
```

Fungsi masing-masing folder tidak berubah dari v1.0. Detail lengkap tetap berlaku sebagaimana didefinisikan sebelumnya.

**Belum diputuskan (dibawa dari v1.0):** *Status: **PROPOSED*** — path View per domain: apakah view domain disimpan terpusat di `app/Views/{domain}/`, atau ikut dalam folder domain masing-masing. Menunggu uji coba konfigurasi path CI4 sebelum dikunci.

---

## 5. Domain Architecture

*Status: **APPROVED** — 8 domain final (Revision 2, 3, 4).*

| Domain | Tanggung Jawab |
|---|---|
| **System** | Identitas, akses, dan tata kelola teknis sistem: Auth, RBAC (Users/Roles/Permissions), Settings, Activity Log, Theme Manager. |
| **CMS** | Publikasi konten umum non-operasional harian: Halaman, Artikel, Menu, Banner. |
| **Masjid** | Data dan jadwal operasional ibadah harian: Kajian, Jadwal Imam, Jadwal Khatib, Jadwal Muadzin, Agenda. |
| **Media** | Pengelolaan aset media terpusat, dikonsumsi lintas domain: Media Library, Galeri, Video. |
| **Keuangan** | Transparansi dan pencatatan dana: Donasi, ZISWAF. |
| **TPQ** | Data pendidikan Al-Qur'an di lingkungan masjid: Santri, Kelas, Pengajar. |
| **Asset** | Manajemen aset fisik dan ketersediaannya: Inventaris, Ambulans, Kendaraan, Ruangan, Peralatan. |
| **Communication** | Layanan komunikasi lintas domain: Email, WhatsApp, SMS (future), Push Notification (future), In-App Notification. |

### Domain Asset — alasan arsitektural (Revision 3)

Inventaris dan Ambulans (dari dokumen analisis awal) dipindahkan ke domain baru **Asset**, digabung dengan Kendaraan, Ruangan, dan Peralatan. Alasannya:

1. **Kohesi tanggung jawab.** Kelima entitas ini sama-sama menjawab pertanyaan "apa yang dimiliki masjid dan apa statusnya" (tersedia/dipakai/rusak/dipinjam) — bukan "kapan sebuah ibadah/kegiatan berlangsung" seperti domain Masjid. Menyatukannya mencegah domain Masjid membengkak dengan konsep yang berbeda sifat (jadwal waktu vs status kepemilikan aset).
2. **Pola data yang mirip.** Kendaraan, Ruangan, Peralatan, dan Ambulans secara struktural punya kebutuhan field yang serupa (status, lokasi, penanggung jawab, jadwal pemeliharaan) — cocok berbagi abstraksi dasar dalam satu domain, dibanding dipisah sendiri-sendiri atau dipaksakan ke domain lain.
3. **Antisipasi kebutuhan lintas fitur.** Peminjaman ruangan/kendaraan ke depan kemungkinan butuh alur booking/approval yang mirip — lebih mudah dikembangkan bersama dalam satu domain daripada domain terpisah yang harus saling memanggil untuk logic yang sebetulnya sejenis.

**Catatan scope:** Inventaris dan Ambulans sudah ada di Roadmap Phase 3 sebelumnya. Kendaraan, Ruangan, dan Peralatan adalah **modul baru** yang belum tercatat di dokumen Roadmap awal — masuk sebagai bagian domain Asset secara arsitektural (*Status: **APPROVED*** untuk strukturnya), tapi prioritas pengerjaannya di Roadmap tetap perlu dikonfirmasi terpisah (*Status modul baru ini di Roadmap: **PROPOSED***, bukan otomatis masuk Phase 3 tanpa keputusan eksplisit).

### Domain Communication — penjelasan (Revision 4)

Domain ini **tidak memiliki UI/fitur yang berdiri sendiri** bagi pengguna akhir — perannya adalah **penyedia layanan komunikasi** yang dikonsumsi domain lain lewat Service publiknya. Contoh pemakaian:
- Domain Keuangan memanggil `Communication\Services\NotificationService::send()` untuk mengirim konfirmasi donasi lewat WhatsApp/Email.
- Domain Masjid memanggil layanan yang sama untuk mengirim pengingat kajian.
- Domain System memanggil layanan yang sama untuk notifikasi reset password.

Dengan memusatkan semua channel komunikasi (Email, WhatsApp, SMS, Push, In-App Notification) dalam satu domain, setiap domain lain tidak perlu tahu detail integrasi pihak ketiga (kredensial API WhatsApp, SMTP, dsb.) — mereka hanya memanggil kontrak Service yang seragam. Ini konsisten dengan aturan lintas-domain di Bagian 6: domain lain hanya boleh memanggil Service publik Communication, tidak pernah menyentuh implementasi channel-nya langsung.

**Status channel:**
- Email — **APPROVED** untuk Phase 1/awal (kebutuhan dasar: reset password, notifikasi transaksi).
- In-App Notification — **APPROVED** untuk Phase 1/awal.
- WhatsApp — **APPROVED** sebagai kebutuhan channel utama untuk konteks masjid Indonesia, implementasi menyusul setelah integrasi pihak ketiga dipilih.
- SMS — **FUTURE**.
- Push Notification — **FUTURE** (baru relevan saat ada aplikasi mobile).

---

## 6. Dependency Rules

*Status: **APPROVED***

```
Controller ──► Service ──► Repository ──► Model ──► Database

Controller  TIDAK BOLEH memanggil Repository atau Model secara langsung.
Service     TIDAK BOLEH memanggil Model secara langsung (harus lewat Repository).
Repository  TIDAK BOLEH mengandung logic bisnis, validation, authorization, atau response formatting.
```

**Aturan lintas-domain:**
```
Domain A → Service publik Domain B   ✅ diperbolehkan
Domain A → Repository/Model Domain B ❌ dilarang
```

**Aturan khusus domain Communication:** karena sifatnya sebagai layanan bersama yang dipanggil hampir semua domain lain, Communication **tidak boleh** balik memanggil Service domain manapun (arah dependency satu arah — domain lain → Communication, tidak sebaliknya). Ini mencegah Communication diam-diam menjadi terikat pada satu domain tertentu dan kehilangan sifat generiknya.

**Aturan Filter:** Filter hanya boleh memanggil Service (untuk cek permission/otorisasi), tidak boleh mengakses Repository/Model.

**Aturan Core:** `app/Core` tidak boleh bergantung pada `app/Domains` (Domains boleh pakai Core, tidak sebaliknya).

---

## 7. Coding Convention

*Status: **APPROVED***

| Aspek | Aturan |
|---|---|
| **Namespace** | `App\Domains\{DomainName}\{Layer}\{ClassName}` — contoh: `App\Domains\Asset\Services\KendaraanService` |
| **Naming kelas** | PascalCase, deskriptif, tanpa singkatan ambigu |
| **Naming method/variabel** | camelCase |
| **Naming folder domain** | PascalCase tunggal atau frasa domain (`Masjid`, `Keuangan`, `Asset`, `Communication`) |
| **Naming tabel & kolom DB** | snake_case, tabel plural |
| **Naming Service** | `{Entity}Service.php` |
| **Naming Repository** | `{Entity}Repository.php` (tanpa interface di v1) |
| **Naming Migration** | `{YYYY-MM-DD-HHiiss}_{AksiEntity}Table.php` |
| **Naming Seeder** | `{Entity}Seeder.php`, diorkestrasi lewat `DatabaseSeeder.php` |
| **Naming permission** | `{module}.{action}` slug, lowercase-kebab untuk module majemuk |
| **Naming route API** | `/api/v1/{resource-kebab-case}` |

---

## 8. Error Handling

*Status: **APPROVED***

| Aspek | Standar |
|---|---|
| **Exception dasar** | Custom exception domain-specific extend dari `app/Core/Exceptions` (`DomainException`, `NotFoundException`, `AuthorizationException`, `ValidationException`). |
| **Validation** | Validation library bawaan CI4, rule didefinisikan di layer Service. |
| **Response error (API)** | `{"status": "error", "data": null, "message": "...", "errors": {...opsional...}}`. |
| **Response error (Web)** | Redirect ke form dengan flash message + validation errors. |
| **Logging teknis** | `log_message()` ke `writable/logs`, terpisah dari Activity Log bisnis. |
| **Logging bisnis** | Perubahan data penting dicatat ke `activity_logs` (domain System). |

---

## 9. Configuration Strategy

*Status: **APPROVED***

| Sumber | Contoh | Alasan |
|---|---|---|
| **Config (file)** | Base URL, koneksi database, driver session/cache, environment | Wajib ada sebelum aplikasi berjalan. |
| **Database (`settings`)** | Judul situs, logo, kontak, rekening donasi, tema aktif, meta SEO, maintenance mode | Wajar diubah Super Admin tanpa deploy ulang. |
| **Hardcoded** | Daftar aksi permission, struktur domain, format JSON envelope | Perubahan harus memicu review kode, bukan diubah diam-diam via UI. |

---

## 10. Engineering Standards

*Status: **APPROVED** (bagian baru, Revision 5).*

| Standar | Penerapan |
|---|---|
| **PSR-12** | Seluruh kode PHP wajib mengikuti PSR-12 (coding style standar PHP-FIG). |
| **Composer** | Wajib untuk seluruh dependency management, tidak ada library yang di-vendor manual. |
| **Autoload** | PSR-4 autoloading via Composer, namespace `App\Domains\*` dipetakan sesuai struktur folder di Bagian 4. |
| **PHPStan** | *Status: **FUTURE*** — statis analysis ditambahkan setelah struktur domain stabil pasca-Phase 1, mulai level rendah (level 3–5) lalu dinaikkan bertahap. |
| **Pint / PHP-CS-Fixer** | Salah satu dipilih untuk auto-format kode agar konsisten dengan PSR-12 tanpa debat manual saat code review. Rekomendasi: **Pint** (lebih ringan setup-nya di ekosistem non-Laravel sekalipun, konfigurasi minimal). *Status: **PROPOSED*** — pilihan final menunggu konfirmasi tim. |
| **Commit Convention** | Conventional Commits (`feat:`, `fix:`, `docs:`, `refactor:`, `chore:`), contoh: `feat(masjid): tambah endpoint jadwal kajian`. |
| **Branch Convention** | Lihat Bagian 11 (Git Workflow). |
| **Folder Convention** | Mengikuti Bagian 4 dan Bagian 7 — tidak ada file domain di luar `app/Domains/{Domain}/`. |

---

## 11. Git Workflow

*Status: **APPROVED** (bagian baru, Revision 6).*

| Branch | Kapan Digunakan |
|---|---|
| **main** | Kode yang sudah rilis stabil di produksi. Tidak pernah di-commit langsung — hanya menerima merge dari `release/*` atau `hotfix/*`. |
| **develop** | Integrasi seluruh fitur yang sedang berjalan, mencerminkan kondisi "siap untuk rilis berikutnya". Basis dari semua `feature/*`. |
| **feature/\*** | Satu fitur/modul per branch, dibuat dari `develop`, di-merge kembali ke `develop` setelah selesai dan direview. Contoh: `feature/masjid-jadwal-kajian`. |
| **release/\*** | Dibuat dari `develop` saat mendekati rilis (mis. `release/1.0.0`), dipakai untuk stabilisasi (bugfix minor, update dokumentasi rilis), tidak menerima fitur baru. Setelah siap, merge ke `main` dan `develop`. |
| **hotfix/\*** | Dibuat dari `main` untuk perbaikan darurat di produksi, di-merge kembali ke `main` **dan** `develop` agar perbaikan tidak hilang di rilis berikutnya. |

**Catatan proses:** model ini (Git Flow) cukup formal untuk tim yang berkembang. Jika tim tetap kecil (1–2 developer) dalam jangka pendek, `release/*` dan `develop` boleh disederhanakan menjadi langsung `feature/* → main` — tapi ini penyederhanaan operasional, bukan perubahan arsitektur, sehingga tidak perlu revisi dokumen ini untuk melakukannya.

---

## 12. Release Strategy

*Status: **APPROVED** (bagian baru, Revision 7).*

```
Development ──► Alpha ──► Beta ──► RC ──► Stable ──► Hotfix
```

| Tahap | Kriteria |
|---|---|
| **Development** | Kerja aktif di `develop`/`feature/*`, tidak untuk diuji pihak luar. |
| **Alpha** | Fitur inti (Phase 1 & 2 di Roadmap) selesai, masih ada bug diketahui, hanya untuk internal testing. |
| **Beta** | Seluruh modul domain (Phase 3) selesai secara fungsional, dibuka untuk uji coba terbatas ke pengurus masjid asli. |
| **RC (Release Candidate)** | Tidak ada bug kritis diketahui, hanya menerima perbaikan, siap dianggap final menunggu konfirmasi. |
| **Stable** | Rilis resmi di `main`, versi yang direkomendasikan untuk produksi. |
| **Hotfix** | Perbaikan darurat di atas versi Stable yang sudah berjalan, mengikuti branch `hotfix/*` di Bagian 11. |

---

## 13. Architecture Decision Records (ADR)

*Status: **APPROVED** — struktur dan aturan (Revision 8). Isi ADR belum dibuat, sesuai instruksi.*

**Fungsi ADR:** mencatat *satu keputusan arsitektur signifikan* per file — bukan seluruh dokumen arsitektur (itu tugas SAD ini), melainkan jejak historis *mengapa* satu keputusan spesifik diambil, kapan, dan alternatif apa yang dipertimbangkan. ADR menjawab pertanyaan "kenapa dulu kita memilih ini?" tanpa perlu menelusuri riwayat chat/diskusi lama.

**Struktur folder:**
```
docs/
└── ADR/
    ├── 0001-project-foundation.md
    ├── 0002-repository.md
    ├── 0003-domain-architecture.md
    └── 0004-configurable-system.md
```

**Aturan penulisan ADR (format baku, mengikuti konvensi umum Michael Nygard ADR):**
- Penomoran berurut, tidak pernah dipakai ulang meski sebuah keputusan di-deprecate.
- Setiap ADR minimal berisi: **Context** (situasi/masalah), **Decision** (keputusan yang diambil), **Status** (mengikuti lifecycle Bagian 14), **Consequences** (konsekuensi/trade-off yang diterima).
- ADR **tidak pernah diedit isinya setelah difinalkan** — jika keputusan berubah, buat ADR baru dengan nomor baru dan tandai ADR lama sebagai `DEPRECATED`, dengan referensi silang ke ADR penggantinya.
- ADR mencatat satu keputusan spesifik (mis. "kenapa Repository tanpa interface"), bukan rekap seluruh SAD.

**Catatan:** empat file di atas baru berupa *struktur yang disiapkan* — isinya belum ditulis sesuai instruksi task ini ("Jangan membuat isi ADR"). Pengisian ADR menjadi task terpisah setelah SAD v1.1 ini disetujui.

---

## 14. Decision Status — Ringkasan Lifecycle

*Status: **APPROVED** (mekanisme, Revision 9).*

Setiap keputusan arsitektur di dokumen ini mengikuti salah satu status berikut, dan status ini **wajib diperbarui** setiap kali dokumen direvisi:

| Status | Arti | Implikasi |
|---|---|---|
| **APPROVED** | Final, disetujui Lead Tech, siap jadi acuan implementasi. | Boleh langsung dijadikan dasar kode. |
| **PROPOSED** | Usulan, belum final. | Tidak boleh diimplementasikan sebagai keputusan tetap sebelum dikonfirmasi. |
| **FUTURE** | Kebutuhan disadari, sengaja belum dikerjakan. | Tidak dikerjakan sekarang, tapi arsitektur harus tidak menutup kemungkinan ini nanti. |
| **DEPRECATED** | Pernah berlaku, sudah tidak dipakai. | Dipertahankan sebagai jejak sejarah (biasanya lewat ADR), tidak dipakai untuk kode baru. |

### Ringkasan status keputusan utama (v1.1)

| Keputusan | Status |
|---|---|
| Repository Pattern (Controller→Service→Repository→Model) | APPROVED |
| Repository tanpa interface/contract | APPROVED (interface: FUTURE) |
| 8 Domain final (System, CMS, Masjid, Media, Keuangan, TPQ, Asset, Communication) | APPROVED |
| Modul baru Kendaraan/Ruangan/Peralatan masuk domain Asset (struktur) | APPROVED |
| Modul baru Kendaraan/Ruangan/Peralatan masuk Roadmap Phase 3 (prioritas) | PROPOSED |
| Domain Communication & channel Email/In-App | APPROVED |
| Channel WhatsApp | APPROVED (implementasi menyusul) |
| Channel SMS, Push Notification | FUTURE |
| Path View per domain (terpusat vs per-domain) | PROPOSED |
| PHPStan | FUTURE |
| Pilihan Pint vs PHP-CS-Fixer | PROPOSED |
| Git Flow (main/develop/feature/release/hotfix) | APPROVED |
| Release stage Development→Alpha→Beta→RC→Stable→Hotfix | APPROVED |
| Struktur ADR | APPROVED |

---

## 15. Future Architecture Roadmap

*Status: seluruh item **FUTURE** (bagian baru, Revision 10) — disadari relevan, sengaja tidak dikerjakan sekarang.*

| Item | Deskripsi Singkat |
|---|---|
| **Plugin System** | Mekanisme resmi agar `plugins/` (Bagian 4) bisa memuat ekstensi pihak ketiga tanpa mengubah kode inti — dibutuhkan setelah domain inti stabil. |
| **Queue** | Job asinkron (mis. pengiriman notifikasi massal via Communication domain, generate laporan keuangan besar) tanpa memblokir request. |
| **Scheduler** | Tugas terjadwal (reminder kajian harian, backup otomatis, rotasi jadwal imam) — kandidat kuat begitu volume data bertambah. |
| **WebSocket** | Update real-time (mis. status ambulans, notifikasi in-app langsung) — baru relevan jika ada kebutuhan UI real-time nyata. |
| **Multi Mosque** | Satu instalasi mengelola beberapa masjid dalam satu organisasi/yayasan — berdampak besar pada skema data (perlu `mosque_id` di hampir semua tabel), sehingga sengaja tidak dirancang sekarang agar tidak menambah kompleksitas prematur. |
| **Multi Tenant** | Satu instalasi melayani banyak organisasi independen (bukan sekadar banyak masjid dalam satu yayasan) — perubahan arsitektur besar (isolasi data per tenant), jauh di luar cakupan v1. |
| **Microservice Gateway** | Pemisahan domain menjadi layanan terpisah dengan API Gateway — hanya relevan jika skala pengguna/tim jauh melampaui kapasitas monolith saat ini. |

**Catatan penting:** struktur domain-oriented di Bagian 4–5 sengaja dipilih (bukan struktur flat) salah satunya karena mempermudah transisi ke arah Plugin System atau bahkan Microservice di masa depan — setiap domain sudah punya batas yang jelas jika suatu saat perlu "dikeluarkan" menjadi layanan terpisah. Ini bukan berarti microservice sedang dikerjakan sekarang; ini alasan tambahan mengapa keputusan di Bagian 4–5 masuk akal untuk jangka panjang.

---

*Dokumen ini adalah SAD v1.1. Perubahan struktur di masa depan wajib dicatat sebagai revisi bernomor baru (v1.2, dst.) di file ini, dengan status lifecycle yang diperbarui sesuai Bagian 14 — bukan menimpa tanpa jejak.*
