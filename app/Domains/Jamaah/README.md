# Domain Jamaah — Business Domain Foundation

Dokumen ini menjelaskan arsitektur, struktur internal, dan spesifikasi implementasi **Domain Jamaah** di MasjidCMS sesuai **Golden Domain Template (docs/DOMAIN_TEMPLATE.md)**.

---

## 1. Domain Architecture

Domain Jamaah dibangun secara independen di bawah namespace `App\Domains\Jamaah`. Seluruh akses data dan logika bisnis menggunakan abstraksi **Core Platform v1.0**.

```
app/Domains/Jamaah/
├── Config/             # Konfigurasi domain (JamaahConfig)
├── Controllers/        # REST Controller (JamaahController)
├── DTO/                # Data Transfer Objects (CreateJamaahDTO, UpdateJamaahDTO)
├── Entities/           # Domain Entities (Jamaah)
├── Models/             # CI4 Model Adapter (JamaahModel)
├── Policies/           # Authorization Policies (JamaahPolicy)
├── Providers/          # Domain Lifecycle Provider (JamaahProvider)
├── Repositories/       # Data Access Layer (JamaahRepository)
├── Routes/             # Modular Routes (jamaah.php)
├── Services/           # Business Logic Layer (JamaahService)
└── README.md           # Domain Documentation
```

---

## 2. Dependency Diagram

```
 [ HTTP Request ]
        │
        ▼
[ JamaahController ] ──(DTO)──► [ CreateJamaahDTO / UpdateJamaahDTO ]
        │
        ▼
 [ JamaahService ] ◄──(Extends)── [ CrudService (Core) ]
    │    │    │
    │    │    ├──────► [ Validator (Core Validation Engine) ]
    │    │    ├──────► [ UnitOfWork & TransactionManager (Core) ]
    │    │    └──────► [ EventDispatcher (Generic Events) ]
    │    │
    │    ▼
[ JamaahRepository ] ◄──(Extends)── [ BaseRepository (Core) ]
    │
    ▼
[ JamaahModel ] ──► [ DB Table: jamaahs ]
```

---

## 3. CRUD Flow & Request Lifecycle

```
Client Payload ──► JamaahController ──► DTO Parsing
                                               │
                                               ▼
                                   JamaahService::create(data)
                                               │
                                               ▼
                                    validateCreate() [BEFORE Tx]
                                               │
                                               ▼
                                     TransactionManager::begin()
                                               │
                                               ▼
                                     JamaahRepository::create()
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

## 4. Endpoints

| Method | Endpoint | Description |
| :--- | :--- | :--- |
| `GET` | `/jamaah` | Mengambil daftar Jamaah (terpaginasi) |
| `GET` | `/jamaah/{id}` | Mengambil detail Jamaah |
| `POST` | `/jamaah` | Membuat Jamaah baru |
| `PUT` | `/jamaah/{id}` | Memperbarui Jamaah |
| `DELETE` | `/jamaah/{id}` | Menghapus Jamaah (Soft Delete) |
