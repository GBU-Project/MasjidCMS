# Core Module

## Purpose
`app/Core` menyediakan fondasi teknis, kelas abstrak dasar, pendukung, dan utilitas yang digunakan lintas domain di MasjidCMS.

## Allowed Responsibility
- Menyediakan base class (`BaseController`, `BaseService`, `BaseRepository`).
- Menyediakan format respons standar (`ResponseFormatter`).
- Menyediakan exception bawaan arsitektur (`DomainException`, `NotFoundException`, dll).
- Menyediakan trait pendukung entity/model (`TimestampTrait`, `UuidTrait`, `UserStampTrait`).
- Menyediakan kontrak/interface dasar.

## Forbidden Responsibility
- DILARANG mengandung business logic atau aturan domain spesifik.
- DILARANG bergantung pada folder `app/Domains/*` (Core tidak boleh mengenal Domain).
- DILARANG menyimpan query database spesifik entitas bisnis.
