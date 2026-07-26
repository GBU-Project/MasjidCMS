# MasjidCMS — Domain System (Authentication)

Dokumen ini menjelaskan struktur, siklus kerja, dan rancangan fondasi **Domain System** spesifik modul **Authentication** sesuai dengan **SOFTWARE_ARCHITECTURE.md (v1.1)** dan **CORE_FRAMEWORK.md**.

---

## 1. Domain Overview

Domain `System` merupakan domain fondasi teknis dan identitas di MasjidCMS. Tanggung jawabnya meliputi:
- **Authentication**: Pengelolaan login, logout, sesi, dan verifikasi pengguna.
- **RBAC (Future)**: Pengelolaan Role, Permission, dan Hak Akses.
- **User Management (Future)**: Pengelolaan profil pengguna dan status akun.
- **Settings (Future)**: Pengelolaan konfigurasi global aplikasi.
- **Activity Log (Future)**: Audit trail perubahan data.

---

## 2. Authentication Architecture & Lifecycle Flow

```
[ HTTP Request ]
       │
       ▼
AuthenticationController (App\Domains\System\Controllers\AuthenticationController)
       │ - Extend App\Core\Controllers\BaseController
       │ - Menerima DTO LoginRequest
       ▼
AuthenticationService (App\Domains\System\Services\AuthenticationService)
       │ - Extend App\Core\Services\BaseService
       │ - Memvalidasi Kredensial via Repositori
       │ - Menerbitkan Sesi / AuthenticatedUser Entity
       ▼
AuthenticationRepository (App\Domains\System\Repositories\AuthenticationRepository)
       │ - Extend App\Core\Repositories\BaseRepository
       │ - Query data pengguna dari tabel `users`
       ▼
[ Database / Entity AuthenticatedUser ]
```

---

## 3. Dependency Rules

- **Independensi Absolute**: Domain `System` tidak boleh memiliki ketergantungan (dependency) ke domain bisnis manapun (`Masjid`, `Keuangan`, `CMS`, `TPQ`, `Asset`, `Media`, `Communication`).
- **Core Inheritance**: Seluruh Controller, Service, dan Repository di Domain `System` wajib menurunkan kelas dari `App\Core\*`.
- **Domain Consumer**: Domain bisnis lain dapat mengonsumsi `AuthenticationService` atau `AuthenticatedUser` dari Domain `System` untuk keperluan otorisasi.

---

## 4. Future RBAC & User Management Roadmap

1. **Phase 1.2 (Planned)**:
   - Skema Migrasi Tabel `users`, `roles`, `permissions`, `role_permissions`, `user_roles`.
   - Implementasi logika konkrit Password Hashing (Bcrypt) dan Session Management.
2. **Phase 1.3 (Planned)**:
   - Middleware/Filter Otorisasi (`AuthFilter`, `RbacFilter`).
   - Management UI User & Role Permission Matrix.
