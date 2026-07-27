# MASJIDCMS DATABASE DESIGN SPECIFICATION & AUDIT MATRIX

---

## 1. Audit Matrix & Gap Analysis (25 Core Domain Tables)

| Domain | Entity / Model | Controller | Database Table | Primary Key | Foreign Keys & Constraints | Status |
| :--- | :--- | :--- | :--- | :--- | :--- | :-: |
| **Security** | User | AuthController | `users` | BIGINT (id) | UNIQUE `username`, `email` | **ACTIVE** |
| **Security** | Role | RbacController | `roles` | BIGINT (id) | UNIQUE `name` | **ACTIVE** |
| **Security** | Permission | RbacController | `permissions` | BIGINT (id) | UNIQUE `name` | **ACTIVE** |
| **Security** | RolePermission | RbacController | `role_permissions` | Composite | FK -> `roles`, `permissions` | **ACTIVE** |
| **Security** | UserRole | RbacController | `user_roles` | Composite | FK -> `users`, `roles` | **ACTIVE** |
| **Masjid** | Masjid | MasterDataController | `masjids` | BIGINT (id) | UNIQUE `code`, `uuid` | **ACTIVE** |
| **Masjid** | Branch | MasterDataController | `branches` | BIGINT (id) | FK -> `masjids` | **ACTIVE** |
| **Jamaah** | Jamaah | MasterDataController | `jamaah` | BIGINT (id) | FK -> `families`, UNIQUE `nik` | **ACTIVE** |
| **Jamaah** | Contact | MasterDataController | `jamaah_contacts` | BIGINT (id) | FK -> `jamaah` | **ACTIVE** |
| **Keluarga** | Family | MasterDataController | `families` | BIGINT (id) | UNIQUE `family_card_number` | **ACTIVE** |
| **Keluarga** | FamilyMember | MasterDataController | `family_members` | BIGINT (id) | FK -> `families`, `jamaah` | **ACTIVE** |
| **Keuangan** | Fund | FinancialController | `funds` | BIGINT (id) | UNIQUE `fund_code` | **ACTIVE** |
| **Keuangan** | COA Account | FinancialController | `coa_accounts` | BIGINT (id) | UNIQUE `account_code` | **ACTIVE** |
| **Keuangan** | Financial Account | FinancialController | `financial_accounts` | BIGINT (id) | UNIQUE `code` | **ACTIVE** |
| **Keuangan** | Financial Period | FinancialController | `financial_periods` | BIGINT (id) | UNIQUE `period_code` | **ACTIVE** |
| **Keuangan** | Budget | FinancialController | `budget` | BIGINT (id) | FK -> `periods`, `coa`, `funds` | **ACTIVE** |
| **Keuangan** | Transaction | FinancialController | `financial_transactions` | BIGINT (id) | FK -> `funds`, `financial_accounts` | **ACTIVE** |
| **Keuangan** | Journal Entry | FinancialController | `journal_entries` | BIGINT (id) | FK -> `financial_transactions` | **ACTIVE** |
| **Keuangan** | Journal Detail | FinancialController | `journal_details` | BIGINT (id) | FK -> `journal_entries`, `coa` | **ACTIVE** |
| **Approval** | Approval Request | ApprovalController | `approval_requests` | BIGINT (id) | FK -> `financial_transactions` | **ACTIVE** |
| **Approval** | Approval Step | ApprovalController | `approval_steps` | BIGINT (id) | FK -> `approval_requests` | **ACTIVE** |
| **Approval** | Approval Log | ApprovalController | `approval_logs` | BIGINT (id) | FK -> `financial_transactions` | **ACTIVE** |
| **Program** | Program | ProgramController | `programs` | BIGINT (id) | FK -> `program_categories` | **ACTIVE** |
| **CMS** | Content & Page | PublicPortalController| `pages`, `posts`, `media` | BIGINT (id) | UNIQUE `slug` | **ACTIVE** |
| **System** | Settings & Audit | SystemController | `settings`, `audit_logs` | Composite/PK | INDEX `user_id`, `created_at` | **ACTIVE** |

---

## 2. Normalization & Integrity Enforcement
- **3NF Normalization**: All domain tables enforce Third Normal Form (3NF) to prevent redundant data storage.
- **Foreign Keys**: Foreign keys use strict `ON DELETE RESTRICT` for financial and ledger entities, and `ON DELETE CASCADE` for parent-child relations (`journal_details`, `family_members`, `role_permissions`).
- **Indexes**: Composite indexes applied on frequently filtered columns (`transaction_number`, `journal_number`, `fund_id`, `username`, `email`).
