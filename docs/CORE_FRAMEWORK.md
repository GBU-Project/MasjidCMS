# MasjidCMS — Core Framework Foundation

Dokumen ini mendokumentasikan komponen teknis dasar pada layer `app/Core` sesuai dengan ketetapan **SOFTWARE_ARCHITECTURE.md (v1.1)**.

---

## 1. Filosofi Core Framework

1. **Independent & Agnostic**: Layer Core bebas dari logika domain bisnis masjid. Core tidak mengetahui adanya domain `Masjid`, `Keuangan`, `System`, `TPQ`, `Asset`, `CMS`, `Media`, atau `Communication`.
2. **One-Way Dependency**: Core hanya boleh di-import/dikonsumsi oleh layer di atasnya (Domain, Controller, Filters). Core **TIDAK BOLEH** bergantung pada `app/Domains/*`.
3. **Predictable & Lightweight**: Menyediakan kelas abstrak dasar (`BaseController`, `BaseService`, `BaseRepository`), pembungkus exception, dan helper response yang konsisten untuk seluruh aplikasi.

---

## 2. Struktur Direktori Core

```
app/Core/
├── Contracts/
│   └── README.md
├── Controllers/
│   ├── BaseController.php
│   └── README.md
├── Exceptions/
│   ├── AuthorizationException.php
│   ├── DomainException.php
│   ├── NotFoundException.php
│   ├── ValidationException.php
│   └── README.md
├── Repositories/
│   ├── BaseRepository.php
│   └── README.md
├── Services/
│   ├── BaseService.php
│   └── README.md
├── Support/
│   ├── README.md
│   └── ResponseFormatter.php
├── Traits/
│   ├── README.md
│   ├── TimestampTrait.php
│   ├── UserStampTrait.php
│   └── UuidTrait.php
└── README.md
```

---

## 3. Core Component Responsibilities

| Komponen | Tanggung Jawab Utama |
|---|---|
| **BaseController** | Load helper global, memuat request & logger framework, serta menyediakan helper `respondSuccess()` dan `respondError()`. |
| **BaseService** | Utilitas validasi (`validate`), manajemen transaksi DB (`transaction`), serta logging teknis (`logInfo`, `logError`). |
| **BaseRepository** | Helper Query Builder (`builder`), pagination (`paginate`), filtering (`applyFilters`), dan sorting (`applySorting`). |
| **ResponseFormatter** | Membentuk payload JSON standar (`status`, `message`, `data`/`errors`) untuk API & Web. |
| **DomainException** | Base class custom exception (extend `\Exception`) berserta turunan: `ValidationException` (422), `AuthorizationException` (403), `NotFoundException` (404). |
| **Traits** | Utility skeleton untuk timestamps, userstamps, dan UUID generation. |

---

## 4. Request Lifecycle dalam Arsitektur Core

```
HTTP Request
    │
    ▼
Global Filters (Auth / Cors / RateLimit)
    │
    ▼
Domain Controller (Extend App\Core\Controllers\BaseController)
    │  - Memvalidasi parameter request awal
    │  - Memanggil Domain Service
    ▼
Domain Service (Extend App\Core\Services\BaseService)
    │  - Menggunakan helper validate() & transaction() dari BaseService
    │  - Mengeksekusi logika bisnis domain
    │  - Memanggil Domain Repository
    ▼
Domain Repository (Extend App\Core\Repositories\BaseRepository)
    │  - Menggunakan helper builder(), applyFilters(), applySorting(), & paginate()
    │  - Menjalankan query ke database
    ▼
Model / Database
    │
    ▼
Return Data ke Service ──► Return ke Controller
                                │
                                ▼
ResponseFormatter (App\Core\Support\ResponseFormatter)
    │
    ▼
HTTP Response (JSON / View)
```

---

## 5. Dependency Rules

```
[ Domain Layer ] ──► [ Core Layer ] ──► [ CodeIgniter 4 Framework ]
       │                                         ▲
       └─────────────────────────────────────────┘
```

- **Rule 1**: Domain layer dapat mengimpor dan menurunkan kelas dari Core layer.
- **Rule 2**: Core layer sama sekali **TIDAK mengenal** Domain layer.
- **Rule 3**: Segala penambahan fitur teknis lintas domain harus melalui peninjauan ulang arsitektur sebelum dimasukkan ke Core.
