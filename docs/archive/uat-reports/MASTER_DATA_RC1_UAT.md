# TASK-024 — Master Data Integration RC1 User Acceptance Testing (UAT) Checklist

**Domain:** Master Data Integration (Jamaah + Family RC1)  
**Tanggal:** 27 Juli 2026  
**Status:** PASS  

---

| Item Pengujian | Deskripsi Skenario Integrasi | Ekspektasi | Hasil | Status |
| :--- | :--- | :--- | :---: | :---: |
| **Create Family & Head** | Membuat Family baru & menunjuk Jamaah sebagai Head | `families.head_jamaah_id` & `jamaahs.family_relation_type = 'HEAD'` terisi | Verified | **PASS** |
| **Add Member** | Menambahkan Jamaah sebagai Istri / Anak (`addMember()`) | `family_id` & `family_relation_type` di Jamaah terbarui | Verified | **PASS** |
| **Duplicate HEAD Prevention**| Menambahkan anggota kedua dengan role `HEAD` | Menolak permintaan (Throw `ValidationException`) | Verified | **PASS** |
| **Transfer Head** | Memindahkan Kepala Keluarga ke anggota `ACTIVE` | `head_jamaah_id` berpindah & role terupdate otomatis | Verified | **PASS** |
| **Transfer Head Inactive**| Memindahkan Kepala Keluarga ke Jamaah `DECEASED`/`MOVED` | Menolak permintaan (Throw `ValidationException`) | Verified | **PASS** |
| **Move Member** | Memindahkan Jamaah ke Family lain (`moveMember()`) | Jamaah berpindah `family_id` secara bersih | Verified | **PASS** |
| **Remove Member** | Mengeluarkan anggota non-HEAD (`removeMember()`) | `family_id` & `family_relation_type` menjadi `NULL` | Verified | **PASS** |
| **Remove Head Block** | Mengeluarkan Head of Family via `removeMember()` | Menolak pengeluaran Head sebelum transfer Head | Verified | **PASS** |
| **Delete Head Block** | Soft delete Jamaah berstatus `HEAD` (`JamaahService::delete`) | Menolak soft delete Jamaah Head | Verified | **PASS** |
| **Family Soft Delete** | Soft delete Family (`FamilyService::delete`) | Family ter-soft delete, Jamaah di-detach (`NULL`) | Verified | **PASS** |
| **Audit & Events** | Seluruh transaksi integrasi Master Data | Event dipicu & aktivitas tercatat di Audit Log | Verified | **PASS** |

---

## Kesimpulan UAT Integrasi
Seluruh pengujian integrasi Master Data Jamaah & Family RC1 dinyatakan **PASS**.
