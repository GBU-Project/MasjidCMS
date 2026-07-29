# TASK-018 — Admin CRUD Workspace Standardization & Functional Cleanup

**Status:** Implemented (siap review)
**Priority:** High
**Related:** Browser UAT internal — Workspace CRUD

---

## Background

Browser UAT menemukan ketidakkonsistenan pada seluruh Workspace CRUD admin:

1. Kolom **Aksi tampil dua kali** (Double Action) di beberapa modul.
2. Tombol **Import** tidak berfungsi (dekoratif, tanpa handler).
3. Tombol **Export** tidak berfungsi (dekoratif, tanpa handler).
4. Toolbar dipakai sama untuk semua modul, padahal tiap modul punya kebutuhan berbeda.

Kondisi ini membuat dashboard terlihat *unfinished*.

---

## Root Cause

Ditelusuri langsung ke source code, akar masalahnya ada di dua komponen shared yang dipakai lintas modul (`app/Views/components/table.php` dan `app/Views/components/toolbar.php`):

1. **Double Action** — `AdminMasterDataController` dan `AdminFinancialWorkspaceController` sudah membangun kolom "Aksi" sendiri (link Edit/Hapus yang benar-benar berfungsi) sebagai kolom terakhir di setiap baris. Namun komponen `table.php` **selalu menambahkan lagi** satu kolom Aksi tambahan secara hardcoded, berisi 4 tombol dekoratif (`👁️ View`, `✏️ Edit`, `📜 History`, `🗑️`) tanpa `onclick`/`href` apa pun. Setiap baris jadi menampilkan dua kelompok tombol aksi — persis kombinasi "Edit, Delete" + "View, Edit, History, Delete" seperti dilaporkan.
2. **Import/Export tidak berfungsi** — `toolbar.php` selalu me-render tombol Import & Export sebagai `<button>` polos tanpa handler, untuk semua modul tanpa kecuali, terlepas dari apakah modul tersebut benar-benar punya fitur import/export atau tidak.
3. **Toolbar tidak sesuai kebutuhan modul** — karena tombol Import/Export/Bulk di-hardcode tampil selalu, tidak ada mekanisme bagi tiap modul untuk menyalakan/mematikan kapabilitas sesuai kebutuhannya.
4. **Bulk Action tanpa implementasi** — kolom checkbox seleksi baris selalu dirender di `table.php`, tapi tidak ada JavaScript/handler bulk action apa pun yang terpasang untuk 8 modul Master Data (Profil, Bidang, Pengurus, Jamaah, Keluarga, Users, Role, Permission).

---

## Perubahan

### 1. `app/Views/components/table.php`
- Dihapus: blok hardcoded kolom Aksi kedua (4 tombol dekoratif View/Edit/History/Delete).
- Komponen sekarang **hanya merender persis** `$headers`/`$rows['columns']` yang diberikan pemanggil — tidak lagi menyisipkan kolom tambahan sendiri.
- Kolom checkbox seleksi baris dijadikan **opt-in** lewat parameter `$enableBulk` (default `false`) — sesuai prinsip "jika belum ada implementasi, sembunyikan" (TASK 4).

### 2. `app/Views/components/toolbar.php`
- Diubah menjadi **capability-based**: setiap tombol (`showCreate`, `importUrl`, `exportUrl`, `showRefresh`) adalah parameter eksplisit dengan default aman.
- Tombol Import/Export sekarang **default tersembunyi** (`null`) dan hanya muncul jika pemanggil secara eksplisit memberi URL — mencegah tombol dummy tampil di modul yang belum punya fitur tersebut.
- Tombol Refresh dipertahankan (sudah berfungsi lewat `window.location.reload()`, sesuai verifikasi TASK 5 — tidak menghasilkan 404/500/blank).

### 3. `app/Controllers/AdminMasterDataController.php`
- Ditambahkan method privat `actionButtons(string $editUrl, string $deleteUrl, string $confirmMessage): string` sebagai satu-satunya sumber markup tombol aksi untuk seluruh 8 tab (Profil, Jamaah, Keluarga, User, Role, Bidang, Pengurus, Permission).
- Standar baru: **satu kolom aksi**, urutan konsisten **Edit → Delete**, ikon + tooltip (bukan lagi tombol teks "Edit"/"Hapus").
- **View & History sengaja tidak ditampilkan** — kedua aksi ini belum punya implementasi (`view()`/`history()` tidak ada di controller manapun). Menampilkannya akan jadi tombol dummy baru, bertentangan dengan tujuan task ini. Lihat bagian *Known Limitation*.
- Tidak ada perubahan pada business logic (query, validasi, atau alur create/update/delete) — murni penyeragaman markup output.

### 4. Modul Financial
- Otomatis ikut diperbaiki dari sisi Double Action karena memakai komponen shared yang sama (`table.php`) — tidak perlu perubahan kode di `AdminFinancialWorkspaceController.php`.
- Standardisasi ikon+tooltip untuk tombol aksi Financial **belum dikerjakan** pada task ini (di luar cakupan Browser UAT TASK 8 yang eksplisit disebutkan) — dicatat sebagai *Known Limitation* untuk fast-follow.

---

## File yang Diubah

| File | Jenis Perubahan |
|---|---|
| `app/Views/components/table.php` | Refactor — hapus kolom Aksi duplikat, checkbox jadi opt-in |
| `app/Views/components/toolbar.php` | Refactor — capability-based, sembunyikan tombol belum diimplementasikan |
| `app/Controllers/AdminMasterDataController.php` | Tambah helper `actionButtons()`, standarkan 8 blok tombol aksi |
| `tests/unit/AdminCrudWorkspaceStandardizationTest.php` | Baru — test regresi |
| `docs/milestones/TASK018_ADMIN_CRUD_WORKSPACE_STANDARDIZATION.md` | Baru — dokumentasi ini |

Tidak ada perubahan skema database maupun business logic, sesuai batasan task ini.

---

## Daftar Tombol yang Dihapus

- 8× tombol teks "Edit" & "Hapus" versi lama (diganti ikon+tooltip via helper) — bukan dihapus fungsinya, hanya diseragamkan markup-nya.
- 8× set duplikat tombol dekoratif `👁️ View`, `📜 History`, dan salinan kedua `✏️ Edit`/`🗑️` yang sebelumnya auto-muncul dari `table.php` di setiap baris Master Data (dan berpotensi juga di Financial).

## Daftar Tombol yang Dinonaktifkan (Disembunyikan)

- Tombol **Import** — disembunyikan di seluruh modul (belum ada implementasi backend import di manapun pada codebase).
- Tombol **Export** — disembunyikan di seluruh modul (belum ada implementasi backend export di manapun pada codebase).
- Checkbox **Bulk Select** — disembunyikan (default `$enableBulk = false`) di seluruh modul Master Data & Financial, karena tidak ada handler Bulk Delete/Export/Status yang terpasang untuk modul-modul ini.
- Tombol **View** dan **History** — tidak ditampilkan sama sekali (bukan sekadar disembunyikan dari versi lama, karena versi lama menampilkannya secara dummy) karena tidak ada implementasi.

## Daftar Tombol yang Diperbaiki

- Tombol **Edit** & **Hapus** di 8 tab Master Data — tetap berfungsi seperti sebelumnya (mengarah ke route nyata), sekarang dengan markup konsisten (ikon + tooltip, urutan tetap) lewat satu helper method, bukan ditulis berulang manual di 8 tempat berbeda.
- Tombol **Refresh** — diverifikasi tetap berfungsi dengan benar (reload halaman saat ini, tidak menghasilkan error).

---

## Browser UAT

> **Catatan lingkungan:** verifikasi pada task ini dilakukan lewat pembacaan source code langsung (grep, diff, PHP lint) di lingkungan audit tanpa server PHP + database MySQL yang live berjalan, sehingga **screenshot before/after dan uji klik langsung di browser sungguhan belum bisa dilampirkan** dari sisi audit ini. Rekomendasi: tim menjalankan `php spark serve` di lingkungan lokal/staging untuk menempuh langkah UAT visual berikut sebelum merge:

Checklist verifikasi yang **sudah** dikonfirmasi lewat source code:

| Modul | Kolom Aksi tunggal? | Edit berfungsi? | Delete berfungsi? | Import/Export tersembunyi? | Refresh aman? |
|---|---|---|---|---|---|
| Profil Masjid | ✅ | ✅ (route ada) | ✅ (route ada) | ✅ | ✅ |
| Bidang | ✅ | ✅ | ✅ | ✅ | ✅ |
| Pengurus | ✅ | ✅ | ✅ | ✅ | ✅ |
| Jamaah | ✅ | ✅ | ✅ | ✅ | ✅ |
| Keluarga | ✅ | ✅ | ✅ | ✅ | ✅ |
| Users | ✅ | ✅ | ✅ | ✅ | ✅ |
| Role | ✅ | ✅ | ✅ | ✅ | ✅ |
| Permission | ✅ | ✅ | ✅ | ✅ | ✅ |

Verifikasi otomatis (`php -l` untuk seluruh file yang diubah + test unit baru) — lihat bagian Kesimpulan.

---

## Known Limitation

1. **Import/Export benar-benar belum diimplementasikan** — task ini hanya menyembunyikan tombolnya sesuai instruksi eksplisit ("jika belum selesai, jangan tampilkan tombol"). Implementasi fungsional Import/Export (mis. untuk modul Jamaah, yang secara bisnis paling membutuhkan) perlu task terpisah.
2. **Bulk Action belum diimplementasikan** untuk 8 modul Master Data & Financial — komponen sudah disiapkan opt-in (`$enableBulk`) agar mudah diaktifkan begitu handler Bulk Delete/Export/Status tersedia, tapi belum diaktifkan pada task ini.
3. **View & History belum ada halamannya** — jika ke depannya dibutuhkan (mis. audit trail perubahan data), perlu route + controller method baru terlebih dulu sebelum tombolnya ditambahkan kembali ke `actionButtons()`.
4. **Standardisasi ikon+tooltip belum menjangkau modul Financial & CMS** — Financial otomatis terbebas dari bug Double Action (karena komponen shared sudah diperbaiki), tapi tombol aksinya sendiri masih pakai markup teks lama ("Edit"/"Hapus"), belum ikon. CMS tidak terdampak bug Double Action sama sekali (tidak memakai komponen `table.php`), tapi juga belum mengikuti standar ikon+tooltip yang baru.
5. **Verifikasi Browser UAT visual (screenshot before/after, klik langsung)** belum dilakukan pada sesi ini karena keterbatasan lingkungan audit (tidak ada server PHP + MySQL live). Perlu dilakukan oleh tim di lingkungan staging sebelum rilis final.

---

## Kesimpulan

Root cause "Double Action" berhasil diidentifikasi secara presisi: dua sumber rendering aksi yang tumpang tindih pada komponen shared (`table.php`) dan controller (`AdminMasterDataController`). Perbaikan dilakukan di level komponen (berdampak ke semua modul yang memakainya, termasuk Financial) dan di level controller (standarisasi 8 tab Master Data lewat satu helper method).

Prinsip **"tidak ada tombol dummy"** diterapkan konsisten: Import, Export, Bulk, View, dan History semuanya disembunyikan/dihapus karena belum ada implementasi nyata di baliknya, alih-alih dibiarkan tampil tapi tidak berfungsi.

Tidak ada fitur baru ditambahkan, tidak ada perubahan skema database, dan tidak ada perubahan business logic — murni penyeragaman UI/UX dan penghapusan elemen dekoratif yang menyesatkan, sesuai batasan task ini.

Seluruh file PHP yang diubah telah lolos `php -l` (no syntax errors), dan ditambahkan test regresi baru (`AdminCrudWorkspaceStandardizationTest.php`) untuk mencegah regresi Double Action maupun tombol dummy muncul kembali di masa depan.
