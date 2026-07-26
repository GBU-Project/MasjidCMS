# MasjidCMS — Activity Log & Audit Engine Foundation

Dokumen ini mendokumentasikan arsitektur, siklus perekaman (**Audit Lifecycle**), dan spesifikasi **Activity Log & Audit Engine** di layer `app/Core/Audit` sesuai dengan **SOFTWARE_ARCHITECTURE.md (v1.1)**, **DOMAIN_EVENTS.md**, dan **TRANSACTION_ENGINE.md**.

---

## 1. Vision & Architecture Overview

Activity Log & Audit Engine beroperasi sepenuhnya terisolasi dari domain bisnis dengan memanfaatkan **Event-Driven Architecture**. 

Prinsip Perekaman Audit:
1. **Decoupled Recording**: Domain Service tidak perlu memanggil `AuditService` secara manual. Domain Service hanya mempublikasikan `DomainEvent` setelah transaksi DB berhasil di-commit.
2. **Context Enrichment**: `AuditEventListener` memperkaya data event dengan metadata konteks dari `SecurityContext` (`userId`) dan HTTP Request (`ipAddress`, `userAgent`).
3. **Guaranteed Commit Safety**: Karena Domain Event dipublikasikan hanya setelah commit transaksi bisnis berhasil, log audit hanya mencatat transaksi nyata yang telah tersimpan permanen.

---

## 2. Audit Event Flow & Lifecycle

```
+-----------------------------------------------------------------------------------+
| CRUD Operation (e.g., CrudService::create / update / delete)                      |
+-----------------------------------------------------------------------------------+
                                         │
                                         ▼ (Commit Successful)
+-----------------------------------------------------------------------------------+
| Domain Event Dispatched (EntityCreatedEvent / EntityUpdatedEvent / etc)           |
+-----------------------------------------------------------------------------------+
                                         │
                                         ▼
+-----------------------------------------------------------------------------------+
| AuditEventListener::handle(event)                                                |
|  - Calls AuditService::recordEvent(event)                                         |
|  - Enriches payload with SecurityContext::id(), IP Address, User Agent            |
+-----------------------------------------------------------------------------------+
                                         │
                                         ▼
+-----------------------------------------------------------------------------------+
| AuditService::record(AuditEntry)                                                  |
+-----------------------------------------------------------------------------------+
                                         │
                                         ▼
+-----------------------------------------------------------------------------------+
| AuditRepositoryInterface::store(entry)                                             |
|  - Inserts row to `activity_logs` table                                           |
+-----------------------------------------------------------------------------------+
```

---

## 3. Storage Schema & AuditEntry Entity

Rekaman audit disimpan ke dalam tabel `activity_logs` dengan pemetaan properti `AuditEntry`:

| Property | Database Field | Type | Purpose |
|---|---|---|---|
| `id` | `id` | BIGINT (PK) | Unique Identifier entri log. |
| `event` | `event` | VARCHAR(100) | Nama event (contoh: `masjid.created`, `auth.login`). |
| `entity` | `entity` | VARCHAR(100) | Nama entitas terkait (contoh: `Masjid`, `User`). |
| `entityId` | `entity_id` | VARCHAR(64) | ID unik entitas yang dimanipulasi. |
| `userId` | `user_id` | BIGINT/NULL | ID pengguna pelaksana aksi (dari `SecurityContext`). |
| `ipAddress` | `ip_address` | VARCHAR(45) | IP Address klien. |
| `userAgent` | `user_agent` | TEXT | User Agent browser/klien. |
| `payload` | `payload` | JSON | Detail payload perubahan data mentah. |
| `createdAt` | `created_at` | DATETIME | Timestamp saat event terjadi. |

---

## 4. Future Retention Policy Roadmap

Pada fase `Phase 2.4`, seluruh log audit tersimpan secara terus-menerus di tabel `activity_logs`.

Rencana Kebijakan Retensi (Future Architecture Roadmap):
- **Log Rotation Job**: Cron job terjadwal untuk mengarsip log audit lama (> 1 tahun) ke cold storage (S3 / File Archive / Compressed Backup).
- **Log Pruning**: Kebijakan penghapusan otomatis log berisiko rendah yang telah melewati masa retensi legal/kompatibilitas (misal > 3 tahun).
