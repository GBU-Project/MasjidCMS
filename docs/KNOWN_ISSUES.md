# KNOWN ISSUES & PRODUCT BACKLOG — MASJIDCMS

---

## 1. Document Purpose
This document tracks known limitations, deferred features, operational notes, and the product backlog for post-v1.0.0 releases of **MasjidCMS**.

---

## 2. Known Limitations (v1.0.0-rc1)

| Limitation ID | Feature Area | Description | Operational Workaround | Target Milestone |
| :-: | :--- | :--- | :--- | :-: |
| **LIM-001** | Reporting Engine | Exporting financial reports directly to PDF or Excel formats is not built into RC1. | Users can print report views to PDF via browser print functionality (`Ctrl+P`). | **v1.1.0 (RC2)** |
| **LIM-002** | Notifications | Real-time Email / WhatsApp / SMS notifications for DKM approval workflows are not attached to domain event listeners in RC1. | Approvers log into the admin portal to review pending approval lists. | **v1.1.0 (RC2)** |
| **LIM-003** | Visual Analytics | Financial reports are delivered in structured DTO / HTML table formats without interactive graphical charts. | Table data provides complete numbers; charts will be integrated into the dashboard. | **v1.1.0 (RC2)** |
| **LIM-004** | File Uploads | Profile image and file attachment pipeline is built in `App/Core/Upload` with fail-closed validation, but UI uploader forms are deferred. | Attachments managed via reference links or manual upload. | **v1.1.0 (RC2)** |

---

## 3. RC2 Product Backlog

1. **PDF & Excel Exporter Module**:
   - Dompdf / PhpSpreadsheet integration for Trial Balance, General Ledger, Cash Book, and Fund Balance reports.
2. **Event Listener Notification Engine**:
   - Attach Email & WhatsApp notification handlers to `FinancialTransactionSubmittedEvent` and `FinancialTransactionApprovedEvent`.
3. **Interactive Dashboard Charts**:
   - Chart.js / ApexCharts integration for cash inflow/outflow trends and fund allocation breakdown.
4. **Upload Form Integration**:
   - UI forms for uploading Jamaah profile pictures and financial transaction receipts with `UploadPipeline` validation.

---

## 4. Operational & Deployment Notes

- **CLI Unit Test Driver Fallback**: In CLI test environments where SQLite or MySQL extensions are absent, repositories and Unit of Work degrade gracefully to mock mode.
- **Database Engine**: Production deployments MUST use MySQL 8.0+ / MariaDB 10.5+ with `InnoDB` storage engine for pessimistic row locking (`SELECT ... FOR UPDATE`) support.
- **CSRF Token Handling**: Web forms MUST include `<?= csrf_field() ?>` or custom token header `X-CSRF-TOKEN`.
