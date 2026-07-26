# Domain Masjid

## Responsibility
Domain `Masjid` bertanggung jawab atas pengelolaan profil identitas masjid, profil fisik, kontak, dan konfigurasi dasar unit masjid di MasjidCMS.

## Dependency Rules
- **Layering Rule**:
  `MasjidController` ──► `MasjidService` ──► `MasjidRepository` ──► `Database`
- **Controller Rule**: `MasjidController` DILARANG memanggil `MasjidRepository` atau Query Builder secara langsung.
- **Cross-Domain Otorisasi**: `MasjidPolicy` menggunakan `App\Domains\System\Services\RBACService` dari Domain `System` (Public Service) untuk verifikasi hak akses.
- **Independensi Model/DB**: Domain `Masjid` berdiri independen dari domain bisnis lain (`Keuangan`, `TPQ`, `Asset`, dll).

## Component Overview
- **Entities**: `Masjid` (Immutable / Encapsulated Data Model).
- **DTO**: `CreateMasjidRequest`, `UpdateMasjidRequest` (Typed, Immutable Request Payload).
- **Service**: `MasjidService` (Orkestrasi Logika Bisnis & Validasi).
- **Repository**: `MasjidRepository` (Abstraksi Akses Query Database).
- **Policy**: `MasjidPolicy` (Otorisasi Akses Khusus Modul Masjid).
- **Controller**: `MasjidController` (HTTP Orchestration).
