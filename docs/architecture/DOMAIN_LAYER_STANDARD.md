# MasjidCMS — Domain Layer Implementation Architecture Standard

**Versi:** 1.0 (DDD Implementation Standard & Architecture Guideline)  
**Status:** APPROVED ARCHITECTURE SPECIFICATION  
**Fase:** Product Development RC1  
**Tanggal:** 27 Juli 2026  
**Penulis:** Lead Software Architect & DDD Architecture Board  

---

## Executive Summary

Dokumen ini mendokumentasikan **Standar Implementasi Domain Layer (Domain-Driven Design - DDD)** yang berlaku wajib untuk seluruh domain modul bisnis pada **MasjidCMS Platform** (*Organization, Jamaah, Family, Financial, Masjid, Zis, Qurban*).

Standar ini menjamin bahwa seluruh kode domain terisolasi secara murni (*Clean Architecture*), independen dari kerangka kerja (*framework agnostic*), serta bebas dari ketergantungan HTTP, pengemudi basis data (*DB Drivers*), maupun elemen antarmuka (*UI/Views*).

---

## 1. Domain Folder Structure Standard

Setiap modul domain bisnis di dalam `app/Domains/<DomainName>/` WAJIB mengikuti struktur direktori kanonis berikut:

```text
app/Domains/<DomainName>/
├── DTO/                        # Data Transfer Objects (Request/Response)
├── Entities/                   # Domain Entities & Aggregate Roots
│   └── ValueObjects/           # Immutable Value Objects
├── Events/                     # Pure Domain Events
├── Exceptions/                 # Domain-Specific Exception Classes
├── Factories/                  # Entity Construction Factories
├── Policies/                   # Domain Security & Authorization Policies
├── Providers/                  # Domain Service Provider (Registration)
├── Repositories/               # Repository Layer
│   ├── Contracts/              # Domain Repository Interfaces (Mandatory in Domain)
│   └── Database/               # Persistence Implementations
├── Routes/                     # Domain Route Definitions
├── Services/                   # Domain Services (Business Use Cases)
├── Specifications/             # Reusable Business Rule Predicates
└── README.md                   # Domain Specification & API Contracts
```

---

## 2. Entity Standard

### 2.1 Aturan Enkapsulasi Entitas
- **Identitas Unik:** Entitas wajib memiliki identitas unik (`id BIGINT` atau `uuid CHAR(36)`). Dua entitas dianggap sama jika dan hanya jika identitas uniknya sama (`$entityA->equals($entityB)`).
- **Protected State:** Seluruh properti status internal wajib bertipe `protected` atau `private`. Penanganan properti wajib melalui *getter* dan *mutator methods* bermakna bisnis.
- **State Validation:** Entitas bertanggung jawab menjaga keabsahan status internalnya sendiri (*self-validating invariants*).

```php
// Contoh Konvensi Entitas Domain
namespace App\Domains\Financial\Entities;

use App\Domains\Financial\Exceptions\BusinessRuleException;

class Fund
{
    protected int $id;
    protected string $uuid;
    protected string $fundCode;
    protected string $status;

    public function deactivate(): void
    {
        if ($this->status === 'INACTIVE') {
            throw new BusinessRuleException("Kantong dana sudah berstatus INACTIVE.");
        }
        $this->status = 'INACTIVE';
    }
}
```

---

## 3. Aggregate Root Standard

### 3.1 Batas Konsistensi Transaksional
- **Aggregate Root (Akar Agregat):** Entitas utama yang menjadi pintu gerbang tunggal untuk mengakses dan memanipulasi entitas anak (*sub-entities*) di dalam agregat yang sama.
- **Direct Access Prohibition:** Objek luar DILARANG mengabaikan Aggregate Root untuk mengubah sub-entitas secara langsung.
- **Global Identity:** Hanya Aggregate Root yang memiliki identitas global yang dapat dicari langsung via Repository.

---

## 4. Value Object Standard

### 4.1 Prinsip Value Object
- **Immutability (Sifat Tak Berubah):** Nilai Value Object diatur saat instansiasi dan **DILARANG** diubah. Perubahan menghasilkan instance baru.
- **Value-Based Equality:** Dua Value Object dianggap sama jika seluruh nilainya identik.
- **Self-Validation:** Throws `InvalidArgumentException` atau `DomainException` jika format nilai yang dimasukkan tidak valid.

```php
// Contoh Value Object Imutabel
namespace App\Domains\Financial\Entities\ValueObjects;

use InvalidArgumentException;

final class Money
{
    private decimal $amount;
    private string $currency;

    public function __construct(float $amount, string $currency = 'IDR')
    {
        if ($amount < 0) {
            throw new InvalidArgumentException("Nominal uang tidak boleh bernilai negatif.");
        }
        $this->amount = $amount;
        $this->currency = $currency;
    }
}
```

---

## 5. Repository Interface Standard (Domain Contracts)

### 5.1 Lokasi & Aturan Interface
- Interface Repository **WAJIB** berada di dalam Domain Layer (`App\Domains\<DomainName>\Repositories\Contracts\`).
- Metode repository menggunakan istilah bahasa bisnis domain (`findFundByCode`, `save`, `delete`), bukan istilah SQL (*query*, *execute*, *table*).

---

## 6. Repository Implementation Boundary

- Implementasi konkret repository (misal `SqliteFundRepository` atau `MySQLFundRepository`) ditempatkan di `App\Domains\<DomainName>\Repositories\Database\`.
- Domain Layer hanya bergantung pada **Interface Contract**, sehingga implementasi penyimpanan dapat diganti tanpa menyentuh logika bisnis.

---

## 7. Domain Service Standard

### 7.1 Kapan Menggunakan Domain Service?
Domain Service digunakan untuk logika bisnis murni yang:
1. Melibatkan multiple Aggregate Roots (misal transfer antar-Fund).
2. Memerlukan eksekusi aturan bisnis yang tidak cocok dimiliki oleh satu entitas tunggal.

---

## 8. Factory Standard

### 8.1 Tanggung Jawab Factory
Factory digunakan saat konstruksi Aggregate Root memerlukan alur kompleks, pemrosesan default value, atau instansiasi bertingkat yang menguji *invariants*.

---

## 9. Specification Pattern

### 9.1 Predikat Bisnis Reusable
Specification merangkum aturan bisnis boolean yang dapat dipasangkan pada pencarian repository maupun evaluasi entitas:

```php
namespace App\Domains\Financial\Specifications;

use App\Domains\Financial\Entities\Fund;

class IsRestrictedFundSpecification
{
    public function isSatisfiedBy(Fund $fund): bool
    {
        return $fund->getFundType() === 'RESTRICTED';
    }
}
```

---

## 10. Domain Event Policy

### 10.1 Notifikasi Kejadian Domain
- Event domain merepresentasikan fakta sejarah (*immutable past event*) yang telah terjadi di dalam domain (misal `FinancialTransactionPostedEvent`).
- Event implementasi menggunakan interface `App\Core\Contracts\Events\DomainEventInterface`.

---

## 11. Exception Hierarchy Standard

Seluruh exception domain wajib mewarisi hierarki exception resmi platform:

```text
App\Core\Exceptions\BaseException
└── App\Domains\<DomainName>\Exceptions\DomainException
    ├── BusinessRuleException        # Pelanggaran aturan bisnis (e.g. Dana Zakat minus)
    ├── EntityNotFoundException      # Entitas tidak ditemukan
    └── InvalidValueObjectException  # Gagal validasi Value Object
```

---

## 12. Business Rule Placement Matrix

| Lokasi Aturan Bisnis | Jenis Aturan / Tanggung Jawab | Contoh Kasus |
| :--- | :--- | :--- |
| **Value Object** | Format data internal & pembatas kisaran nilai | Validasi format Kode COA (`10100`) / Nominal Uang > 0 |
| **Entity** | Aturan status internal & invariansi tunggal | Transaksi berstatus `POSTED` dilarang diedit |
| **Aggregate Root** | Konsistensi antar-entitas di dalam agregat | Total Debit wajib sama dengan Kredit di Journal |
| **Domain Service** | Aturan lintas-agregat / Inter-fund logic | Penolakan transfer dari Dana Zakat ke Operasional |
| **Specification** | Predikat kelayakan aturan reusable | Pemeriksaan syarat penutupan kas bulanan |

---

## 13. Transaction Boundary (`UnitOfWork`)

- Perubahan pada Aggregate Root wajib terjadi dalam batas transaksi atomic basis data (`UnitOfWork`).
- Jika operasi domain gagal di tengah jalan, seluruh entri wajib di-rollback untuk menjaga atomisitas.

---

## 14. Strict Dependency Rules (Inversion of Control)

```text
┌────────────────────────────────────────────────────────────────────────┐
│                        STRICT DEPENDENCY RULES                         │
├────────────────────────────────────────────────────────────────────────┤
│ 1. NO HTTP / CONTROLLER DEPENDENCY: Domain Layer DILARANG mengimpor    │
│    CodeIgniter\HTTP\RequestInterface atau BaseController.              │
│                                                                        │
│ 2. NO UI / VIEW DEPENDENCY: Domain Layer DILARANG memanggil fungsi     │
│    rendering view atau template UI.                                    │
│                                                                        │
│ 3. NO DB DRIVER DEPENDENCY: Domain Layer DILARANG memanggil SQL query  │
│    mentah atau driver MySQLi/SQLite secara langsung.                   │
│                                                                        │
│ 4. NO FRAMEWORK HELPER: Gunakan standar PHP murni / Core Contracts.    │
└────────────────────────────────────────────────────────────────────────┘
```

---

## 15. Coding Conventions

1. **Interface Naming:** Diakhiri dengan `Interface` (e.g., `FundRepositoryInterface`).
2. **DTO Naming:** Diakhiri dengan `Request` atau `Response` (e.g., `CreateTransactionRequest`).
3. **Event Naming:** Menggunakan kata kerja lampau (e.g., `JournalPostedEvent`).
4. **Exception Naming:** Diakhiri dengan `Exception` (e.g., `BusinessRuleException`).
5. **Strict Typing:** `declare(strict_types=1);` pada seluruh file domain.
