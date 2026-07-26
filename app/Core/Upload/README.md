# Upload Pipeline Foundation

## Purpose
Folder `app/Core/Upload` memuat komponen persiapan dan penyaringan berkas sebelum diserahkan ke Media Storage Engine (`UploadContext`, `UploadPipelineInterface`, `UploadPipeline`, `FilenameGeneratorInterface`, `ChecksumGeneratorInterface`).

## Allowed Responsibility
- Menyiapkan metadata berkas upload dalam DTO `UploadContext`.
- Memverifikasi keberadaan, MIME Type, ekstensi, dan batasan ukuran berkas.
- Membentuk nama unik berkas via `FilenameGeneratorInterface` (`UuidFilenameGenerator`).
- Menghitung nilai checksum integritas via `ChecksumGeneratorInterface` (`Sha256ChecksumGenerator`).
- Menyerahkan berkas terenkapsulasi ke `StorageService`.

## Forbidden Responsibility
- DILARANG mengeksekusi pengolahan gambar (image resize, watermark, thumbnail, OCR) pada layer fondasi ini.
- DILARANG membuat Upload Controller atau UI Form upload.
