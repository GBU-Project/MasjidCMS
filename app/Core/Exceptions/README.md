# Core Exceptions

## Purpose
Menyediakan hierarki custom exception standar untuk MasjidCMS.

## Allowed Responsibility
- Menyediakan `DomainException` sebagai induk exception.
- Menyediakan `ValidationException`, `AuthorizationException`, dan `NotFoundException`.
- Mengelola rincian error payload melalui method `getErrors()`.

## Forbidden Responsibility
- DILARANG melakukan rendering HTTP response HTML/JSON di dalam Exception class.
- DILARANG mengandung logika bisnis domain tertentu.
