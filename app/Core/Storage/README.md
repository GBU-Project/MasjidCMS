# Media Storage Engine

## Purpose
Folder `app/Core/Storage` memuat komponen dasar pengelola berkas / media storage (`StorageProviderInterface`, `LocalStorageProvider`, `StorageFactory`, `StorageConfig`, `StorageService`, `Media`) di MasjidCMS.

## Allowed Responsibility
- Menyediakan abstraksi penyimpanan berkas terpadu via `StorageProviderInterface`.
- Menyediakan driver `LocalStorageProvider` untuk filesystem server lokal.
- Menyediakan `StorageService` yang menyediakan operasi `store`, `delete`, `move`, `copy`, `url`, `temporaryUrl`.
- Mengembalikan entitas `Media` yang membawa properti metadata file.

## Forbidden Responsibility
- DILARANG mengeksekusi pengolahan gambar (image resize, watermark, thumbnail) pada layer fondasi ini.
- DILARANG menangani UI upload atau HTTP Controller upload secara langsung.
