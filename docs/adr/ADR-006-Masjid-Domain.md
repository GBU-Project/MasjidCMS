# ADR-006: Masjid Domain Architecture (Pilot Domain)

- **Status:** APPROVED
- **Tanggal:** 27 Juli 2026
- **Pengambil Keputusan:** Lead Software Architect & Core Platform Team

---

## 1. Context & Business Rationale

MasjidCMS memerlukan modul pengelolaan profil dan identitas entitas Masjid. Sebagai entitas utama dalam sistem, domain ini diposisikan sebagai **Pilot Domain** (Domain Percontohan) untuk memvalidasi arsitektur modular, Generic CRUD Engine, serta siklus hidup event/audit yang disediakan oleh Core Platform v1.0.

---

## 2. Decision Outcomes & Architectural Justifications

### 2.1 Mengapa Domain Masjid Merupakan Business Domain Terpisah (Bukan Sekadar Settings)?
- **Identitas Bisnis Independen:** Entitas Masjid bukan sekadar pasangan key-value konfigurasi aplikasi (seperti `site_name` atau `theme`). Masjid memiliki atribut relasional kompleks seperti alamat geografis (latitude/longitude), timezone operasional, tipe masjid, relasi donasi, serta kepemilikan media logo.
- **Skalabilitas Multi-Tenant / Multi-Masjid:** Pemisahan ke dalam Business Domain mandiri (`app/Domains/Masjid`) memungkinkan arsitektur berkembang ke arah multi-masjid tanpa perombakan skema dasar.

### 2.2 Mengapa Menggunakan Repository Pattern?
- **Separation of Concerns:** Controller dan Service tidak boleh berhubungan langsung dengan SQL atau CodeIgniter Query Builder.
- **Portabilitas & Testabilitas:** `MasjidRepository` mengadaptasi `MasjidModel` CI4 sekaligus mengimplementasikan `CrudRepositoryInterface`. Hal ini memungkinkan penggantian atau pengujian database secara fleksibel tanpa mengubah lapisan bisnis `MasjidService`.

### 2.3 Mengapa Menggunakan Generic Domain Events (`EntityCreatedEvent`, dll.)?
- **Prinsip "Boring is Good":** Untuk siklus hidup CRUD standar (Create, Update, Delete), Generic Domain Event yang disediakan `app/Core/Events/` sudah cukup membawa metadata entitas dan payload hasil operasi.
- **Efisiensi Kode:** Mencegah redundansi pembuatan kelas Event khusus yang hanya menduplikasi fungsi dasar tanpa membawa payload proses bisnis unik.

### 2.4 Mengapa Menggunakan `logo_media_id` (Bukan Menyimpan Path File Langsung)?
- **Integritas Media Storage Engine:** Jalur penyimpanan fisik (`path`), mime type, checksum (SHA-256), dan visibility dikelola sepenuhnya oleh `Media Storage Engine` (`app/Core/Storage`).
- **Absorpsi Perubahan File:** Perubahan nama file, pemindahan folder, atau migrasi cloud storage tidak memutus referensi data di tabel `masjids`.

---

## 3. Consequences

- Seluruh domain bisnis yang dibangun setelah ini wajib mengikuti pola referensi `app/Domains/Masjid/`.
- Perubahan pada skema `masjids` harus selalu diselaraskan dengan `Masjid` Entity, `CreateMasjidDTO`, `UpdateMasjidDTO`, dan `MasjidRepository`.
