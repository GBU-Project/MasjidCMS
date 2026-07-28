# Changelog — MasjidCMS

All notable changes to the MasjidCMS platform are documented in this file. Format follows [Keep a Changelog](https://keepachangelog.com/en/1.0.0/) and adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## RC1 Audit Fixes (2026-07-28)

- Fixed duplicate migration blocker
- Added missing CMS/System migrations
- Fixed Dashboard schema mismatches
- Connected dashboard statistics to live database
- Fixed Financial Workspace queries
- Implemented transaction detail page
- Fixed Family-Jamaah joins
- Enabled secure session regeneration
- Added SECURITY.md
- Completed QA Audit #1 remediation

---

## [v1.0.0-rc1] - 2026-07-27

### Added
- **Financial Module RC1**: Complete Syariah-compliant Fund Accounting engine for mosques (ADR-0005, ADR-0006).
- **Double-Entry Posting Engine**: Automatic journal builder (`JournalBuilder`) forming balanced debit/credit line items (`Debit == Credit`).
- **Multi-Level Approval Workflow**: Approval State Machine (`DRAFT` -> `PENDING_APPROVAL` -> `APPROVED` -> `POSTED` -> `VOID` / `REJECTED` / `CANCELLED`) with RBAC authorization (`Treasurer`, `Finance Manager`, `Chairman`, `Super Admin`).
- **Financial Persistence Layer**: 6 Data Mappers, 6 Infrastructure Repositories, and `FinancialUnitOfWork` for atomic transactions.
- **Read-Only Reporting Engine**: 6 Report Services serving Trial Balance (Neraca Saldo), General Ledger (Buku Besar), Cash Book (Buku Kas), Fund Balances, Income & Expense Statements, and Transaction History.
- **REST API Foundation**: Thin HTTP controllers (`FinancialApiController`, `FinancialController`) mapping request DTOs to application use cases.
- **Security Hardening**:
  - `FIX-001`: Financial posting pessimistic locking (`SELECT ... FOR UPDATE`) preventing lost updates & race conditions under concurrent requests.
  - `FIX-002`: Global CSRF protection enabled with token randomization (`tokenRandomize = true`).
  - `FIX-003`: Login rate limiting throttler (`service('throttler')`) locking IPs after 5 failed attempts (HTTP 429).
- **Domain Boundary Alignment**: Institutional profile domain (`App\Domains\Organization`) separated cleanly from worship operational domain (`App\Domains\Masjid`).

### Fixed
- Audit Finding 4.1: CSRF filter activated globally on POST/PUT/DELETE routes.
- Audit Finding 4.2: Login endpoint rate limiting throttling implemented.
- Audit Finding 4.5: Financial posting race condition eliminated via Unit of Work boundary shift and pessimistic locking.

---

## [v1.0.0-alpha] - 2026-07-20
- Initial Core Platform Architecture v1.0.
- Master Data Management (Masjid, Jamaah, Family).
- Dynamic RBAC Foundation (Users, Roles, Permissions, SecurityContext).
