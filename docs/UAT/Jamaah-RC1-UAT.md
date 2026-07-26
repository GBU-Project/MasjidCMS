# TASK-021 — Jamaah Module RC1 User Acceptance Testing (UAT) Checklist

**Domain:** Jamaah (Business Module RC1)  
**Tanggal:** 27 Juli 2026  
**Status:** PASS  

---

| Item Pengujian | Deskripsi Skenario | Ekspektasi | Hasil | Status |
| :--- | :--- | :--- | :---: | :---: |
| **Create Jamaah** | Mendaftarkan data jamaah baru via `JamaahService::create()` / `POST /jamaah` | Menghasilkan ID UUID, tersimpan di DB | Verified | **PASS** |
| **Read / Detail** | Mengambil detail jamaah berdasarkan UUID via `GET /jamaah/{id}` | Mengembalikan data detail jamaah | Verified | **PASS** |
| **Search Jamaah** | Mencari jamaah via `GET /jamaah?search=Ahmad` | Mencari di `member_no`, `nik`, `full_name`, `phone`, `email` | Verified | **PASS** |
| **Filter Jamaah** | Menyaring jamaah via `GET /jamaah?status=ACTIVE&gender=male` | Mengembalikan hasil sesuai kriteria | Verified | **PASS** |
| **Sort Jamaah** | Mengurutkan jamaah via `GET /jamaah?sort_by=full_name&sort_order=ASC` | Mengembalikan data terurut | Verified | **PASS** |
| **Update Jamaah** | Memperbarui profil jamaah via `PUT /jamaah/{id}` | Update data & `updated_at` diisi | Verified | **PASS** |
| **Delete Jamaah** | Menghapus jamaah via `DELETE /jamaah/{id}` | Soft Delete (`deleted_at` diisi) | Verified | **PASS** |
| **Validation Rules** | Input `member_no` / `nik` duplikat atau status invalid | Throw `ValidationException` (HTTP 422) | Verified | **PASS** |
| **Transaction Boundary**| Eksekusi CRUD di dalam `TransactionManager` | Commit & Rollback atomik | Verified | **PASS** |
| **Audit Log** | Operasi mutasi data jamaah | Aktivitas tercatat di Audit Log | Verified | **PASS** |
| **Generic Event** | Eksekusi Create, Update, Delete | Menembakkan Generic Domain Events | Verified | **PASS** |

---

## Kesimpulan UAT
Seluruh checklist pengujian fungsional dan aturan bisnis Modul Jamaah RC1 dinyatakan **PASS**.
