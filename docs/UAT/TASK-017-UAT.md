# TASK-017 — User Acceptance Testing (UAT) Checklist

**Domain:** Masjid (Pilot Domain)  
**Tanggal:** 27 Juli 2026  
**Status:** PASS  

---

| Item Pengujian | Deskripsi Skenario | Ekspektasi | Hasil | Status |
| :--- | :--- | :--- | :---: | :---: |
| **Create** | Membuat data masjid baru melalui `MasjidService::create()` / `POST /masjid` | Data tersimpan di DB, mengembalikan ID & Entity | Verified | **PASS** |
| **Read** | Mengambil detail masjid (`find`) dan daftar terpaginasi (`paginate`) | Mengembalikan data masjid yang tepat | Verified | **PASS** |
| **Update** | Memperbarui atribut masjid melalui `MasjidService::update()` / `PUT /masjid/{id}` | Data terbarui di DB & `updated_at` diisi | Verified | **PASS** |
| **Delete** | Menghapus data masjid melalui `MasjidService::delete()` / `DELETE /masjid/{id}` | `deleted_at` diisi (Soft Delete) | Verified | **PASS** |
| **Validation** | Mengirimkan data invalid (kode duplikat, email salah, nama kosong) | Membuang `ValidationException` (HTTP 422) | Verified | **PASS** |
| **Transaction** | Eksekusi operasi CRUD dalam boundary `TransactionManager` | Membuka & commit transaksi secara atomik | Verified | **PASS** |
| **Rollback** | Terjadi exception saat proses insert/update | Transaksi di-rollback tanpa menyisa di DB | Verified | **PASS** |
| **Audit** | Eksekusi operasi CRUD pada `MasjidService` | Logging aktivitas tercatat di Audit Log | Verified | **PASS** |
| **Generic Event** | Eksekusi Create, Update, & Delete | Menembakkan `EntityCreatedEvent`, `EntityUpdatedEvent`, `EntityDeletedEvent` | Verified | **PASS** |

---

## Kesimpulan UAT
Seluruh checklist pengujian fungsional dan integrasi arsitektur pada Domain Masjid dinyatakan **PASS**.
