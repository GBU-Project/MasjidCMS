# MasjidCMS — Master Data Integration Architecture (RC1)

**Versi:** 1.0 (Integration Architecture & Specification)  
**Status:** APPROVED INTEGRATION SPECIFICATION  
**Fase:** Product Development RC1  
**Tanggal:** 27 Juli 2026  
**Penulis:** Lead Software Architect & Product Engineering Team  

---

## 1. Master Data Architecture

Master Data Integration RC1 menyatukan **Domain Jamaah** dan **Domain Keluarga (Family)** menjadi satu kesatuan sistem terintegrasi tanpa mengorbankan modulitas domain (*domain isolation*) maupun Core Platform.

```
┌─────────────────────────────────────────────────────────────────────────┐
│                        MASTER DATA LAYER RC1                            │
│                                                                         │
│   ┌───────────────────────────┐         ┌───────────────────────────┐   │
│   │       JAMAAH DOMAIN       │         │       FAMILY DOMAIN       │   │
│   │                           │         │                           │   │
│   │  - Jamaah Entity          │◄───────►│  - Family Entity          │   │
│   │  - JamaahRepository       │ 1-to-N  │  - FamilyRepository        │   │
│   │  - JamaahService          │         │  - FamilyService          │   │
│   └─────────────┬─────────────┘         └─────────────┬─────────────┘   │
└─────────────────┼─────────────────────────────────────┼─────────────────┘
                  │                                     │
                  ▼                                     ▼
┌─────────────────────────────────────────────────────────────────────────┐
│                      CORE PLATFORM SERVICES (LOCKED)                    │
│                                                                         │
│   [ Validation Engine ] ──► [ UnitOfWork / Tx ] ──► [ Audit Engine ]   │
│                                                                         │
└─────────────────────────────────────────────────────────────────────────┘
```

---

## 2. Final ER Diagram

```mermaid
erDiagram
    FAMILIES ||--o{ JAMAAHS : "contains members (jamaahs.family_id)"
    JAMAAHS ||--o| FAMILIES : "headed by (families.head_jamaah_id)"

    FAMILIES {
        string id PK "UUID v4"
        string family_no UK "Nomor Registrasi KK (Unique)"
        string kk_number UK "Nomor Kartu Keluarga (Unique)"
        string name "Nama Keluarga"
        string head_jamaah_id FK "UUID Kepala Keluarga"
        text address "Alamat Utama"
        string district "Kecamatan"
        string city "Kota / Kabupaten"
        string province "Provinsi"
        string postal_code "Kode Pos"
        string family_status "ACTIVE | INACTIVE | MOVED"
        text notes "Catatan"
        datetime created_at
        datetime updated_at
        datetime deleted_at "Soft Delete"
    }

    JAMAAHS {
        string id PK "UUID v4"
        string family_id FK "UUID Keluarga (Nullable)"
        string family_relation_type "HEAD | HUSBAND | WIFE | CHILD | PARENT | GUARDIAN | OTHER"
        string member_no UK "Nomor Anggota Jamaah"
        string nik UK "NIK (Unique)"
        string full_name "Nama Lengkap"
        string gender "male / female"
        string status "ACTIVE | INACTIVE | MOVED | DECEASED"
        datetime created_at
        datetime updated_at
        datetime deleted_at "Soft Delete"
    }
```

---

## 3. Business Rules Matrix

| Komponen | Rule ID | Deskripsi Aturan Bisnis | Tindakan Sistem Jika Dilanggar |
| :--- | :--- | :--- | :--- |
| **Family** | `BR-FAM-01` | `family_no` wajib unik dan tidak boleh duplikat. | Return `422 Unprocessable Entity` (ValidationException). |
| **Family** | `BR-FAM-02` | `kk_number` bersifat nullable, namun jika diisi wajib unik. | Return `422 Unprocessable Entity`. |
| **Head Pointer**| `BR-HEAD-01` | Hanya boleh ada 1 Kepala Keluarga (`HEAD`) dalam 1 Family. | Menghentikan eksekusi & meminta menggunakan `transferHead()`. |
| **Head Pointer**| `BR-HEAD-02` | Kepala Keluarga baru pada `transferHead()` wajib berstatus `ACTIVE`. | Penolakan transfer dengan pesan error jika Jamaah `DECEASED`/`MOVED`. |
| **Delete Protection**| `BR-DEL-01` | Jamaah yang sedang menjadi Kepala Keluarga (`HEAD`) **dilarang di-soft delete**. | Throw `ValidationException` meminta transfer Head terlebih dahulu. |
| **Delete Integrity**| `BR-DEL-02` | Jika Family di-soft delete, seluruh jamaah anggotanya **tetap ada** & `family_id` di-detach (`null`). | Otomatisasi update `family_id = null` & `family_relation_type = null`. |

---

## 4. Integration Flow & Member Management

### 4.1 End-to-End Master Data Flow
1. **Create Family:** Admin membuat Keluarga baru & menunjuk Jamaah `head_jamaah_id`.
2. **Assign Member:** Admin menambahkan Istri & Anak via `addMember()`.
3. **Transfer Head:** Admin memindahkan peran Kepala Keluarga ke Istri/Anak via `transferHead()`.
4. **Move Member:** Admin memindahkan Jamaah ke KK lain via `moveMember()`.
5. **Soft Delete Safety:** Rejection saat menghapus Head Jamaah & safe detachment saat menghapus Family.

---

## 5. State Diagram

```mermaid
stateDiagram-v2
    [*] --> JamaahIndependent : Jamaah Registered (family_id = null)
    JamaahIndependent --> JamaahMember : addMember() / Assign to Family
    JamaahMember --> JamaahHead : transferHead() / Assigned as HEAD
    JamaahHead --> JamaahMember : transferHead() / Demoted to OTHER
    JamaahMember --> JamaahIndependent : removeMember() / Family Soft-deleted
    JamaahHead --> [*] : Soft Delete Blocked (Must Transfer Head First)
    JamaahIndependent --> [*] : Soft Delete Allowed (deleted_at set)
```

---

## 6. Sequence Diagram (Transfer Head of Family)

```mermaid
sequenceDiagram
    autonumber
    actor Admin as Pengurus Masjid
    participant Controller as FamilyController
    participant Service as FamilyService
    participant DB as Database Layer
    participant Event as EventDispatcher
    participant Audit as Audit Engine

    Admin->>Controller: POST /family/{id}/transfer-head (new_head_jamaah_id)
    Controller->>Service: transferHead(familyId, newHeadId)
    Service->>DB: Fetch newHead Jamaah & Verify status === 'ACTIVE'
    alt New Head Inactive or Not Found
        Service-->>Controller: Throw ValidationException (422)
        Controller-->>Admin: Error Response JSON
    else New Head Valid & Active
        Service->>DB: Update newHead Jamaah set relation_type='HEAD'
        Service->>DB: Update oldHead Jamaah set relation_type='OTHER'
        Service->>DB: Update Family set head_jamaah_id = newHeadId
        Service->>Event: Dispatch EntityUpdatedEvent('Family')
        Event->>Audit: AuditEventListener Logs Activity Entry
        Service-->>Controller: Return Updated Family Object
        Controller-->>Admin: Response JSON (HTTP 200 OK)
    end
```

---

## 7. Constraint Matrix

| Atribut Integrasi | Constraints | Enforced By |
| :--- | :--- | :--- |
| `family_no` | `UNIQUE, NOT NULL` | Database Index & `FamilyService::validateCreate` |
| `kk_number` | `UNIQUE, NULLABLE` | Database Index & `FamilyService::validateCreate` |
| `jamaahs.family_id` | `FOREIGN KEY` (Logical/DB) | `FamilyService::assignMemberToFamily` |
| `head_jamaah_id` | `UUID ACTIVE JAMAAH` | `FamilyService::transferHead` |
| `deleted_at` | `SOFT DELETE TIMESTAMP` | `JamaahModel` & `FamilyModel` |

---

## 8. Known Limitations

- Integrasi Master Data RC1 mengelola integritas relasional Jamaah & Family di backend. Tampilan kartu keluarga visual / antarmuka GUI pohon silsilah keluarga diserahkan ke komponen frontend Web App.
