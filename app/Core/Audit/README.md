# Activity Log & Audit Engine

## Purpose
Folder `app/Core/Audit` memuat komponen perekaman audit trail & log aktivitas pengguna (`AuditEntry`, `AuditRepositoryInterface`, `DatabaseAuditRepository`, `AuditConfig`, `AuditService`, `AuditEventListener`) di MasjidCMS.

## Allowed Responsibility
- Mendengarkan Domain Event via `AuditEventListener`.
- Merubah payload `DomainEventInterface` menjadi `AuditEntry`.
- Menyimpan entri audit ke database (`activity_logs` table) via `AuditRepositoryInterface`.
- Menyediakan pengolahan data `userId`, `ipAddress`, dan `userAgent` dari `SecurityContext` & HTTP Request.

## Forbidden Responsibility
- DILARANG menampilkan UI dashboard/reporting di dalam folder Core Audit.
- DILARANG memicu eksekusi pembersihan data otomatis (Retention Job) secara synchronous di main request thread.
