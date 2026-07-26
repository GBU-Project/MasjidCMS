# Core Services

## Purpose
Menyediakan `BaseService` sebagai induk abstrak dari seluruh Service layer di MasjidCMS.

## Allowed Responsibility
- Menyediakan helper validasi umum.
- Menyediakan wrapper transaksi database (`transaction`).
- Menyediakan logger bawaan untuk pencatatan operasi teknis & log sistem.
- Menyediakan utilitas umum yang bersifat non-domain.

## Forbidden Responsibility
- DILARANG mengandung aturan bisnis atau logika domain tertentu.
- DILARANG menyimpan query SQL atau bergantung pada tabel database tertentu.
- DILARANG menangani HTTP response formatting secara langsung.
