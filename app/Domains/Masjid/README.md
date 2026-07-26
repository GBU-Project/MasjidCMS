# Domain Masjid — Pilot Domain Foundation

Dokumen ini menjelaskan arsitektur, struktur internal, dan spesifikasi implementasi **Domain Masjid** sebagai Pilot Domain di MasjidCMS sesuai **SOFTWARE_ARCHITECTURE.md (v1.1)** dan **ADR-006**.

---

## 1. Domain Architecture

Domain Masjid dibangun secara independen di bawah namespace `App\Domains\Masjid`. Seluruh akses data dan logika bisnis menggunakan abstraksi **Core Platform v1.0** tanpa mengubah komponen internal `app/Core/`.

```
app/Domains/Masjid/
├── Config/             # Domain specific configuration
├── Controllers/        # REST API Controllers (MasjidController)
├── DTO/                # Data Transfer Objects (CreateMasjidDTO, UpdateMasjidDTO)
├── Entities/           # Domain Entities (Masjid)
├── Models/             # CI4 Model Adapter (MasjidModel)
├── Policies/           # Authorization Policies (MasjidPolicy)
├── Providers/          # Domain Lifecycle Provider (MasjidProvider)
├── Repositories/       # Data Access Layer (MasjidRepository)
├── Routes/             # Modular Routes (masjid.php)
├── Services/           # Business Logic Layer (MasjidService)
└── README.md           # Domain Documentation
```

---

## 2. Dependency Diagram

```
 [ HTTP Request / Client ]
            │
            ▼
   [ MasjidController ] ──(Uses DTO)──► [ CreateMasjidDTO / UpdateMasjidDTO ]
            │
            ▼
    [ MasjidService ] ◄──(Extends)── [ CrudService (Core) ]
       │    │    │
       │    │    ├──────► [ Validator (Core Validation Engine) ]
       │    │    ├──────► [ UnitOfWork & TransactionManager (Core) ]
       │    │    └──────► [ EventDispatcher (Generic Domain Events) ]
       │    │
       │    ▼
  [ MasjidRepository ] ◄──(Extends)── [ BaseRepository (Core) ]
       │
       ▼
  [ MasjidModel Adapter ] ──► [ Database (masjids table) ]
```

---

## 3. CRUD Flow & Request Lifecycle

```
Client Payload ──► MasjidController ──► DTO Parsing
                                              │
                                              ▼
                                    MasjidService::create(data)
                                              │
                                              ▼
                                   validateCreate() [BEFORE Tx]
                                              │
                                              ▼
                                    TransactionManager::begin()
                                              │
                                              ▼
                                    MasjidRepository::create()
                                              │
                                              ▼
                                    UnitOfWork::registerNew()
                                              │
                                              ▼
                                    TransactionManager::commit()
                                              │
                                              ▼
                                    EntityCreatedEvent dispatched
                                              │
                                              ▼
                                    ResponseFormatter (HTTP 201)
```

---

## 4. Event Lifecycle

Domain Masjid tidak membuat Event khusus, melainkan menggunakan **Generic Domain Events**:
1. **Create Operation:** Menembakkan `App\Core\Events\EntityCreatedEvent('Masjid', $entity)`
2. **Update Operation:** Menembakkan `App\Core\Events\EntityUpdatedEvent('Masjid', $entity)`
3. **Delete Operation:** Menembakkan `App\Core\Events\EntityDeletedEvent('Masjid', $id)`

Event ditangkap oleh **Audit Log Listener** (Core) untuk secara otomatis mencatat jejak audit entitas tanpa duplicate logic.

---

## 5. Upload Flow (`logo_media_id`)

Setiap lampiran logo masjid dikelola melalui **Media Storage Engine**:
1. File diunggah melalui `StorageService` / `UploadPipeline`.
2. Storage Engine menghasilkan objek `Media` beserta `media_id`.
3. ID media disimpan pada atribut `logo_media_id` di tabel `masjids`.

---

## 6. Verification & Endpoints

| Method | Endpoint | Description |
| :--- | :--- | :--- |
| `GET` | `/masjid` | Mengambil daftar masjid (terpaginasi) |
| `GET` | `/masjid/{id}` | Mengambil detail profil masjid |
| `POST` | `/masjid` | Membuat profil masjid baru |
| `PUT` | `/masjid/{id}` | Memperbarui profil masjid |
| `DELETE` | `/masjid/{id}` | Menghapus profil masjid (Soft Delete) |
