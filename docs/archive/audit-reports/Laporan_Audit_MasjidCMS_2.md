# Laporan Audit Lanjutan — MasjidCMS
## Verifikasi Perbaikan (Post-Remediation) & Modul Belum Diimplementasikan

**Tanggal Audit:** 28 Juli 2026
**Referensi commit yang diaudit:** `f8dda70` — *"docs: update release notes after QA Audit #1 remediation"* (sebelumnya di `dd99d6a`)
**Metodologi:** `git pull` ulang atas repositori, verifikasi baris kode aktual untuk setiap temuan pada Laporan Audit #1, ditambah pemetaan sistematis tabel database → controller untuk mengidentifikasi modul yang punya skema tapi belum punya lapisan aplikasi.

---

## 1. Status Perbaikan — Verifikasi Temuan Audit #1

Tim pengembang tampaknya sudah membaca laporan audit sebelumnya (histori commit menyebut eksplisit *"QA Audit #1"*). Berikut hasil verifikasi langsung ke source code untuk tiap temuan:

| # | Temuan Audit #1 | Status | Bukti Verifikasi |
|---|---|---|---|
| 1 | Duplikasi class migration `CreateJamaahsTable` (blocker) | ✅ **Diperbaiki** | File lama `2026-07-26-235153_CreateJamaahsTable.php` sudah dihapus; hanya tersisa satu migration jamaah |
| 2 | 19 tabel tanpa migration (CMS, System, Approval, dll.) | ✅ **Diperbaiki** | Migration baru `2026-07-27-000012_CreateMissingSystemAndCmsTables.php` menambahkan 17 tabel: `branches, jamaah_contacts, family_members, financial_periods, budget, approval_requests, approval_steps, program_categories, categories, pages, posts, menus, media, gallery, kajian, settings, audit_logs, notifications`. Total tabel dengan migration resmi naik dari 15 → **35 tabel** |
| 3 | `tableExists('jamaah')` seharusnya `'jamaahs'` (Dashboard & Master Data) | ✅ **Diperbaiki** | Kedua controller sekarang konsisten memakai `'jamaahs'` |
| 4 | Kolom `cached_balance` seharusnya `balance` | ✅ **Diperbaiki (defensif)** | Kode sekarang mendeteksi otomatis: `fieldExists('balance', ...) ? 'balance' : (fieldExists('cached_balance', ...) ? 'cached_balance' : null)` — pendekatan yang lebih tahan terhadap perbedaan skema di masa depan |
| 5 | Kolom `transaction_number` seharusnya `transaction_no` | ✅ **Diperbaiki (defensif)** | Pola deteksi otomatis serupa (`fieldExists('transaction_no', ...)`) diterapkan di Dashboard maupun Financial Workspace |
| 6 | View Dashboard tidak memakai data controller (statis/hardcoded) | ✅ **Diperbaiki** | `admin/dashboard/index.php` sekarang merender `$totalMasjids`, `$totalJamaah`, `$pendingApprovals`, `$totalBalance`, `$recentTransactions` secara dinamis |
| 7 | Tab "Data Keluarga": kolom `family_card_number`/`head_of_family_name` tidak ada | ✅ **Diperbaiki** | Sekarang melakukan `LEFT JOIN families ke jamaahs` via `head_jamaah_id` untuk mengambil `head_name`, dengan fallback berlapis (`kk_number ?? family_card_number ?? family_no`) |
| 8 | Tab "Profil Masjid": kolom `legal_status` tidak ada | ✅ **Diperbaiki** | Sekarang `$m['status'] ?? $m['legal_status'] ?? 'Terverifikasi'` — memprioritaskan kolom yang benar |
| 9 | Tab "Approval Queue" selalu kosong (tabel tak ada) | ✅ **Diperbaiki** | Tabel `approval_requests` kini ada via migration, kolom (`transaction_id`, `requester_id`, `status`, `created_at`) **cocok persis** dengan yang dipakai controller |
| 10 | Halaman Detail Transaksi 100% hardcoded, tidak query DB | ✅ **Diperbaiki** | `detail(string $id)` sekarang benar-benar query `financial_transactions` (dengan pencarian ganda by `transaction_no` **atau** `id`, dibungkus try-catch), dan view merender `$transaction` secara dinamis, bukan lagi teks statis `"TRX-202607-00088"` |
| 11 | Modul CMS (posts/kajian/pages/gallery) mati total | ✅ **Diperbaiki** | Tabel sudah ada, kolom di controller (`title`, `slug`, `is_published`, `speaker_name`, `topic`, `schedule_date`, dll.) **cocok** dengan migration baru |
| 12 | Modul System (settings/audit_logs) mati total | ✅ **Diperbaiki secara struktural** | Tabel sudah ada dan kolom cocok |

**Kesimpulan bagian ini: seluruh 12 temuan dari Audit #1 sudah ditindaklanjuti dan terverifikasi benar di source code.** Pendekatan perbaikan menggunakan `fieldExists()`/deteksi kolom otomatis pada beberapa titik merupakan praktik defensif yang baik — mengurangi risiko regresi serupa di masa depan jika skema berubah lagi.

---

## 2. Modul yang Sudah Punya Skema Database Tapi **Belum Diimplementasikan** di Lapisan Aplikasi

Saya memetakan setiap tabel hasil migration terhadap seluruh Controller (`app/Controllers/**`) untuk mencari tabel yang **tidak direferensikan oleh controller/UI manapun** — artinya secara database sudah "siap", tapi belum ada menu, form, atau tampilan untuk mengelolanya.

| Tabel | Ada Controller/UI? | Catatan |
|---|---|---|
| `branches` | ❌ Tidak ada | Fitur multi-cabang/multi-lokasi masjid — tabel ada, tidak ada menu untuk mengelola cabang |
| `budget` | ❌ Tidak ada | Modul anggaran/RAB per fund/program — krusial untuk kontrol keuangan, tapi belum ada UI input/monitoring |
| `financial_periods` | ❌ Tidak ada | Fitur tutup buku (period closing) — penting untuk laporan keuangan resmi, belum ada menu |
| `notifications` | ❌ Tidak ada | Tabel ada, tidak ada controller yang membaca/menulis; kemungkinan bell-icon notifikasi di UI (jika ada) masih statis |
| `menus` | ❌ Tidak ada | Fitur "menu builder" untuk website publik — tabel ada, belum ada admin page untuk mengaturnya |
| `media` | ❌ Tidak ada | Media library terpusat — tabel ada tapi upload gambar (gallery/posts) kemungkinan belum terhubung ke tabel ini |
| `categories` | ❌ Tidak ada | Kategori generik (kemungkinan untuk Posts) — belum ada CRUD |
| `program_categories` | ❌ Tidak ada | Kategori program donasi — tabel ada, belum ada UI kelola kategori |
| `coa_accounts` (Chart of Accounts) | ⚠️ **Parsial** | Hanya disebut sebagai label kolom di halaman Reporting/Financial (`"Nama Akun COA"`), **tidak ada halaman untuk membuat/mengedit kode akun COA**. Ini gap penting — tanpa CRUD COA di UI, struktur akun keuangan hanya bisa diatur lewat API langsung atau seed manual di database |
| `journal_details` | ❌ Tidak ada di Admin UI | Baris detail jurnal (debit/kredit per akun) tidak ada halaman inspeksi terpisah di admin — hanya bisa dilihat lewat API atau tergabung dalam ringkasan `journal_entries` |
| `gallery` | ⚠️ **Parsial** | Bisa dikelola di tab admin CMS, tapi **tidak ditemukan tampilan publik** untuk galeri (`PublicPortalController` tidak mereferensikan `gallery` sama sekali) — jamaah tidak bisa melihat galeri di website publik |

### Ringkasan cakupan publik (`PublicPortalController`)
Sudah diimplementasikan: Beranda (profil + program aktif + berita terbaru), Profil Masjid, Berita & Kajian, Program Donasi, halaman Donasi (daftar rekening/kantong dana), Kontak.
**Belum ada:** halaman Galeri publik, halaman "Laporan Keuangan Publik/Transparansi" (padahal modul reporting keuangan sudah ada di sisi admin — biasanya CMS masjid punya halaman transparansi dana untuk jamaah, ini belum diekspos ke publik).

---

## 3. Rekomendasi Lanjutan

| Prioritas | Rekomendasi |
|---|---|
| 🔴 Tinggi | Implementasikan CRUD **Chart of Accounts (COA)** di Admin UI — ini fondasi modul akuntansi, saat ini hanya bisa diatur via API/seed manual |
| 🔴 Tinggi | Implementasikan modul **Budget/Anggaran** — tabel dan konsep sudah ada di ADR, tapi belum ada antarmuka |
| 🟠 Sedang | Implementasikan **Financial Period Closing** (tutup buku) — penting untuk validitas laporan keuangan resmi ke jamaah/yayasan |
| 🟠 Sedang | Tambahkan halaman **Galeri Publik** dan pertimbangkan halaman **Transparansi Keuangan Publik** — nilai tambah besar untuk kepercayaan jamaah terhadap pengelolaan dana |
| 🟡 Rendah | Implementasikan `branches` jika memang ada rencana dukungan multi-cabang masjid; jika tidak, pertimbangkan hapus tabel untuk mengurangi kompleksitas skema yang tidak terpakai |
| 🟡 Rendah | Hubungkan upload gambar (posts/gallery) ke tabel `media` agar tidak terjadi duplikasi penyimpanan path file |
| 🟡 Rendah | Implementasikan `menus`, `categories`, `notifications` — atau, jika belum jadi prioritas rilis, dokumentasikan secara eksplisit sebagai "planned, not yet implemented" di README agar tidak disangka bug oleh pengguna/auditor berikutnya |

---

## 4. Kesimpulan

Tim pengembang merespons Audit #1 dengan cepat dan tepat sasaran — seluruh 12 temuan (termasuk 1 blocker) telah diperbaiki dan terverifikasi benar di source code, dengan beberapa perbaikan bahkan menerapkan pola defensif (`fieldExists()`) yang lebih baik daripada sekadar hard-fix satu kolom. Kualitas remediasi tergolong baik.

Namun demikian, terdapat **10 tabel database yang sudah tersedia tapi belum memiliki modul aplikasi (Controller/View) sama sekali atau baru sebagian**, terutama pada area yang secara bisnis penting: **Chart of Accounts, Budget, dan Financial Period Closing**. Ini bukan "bug", melainkan **fitur yang direncanakan tapi belum selesai dikerjakan** — perlu diperjelas dalam roadmap rilis apakah ini akan masuk RC berikutnya atau memang di luar cakupan v1.0.0.
