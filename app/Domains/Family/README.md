# Family Business Module RC1 — Domain Documentation

Dokumen ini mendokumentasikan spesifikasi teknis, skema data, ER diagram, alur bisnis, dan daftar REST endpoint untuk **Modul Keluarga RC1** pada **MasjidCMS Platform v1.0**.

---

## 1. Domain Architecture & ER Diagram

```mermaid
erDiagram
    FAMILIES ||--o{ JAMAAHS : "has members"

    FAMILIES {
        string id PK "UUID v4"
        string family_no UK "Nomor Registrasi Keluarga (Unique)"
        string kk_number UK "Nomor Kartu Keluarga (Unique)"
        string name "Nama Keluarga"
        string head_jamaah_id FK "UUID Kepala Keluarga"
        text address "Alamat Tempat Tinggal"
        string district "Kecamatan"
        string city "Kota / Kabupaten"
        string province "Provinsi"
        string postal_code "Kode Pos"
        string family_status "ACTIVE | INACTIVE | MOVED"
        text notes "Catatan Tambahan"
        datetime created_at
        datetime updated_at
        datetime deleted_at "Soft Delete"
    }

    JAMAAHS {
        string id PK "UUID v4"
        string family_id FK "UUID Keluarga"
        string family_relation_type "HEAD | HUSBAND | WIFE | CHILD | PARENT | GUARDIAN | OTHER"
        string member_no UK "Nomor Anggota"
        string full_name "Nama Lengkap"
    }
```

---

## 2. REST API Endpoints Specification

| Method | Endpoint | Description |
| :--- | :--- | :--- |
| `GET` | `/family` | Menampilkan daftar keluarga (Paginasi, Search, Filter, Sort) |
| `GET` | `/family/{id}` | Menampilkan detail keluarga berdasarkan UUID |
| `POST` | `/family` | Membuat Kartu Keluarga baru & menunjuk Kepala Keluarga |
| `PUT` | `/family/{id}` | Memperbarui data keluarga |
| `DELETE` | `/family/{id}` | Menghapus keluarga (Soft Delete) |
| `GET` | `/family/{id}/members` | Menampilkan seluruh jamaah anggota keluarga |
| `POST` | `/family/{id}/transfer-head` | Memindahkan peran Kepala Keluarga ke anggota lain |
