# MasjidCMS — Domain System (Authentication, Identity, RBAC & Security Pipeline)

Dokumen ini menjelaskan struktur, siklus kerja, dan rancangan fondasi **Domain System** mencakup **Authentication Engine**, **Identity Provider Pattern**, **RBAC Provider Foundation**, dan **Security Pipeline** sesuai dengan **SOFTWARE_ARCHITECTURE.md (v1.1)**, **CORE_FRAMEWORK.md**, dan **TASK-008**.

---

## 1. Domain Overview

Domain `System` merupakan domain fondasi teknis, identitas, otorisasi, dan filter keamanan di MasjidCMS. Tanggung jawabnya meliputi:
- **Authentication**: Pengelolaan login, logout, sesi, dan verifikasi pengguna.
- **Identity Provider Decoupling**: Pemisahan otentikasi dari sumber data identitas (`IdentityProviderInterface`).
- **RBAC Architecture Decoupling**: Pemisahan otorisasi dari sumber data hak akses (`PermissionProviderInterface`).
- **Security Pipeline**: Pengawasan jalur request melalui `AuthenticationFilter`, `SecurityContext`, dan `AuthorizationFilter`.
- **User Management (Future)**: Pengelolaan profil pengguna dan status akun.
- **Settings (Future)**: Pengelolaan konfigurasi global aplikasi.
- **Activity Log (Future)**: Audit trail perubahan data.

---

## 2. Security Pipeline Architecture

Security Pipeline adalah satu-satunya pintu gerbang resmi sebelum sebuah HTTP Request diperbolehkan mengeksekusi Controller.

```
+-----------------------------------------------------------------------------------+
|                                 HTTP REQUEST                                      |
+-----------------------------------------------------------------------------------+
                                          │
                                          ▼
+-----------------------------------------------------------------------------------+
|   AuthenticationFilter (App\Filters\AuthenticationFilter)                        |
|   - Membaca Session via AuthenticationService                                     |
|   - Mengisi SecurityContext jika terotentikasi                                    |
|   - Return HTTP 401 Unauthorized jika tidak valid                                 |
+-----------------------------------------------------------------------------------+
                                          │
                                          ▼
+-----------------------------------------------------------------------------------+
|   SecurityContext (App\Core\Security\SecurityContext)                             |
|   - Request-scoped singleton yang menyimpan entitas AuthenticatedUser             |
+-----------------------------------------------------------------------------------+
                                          │
                                          ▼
+-----------------------------------------------------------------------------------+
|   AuthorizationFilter (App\Filters\AuthorizationFilter)                          |
|   - Membaca user dari SecurityContext (DILARANG baca Session langsung)            |
|   - Super Admin Rule: Bypass otorisasi jika isSuperAdmin() == true                |
|   - Memanggil RBACService::authorize() sesuai parameter filter                    |
|   - Return HTTP 403 Forbidden jika tidak punya hak akses                          |
+-----------------------------------------------------------------------------------+
                                          │
                                          ▼
+-----------------------------------------------------------------------------------+
|   Controller (Extends App\Core\Controllers\BaseController)                        |
|   - Bebas dari pemeriksaan manual AuthenticationService / RBACService             |
|   - Menerima request yang sudah tervalidasi aman oleh Security Pipeline           |
+-----------------------------------------------------------------------------------+
                                          │
                                          ▼
+-----------------------------------------------------------------------------------+
|                                 HTTP RESPONSE                                     |
+-----------------------------------------------------------------------------------+
```

---

## 3. Separation of Concerns & Rules

1. **AuthenticationFilter Rule**:
   - Hanya memverifikasi otentikasi sesi via `AuthenticationService`.
   - Mengisi `SecurityContext`.
   - **DILARANG** melakukan pengecekan otorisasi/RBAC.
2. **AuthorizationFilter Rule**:
   - Hanya membaca data user dari `SecurityContext`.
   - **DILARANG** membaca Session secara langsung.
   - Mengabaikan pengecekan jika `user->isSuperAdmin() === true`.
3. **Controller Rule**:
   - Controller **DILARANG** memanggil `AuthenticationService` atau `RBACService` secara manual di dalam method action untuk memvalidasi keamanan.
   - Controller menganggap request yang sampai ke method action sudah 100% aman karena telah melewati Security Pipeline.
