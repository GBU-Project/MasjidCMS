# UPGRADE DOCUMENTATION — TASK-016 (MOSQUE BUSINESS MODULES)

Dokumen ini menjelaskan panduan upgrade database, skema baru, dan seeder untuk **TASK-016: Mosque Business Modules Foundation** pada MasjidCMS.

---

## 1. MIGRATION BARU

Migration baru ditambahkan dengan timestamp aman tanpa mengubah atau merusak migration sebelumnya:

- **File Migration:** `app/Database/Migrations/2026-07-28-000013_CreateMosqueBusinessModulesTables.php`
- **Class:** `App\Database\Migrations\CreateMosqueBusinessModulesTables`

---

## 2. DOKUMENTASI SCHEMA DATABASE BARU

### 1. Tabel `bidang` (Master Data Bidang / Departemen)
- `id` (BIGINT, Primary Key, Auto Increment)
- `name` (VARCHAR 128, Not Null)
- `slug` (VARCHAR 128, Unique, Not Null)
- `description` (TEXT, Nullable)
- `icon` (VARCHAR 64, Default `'🏛️'`)
- `sort_order` (INT 11, Default `1`)
- `status` (VARCHAR 20, Default `'ACTIVE'`)
- `created_at` (DATETIME, Nullable)
- `updated_at` (DATETIME, Nullable)
- `deleted_at` (DATETIME, Soft Delete)

### 2. Tabel `pengurus` (Pengurus Masjid DKM)
- `id` (BIGINT, Primary Key, Auto Increment)
- `bidang_id` (BIGINT, Foreign Key referencing `bidang.id`)
- `nama` (VARCHAR 128, Not Null)
- `jabatan` (VARCHAR 100, Not Null)
- `foto` (VARCHAR 255, Nullable)
- `jenis_kelamin` (ENUM `'L'`, `'P'`, Default `'L'`)
- `telepon` (VARCHAR 30, Nullable)
- `email` (VARCHAR 128, Nullable)
- `alamat` (TEXT, Nullable)
- `bio` (TEXT, Nullable)
- `periode_mulai` (DATE, Nullable)
- `periode_selesai` (DATE, Nullable)
- `urutan` (INT 11, Default `1`)
- `status` (VARCHAR 20, Default `'ACTIVE'`)
- `created_at` (DATETIME, Nullable)
- `updated_at` (DATETIME, Nullable)

### 3. Tabel `program_kegiatan` (Program & Kegiatan Masjid)
- `id` (BIGINT, Primary Key, Auto Increment)
- `bidang_id` (BIGINT, Foreign Key referencing `bidang.id`)
- `nama` (VARCHAR 255, Not Null)
- `slug` (VARCHAR 255, Unique, Not Null)
- `ringkasan` (TEXT, Nullable)
- `deskripsi` (TEXT, Nullable)
- `gambar` (VARCHAR 255, Nullable)
- `penanggung_jawab` (VARCHAR 128, Nullable)
- `tanggal_mulai` (DATE, Nullable)
- `tanggal_selesai` (DATE, Nullable)
- `lokasi` (VARCHAR 128, Nullable)
- `status` (VARCHAR 20, Default `'ACTIVE'`)
- `featured` (TINYINT 1, Default `0`)
- `created_at` (DATETIME, Nullable)
- `updated_at` (DATETIME, Nullable)

### 4. Tabel `layanan_masjid` (Katalog Layanan Kemasyarakatan)
- `id` (BIGINT, Primary Key, Auto Increment)
- `nama` (VARCHAR 128, Not Null)
- `slug` (VARCHAR 128, Unique, Not Null)
- `icon` (VARCHAR 64, Default `'🤝'`)
- `gambar` (VARCHAR 255, Nullable)
- `deskripsi` (TEXT, Nullable)
- `persyaratan` (TEXT, Nullable)
- `jam_layanan` (VARCHAR 100, Nullable)
- `kontak` (VARCHAR 64, Nullable)
- `lokasi` (VARCHAR 128, Nullable)
- `status` (VARCHAR 20, Default `'ACTIVE'`)
- `urutan` (INT 11, Default `1`)
- `created_at` (DATETIME, Nullable)
- `updated_at` (DATETIME, Nullable)

### 5. Permissions Baru (`permissions`)
- `bidang.manage` (Kelola Master Data Bidang)
- `pengurus.manage` (Kelola Data Pengurus Masjid)
- `program.manage` (Kelola Program & Kegiatan Masjid)
- `layanan.manage` (Kelola Layanan Masjid)

---

## 3. CARA UPGRADE DATABASE EXISTING

Bagi instalasi MasjidCMS yang **sudah berjalan/existing**, ikuti langkah berikut untuk meng-upgrade tanpa kehilangan data lama:

```bash
# 1. Jalankan migration terbaru
php spark migrate

# 2. Jalankan seeder untuk mengisi data awal modul baru
php spark db:seed MosqueBusinessSeeder
```

> **Aman & Idempotent:** Migration menggunakan perintah `IF NOT EXISTS` dan Seeder memeriksa keberadaan data terlebih dahulu, sehingga tidak akan memduplikasi atau merusak data yang sudah ada.

---

## 4. CARA FRESH INSTALLATION

Untuk instalasi baru (Fresh Install) MasjidCMS dari nol:

```bash
# 1. Jalankan seluruh migration dari awal
php spark migrate

# 2. Jalankan Master Seeder untuk mengisi seluruh data awal platform
php spark db:seed DatabaseSeeder
```

---

## 5. SEEDER YANG DITAMBAHKAN

- **`App\Database\Seeds\MosqueBusinessSeeder`**
  - Otomatis dipanggil oleh `DatabaseSeeder`.
  - Mengisi 8 data Bidang standar, 3 data Pengurus DKM, 4 data Program Kegiatan, 4 data Layanan Masjid, serta 4 permission baru.
