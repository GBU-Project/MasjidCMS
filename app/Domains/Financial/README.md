# Financial Domain RC1 Specification

## Domain Overview
Domain Keuangan (**Financial Domain**) mengelola pembukuan nirlaba masjid berbasis **Fund Accounting** (ADR-0005) dan **Double-Entry Journaling** (ADR-0006).

## Domain Structure (`app/Domains/Financial/`)

- `Entities/`: `Fund`, `CoaAccount`, `FinancialAccount`, `Program`, `FinancialTransaction`, `JournalEntry`, `JournalDetail`, `ApprovalLog`.
- `Entities/ValueObjects/`: `Money`, `TransactionNumber`, `JournalNumber`, `FundCode`, `AccountCode`, `ProgramCode`.
- `Exceptions/`: `FinancialDomainException`, `BusinessRuleException`, `EntityNotFoundException`, `InvalidValueObjectException`.
- `Factories/`: `FinancialTransactionFactory`, `JournalEntryFactory`.
- `Specifications/`: `IsBalancedJournalSpecification`, `IsRestrictedFundSpecification`.
- `Repositories/Contracts/`: `FundRepositoryInterface`, `CoaAccountRepositoryInterface`, `FinancialAccountRepositoryInterface`, `ProgramRepositoryInterface`, `FinancialTransactionRepositoryInterface`, `JournalEntryRepositoryInterface`.
- `Services/`: `FinancialDomainService`.

## Business Rules Enforced
- **BR-FIN-01:** Transfer Dana Zakat ke Operasional Umum dilarang (`BusinessRuleException`).
- **BR-FIN-03:** Dana Qurban terisolasi dari kantong dana lain (`BusinessRuleException`).
- **BR-FIN-04:** Saldo kas tidak boleh bernilai minus/defisit (`BusinessRuleException`).
- **Jurnal Ganda Seimbang:** Total Debit WAJIB sama dengan Total Kredit (`IsBalancedJournalSpecification`).
- **Transaction Immutability:** Transaksi `POSTED` atau `VOID` dilarang diedit/dihapus.
