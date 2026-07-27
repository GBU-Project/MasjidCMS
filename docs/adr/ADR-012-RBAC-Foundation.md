# ADR-012: RBAC Foundation & Policy Engine Architecture (RC1)

- **Status:** APPROVED
- **Tanggal:** 27 Juli 2026
- **Pengambil Keputusan:** Lead Software Architect & Security Engineering Team

---

## 1. Context & Business Rationale

Fondasi Otorisasi & RBAC RC1 diimplementasikan untuk menyediakan mekanisme pengawasan keamanan, evaluasi peran (*Role Assignment*), evaluasi hak akses (*Permission Check*), resolusi identitas (*Guard Resolver*), dan penyaringan rute HTTP (*AuthorizationFilter*) pada MasjidCMS.

---

## 2. Decision Outcomes

- **Multi-Tenant Scoping (`masjid_user_roles`):** Menyediakan skema peran berlingkup masjid (`masjid_id`) untuk isolasi tenant.
- **Policy Engine Resolution (`PolicyResolver`):** Menyediakan pengujian otomatis kemampuan (`viewAny`, `view`, `create`, `update`, `delete`, `approve`).
- **Multi-Guard Strategy (`GuardResolver`):** Mendukung resolusi sesi web dan Bearer token JWT.
- **Core Protection:** Seluruh fungsi RBAC memanfaatkan kontrak interface Core (`PermissionProviderInterface`, `IdentityProviderInterface`) tanpa mengubah `app/Core/`.
