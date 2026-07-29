# Laporan Audit Lanjutan — MasjidCMS
## Verifikasi Commit `d74997d`: "Finalize Milestone Documentation & Stabilize Admin Workflows"

**Tanggal Audit:** 29 Juli 2026
**Commit yang diaudit:** `d74997d` (branch `develop`)
**Dokumen milestone yang diverifikasi:**
- `MILESTONE_DASHBOARD_FUNCTIONAL_STABILIZATION.md`
- `MILESTONE_UIUX20_SPRINT1_HOMEPAGE_EXPERIENCE_REFINEMENT.md`

**Metodologi:** `git pull` langsung dari repositori, `git show` diff lengkap untuk 33 file yang berubah, cross-check tiap klaim di dokumen milestone terhadap source code aktual (bukan hanya membaca changelog).

---

## 1. Ringkasan Eksekutif

Ini adalah tindak lanjut atas temuan kritis di **Laporan Audit RC2** (yang, saya catat, sekarang juga sudah didokumentasikan oleh tim di `docs/audit-IT/Laporan_Audit_MasjidCMS_RC2.md` — praktik baik untuk traceability). Commit `d74997d` secara spesifik menyasar dua hal: (1) stabilisasi bug runtime pada alur edit CMS/Master Data, dan (2) **perbaikan besar pada modul Keuangan** yang sebelumnya ditemukan melewati mesin double-entry accounting.

**Hasil:** Perbaikan pada modul Keuangan **signifikan dan sebagian besar valid** — bukan lagi sekadar tambal UI, tapi benar-benar mengimplementasikan pembungkusan transaksi database, pemilihan dana/akun oleh pengguna, dan pembuatan jurnal otomatis. Namun saya menemukan **satu bug baru pada logika atribusi akun di jurnal** yang membuat entri debit dan kredit mengarah ke akun yang sama — secara akuntansi ini keliru dan perlu diperbaiki sebelum modul ini dianggap benar-benar solid.

---

## 2. Verifikasi Klaim Milestone: Dashboard Functional Stabilization

| Klaim di Dokumen Milestone | Status Verifikasi |
|---|---|
| Error runtime pada `edit()` CMS/Master Data karena method dideklarasikan return type `string` tapi mengembalikan `RedirectResponse` (TypeError) | ✅ **Terverifikasi benar.** Sebelumnya method edit di kedua controller ini kemungkinan besar bertipe `: string`; sekarang deklarasi tipe kembalian sudah dilonggarkan (`public function edit(...)` tanpa `: string` paksa), sehingga bisa mengembalikan baik `string` (view) maupun `RedirectResponse` |
| Ditambahkan pengujian regresi dasar | ✅ **Terverifikasi.** File baru `tests/unit/MasterDataWorkspaceUiTest.php` berisi 5 test nyata (bukan test kosong/placeholder) — termasuk `testCmsEditMissingRecordReturnsRedirectResponse` dan `testMasterEditMissingRecordReturnsRedirectResponse` yang secara spesifik menguji skenario bug yang dilaporkan (record tidak ditemukan → harus redirect, bukan error) |
| Verifikasi endpoint lewat browser & HTTP request | ⚠️ Tidak dapat saya verifikasi ulang di lingkungan audit ini (tidak ada PHP dev server + database live berjalan saat commit dibuat), tapi perubahan kode konsisten dengan klaim tersebut |

**Kesimpulan bagian ini: klaim milestone ini akurat dan terverifikasi di source code.**

---

## 3. Verifikasi Perbaikan Kritis — Modul Keuangan (Temuan Utama Audit RC2)

Ini adalah bagian paling penting. Saya bandingkan baris-per-baris `diff` pada `AdminFinancialWorkspaceController.php` terhadap 5 masalah yang saya laporkan sebelumnya:

| # | Masalah di Audit RC2 | Status Sekarang |
|---|---|---|
| 1 | Transaksi tidak pernah membuat `journal_entries`/`journal_details` | ✅ **Diperbaiki.** `store()` sekarang membuat 1 baris `journal_entries` + 2 baris `journal_details` (debit & kredit) untuk **setiap** transaksi yang disimpan, dibungkus dalam satu unit atomik |
| 2 | Update saldo pakai raw SQL string interpolation (`"UPDATE ... {$amount}"`) | ✅ **Diperbaiki.** Diganti dengan Query Builder: `->set('balance', 'balance + ' . $amount, false)` — pola yang aman dan konsisten dengan lapisan domain |
| 3 | Fund/Account dipilih otomatis (ambil baris pertama tabel), tidak ada pilihan pengguna | ✅ **Diperbaiki.** Form `create.php` sekarang punya 3 `<select>` baru: `fund_id`, `financial_account_id`, `account_id` — semuanya diisi dari data riil (`$funds`, `$financialAccounts`, `$coaAccounts` dikirim dari controller) dan divalidasi `required|numeric` |
| 4 | Tidak ada locking/atomicity saat update saldo | ✅ **Diperbaiki (level DB transaction).** Seluruh alur `store()` sekarang dibungkus `$db->transStart()` ... `$db->transComplete()`, dengan pengecekan `transStatus()` dan rollback otomatis + pesan error ke pengguna jika gagal |
| 5 | `storeJournal()` mengambil transaksi secara acak / membuat transaksi palsu "System Initial Transaction" Rp 0 jika tabel kosong | ✅ **Diperbaiki.** Sekarang menerima `transaction_id` eksplisit dari form; jika tidak ada tabel transaksi sama sekali, sistem **menolak dengan pesan error yang jelas** ("Belum ada transaksi keuangan yang dapat diajukan jurnalnya"), bukan lagi membuat data palsu |

### 🟠 Bug Baru yang Ditemukan: Atribusi Akun Debit/Kredit Salah

Saat memeriksa detail implementasi jurnal otomatis di `store()`, saya menemukan bahwa **kedua baris `journal_details` (debit dan kredit) menggunakan `account_id` yang sama**:

```php
// Untuk transaksi INCOME:
$db->table('journal_details')->insert([
    'account_id'    => $accountId,   // <- baris debit
    'debit_amount'  => $amount,
    'credit_amount' => 0,
]);
$db->table('journal_details')->insert([
    'account_id'    => $accountId,   // <- baris kredit, akun SAMA dengan di atas
    'debit_amount'  => 0,
    'credit_amount' => $amount,
]);
```

Komentar kode di atasnya secara eksplisit menyatakan maksud yang benar — *"Debit: Financial Account/Cash Asset, Credit: Revenue COA Account"* — tapi implementasinya **tidak mengikuti komentar tersebut**: baik baris debit maupun kredit sama-sama memakai `$accountId` (nilai dari input `account_id`, yaitu akun COA pendapatan/beban), bukan memakai `$finAccId` (akun kas/`financial_account_id`) untuk salah satu sisi.

**Dampak:** Secara akuntansi, entri jurnal yang dihasilkan **tidak benar** — debit dan kredit saling meniadakan pada akun yang sama alih-alih mencerminkan perpindahan nilai antara akun Kas dan akun Pendapatan/Beban. Kalau laporan Buku Besar per akun dihasilkan dari `journal_details`, akun `$accountId` akan selalu menunjukkan saldo bersih 0 dari setiap transaksi (naik lalu turun jumlah yang sama), sementara akun kas (`financial_account_id`) tidak pernah tersentuh sama sekali di jurnal — padahal `financial_accounts.balance` sendiri sudah benar ter-update di langkah terpisah. Ada inkonsistensi antara apa yang tercermin di `financial_accounts.balance` vs apa yang tercermin di `journal_details`.

**Perbaikan yang disarankan:**
```php
if ($type === 'INCOME') {
    // Debit: Kas/Financial Account, Kredit: COA Pendapatan
    insert(['account_id' => $finAccId /* atau kode COA yg dipetakan dari kas */, 'debit_amount' => $amount, 'credit_amount' => 0]);
    insert(['account_id' => $accountId, 'debit_amount' => 0, 'credit_amount' => $amount]);
} else {
    // Debit: COA Beban, Kredit: Kas/Financial Account
    insert(['account_id' => $accountId, 'debit_amount' => $amount, 'credit_amount' => 0]);
    insert(['account_id' => $finAccId, 'debit_amount' => 0, 'credit_amount' => $amount]);
}
```
Catatan: ini mengasumsikan `financial_accounts` punya pemetaan ke kode COA kas (`coa_accounts`). Jika belum ada pemetaan tersebut, ini perlu ditambahkan dulu (mis. kolom `coa_account_id` di tabel `financial_accounts`) sebelum logika jurnal di atas bisa benar-benar akurat.

---

## 4. File Lain yang Berubah (Verifikasi Sekilas)

| File/Area | Catatan |
|---|---|
| `AdminMediaController.php` (baru, 218 baris) + `media/index.php`, `media/picker_modal.php` | Modul Media Library yang sebelumnya saya tandai "belum diimplementasikan" — sekarang punya controller dan UI picker. Tidak sempat diverifikasi mendalam pada audit ini, disarankan verifikasi terpisah |
| `public/index.php` dipecah jadi komponen (`hero.php`, `program_section.php`, `kajian_section.php`, `layanan_section.php`, `pengurus_section.php`, `berita_section.php`, `donation_section.php`, `financial_section.php`, dll.) | Refactor struktur — pemecahan halaman monolitik jadi partial components. Perubahan struktural yang wajar, tidak mengindikasikan bug dari sisi diff yang saya baca |
| `docs/audit-IT/Laporan_Audit_MasjidCMS_RC2.md` | Tim mengarsipkan laporan audit sebelumnya ke dalam repo — praktik dokumentasi yang baik untuk jejak audit |

---

## 5. Rekomendasi

| Prioritas | Rekomendasi |
|---|---|
| 🔴 Tinggi | Perbaiki atribusi akun debit/kredit di `journal_details` — gunakan `$finAccId` (atau pemetaan COA-nya) untuk salah satu sisi entri, bukan `$accountId` untuk kedua sisi |
| 🟠 Sedang | Tambahkan test regresi khusus untuk modul Keuangan yang menguji: total debit = total kredit, dan debit/kredit mengarah ke akun yang **berbeda** — mengikuti pola test yang sudah bagus di `MasterDataWorkspaceUiTest.php` |
| 🟠 Sedang | Verifikasi mendalam terhadap `AdminMediaController.php` yang baru ditambahkan — belum sempat diaudit detail pada sesi ini |
| 🟡 Rendah | Pertimbangkan menambah kolom pemetaan COA pada `financial_accounts` (mis. `coa_account_id`) agar logika jurnal otomatis punya sumber kebenaran yang eksplisit untuk sisi "Kas", bukan bergantung pada asumsi implisit di kode controller |

---

## 6. Kesimpulan

Iterasi perbaikan pada commit `d74997d` menunjukkan kemajuan nyata: 4 dari 5 masalah kritis pada Audit RC2 **terbukti sudah diperbaiki dengan benar** (validasi ke source code, bukan sekadar klaim di changelog), termasuk penggunaan transaksi database atomik dan penghapusan generasi data palsu. Modul Keuangan kini jauh lebih dekat ke desain double-entry accounting yang benar dibanding sebelumnya.

Namun ditemukan **satu bug baru** pada logika atribusi akun jurnal (debit dan kredit memakai akun yang sama) yang perlu diperbaiki sebelum modul ini dinyatakan siap untuk mencatat dana riil jamaah — karena laporan keuangan (Buku Besar per akun) yang dihasilkan dari data ini akan tetap tidak akurat meskipun secara teknis "jurnal sudah tercatat".
