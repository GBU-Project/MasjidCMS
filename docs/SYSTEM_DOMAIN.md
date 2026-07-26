# MasjidCMS — Domain System (Authentication, Identity & RBAC Provider)

Dokumen ini menjelaskan struktur, siklus kerja, dan rancangan fondasi **Domain System** mencakup **Authentication Engine**, **Identity Provider Pattern**, dan **RBAC Provider Foundation** sesuai dengan **SOFTWARE_ARCHITECTURE.md (v1.1)**, **CORE_FRAMEWORK.md**, dan **TASK-007**.

---

## 1. Domain Overview

Domain `System` merupakan domain fondasi teknis, identitas, dan otorisasi di MasjidCMS. Tanggung jawabnya meliputi:
- **Authentication**: Pengelolaan login, logout, sesi, dan verifikasi pengguna.
- **Identity Provider Decoupling**: Pemisahan otentikasi dari sumber data identitas (`IdentityProviderInterface`).
- **RBAC Architecture Decoupling**: Pemisahan otorisasi dari sumber data hak akses (`PermissionProviderInterface`).
- **User Management (Future)**: Pengelolaan profil pengguna dan status akun.
- **Settings (Future)**: Pengelolaan konfigurasi global aplikasi.
- **Activity Log (Future)**: Audit trail perubahan data.

---

## 2. Identity Provider Architecture

```
[ AuthenticationService ]
           │
           ▼ (Depends ONLY on Contract)
[ IdentityProviderInterface ] (App\Core\Contracts\Auth\IdentityProviderInterface)
           │
           ├───────────────► DatabaseIdentityProvider (Active)
           │                        │
           │                        ├─► AuthenticationRepository
           │                        └─► PasswordService
           │
           ├───────────────► LDAPIdentityProvider (Future)
           ├───────────────► OAuthIdentityProvider (Future)
           └───────────────► SAML / OIDC / Azure / Google (Future)
```

---

## 3. RBAC Architecture Flow

```
[ AuthenticatedUser Entity ]
             │
             ▼
[ RBACService ] (App\Domains\System\Services\RBACService)
             │
             ▼ (Depends ONLY on Contract)
[ PermissionProviderInterface ] (App\Core\Contracts\Auth\PermissionProviderInterface)
             │
             ├───────────────► DatabasePermissionProvider (Active)
             │                        │
             │                        ├─► RoleRepository
             │                        └─► PermissionRepository
             │
             ├───────────────► ConfigPermissionProvider (Future)
             └───────────────► ExternalPolicyProvider (Future)
```

### Keunggulan RBAC Provider Pattern:
1. **Decoupled Authorization**: `RBACService` tidak peduli apakah role/permission berasal dari relasi tabel SQL, file konfigurasi, atau policy external microservice.
2. **Standard Exception Handling**: Pengecekan otorisasi via `RBACService::authorize()` secara standar melempar `App\Core\Exceptions\AuthorizationException`.

---

## 4. Dependency Rules

- **Independensi Absolute**: Domain `System` tidak memiliki dependency ke domain bisnis manapun.
- **Core Contract Dependency**: `RBACService` hanya mengenal `PermissionProviderInterface` dari `App\Core\Contracts\Auth`.
- **Zero Direct Provider Coupling**: `RBACService` dan `AuthenticationService` tidak boleh mengimpor kelas provider konkrit secara langsung (wajib melalui Interface/Factory).
