# FINANCIAL GOVERNANCE & WORKFLOW SPECIFICATION

**Status:** Proposal — belum diimplementasikan. Dokumen ini adalah *spesifikasi aturan bisnis*, bukan kode. Ditulis setelah audit menemukan bahwa mekanisme approval yang ada (`ApprovalPolicy`, `ApprovalWorkflowService`) sudah dirancang ke arah ini tapi **terputus** dari jalur eksekusi nyata (lihat §7 "Kesenjangan implementasi saat ini").

**Tujuan:** Menjadi acuan tunggal untuk siapa boleh melakukan apa, pada tahap mana, dalam siklus hidup transaksi keuangan — sebelum tim menulis atau mengubah kode apa pun terkait ini.

---

## 1. Prinsip Dasar

1. **Segregation of Duties (SoD):** orang yang menginput transaksi tidak boleh menjadi orang yang memverifikasi/menyetujui transaksi yang sama (*maker-checker*).
2. **Irreversible posting:** begitu transaksi berstatus `POSTED`, tidak ada jalur *update* — hanya jalur `VOID` + jurnal pembalik (*reversal*), keduanya tercatat, tidak menghapus jejak apa pun.
3. **Least privilege per peran:** setiap peran hanya boleh melakukan aksi yang menjadi tanggung jawabnya; peran yang lebih senior (mis. Ketua DKM) *tidak otomatis* mewarisi hak Bendahara kecuali dinyatakan eksplisit di §3.
4. **Semua transisi status tercatat** (aktor, waktu, status sebelum/sesudah, catatan) — auditability bukan fitur tambahan, tapi syarat lulus setiap transisi.
5. **Super Admin adalah peran teknis darurat, bukan peran operasional harian** — penggunaannya untuk aksi keuangan sebaiknya dibatasi dan tercatat terpisah (lihat §6).

---

## 2. Peran (Role) dan Definisi Tanggung Jawab

| Peran | Tanggung jawab utama dalam domain keuangan |
|---|---|
| **Staf Operasional / Admin Input** | Menginput draf transaksi harian (pemasukan/pengeluaran rutin). Tidak punya wewenang menyetujui atau mem-posting. |
| **Bendahara** | Memverifikasi kelayakan draf transaksi, melakukan persetujuan (approval) tingkat-1, dan mem-posting transaksi yang sudah disetujui. |
| **Ketua DKM** | Penyetuju akhir (*final approver*) untuk transaksi bernominal besar di atas ambang batas (threshold), serta penerima laporan audit berkala. |
| **Auditor (Internal/Eksternal)** | Hak akses *read-only* ke seluruh riwayat, jurnal, dan log tanpa wewenang mengubah atau memicu transisi status. |
| **Super Admin** | Pengelola sistem teknis. **Tidak boleh** dipakai untuk transaksi harian. Aksi keuangan oleh Super Admin memicu peringatan audit (*audit warning*). |

---

## 3. Matriks Hak Akses Peran vs. Transisi Status

| Transisi Status | Staf / Admin Input | Bendahara | Ketua DKM | Auditor | Super Admin |
|---|---|---|---|---|---|
| `Buat (DRAFT)` | ✅ | ✅ | ❌ | ❌ | ⚠️ *Emergency only* |
| `Submit (DRAFT → PENDING_APPROVAL)` | ✅ (milik sendiri) | ✅ | ❌ | ❌ | ⚠️ *Emergency only* |
| `Approve (PENDING_APPROVAL → APPROVED)` | ❌ | ✅ (bukan buatan sendiri) | ✅ (jika > threshold) | ❌ | ❌ *Disallowed* |
| `Reject (PENDING_APPROVAL → REJECTED)` | ❌ | ✅ | ✅ | ❌ | ❌ *Disallowed* |
| `Post (APPROVED → POSTED)` | ❌ | ✅ | ❌ | ❌ | ❌ *Disallowed* |
| `Void (POSTED → VOID)` | ❌ | ✅ (+ usulan tertulis) | ✅ (persetujuan wajib) | ❌ | ❌ *Disallowed* |
| `View / Export Audit` | ✅ (terbatas) | ✅ | ✅ | ✅ | ✅ |

*Keterangan:*
- ✅ **Allowed:** Boleh dilakukan secara mandiri sesuai alur.
- ❌ **Disallowed:** Ditolak sistem (*BusinessRuleException*).
- ⚠️ **Emergency only:** Diizinkan hanya jika flag *emergency override* aktif, dan selalu mencatat log audit tingkat tinggi (*severity: CRITICAL*).

---

## 4. Matriks Matriks Maker-Checker & Ambang Batas (Threshold)

### 4.1 Aturan Maker-Checker (Paling Utuh)
Setiap transaksi wajib diperiksa dan disetujui oleh **pengguna yang berbeda** (`approver_user_id !== created_by_user_id`). Aturan ini berlaku berdasarkan `user_id` aktual, bukan nama peran, untuk mencegah situasi di mana satu pengguna memegang dua peran sekaligus lalu menyetujui buatannya sendiri.

### 4.2 Ambang Batas Persetujuan Berjenjang (Approval Thresholds)

```
                       +-------------------------+
                       |   Draf Transaksi Dibuat |
                       +------------+------------+
                                    |
                                    v
                       +-------------------------+
                       |    PENDING_APPROVAL     |
                       +------------+------------+
                                    |
                  +-----------------+-----------------+
                  |                                   |
                  v                                   v
          Nominal <= Rp 5.000.000             Nominal > Rp 5.000.000
        (Cukup Persetujuan Bendahara)      (Wajib Persetujuan Ketua DKM)
                  |                                   |
                  v                                   v
         +-----------------+                 +-----------------+
         |    APPROVED     |                 | PENDING_CHAIRMAN|
         +--------+--------+                 +--------+--------+
                  |                                   |
                  |                                   v
                  |                          +-----------------+
                  |                          |    APPROVED     |
                  |                          +--------+--------+
                  |                                   |
                  +-----------------+-----------------+
                                    |
                                    v
                           +-----------------+
                           |     POSTED      |
                           +-----------------+
```

- **Nominal <= Rp 5.000.000,-**
  - Cukup 1 tingkat persetujuan: **Bendahara** (selain pembuat).
- **Nominal > Rp 5.000.000,-**
  - Wajib 2 tingkat persetujuan:
    1. Verifikasi kelengkapan berkas oleh **Bendahara**.
    2. Persetujuan akhir oleh **Ketua DKM**.

---

## 5. Aturan Immutability & Reversal (Void)

1. **Definisi Status Hakiki Immutable:** `POSTED`, `VOID`, `CANCELLED`.
   - Transaksi dalam status ini **dilarang di-UPDATE atau di-DELETE** lewat query SQL biasa, ORM, maupun API endpoint.
2. **Prosedur Pembatalan (VOID):**
   - Transaksi yang sudah `POSTED` hanya dapat dibatalkan melalui aksi `VOID`.
   - Aksi `VOID` **tidak menghapus** baris transaksi asal.
   - Aksi `VOID` otomatis membangkitkan:
     - 1 transaksi pembalik berstatus `VOID`.
     - 1 entri **Jurnal Pembalik (Reversal Journal)** yang membalik posisi Debit dan Kredit transaksi asal dengan nilai nominal yang persis sama.
     - Catatan alasan VOID yang diinput oleh aktor.

---

## 6. Penanganan Peran Super Admin pada Domain Keuangan

Super Admin dirancang untuk pemeliharaan infrastruktur (konfigurasi database, pengguna, sistem log). Untuk menjaga integritas laporan keuangan:

1. Super Admin **tidak boleh** melakukan *bypass* persetujuan (tidak bisa langsung mengubah status ke `APPROVED` atau `POSTED`).
2. Jika Super Admin membuat transaksi draf dalam kondisi darurat, transaksi tersebut tetap wajib melalui persetujuan Bendahara/Ketua DKM sesuai alur normal.
3. Seluruh interaksi Super Admin dengan rute keuangan menghasilkan log berperingkat **WARNING / CRITICAL** pada tabel audit trail.

---

## 7. Kesenjangan Implementasi saat Ini (Gap Analysis per Audit 2026-08)

Dokumen ini disusun sebagai respons atas temuan bahwa kode produksi saat ini memiliki **tiga kelas `Approval*` yang sudah lengkap tetapi terputus (*disconnected*)**:

1. `App\Domains\Financial\Services\ApprovalWorkflowService` (sudah ada, tidak dipanggil controller manapun).
2. `App\Domains\Financial\Policies\ApprovalPolicy` (sudah ada, tidak pernah di-bind ke event handler).
3. `AdminFinancialWorkspaceController::store()` (sebelumnya memanggil `insertTransaction()` dengan status `POSTED` mentah, melewati seluruh hirarki di atas).

---

## 8. Langkah Penyesuaian Kode Selanjutnya (Roadmap Penyeragaman)

1. **Fase 1 (Selesai di PR ini):**
   - Hapus *bypass* `insertTransaction()` dari `AdminFinancialWorkspaceController`.
   - Enforce status `DRAFT` pada pembuatan transaksi di Admin UI.
   - Enforce *maker-checker* pada `FinancialTransaction::approve()`.
   - Enforce `post()` hanya menerima status `APPROVED`.
2. **Fase 2 (Follow-up Rilis Berikutnya):**
   - Hubungkan `ApprovalWorkflowService` ke `AdminFinancialWorkspaceController` dan `FinancialApiController`.
   - Aktifkan ambang batas Rp 5.000.000,- untuk kewajiban approval Ketua DKM.
   - Tambahkan unit test otomatis untuk seluruh matriks persetujuan pada spesifikasi ini.
