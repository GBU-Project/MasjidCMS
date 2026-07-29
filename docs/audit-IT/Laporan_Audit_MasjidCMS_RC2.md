# Laporan Audit Lanjutan — MasjidCMS (RC2)
## Verifikasi Remediasi & Temuan Kritis Baru: Integritas Modul Keuangan

**Tanggal Audit:** 28 Juli 2026
**Commit yang diaudit:** `5cf7ecd` (branch `develop`)
**Riwayat commit yang diverifikasi:**
- `64a9820` — feat(rc2): implement admin CRUD foundation and portal enhancements
- `38ccc83` — docs: update RC2 development progress
- `175e654` — fix(frontend): resolve 500 error on frontend portal and fix CI4 route method deprecation warnings
- `e8992a5` — feat(rc2): implement mosque business modules and homepage manager
- `5cf7ecd` — style(homepage): improve section donation form UX and auto-resizing textarea

**Pengerjaan perbaikan (remediasi) pada rentang commit di atas dilakukan oleh: Antigravity.**
**Metodologi audit:** `git pull` langsung dari repositori, verifikasi otomatis seluruh pasangan Route↔Controller method, pembacaan penuh source code (Controller, Model, Migration, View) untuk setiap klaim perbaikan, dibandingkan terhadap temuan Audit #1, #2, dan UAT sebelumnya.

---

## 1. Ringkasan Eksekutif

Tim (Antigravity) merespons seluruh temuan UAT dan Audit sebelumnya dengan cepat, dalam 5 commit berturut-turut dalam satu hari kerja. Hasil verifikasi menunjukkan **mayoritas perbaikan valid dan terverifikasi benar** di source code — termasuk perbaikan struktural yang cukup signifikan (CRUD penuh untuk semua modul, konsistensi Route↔Controller, migration modul bisnis masjid baru).

Namun, audit menemukan **satu temuan kritis baru**: implementasi "wiring form ke backend" untuk modul Keuangan dilakukan dengan **melewati sepenuhnya mesin akuntansi double-entry** yang sudah dibangun dengan baik di lapisan domain (`app/Domains/Financial`) pada versi sebelumnya. Ini berisiko merusak integritas data keuangan justru pada modul yang menjadi nilai jual utama aplikasi.

---

## 2. Verifikasi Perbaikan — Status Terkonfirmasi

### 2.1 Konsistensi Route ↔ Controller (perbaikan terhadap temuan UAT sebelumnya)

Saya jalankan skrip verifikasi otomatis yang mencocokkan **setiap** pasangan `Controller::method` di `app/Config/Routes.php` terhadap method yang benar-benar ada di source code controller.

**Hasil: 0 route yang menunjuk ke method yang tidak ada** (sebelumnya ditemukan 3 route rusak: `storeCoa`, `storeBudget`, `storePeriod`). Seluruh route sekarang valid, termasuk penambahan baru untuk `edit`/`update`/`delete` di semua modul (Master Data, CMS, Financial, COA, Budget, Periode, Menu, Media, Notifikasi).

### 2.2 CRUD Modul Admin — Terverifikasi Berfungsi

| Modul | `create()` | `store()` | `edit()` | `update()` | `delete()` | Verifikasi Kolom vs Migration |
|---|---|---|---|---|---|---|
| CMS (Posts/Kajian/Pages/Gallery) | ✅ | ✅ | ✅ | ✅ | ✅ | Cocok — insert `posts`, `kajian`, `pages`, `media`+`gallery` sesuai skema |
| Master Data (Masjid/Jamaah/Family/User/Role/Permission) | ✅ | ✅ | ✅ | ✅ | ✅ | Cocok — kolom `jamaahs`, `families`, `masjids`, `users`, dll. sesuai migration |
| Settings | — | ✅ (upsert) | — | — | ✅ | Cocok — `setting_key` sudah jadi primary key, upsert bekerja dengan benar |
| Homepage Manager | ✅ (kelola urutan section) | ✅ (`saveOrder`, `saveSettings`, `bulkAction`, `resetDefault`, `clearCache`) | — | — | — | Migration seeding default settings (`homepage_section_order`, dll.) sudah defensif (cek `tableExists`/`fieldExists` sebelum insert) |

### 2.3 Modul Bisnis Masjid Baru (Bidang, Pengurus, Program Kegiatan, Layanan Masjid)

Migration `2026-07-28-000013_CreateMosqueBusinessModulesTables.php` dan Model terkait (`BidangModel`, `PengurusModel`, `ProgramModel`, `LayananModel`) **saya verifikasi konsisten** — nama tabel, `allowedFields`, dan join query (`bidang.name`) semuanya cocok. Tidak ditemukan bug pada modul ini.

### 2.4 Perbaikan 500 Error Frontend

Akar masalah teridentifikasi dan diperbaiki dengan tepat: `BaseController.php` sebelumnya meng-*comment*-kan `$this->helpers = ['form', 'url'];`, padahal fungsi `url_title()` (dipakai di banyak method `store()` untuk generate slug) memerlukan helper `url`. Baris ini sudah diaktifkan kembali — perbaikan valid.

### 2.5 Migration Baru — Tidak Ada Duplikasi Class

Dicek ulang: tidak ada duplikasi nama class migration pada batch ini (masalah blocker di Audit #1 tidak berulang).

---

## 3. 🔴 TEMUAN KRITIS BARU — Modul Keuangan Melewati Mesin Double-Entry

Ini adalah temuan paling penting dari audit lanjutan ini. Form transaksi keuangan **sekarang memang bisa menyimpan data** (validasi input sudah ada, flashdata sukses/gagal sudah ada) — tapi cara penyimpanannya **tidak menggunakan** lapisan domain (`FinancialTransactionRepository`, `JournalEntryRepository`, Application Service) yang sudah dibangun dengan baik dan diverifikasi benar pada Audit #1. Sebagai gantinya, dibuat jalur pintas baru langsung di Controller yang menimbulkan masalah berikut:

| Masalah | Detail | Dampak |
|---|---|---|
| **Tidak membuat jurnal saat transaksi dibuat** | `AdminFinancialWorkspaceController::store()` hanya insert ke `financial_transactions` lalu update `financial_accounts.balance` langsung — **tidak pernah insert ke `journal_entries`/`journal_details`** | Setiap transaksi yang dibuat lewat dashboard **tidak akan tercatat di Buku Jurnal**. Prinsip double-entry (setiap transaksi = minimal 2 baris jurnal debit/kredit) sama sekali tidak diterapkan pada jalur input web |
| **`storeJournal()` mengambil transaksi secara acak** | Method ini mengambil baris pertama dari `financial_transactions` tanpa `WHERE` (`get()->getRowArray()`) untuk dilekatkan ke jurnal baru. Kalau tabel kosong, sistem bahkan **membuat transaksi palsu "System Initial Transaction" senilai Rp 0** hanya agar ada sesuatu untuk dijurnal | Fitur "Buku Jurnal" secara fungsional **tidak mencatat jurnal yang benar** — hanya mengisi tabel agar tampilan terlihat berfungsi, bukan pembukuan yang valid dan bisa diaudit |
| **Update saldo pakai raw SQL string interpolation** | `$db->query("UPDATE financial_accounts SET balance = balance + {$amount} WHERE id = {$finAccId}")` — bukan lewat Query Builder/parameter binding | Risiko injeksi rendah (variabel numerik/dari DB, bukan langsung dari input pengguna), namun tetap merupakan praktik coding yang tidak aman dan tidak konsisten dengan gaya kode di lapisan domain lainnya |
| **Tidak ada pemilihan Fund/Akun oleh pengguna** | Fund, COA Account, dan Financial Account otomatis diambil dari baris pertama tabel masing-masing (`get()->getRowArray()`), bukan dipilih pengguna lewat dropdown di form | Pengguna tidak bisa menentukan transaksi masuk ke dana/kantong mana — semua transaksi akan selalu tercatat ke dana/akun pertama yang ada di database, terlepas dari maksud sebenarnya |
| **Tidak ada locking saat update saldo** | Tidak ada `SELECT ... FOR UPDATE` seperti yang didesain di ADR-0005 | Berisiko *lost update* saat dua transaksi masuk bersamaan (race condition), walau probabilitasnya rendah untuk skala pemakaian masjid single-admin |

**Kesimpulan bagian ini:** perbaikan ini berhasil membuat tombol dan form "terlihat berfungsi" (sesuai kriteria UAT: bisa input data), tapi **mengorbankan integritas akuntansi** yang menjadi fondasi utama proyek ini (ADR-0005, ADR-0006). Ini bukan sekadar bug kecil — ini penyimpangan arsitektural dari desain yang sudah dirancang dengan benar sebelumnya, dan berisiko menghasilkan laporan keuangan yang tidak dapat dipercaya (Neraca/Buku Besar tidak akan mencerminkan transaksi yang diinput lewat dashboard).

---

## 4. Saran Perbaikan (Prioritas)

| # | Rekomendasi | Prioritas |
|---|---|---|
| 1 | **Sambungkan `AdminFinancialWorkspaceController::store()` ke lapisan domain yang sudah ada** (`RecordTransactionApplicationService` / `FinancialTransactionRepository` / `JournalEntryRepository`) alih-alih menulis raw SQL insert langsung di controller. Controller cukup memanggil service, bukan mengimplementasikan ulang logic akuntansi | 🔴 Kritis |
| 2 | **Hapus/nonaktifkan `storeJournal()` versi saat ini** — jangan biarkan sistem mengambil transaksi acak atau membuat transaksi palsu. Jurnal seharusnya dibuat **otomatis dan atomik bersamaan** saat transaksi diposting (satu unit kerja/transaction DB), bukan lewat form manual terpisah yang tidak terhubung ke transaksi manapun | 🔴 Kritis |
| 3 | **Tambahkan dropdown pemilihan Fund/COA Account/Financial Account** di form `admin/financial/create.php`, alih-alih auto-pick baris pertama tabel | 🔴 Tinggi |
| 4 | **Ganti raw query update saldo** dengan Query Builder (`set('balance', 'balance+'.$amount, false)`) atau panggil lewat domain layer yang sudah menerapkan locking (`SELECT ... FOR UPDATE`) sesuai ADR-0005 | 🟠 Sedang |
| 5 | Tambahkan **integration test** khusus: setiap transaksi yang diposting lewat web form harus menghasilkan minimal satu pasang baris `journal_details` (debit = kredit), agar regresi seperti ini tidak lolos lagi ke rilis berikutnya | 🟠 Sedang |
| 6 | Pertimbangkan memindahkan seeding data default (`homepage_section_order`, dll.) dari file migration ke `Seeder` — migration idealnya hanya untuk skema, bukan data (saat ini bekerja dengan baik secara fungsional, ini catatan gaya/best-practice, bukan bug) | 🟡 Rendah |
| 7 | Dokumentasikan secara eksplisit di README/CHANGELOG bahwa modul Keuangan pada RC2 masih dalam status "input dasar berfungsi, integrasi jurnal otomatis belum selesai" — agar tidak disalahartikan sebagai fitur akuntansi yang sudah lengkap dan siap produksi | 🟡 Rendah |

---

## 5. Kesimpulan

Remediasi oleh **Antigravity** pada rentang commit `64a9820` s.d. `5cf7ecd` berhasil menuntaskan seluruh temuan struktural (route rusak, CRUD hilang, 500 error, duplikasi migration) dengan kualitas verifikasi yang baik — hampir semua klaim di commit message terbukti benar saat dicek langsung ke kode.

Namun, cara penyelesaian pada modul Keuangan menimbulkan **regresi arsitektural**: fitur "bisa input data" tercapai, tapi dengan mengorbankan mesin double-entry accounting yang sudah dirancang dan diverifikasi benar sebelumnya. Rekomendasi utama audit ini adalah **menyambungkan kembali form Financial ke lapisan domain yang sudah ada**, bukan mempertahankan jalur pintas raw-SQL yang saat ini berjalan paralel dan tidak konsisten dengannya. Ini perlu menjadi prioritas sebelum modul Keuangan dianggap siap untuk data produksi/dana riil jamaah.
