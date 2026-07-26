# MasjidCMS — Domain System (Authentication & Identity Provider)

Dokumen ini menjelaskan struktur, siklus kerja, dan rancangan fondasi **Domain System** spesifik modul **Authentication Engine & Identity Provider Pattern** sesuai dengan **SOFTWARE_ARCHITECTURE.md (v1.1)**, **CORE_FRAMEWORK.md**, dan **TASK-006**.

---

## 1. Domain Overview

Domain `System` merupakan domain fondasi teknis dan identitas di MasjidCMS. Tanggung jawabnya meliputi:
- **Authentication**: Pengelolaan login, logout, sesi, dan verifikasi pengguna.
- **Identity Provider Decoupling**: Pemisahan otentikasi dari sumber identitas data (DB, LDAP, OAuth, SAML).
- **RBAC (Future)**: Pengelolaan Role, Permission, dan Hak Akses.
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

### Keuntungan Pola Identity Provider:
1. **Zero Coupling**: `AuthenticationService` tidak perlu diubah apabila di masa mendatang sistem diintegrasikan dengan LDAP sekolah/yayasan, Single Sign-On (SSO) Google Workspace, atau Microsoft Azure AD.
2. **Pluggable Architecture**: Cukup menambahkan Provider baru yang mengimplementasikan `IdentityProviderInterface` dan mengatur `AuthConfig::$default_provider`.

---

## 3. Authentication & Provider Lifecycle Flow

```
[ HTTP Request ] ──► AuthenticationController::login()
                           │
                           ▼
                 AuthenticationService::login()
                           │
                           ▼ (findByIdentifier & validateCredential)
                 IdentityProviderInterface
                           │
                           ▼
                 DatabaseIdentityProvider
                  /                 \
                 v                   v
      AuthenticationRepository   PasswordService
                 │                   │
                 ▼                   ▼
      [ Database Query ]     [ bcrypt verify ]
```

---

## 4. Dependency Rules

- **Independensi Absolute**: Domain `System` tidak memiliki dependency ke domain bisnis manapun.
- **Core Contract Dependency**: `AuthenticationService` hanya mengenal `IdentityProviderInterface` dari `App\Core\Contracts\Auth`.
- **Session Isolation**: Sesi pengaktifan login hanya ditangani oleh `SessionService`. Identity Provider dilarang berinteraksi langsung dengan HTTP Session.
