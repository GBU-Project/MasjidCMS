# Core Support

## Purpose
Folder `app/Core/Support` menyediakan kelas pendukung teknis standar lintas domain, seperti penataan respons HTTP, helper utilitas, dan formatter data.

## Allowed Responsibility
- Menyediakan `ResponseFormatter` standar (Success & Error JSON Envelope).
- Menyediakan helper pendukung non-bisnis.
- Menyediakan transformer / formatter format umum.

## Forbidden Responsibility
- DILARANG mengandung aturan bisnis domain.
- DILARANG memodifikasi state database atau membuat query langsung.

## Support System Overview
1. **Response**: Pengaturan format respons seragam (`status`, `message`, `data`/`errors`).
2. **Helper**: Fungsi pembantu serbaguna untuk pengolahan string/array teknis.
3. **Formatter**: Penataan output data agar konsisten bagi Web & API.
4. **Future Pipeline**: Penyiapan middleware/pipeline utilitas tambahan (seperti audit trail transformer atau sanitization filter) di masa depan.
