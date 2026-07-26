# Core Repositories

## Purpose
Menyediakan `BaseRepository` sebagai induk dari seluruh Repository layer di MasjidCMS.

## Allowed Responsibility
- Menyediakan helper query builder dasar.
- Menyediakan utilitas umum pagination (`paginate`).
- Menyediakan helper filtering dan sorting umum.
- Melakukan data mapping mentah.

## Forbidden Responsibility
- DILARANG mengandung logika bisnis, aturan otorisasi, atau validasi input.
- DILARANG menyimpan query domain tertentu di level induk ini.
- DILARANG membentuk HTTP Response (JSON/HTML).
