# Core Traits

## Purpose
Menyediakan reusable PHP Traits untuk digunakan pada class/model di seluruh domain MasjidCMS.

## Allowed Responsibility
- Menyediakan skeleton pembantu penataan timestamp (`TimestampTrait`).
- Menyediakan skeleton pembantu pencatatan user stamp (`UserStampTrait`).
- Menyediakan skeleton pembentukan UUID (`UuidTrait`).

## Forbidden Responsibility
- DILARANG mengeksekusi query database langsung di dalam trait tanpa perantara layer yang sesuai.
- DILARANG mengandung aturan bisnis domain spesifik.
