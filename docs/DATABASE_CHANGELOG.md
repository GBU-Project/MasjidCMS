# MASJIDCMS DATABASE SCHEMA CHANGELOG

---

## [v1.0.0-rc1] - 2026-07-27

### Added
- Created complete relational DDL schema dump in `database/schema.sql` covering 25 domain tables.
- Implemented RBAC tables (`users`, `roles`, `permissions`, `role_permissions`, `user_roles`).
- Implemented Organizational tables (`masjids`, `branches`).
- Implemented Jamaah & Family tables (`jamaah`, `jamaah_contacts`, `families`, `family_members`).
- Implemented Financial & Accounting tables (`funds`, `coa_accounts`, `financial_accounts`, `financial_periods`, `budget`, `financial_transactions`, `journal_entries`, `journal_details`).
- Implemented Approval Workflow tables (`approval_requests`, `approval_steps`, `approval_logs`).
- Implemented Program & Event tables (`programs`, `program_categories`).
- Implemented CMS & Media tables (`categories`, `pages`, `posts`, `menus`, `media`, `gallery`).
- Implemented System & Audit tables (`settings`, `audit_logs`, `notifications`).
- Added composite indexes on `transaction_number`, `journal_number`, `fund_code`, `account_code`, `username`, `email`.
