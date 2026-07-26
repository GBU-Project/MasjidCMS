# System Domain

## Purpose
Domain `System` bertanggung jawab atas tata kelola akses, identitas, otentikasi (Authentication), otorisasi (RBAC), pengaturan konfigurasi sistem, audit log, dan pengelolaan tema di MasjidCMS.

## Responsibility
- Manajemen Otentikasi Pengguna (Login, Logout, Session Refresh, Identity Verification).
- Manajemen Pengguna, Peran (Role), dan Hak Akses (Permission).
- Pengaturan Konfigurasi Global Aplikasi (`settings`).
- Recording Activity Log & Audit Trail.
- Management Tema Aplikasi.

## Allowed Dependency
- `App\Core\*` (BaseController, BaseService, BaseRepository, ResponseFormatter, Exception, Traits).
- PHP Native & CodeIgniter 4 Core Libraries.

## Forbidden Dependency
- DILARANG bergantung pada domain bisnis lain (`App\Domains\Masjid\*`, `App\Domains\Keuangan\*`, `App\Domains\CMS\*`, dll). Domain `System` berdiri independen.

## Future Modules
- **RBAC**: Role Based Access Control (Role & Permission Management).
- **User Management**: User CRUD, Activation, Lockout, Profile.
- **Permission Matrix**: Dynamic Permission Checker.
- **Audit Trail**: Logging aktivitas user & perubahan data sistem.
