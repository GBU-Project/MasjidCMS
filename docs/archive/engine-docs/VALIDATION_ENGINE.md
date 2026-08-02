# MasjidCMS — Validation Engine Foundation

Dokumen ini mendokumentasikan arsitektur, siklus eksekusi, dan spesifikasi **Validation Engine** di layer `app/Core/Validation` sesuai **SOFTWARE_ARCHITECTURE.md (v1.1)**, **CRUD_ENGINE.md**, dan **TRANSACTION_ENGINE.md**.

---

## 1. Vision & Architecture Principle

Validasi di MasjidCMS ditempatkan secara eksplisit pada **Service Layer** melalui **Validation Engine**. 

Prinsip Utama:
1. **Service Layer Ownership**: Validation **bukan** milik Controller (Controller hanya melakukan parsing DTO) dan **bukan** milik Repository.
2. **Pre-Transaction Execution**: Validasi di-eksekusi pada Hook `validateCreate()` / `validateUpdate()` **sebelum** transaksi database dibuka (`TransactionManager::begin()`).
3. **Decoupled Rules**: Aturan validasi bersifat independen (`ValidationRuleInterface`) dan dapat dirangkai dinamis ke dalam runner (`ValidatorInterface`).

---

## 2. Validation Flow & Lifecycle

```
[ Request Payload ] ──► Controller (DTO Parsing)
                             │
                             ▼
                 CrudService::create(DTO)
                             │
                             ▼
                 validateCreate($data) ──► Executed BEFORE Transaction
                             │
                             ▼
                 Validator::validate($data)
                             │
              ┌──────────────┴──────────────┐
              ▼                             ▼
    [ Validation Success ]        [ Validation Failure ]
              │                             │
              ▼                             ▼
    Begin DB Transaction          Throw App\Core\Exceptions\ValidationException
                                            │
                                            ▼
                                  Controller catches & returns HTTP 422 JSON
```

---

## 3. Basic Rules Reference

| Rule Class | Namespace | Purpose |
|---|---|---|
| **RequiredRule** | `App\Core\Validation\Rules\RequiredRule` | Memastikan nilai tidak null, kosong, atau whitespace. |
| **StringRule** | `App\Core\Validation\Rules\StringRule` | Memastikan nilai bertipe string. |
| **IntegerRule** | `App\Core\Validation\Rules\IntegerRule` | Memastikan nilai bertipe integer/angka bulat. |
| **BooleanRule** | `App\Core\Validation\Rules\BooleanRule` | Memastikan nilai bertipe boolean. |
| **EmailRule** | `App\Core\Validation\Rules\EmailRule` | Memastikan nilai berformat email valid. |
| **UrlRule** | `App\Core\Validation\Rules\UrlRule` | Memastikan nilai berformat URL valid. |
| **LengthRule** | `App\Core\Validation\Rules\LengthRule` | Memastikan panjang string dalam rentang min-max. |

---

## 4. Class & Interface Diagram

```
App\Core\Contracts\Validation
 ├── ValidationRuleInterface (Interface)
 └── ValidatorInterface (Interface)

App\Core\Validation
 ├── ValidationResult (Value Object)
 ├── Validator (Implements ValidatorInterface)
 └── Rules\
      ├── RequiredRule (Implements ValidationRuleInterface)
      ├── StringRule
      ├── IntegerRule
      ├── BooleanRule
      ├── EmailRule
      ├── UrlRule
      └── LengthRule
```
