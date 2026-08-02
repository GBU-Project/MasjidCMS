# TASK-XXX — Jamaah Domain User Acceptance Testing (UAT) Checklist

**Domain:** Jamaah  
**Tanggal:** 2026  
**Status:** PASS  

---

| Item Pengujian | Deskripsi Skenario | Ekspektasi | Hasil | Status |
| :--- | :--- | :--- | :---: | :---: |
| **Create** | Membuat data $jamaah baru melalui `JamaahService::create()` | Data tersimpan di DB | Verified | **PASS** |
| **Read** | Mengambil detail $jamaah (`find`) dan daftar (`paginate`) | Mengembalikan data valid | Verified | **PASS** |
| **Update** | Memperbarui data $jamaah melalui `JamaahService::update()` | Data terbarui | Verified | **PASS** |
| **Delete** | Menghapus data $jamaah melalui `JamaahService::delete()` | Soft Delete terisi | Verified | **PASS** |
| **Validation** | Mengirimkan data invalid | Throw ValidationException | Verified | **PASS** |
| **Transaction** | Eksekusi operasi CRUD di Transaction Boundary | Commit & Rollback | Verified | **PASS** |
| **Audit** | Eksekusi operasi CRUD | Log audit tercatat | Verified | **PASS** |
| **Generic Event** | Eksekusi Create, Update, & Delete | Generic events fired | Verified | **PASS** |
