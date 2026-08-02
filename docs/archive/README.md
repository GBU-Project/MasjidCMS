# Documentation Archive

This folder contains historical documentation that is no longer part of the
project's primary, maintained documentation set. Nothing here was deleted —
it's kept for project history and traceability, per TASK-023 (Repository
Cleanup).

## Contents

- **audit-reports/** — Point-in-time internal IT audit reports.
- **uat-reports/** — User Acceptance Testing reports for specific domains/RCs.
- **milestones/** — Implementation summaries and audits for individual
  development tasks (e.g. TASK-018, TASK-019A).
- **rc1-reports/** — Release notes, bug register, and checklists specific to
  the RC1 milestone.
- **point-in-time-reports/** — Architecture freeze, validation, and domain
  analysis snapshots taken at specific points during development.
- **superseded/** — Earlier stub/duplicate versions of documents that now
  live elsewhere (e.g. root `CHANGELOG.md`/`ROADMAP.md`, or a fuller version
  of the same doc under `docs/`).
- **engine-docs/** — Narrower, single-subsystem implementation notes
  (CRUD engine, validation engine, audit engine, transaction engine, domain
  events, upload pipeline, media storage, system domain, database changelog,
  installer guides). Moved out of the active `docs/` set to keep the
  primary documentation focused on the handful of docs most people actually
  need (architecture, database model, design system, financial model).
  These remain useful for deep implementation-level work.

## Where to look instead

For current, maintained documentation, see:
- `README.md` (root) — project overview, features, installation.
- `docs/SOFTWARE_ARCHITECTURE.md`, `docs/architecture/` — current architecture.
- `docs/adr/` — Architecture Decision Records (kept as active history, not archived).
- `docs/DATABASE_DESIGN.md`, `docs/DATABASE_DICTIONARY.md`, `docs/ERD.md` — data model.
- `docs/DESIGN_SYSTEM.md`, `docs/PRODUCT_UI_ARCHITECTURE.md` — UI/UX reference.
- `docs/CORE_FRAMEWORK.md`, `docs/DOMAIN_TEMPLATE.md`, `docs/DOMAIN_GENERATOR_SPEC.md` — for developers extending the domain layer.
- `INSTALLATION.md` (root) — installation guide.
