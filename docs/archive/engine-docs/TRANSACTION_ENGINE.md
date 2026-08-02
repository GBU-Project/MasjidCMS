# MasjidCMS — Transaction & Unit of Work Engine Foundation

Dokumen ini menjelaskan rancangan, alur eksekusi, dan urutan peristiwa (**Transaction Lifecycle & Event Ordering**) dari **Transaction Layer & Unit of Work Engine** di layer `app/Core/Transactions` sesuai **SOFTWARE_ARCHITECTURE.md (v1.1)**, **CORE_FRAMEWORK.md**, **CRUD_ENGINE.md**, dan **DOMAIN_EVENTS.md**.

---

## 1. Vision & Transaction Isolation Principle

Setiap operasi manipulasi data (CUD - Create, Update, Delete) pada `CrudService` wajib berada di dalam batas transaksi yang terisolasi secara ACID.

Prinsip Utama:
- **Atomicity**: Seluruh perubahan disimpan sekaligus (Commit) atau dibatalkan sama sekali (Rollback).
- **Strict Event Ordering**: **Domain Event HANYA BOLEH dipublikasikan SETELAH transaksi database berhasil di-commit.** Jika transaksi gagal dan di-rollback, Domain Event **DILARANG HARUS** ditayangkan. Hal ini mencegah terjadinya *phantom/side-effect event* pada listener audit log atau notifikasi.

---

## 2. Transaction & Unit of Work Lifecycle

```
[ Request Action ]
        │
        ▼
1. validateCreate() / validateUpdate()   (Validation Hook - Outside Transaction)
        │
        ▼
2. TransactionManager::begin()          (Transaction Boundary Opened)
        │
        ├─► 3. beforeCreate() / beforeUpdate()
        ├─► 4. Repository Execution (SQL Insert/Update/Delete)
        ├─► 5. UnitOfWork::registerNew() / registerDirty() / registerDeleted()
        │
        ▼
6. TransactionManager::commit()         (Database Commit Executed)
        │
        ├─► [ Transaction Success ] ──► 7. afterCreate() ──► EventDispatcher::dispatch()
        │
        └─► [ Transaction Exception ] ─► TransactionManager::rollback()
                                           └─► UnitOfWork::rollback()
                                           └─► NO EVENT DISPATCHED!
```

---

## 3. Commit & Rollback Sequence

```
CrudService                    TransactionManager           UnitOfWork            EventDispatcher
    │                                  │                        │                        │
    ├─── begin() ─────────────────────►│                        │                        │
    ├───Repository CUD ────────────────┼───────────────────────►│ (register)             │
    │                                  │                        │                        │
    ├─── commit() ────────────────────►│                        │                        │
    │    (Success)                     ├─── (Commit DB)        │                        │
    │                                  │                        │                        │
    └─── afterCreate() ─────────────────────────────────────────────────────────────────►│ (dispatch)
    
  [ IF EXCEPTION OCCURS ]
    │                                  │                        │                        │
    ├─── rollback() ──────────────────►│ (Rollback DB)          │                        │
    └─── rollback() ───────────────────────────────────────────►│ (Clear tracker)        │
    (Throw Exception & NO Event Dispatched)
```

---

## 4. Component Structure

| Interface / Class | Namespace | Purpose |
|---|---|---|
| **TransactionManagerInterface** | `App\Core\Contracts\Transactions\TransactionManagerInterface` | Contract isolasi batas transaksi (`begin`, `commit`, `rollback`, `transaction`). |
| **DatabaseTransactionManager** | `App\Core\Transactions\DatabaseTransactionManager` | Implementasi pengelola transaksi database berbasis CI4 DB Driver. |
| **UnitOfWorkInterface** | `App\Core\Contracts\Transactions\UnitOfWorkInterface` | Contract pelacak entitas (`registerNew`, `registerDirty`, `registerDeleted`). |
| **UnitOfWork** | `App\Core\Transactions\UnitOfWork` | Implementasi pelacak entitas terintegrasi dengan `TransactionManagerInterface`. |
