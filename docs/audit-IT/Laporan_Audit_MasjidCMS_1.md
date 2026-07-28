# Laporan Audit Teknis & Fungsional
## Proyek: MasjidCMS (GBU-Project/MasjidCMS)

**Auditor:** IT Auditor (review teknis mendalam — arsitektur, keamanan, kesesuaian database-dashboard)
**Tanggal Audit:** 28 Juli 2026
**Metodologi:** Clone repository langsung (`git clone`), instalasi PHP 8.3 + ekstensi terkait di sandbox audit, pembacaan source code penuh (bukan hanya dokumentasi), cross-check sistematis antara *migration* CI4 (sumber kebenaran skema database), `schema.sql` (dump statis), Model/Repository/Entity, Controller, dan View.
**Versi yang diaudit:** v1.0.0-rc1 (branch `develop`)

---

## 1. Ringkasan Eksekutif

MasjidCMS adalah aplikasi manajemen masjid berbasis CodeIgniter 4 dengan arsitektur **Domain-Driven Design (DDD)** yang cukup matang — mencakup modul profil masjid, data jamaah/keluarga, RBAC, dan modul akuntansi dana masjid (*fund accounting*) dengan model double-entry. Dokumentasi arsitektur (ADR, SAD) berkualitas baik dan jarang ditemukan di proyek sejenis.

Namun, audit fungsional mendalam menemukan **kesenjangan serius antara skema database yang sebenarnya (migration) dan kode Controller/View yang melayani dashboard admin**. Lapisan domain/DDD (Repository, Entity, Application Service) ditulis dengan disiplin dan konsisten dengan migration. Tapi **lapisan Controller top-level yang menjalankan dashboard ternyata mengacu ke skema yang salah atau basi**, sehingga sebagian besar menu dashboard tidak akan berfungsi sebagaimana mestinya pada instalasi standar (`php spark migrate`).

**Temuan paling kritis:** dari 34 tabel yang direferensikan di kode/dokumentasi, **hanya 15 tabel yang benar-benar punya migration CI4 resmi** — sisanya (19 tabel, termasuk seluruh modul CMS, Settings, Audit Log, dan Approval Workflow) tidak akan pernah tercipta di database hasil instalasi standar.

---

## 2. Temuan Arsitektur & Desain (Positif)

- **Fund Accounting Model** (ADR-0005) memisahkan dana terikat (Zakat, Wakaf, Qurban) dari dana bebas — pendekatan akuntansi yang tepat untuk domain nirlaba/masjid.
- **Strategi primary key**: `BIGINT AUTO_INCREMENT` (clustered index) + `UUID` publik — menghindari fragmentasi index sekaligus aman untuk diekspos di URL/API.
- **Double-entry posting engine** dengan jurnal pembalik dan *pessimistic locking* (`SELECT ... FOR UPDATE`) — pola yang tepat untuk mencegah race condition pada sistem keuangan multi-user.
- Dokumentasi arsitektur (ADR, SAD, UAT Checklist) tergolong sangat lengkap untuk skala proyek ini.

---

## 3. Temuan Konfigurasi & Keamanan

| Area | Temuan | Risiko |
|---|---|---|
| `encryption.key` | Placeholder default 64 karakter nol di `.env.example` | 🔴 Tinggi jika tidak diganti saat deploy — tidak ada guard boot-time yang menolak start jika key masih default |
| CSRF Protection | `tokenRandomize = true`, cookie/header/token name custom | 🟢 Implementasi matang |
| `session.regenerateDestroy` | `false` | 🟠 Sedang — berisiko *session fixation* residual, sebaiknya `true` di produksi |
| `session.matchIP` | `false` | 🟡 Wajar untuk kompatibilitas mobile/proxy, tapi perlu kompensasi kontrol lain |
| `app.forceGlobalSecureRequests` | `true` | 🟢 Baik, mencegah downgrade HTTP |
| CI/CD | Tidak ditemukan `.github/workflows` aktif meski ada badge "Build Status" | 🟡 Perlu verifikasi apakah badge terhubung ke pipeline nyata |
| `SECURITY.md` | Tidak ada kebijakan disclosure kerentanan formal | 🟠 Penting untuk aplikasi yang menangani dana umat |

---

## 4. Temuan Kritis — Ketidaksesuaian Database vs Dashboard

### 4.1 🔴 BLOCKER — Migration Tidak Bisa Dijalankan

**File:** `2026-07-26-235153_CreateJamaahsTable.php` dan `2026-07-27-000000_CreateJamaahsTable.php`

Dua file migration berbeda **mendefinisikan class PHP yang sama persis**: `App\Database\Migrations\CreateJamaahsTable`, dengan isi skema yang saling bertentangan (satu versi generik `code/name/slug`, satu lagi versi domain asli `member_no/nik/full_name/gender/...`).

- **Dampak:** PHP Fatal Error — *"Cannot declare class ... because the name is already in use"* saat autoloader men-scan folder migration. **Proyek dalam kondisi saat ini tidak bisa di-migrate sama sekali.**
- **Cara verifikasi:** `JamaahModel::$allowedFields` (member_no, nik, full_name, gender, dst.) cocok dengan file kedua → file pertama adalah leftover generator yang harus dihapus.
- **Rekomendasi:** Hapus `2026-07-26-235153_CreateJamaahsTable.php`.

### 4.2 🔴 Dashboard Admin (`AdminDashboardController.php`)

| Kode Bermasalah | Isu | Dampak |
|---|---|---|
| `tableExists('jamaah')` | Nama tabel salah — seharusnya `jamaahs` (jamak), sesuai `JamaahModel::$table` | Kartu **"Total Jamaah" selalu tampil 0** |
| `selectSum('cached_balance')` dari `financial_accounts` | Kolom tidak ada — migration mendefinisikan `balance`, bukan `cached_balance` | SQL error *"Unknown column"* → **widget "Total Saldo" crash** |
| `select('transaction_number, ...')` dari `financial_transactions` | Kolom tidak ada — seluruh domain Financial konsisten memakai `transaction_no` | SQL error → **widget "Transaksi Terbaru" crash** |
| View `admin/dashboard/index.php` | View sama sekali **tidak menggunakan** variabel (`totalJamaah`, `totalBalance`, `pendingApprovals`, `recentTransactions`) yang dikirim controller — semua angka di kartu statistik **hardcoded** (contoh: "485", "142", "3") | Dashboard menampilkan data palsu/statis, terlepas dari data asli di database |

> ✅ **Sudah ditambal** (patch tersedia) untuk 3 baris pertama pada sesi audit ini — lihat Lampiran A.

### 4.3 🔴 Menu Master Data (`AdminMasterDataController.php`)

| Tab | Masalah |
|---|---|
| Jamaah | Bug identik 4.2 — `tableExists('jamaah')`/`table('jamaah')`, seharusnya `jamaahs`. Tab **selalu kosong**. |
| Profil Masjid | `$m['legal_status']` — kolom tidak ada di migration `masjids` (kolom asli: `status`). Berkat fallback `?? 'Terverifikasi'`, tidak crash tapi **selalu menampilkan status yang sama**, menyesatkan. |
| Data Keluarga (KK) | `$f['family_card_number']` dan `$f['head_of_family_name']` **tidak ada** di migration `families` (kolom asli: `kk_number`/`family_no` dan `head_jamaah_id`, yang notabene cuma ID, bukan nama). Kolom "No. KK" dan "Kepala Keluarga" akan **tampil kosong**; perlu `JOIN` ke tabel `jamaahs` yang belum diimplementasikan. |
| User / Role / Permission | ✅ Tidak ada bug — kolom cocok dengan migration RBAC. |

### 4.4 🔴 Menu Financial Workspace (`AdminFinancialWorkspaceController.php`)

| Tab | Masalah |
|---|---|
| Daftar Transaksi | Kolom `transaction_number` seharusnya `transaction_no` → **tab akan error saat dibuka** |
| Queue Persetujuan DKM | Tabel `approval_requests` **tidak punya migration** (hanya `approval_logs` yang ada) → tab **selalu kosong secara diam-diam**, walau lapisan `ApproveTransactionApplicationService` di sisi domain sudah lengkap dan berfungsi |
| Detail Transaksi | Method `detail(string $id)` **tidak pernah query database** — langsung render view. View-nya sendiri berisi teks **hardcoded** `"TRX-202607-00088"` di judul & body. Klik transaksi manapun akan **selalu menampilkan data statis yang sama**. Tombol Approve/Reject juga tidak memiliki handler apa pun. |
| Transfer Kantong Dana / Buku Jurnal | ✅ Tidak ada bug — kolom cocok dengan migration |

### 4.5 🔴 Menu CMS (`AdminCmsWorkspaceController.php`) — Mati Total

Tab **Posts, Kajian, Pages, Gallery** semuanya memakai `tableExists()` sebagai gerbang. Query kolomnya sendiri (`title`, `slug`, `is_published`, `speaker_name`, dll.) sebenarnya sudah benar sesuai `schema.sql`. **Namun keempat tabel ini tidak punya migration CI4 sama sekali** → seluruh menu CMS akan **kosong permanen** pada instalasi standar. Ini gap struktural, bukan bug logika.

### 4.6 🔴 Menu System (`AdminSystemWorkspaceController.php`) — Mati Total

Tabel `settings` dan `audit_logs` juga **tidak punya migration**. Nama kolom di kode sudah benar, tapi tabel tidak pernah tercipta → menu **Pengaturan Platform** dan **Audit Log** kosong permanen.

### 4.7 🟢 Menu Reporting (`AdminReportingWorkspaceController.php`) — Sehat

Menggunakan `financial_transactions`, `amount`, `transaction_type` — semua cocok dengan migration resmi. **Tidak ditemukan bug.**

---

## 5. Analisis Cakupan Migration vs Tabel yang Direferensikan

Dari **34 tabel** yang direferensikan di `schema.sql`/kode aplikasi, hanya **15 tabel** yang memiliki migration CI4 resmi (`php spark migrate` akan membuatnya). **19 tabel berikut tidak akan pernah tercipta** pada instalasi standar:

```
approval_requests   approval_steps      audit_logs
branches            budget              categories
family_members      financial_periods   gallery
jamaah (singular)   jamaah_contacts     kajian
media               menus               notifications
pages               posts               program_categories
settings
```

**Implikasi:** modul CMS (berita, kajian, halaman statis, galeri), pengaturan sistem, audit log, notifikasi, dan approval workflow — seluruhnya bergantung pada tabel yang tidak pernah dibuat migration-nya. Ini adalah **gap struktural terbesar** yang ditemukan dalam audit ini.

---

## 6. Ringkasan Status Fungsional per Modul

| Modul / Menu | Status pada Instalasi Standar |
|---|---|
| Dashboard Utama | 🔴 3 widget error/salah + View statis (tidak menampilkan data asli) |
| Master Data → Jamaah | 🔴 Selalu kosong |
| Master Data → Profil Masjid | 🟠 Data status menyesatkan |
| Master Data → Data Keluarga | 🟠 Kolom kosong, perlu JOIN |
| Master Data → User/Role/Permission | 🟢 Aman |
| Financial → Daftar Transaksi | 🔴 Error kolom |
| Financial → Approval Queue | 🔴 Selalu kosong (tabel tidak ada) |
| Financial → Detail Transaksi | 🔴 Data hardcoded, tidak terhubung ke DB |
| Financial → Transfer/Jurnal | 🟢 Aman |
| CMS (Posts/Kajian/Pages/Gallery) | 🔴 Mati total (tabel tidak ada) |
| System (Settings/Audit Log) | 🔴 Mati total (tabel tidak ada) |
| Reporting | 🟢 Aman |
| Migration `jamaahs` (duplikat class) | 🔴 Blocker — migrate gagal total |

**Kesimpulan kuantitatif:** dari seluruh menu admin yang diaudit, **hanya Reporting dan sebagian Master Data (User/Role/Permission) yang berfungsi normal** pada instalasi standar. Sisanya kosong permanen, menampilkan data salah, atau error runtime.

---

## 7. Rekomendasi Prioritas

| # | Tindakan | Prioritas |
|---|---|---|
| 1 | Hapus file migration duplikat `2026-07-26-235153_CreateJamaahsTable.php` | 🔴 Segera — blocker |
| 2 | Perbaiki semua referensi `jamaah` → `jamaahs` di seluruh Controller (`AdminDashboardController`, `AdminMasterDataController`) | 🔴 Segera |
| 3 | Perbaiki nama kolom (`cached_balance`→`balance`, `transaction_number`→`transaction_no`, `family_card_number`→`kk_number`, `head_of_family_name`→ perlu JOIN ke `jamaahs`, `legal_status`→`status`) di seluruh Controller | 🔴 Segera |
| 4 | Buat migration resmi untuk 19 tabel yang belum ada, khususnya `approval_requests`/`approval_steps` (blocking fitur approval workflow) dan tabel CMS/System | 🔴 Tinggi |
| 5 | Sambungkan View dashboard (`admin/dashboard/index.php`) ke data controller yang sebenarnya — saat ini seluruh angka statistik hardcoded | 🔴 Tinggi |
| 6 | Implementasikan query nyata pada `AdminFinancialWorkspaceController::detail()` — saat ini 100% mockup statis | 🟠 Sedang |
| 7 | Regenerate atau hapus `database/schema.sql` agar tidak lagi jadi sumber acuan yang keliru bagi developer baru | 🟠 Sedang |
| 8 | Tambahkan guard boot-time untuk menolak start aplikasi jika `encryption.key` masih default | 🔴 Tinggi |
| 9 | Set `session.regenerateDestroy = true` di produksi | 🟠 Sedang |
| 10 | Tambahkan `SECURITY.md` untuk kebijakan disclosure kerentanan | 🟠 Sedang |

---

## Lampiran A — Patch yang Sudah Diterapkan pada Sesi Audit Ini

File `app/Controllers/AdminDashboardController.php` telah ditambal sebagai contoh perbaikan (belum di-commit ke repo asli, hanya di lingkungan audit):

```diff
- if ($db->tableExists('jamaah')) {
-     $totalJamaah = $db->table('jamaah')->countAllResults();
+ if ($db->tableExists('jamaahs')) {
+     $totalJamaah = $db->table('jamaahs')->countAllResults();
  }

- $query = $db->table('financial_accounts')->selectSum('cached_balance')->get();
- $row = $query->getRow();
- $totalBalance = (float) ($row->cached_balance ?? 0);
+ $query = $db->table('financial_accounts')->selectSum('balance')->get();
+ $row = $query->getRow();
+ $totalBalance = (float) ($row->balance ?? 0);

  $recentTransactions = $db->table('financial_transactions')
-     ->select('transaction_number, transaction_type, amount, status, created_at')
+     ->select('transaction_no, transaction_type, amount, status, created_at')
```

---

*Laporan ini disusun berdasarkan pembacaan source code langsung (migration, repository, controller, view) dari branch `develop` repositori GBU-Project/MasjidCMS pada 28 Juli 2026. Tidak mencakup pengujian di browser langsung (headless UI testing) karena keterbatasan lingkungan audit; seluruh temuan diverifikasi melalui pembacaan kode dan simulasi query terhadap struktur migration resmi.*
