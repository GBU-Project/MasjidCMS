# Release Notes — MasjidCMS v1.0.0-rc1

**Release Date**: 27 Juli 2026  
**Version**: `v1.0.0-rc1` (Release Candidate 1)  
**License**: MIT License  

---

## Executive Summary

MasjidCMS `v1.0.0-rc1` marks the official Release Candidate milestone for the open-source Mosque Management System built on CodeIgniter 4 (PHP 8.2+) with Domain-Driven Design (DDD).

This release introduces the complete **Syariah Fund Accounting & Financial Management Module**, robust **RBAC authorization**, **Read-Only Reporting Engine**, and critical **security hardening** against CSRF, brute-force login attacks, and concurrent posting lost-updates.

---

## Key Features & Highlights

1. **Syariah Fund Accounting (ADR-0005)**
   - Strict segregation across 7 mosque fund categories (`UNRESTRICTED`, `RESTRICTED` Zakat/Qurban, `ENDOWMENT` Wakaf).
   - Enforces syariah business rules `BR-FIN-01` to `BR-FIN-04` (no illegal Zakat overhead transfers, no cash account deficits).

2. **Double-Entry Posting Engine (ADR-0006)**
   - Automatic journal generation balancing debit and credit entries (`Debit == Credit`).
   - Reversal posting engine maintaining an immutable audit log (No hard deletes on posted journals).

3. **Multi-Level Approval Workflow**
   - Approval state machine with audit history (`ApprovalLog`).
   - Role-based authorization (`Treasurer`, `Finance Manager`, `Chairman`, `Super Admin`).

4. **Financial Reporting Engine**
   - 6 Read-only financial statements: Trial Balance, General Ledger, Cash Book, Fund Balance, Income & Expense, and Transaction History.

5. **Security & Data Integrity Hardening**
   - **Pessimistic Locking**: `SELECT ... FOR UPDATE` prevents race conditions during concurrent financial posts.
   - **CSRF Protection**: Global CSRF token randomization active.
   - **Login Rate Limiting**: CodeIgniter Throttler locks IP/username after 5 failed login attempts (HTTP 429).

---

## Known Limitations (Deferred to RC2 / v1.1.0)
- PDF and Excel report exports.
- Frontend interactive dashboards (React/Vue UI).
- Automated email/WhatsApp notifications on approval state changes.

---

## Release Quality Gate Verification

```text
PHPUnit 10.5.64 by Sebastian Bergmann and contributors.

Runtime:       PHP 8.2.12
Configuration: C:\MasjidCMS\phpunit.dist.xml

................................................................. 65 / 88 ( 73%)
.......................                                           88 / 88 (100%)

Time: 00:00.560, Memory: 18.00 MB

OK (88 tests, 287 assertions)
```
