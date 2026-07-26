# Validation Engine

## Purpose
Folder `app/Core/Validation` memuat komponen Validation Engine (`ValidationResult`, `ValidatorInterface`, `Validator`, `ValidationRuleInterface`, serta Basic Rules) yang menyediakan layanan validasi data bertipe ketat di Service Layer.

## Allowed Responsibility
- Menyediakan kontrak aturan `ValidationRuleInterface` dan runner `ValidatorInterface`.
- Menyediakan Value Object `ValidationResult` (valid status, errors, warnings, messages).
- Menyediakan aturan dasar reusable: `RequiredRule`, `StringRule`, `IntegerRule`, `BooleanRule`, `EmailRule`, `UrlRule`, `LengthRule`.
- Terintegrasi dengan `CrudService` via `validateWith()`.

## Forbidden Responsibility
- DILARANG melakukan penanganan validasi di HTTP Controller (Validation adalah milik Service Layer).
- DILARANG mengandung aturan validasi domain spesifik di layer Core ini.
