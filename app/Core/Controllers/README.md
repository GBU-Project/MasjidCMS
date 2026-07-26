# Core Controllers

## Purpose
Menyediakan `BaseController` sebagai induk dari seluruh HTTP controller di MasjidCMS.

## Allowed Responsibility
- Menerima dan menginisialisasi request, response, serta logger bawaan framework.
- Memuat helper global yang dibutuhkan secara menyeluruh.
- Menyediakan method penolong pembentukan HTTP response standar (sukses & error).

## Forbidden Responsibility
- DILARANG mengeksekusi logika bisnis atau validasi domain.
- DILARANG mengakses Database atau Model secara langsung.
- DILARANG bergantung pada domain spesifik.
