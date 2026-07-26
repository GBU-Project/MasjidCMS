# Transaction & Unit of Work Engine

## Purpose
Folder `app/Core/Transactions` memuat komponen pendukung isolasi transaksi database (`TransactionManagerInterface`, `DatabaseTransactionManager`) dan pelacakan entitas (`UnitOfWorkInterface`, `UnitOfWork`) untuk menjamin konsistensi ACID (*Atomicity, Consistency, Isolation, Durability*) dalam setiap operasi manipulasi data bisnis di MasjidCMS.

## Allowed Responsibility
- Menyediakan pengelola batas transaksi database (`begin`, `commit`, `rollback`, `transaction`).
- Menyediakan pelacak perubahan entitas Unit of Work (`registerNew`, `registerDirty`, `registerDeleted`).
- Menjamin penayangan Domain Event hanya dilakukan **setelah commit transaksi berhasil**.

## Forbidden Responsibility
- DILARANG mengeksekusi transaksi terdistribusi (Distributed Transaction / Saga / Two-Phase Commit) pada fondasi ini.
- DILARANG mengandung aturan bisnis spesifik domain tertentu.
