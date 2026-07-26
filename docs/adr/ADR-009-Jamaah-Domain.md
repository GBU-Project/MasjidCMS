# ADR-XXX: Domain Jamaah Architecture

- **Status:** APPROVED
- **Tanggal:** 2026
- **Pengambil Keputusan:** Lead Software Architect & Core Platform Team

---

## 1. Context & Business Rationale

MasjidCMS memerlukan modul pengelolaan data Jamaah. Modul ini dibangun mengikuti standar arsitektur **Golden Domain Template (docs/DOMAIN_TEMPLATE.md)**.

---

## 2. Decision Outcomes & Architectural Justifications

- **Domain Isolation:** Terisolasi penuh di bawah `App\Domains\Jamaah`.
- **Repository Pattern:** Seluruh akses data dikapsulasi di `JamaahRepository`.
- **Generic Events:** Menggunakan Generic Domain Events (`EntityCreatedEvent`, `EntityUpdatedEvent`, `EntityDeletedEvent`) untuk otomasi audit trail.
