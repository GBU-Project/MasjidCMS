# Core Contracts

## Purpose
Folder `app/Core/Contracts` disiapkan untuk menyimpan Interface/Contract global yang mendefinisikan standar kontrak antar-komponen teknis teknis di MasjidCMS.

## Allowed Responsibility
- Menyediakan definisi interface teknis dasar (mis. `RepositoryInterface`, `ServiceInterface` di masa mendatang jika diperlukan).
- Menentukan metode wajib tanpa implementasi kode.

## Forbidden Responsibility
- DILARANG menyimpan implementasi konkrit (class/method body).
- DILARANG mendefinisikan interface yang terikat pada domain bisnis spesifik.
