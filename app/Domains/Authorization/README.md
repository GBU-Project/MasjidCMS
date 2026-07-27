# Authorization & RBAC Domain RC1

Dokumen ini mendokumentasikan spesifikasi teknis fondasi **Domain Authorization & RBAC RC1** pada **MasjidCMS Platform v1.0**.

---

## 1. ER Diagram

```mermaid
erDiagram
    USERS ||--o{ USER_ROLES : "assigned"
    ROLES ||--o{ USER_ROLES : "granted to"
    ROLES ||--o{ ROLE_PERMISSIONS : "contains"
    PERMISSIONS ||--o{ ROLE_PERMISSIONS : "mapped to"
    USERS ||--o{ MASJID_USER_ROLES : "scoped by masjid"

    USERS {
        string id PK "UUID v4"
        string username UK
        string email UK
        string password_hash
        string status "ACTIVE | SUSPENDED"
    }

    ROLES {
        string id PK "UUID v4"
        string role_code UK "SUPER_ADMIN | ADMIN_MASJID | OPERATOR | VIEWER"
        string name "Nama Peran"
    }

    PERMISSIONS {
        string id PK "UUID v4"
        string permission_code UK "dashboard.view | jamaah.* | family.*"
        string module_name "Module Name"
    }
```

---

## 2. Component Structure

- **`AuthorizationService`:** Implements `PermissionProviderInterface` for role & permission evaluation.
- **`PolicyResolver`:** Evaluates policy methods (`viewAny`, `view`, `create`, `update`, `delete`, `approve`).
- **`GuardResolver`:** Resolves identity via Session Guard or Bearer Token Guard.
- **`AuthorizationFilter`:** CI4 HTTP route filter evaluating user permissions.
