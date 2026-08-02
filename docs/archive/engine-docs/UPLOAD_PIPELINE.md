# MasjidCMS — Upload Pipeline Foundation

Dokumen ini mendokumentasikan arsitektur, urutan tahapan penyaringan (**Pipeline Flow**), dan spesifikasi **Upload Pipeline Foundation** di layer `app/Core/Upload` sesuai dengan **SOFTWARE_ARCHITECTURE.md (v1.1)**, **MEDIA_STORAGE.md**, dan **VALIDATION_ENGINE.md**.

---

## 1. Vision & Architecture Overview

Upload Pipeline bertindak sebagai middleware penyaring teknis yang menyiapkan dan melegitimasi berkas upload sebelum diserahkan ke `StorageService`.

Prinsip Utama:
1. **Sanitization & Validation First**: Seluruh atribut berkas (MIME, ekstensi, ukuran) diverifikasi secara ketat sebelum penulisan ke disk fisik.
2. **UUID Filename Strategy**: Seluruh nama file yang di-upload diubah menjadi nama acak UUID v4 untuk menghindari *Path Traversal Attack* dan *File Overwrite Conflict*.
3. **Integrity Checksum**: Nilai checksum (SHA-256) dihitung dan disimpan untuk menjamin integritas data.

---

## 2. Upload Pipeline Execution Sequence

```
[ Application Upload Call ] ──► StorageService::upload(UploadContext)
                                         │
                                         ▼
                             UploadPipeline::process()
                                         │
 ┌───────────────────────────────────────┴───────────────────────────────────────┐
 │ 1. verifyFile()       -> Memastikan keberadaan payload berkas                │
 │ 2. validateMime()     -> Memastikan MIME Type diizinkan                      │
 │ 3. validateExtension()-> Memastikan ekstensi file diizinkan                 │
 │ 4. validateSize()     -> Memastikan ukuran tidak melebihi maxSizeBytes       │
 │ 5. generateChecksum() -> Menghitung checksum SHA-256                         │
 │ 6. generateFilename() -> Membentuk nama unik UUID v4 & target path           │
 └───────────────────────────────────────┬───────────────────────────────────────┘
                                         │
                                         ▼
                            StorageService::store()
                                         │
                                         ▼
                            StorageProviderInterface (LocalStorageProvider)
                                         │
                                         ▼
                                   [ Filesystem ]
```

---

## 3. Filename & Checksum Strategy

- **Filename Strategy**: Menggunakan `UuidFilenameGenerator` yang menghasilkan String UUID v4 (contoh: `d3b07384-d113-40a4-a081-37d40eb85a73.png`).
- **Checksum Strategy**: Menggunakan `Sha256ChecksumGenerator` yang menghasilkan hash SHA-256 64 karakter heksadesimal dari konten mentah berkas.

---

## 4. Future Image Processing & Optimization Roadmap

Pada Phase 2.7, Upload Pipeline berfokus pada penyaringan teknis dan penyimpanan mentah.

Rencana Ekstensi (Future Architecture Roadmap):
- **ImageProcessorPipe**: Pipe tambahan untuk auto-resize, kompresi WebP/JPEG, dan pembuatan thumbnail gambar secara asinkron.
- **VirusScanPipe**: Pipe pemindai malware/virus pada berkas yang di-upload.
