# ADR-010: Jamaah Business Module Architecture (RC1)

- **Status:** APPROVED
- **Tanggal:** 27 Juli 2026
- **Pengambil Keputusan:** Lead Software Architect & Product Development Team

---

## 1. Context & Business Rationale

Sebagai modul bisnis pertama di atas **MasjidCMS Platform v1.0** (Tag `v1.0.0-platform`), **Modul Jamaah RC1** menangani pengelolaan pendataan jamaah masjid secara komprehensif (profil, status keanggotaan `ACTIVE`, `INACTIVE`, `MOVED`, `DECEASED`, data demografi, pencarian multi-kolom, penyaringan, dan pengurutan).

---

## 2. Decision Outcomes & Architectural Justifications

### 2.1 Penggunaan UUID sebagai Identifier Utama (`id`)
- **Keamanan & Skalabilitas:** Menggunakan Universally Unique Identifier (UUID v4) 36-karakter untuk mencegah enumerasi ID berurutan (*sequential ID scraping*) dan memudahkan sinkronisasi data antar-masjid di masa mendatang.

### 2.2 Status Lifecycle Jamaah (`ACTIVE`, `INACTIVE`, `MOVED`, `DECEASED`)
- **Integritas Data Keanggotaan:** Mengakomodasi perubahan status jamaah tanpa menghapus riwayat data. Jamaah yang pindah rumah (`MOVED`) atau meninggal dunia (`DECEASED`) tetap tersimpan untuk kepentingan arsip dan laporan historis.

### 2.3 Pencarian (Search), Penyaringan (Filter), dan Pengurutan (Sort)
- **Eksekusi Terpusat di Repository:** Fungsi `JamaahRepository::searchAndPaginate` menggabungkan `LIKE` multi-kolom (`member_no`, `nik`, `full_name`, `phone`, `email`), filter kriteria (`status`, `gender`, `city`, `district`), dan sorting (`full_name`, `member_no`, `created_at`) dalam satu kueri terpaginasi efisien.

---

## 3. Consequences

- Seluruh API Jamaah menyajikan respons JSON terstandar via `ResponseFormatter`.
- Log audit dan Generic Domain Events (`EntityCreatedEvent`, `EntityUpdatedEvent`, `EntityDeletedEvent`) otomatis mencatat setiap transaksi perubahan data Jamaah.
