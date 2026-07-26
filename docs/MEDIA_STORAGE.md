# MasjidCMS — Media Storage Engine Foundation

Dokumen ini mendokumentasikan arsitektur, siklus kerja, dan spesifikasi **Media Storage Engine** di layer `app/Core/Storage` sesuai dengan **SOFTWARE_ARCHITECTURE.md (v1.1)** dan **CORE_FRAMEWORK.md**.

---

## 1. Vision & Architecture Overview

Media Storage Engine bertindak sebagai satu-satunya pintu gerbang penyimpanan berkas (image, document, PDF, media) di MasjidCMS.

Prinsip Utama:
1. **Decoupled Driver**: Aplikasi (`StorageService`) tidak pernah bergantung pada detail penyimpanan server lokal. Aplikasi hanya berinteraksi via `StorageProviderInterface`.
2. **Pluggable Cloud Roadmap**: Pengubahan driver penyimpanan (misal dari Local Disk ke Amazon S3 atau MinIO) cukup dilakukan melalui konfigurasi `StorageConfig::$default_provider` tanpa merubah kode aplikasi.
3. **Public & Private Isolation**: Penyimpanan memisahkan file akses umum (public web root `uploads/`) dan file rahasia/dokumen internal (`storage/private/`).

---

## 2. Media Storage Architecture

```
[ Application / Domain Service ]
               │
               ▼
[ StorageService ] (App\Core\Storage\Services\StorageService)
               │
               ▼ (Depends ONLY on Contract)
[ StorageProviderInterface ] (App\Core\Contracts\Storage\StorageProviderInterface)
               │
               ├───────────────► LocalStorageProvider (Active - Local Filesystem)
               │
               ├───────────────► S3StorageProvider (Future Roadmap)
               ├───────────────► MinIOStorageProvider (Future Roadmap)
               ├───────────────► AzureBlobStorageProvider (Future Roadmap)
               └───────────────► GoogleCloudStorageProvider (Future Roadmap)
```

---

## 3. Storage Provider & Future Cloud Roadmap

| Provider Class | Storage Driver | Status | Description |
|---|---|---|---|
| **LocalStorageProvider** | Local Disk Filesystem | **ACTIVE (Phase 2.5)** | Menyimpan file ke `storage/` atau `public/uploads/` server. |
| **S3StorageProvider** | AWS S3 / Compatible | *PLANNED (Future)* | Driver cloud storage Amazon S3 / DigitalOcean Spaces. |
| **MinIOStorageProvider** | MinIO Self-Hosted | *PLANNED (Future)* | Driver S3-compatible private cloud storage. |
| **AzureBlobStorageProvider** | Microsoft Azure Blob | *PLANNED (Future)* | Driver cloud storage Azure. |
| **GoogleCloudStorageProvider** | Google Cloud Storage | *PLANNED (Future)* | Driver cloud storage GCS. |

---

## 4. Media Entity Metadata

Setiap eksekusi `StorageService::store()` mengembalikan objek entitas `App\Core\Storage\Media` yang menyimpan metadata berkas:

```php
new Media(
    id: null,
    disk: 'local',
    path: 'masjid/logos/logo.png',
    filename: 'logo.png',
    extension: 'png',
    mimeType: 'image/png',
    size: 204850,
    checksum: 'e99a18c428cb38d5f260853678922e03',
    visibility: 'public',
    createdAt: '2026-07-26 22:36:00'
);
```
