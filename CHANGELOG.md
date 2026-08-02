# Changelog — MasjidCMS

All notable changes to the MasjidCMS platform are documented in this file. Format follows [Keep a Changelog](https://keepachangelog.com/en/1.0.0/) and adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [v1.0.0-rc0] - 2026-08-03

### Release Highlights
- **RC0 Release Candidate Certification**: Complete codebase stabilization, audit remediation, and security hardening across all core modules.

### Added & Improved
- **Security & Authorization Hardening (TASK-026 & TASK-026A)**:
  - Enforced `rbac:financial.manage` authorization filter on all financial export routes (`/admin/financial/export`, `coa/export`, `budget/export`, `periods/export`, `journal/export`).
  - Added native HTTP Feature Test Suite (`tests/feature/SecurityAuthorizationTest.php`) verifying authentication, permission gates, guest access, and Super Admin bypass rules.
  - Closed Stored XSS vector by removing `image/svg+xml` from allowed upload MIME types in `AdminMediaController`.
  - Untracked `writable/installed.lock` from Git control and added `.gitignore` rules for seamless fresh clones and web installer execution.
- **Master Data & Prayer Location Hierarchy (TASK-025, TASK-025A & TASK-025B)**:
  - Implemented 3-tier prayer location resolution hierarchy (`settings.prayer_city` -> `masjid.city` -> `'Kota Masjid'`), preserving independent astronomical calculation zones while defaulting to mosque identity.
  - Expanded `Profil Masjid` creation form to render complete profile attributes (Sejarah, Visi, Misi, Media Pickers, Social Media, Prayer Config) on initial load.
  - Fixed JavaScript `DOMContentLoaded` ready-state initialization in `media-picker.js`.
  - Removed duplicate `Transparansi Keuangan` card from Homepage Manager grid and simplified public header navigation menu.
- **Financial Posting & Concurrency (TASK-024 & TASK-024A/B)**:
  - Refined CSRF filter configuration to protect `/api/financial/*` endpoints while allowing stateless APIs.
  - Implemented native pessimistic row locking (`SELECT ... FOR UPDATE`) in `AdminFinancialWorkspaceController`.
  - Documented 2-Tier Financial Posting Workflow in `ADR-0006`.
- **Jamaah Domain & Workspace Stabilization (TASK-021 - TASK-023)**:
  - Refactored Jamaah domain under Clean Architecture principles (`ADR-010`).
  - Unified Master Data Workspace UI and Reporting Workspace UI rendering.

## [v1.0.0-rc2] - 2026-07-28

### Added
- **Mosque Business Modules (TASK-016)**: Full database migrations (`2026-07-28-000013`), models (`PengurusModel`, `BidangModel`, `LayananModel`, `ProgramModel`), and seeders (`MosqueBusinessSeeder`).
- **Homepage Manager & Section Control (TASK-016A - 016D)**: Admin workspace (`/admin/homepage-manager`) for section visibility toggles, drag & drop section re-ordering, content limit configuration, featured item toggles, and cache purging.
- **Dynamic Homepage & Donation CTA Refactoring (TASK-016E)**: Removed hardcoded strings from portal view. Fully dynamic Section Donasi CTA driven by `settings` database table (`donation_title`, `donation_subtitle`, `donation_description`, `donation_btn_text`, `donation_btn_url`, `donation_bg_image`).
- **New Public Portal Views**: Public Organizational Structure page (`/struktur-organisasi`) and Katalog Layanan Masjid page (`/layanan`).
- **Admin CRUD Workspace Foundation**: Complete CRUD for CMS modules (Posts, Categories, Banners, Programs, Services, Gallery, Structure), Financials (COA, Budgets, Periods, Transactions), and Master Data.

### Fixed & Improved
- Fixed HTTP 500 errors on public portal and CI4 route method deprecation warnings.
- Fixed database schema alignments for missing system and CMS tables (`CreateMissingSystemAndCmsTables`).
- Updated system documentation and upgrade guides (`UPGRADE_TASK016.md`).

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
