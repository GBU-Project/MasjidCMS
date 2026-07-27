# MASJIDCMS DATABASE DATA DICTIONARY (v1.0.0-rc1)

---

## 1. Table `users`
- `id` (BIGINT, PK, Auto Increment): Primary identifier.
- `uuid` (CHAR(36), Unique): Universally unique identifier.
- `username` (VARCHAR(64), Unique): User login name.
- `email` (VARCHAR(128), Unique): User email address.
- `password_hash` (VARCHAR(255)): Bcrypt password hash.
- `full_name` (VARCHAR(128)): User's full display name.
- `phone` (VARCHAR(32), Nullable): Contact phone number.
- `is_active` (TINYINT(1), Default 1): Account active status flag.

## 2. Table `funds`
- `id` (BIGINT, PK, Auto Increment): Primary identifier.
- `uuid` (CHAR(36), Unique): Fund UUID.
- `masjid_id` (VARCHAR(64)): Associated masjid scope ID.
- `fund_code` (VARCHAR(32)): Code identifier (`GENERAL`, `BUILDING`, `ZAKAT`).
- `name` (VARCHAR(128)): Fund display name.
- `fund_type` (ENUM): `UNRESTRICTED`, `RESTRICTED`, `ENDOWMENT`.
- `description` (TEXT, Nullable): Detailed fund allocation rules.

## 3. Table `financial_transactions`
- `id` (BIGINT, PK, Auto Increment): Primary transaction ID.
- `uuid` (CHAR(36), Unique): Transaction UUID.
- `fund_id` (BIGINT, FK -> funds.id): Fund allocation scope.
- `financial_account_id` (BIGINT, FK -> financial_accounts.id): Cash/Bank account source.
- `transaction_number` (VARCHAR(64), Unique): Auto-generated reference number (`TRX-...`).
- `transaction_type` (ENUM): `INCOME`, `EXPENSE`, `TRANSFER`, `ADJUSTMENT`.
- `amount` (DECIMAL(15, 2)): Financial monetary value.
- `status` (ENUM): `DRAFT`, `SUBMITTED`, `PENDING_APPROVAL`, `APPROVED`, `POSTED`, `VOID`.

## 4. Table `journal_entries` & `journal_details`
- `journal_entries.id` (BIGINT, PK): Journal header ID.
- `journal_entries.transaction_id` (BIGINT, FK -> financial_transactions.id): Source transaction.
- `journal_details.debit` (DECIMAL(15, 2)): Debit entry amount.
- `journal_details.credit` (DECIMAL(15, 2)): Credit entry amount.
