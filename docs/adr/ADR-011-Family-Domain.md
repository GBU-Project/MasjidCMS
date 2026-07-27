# ADR-011: Family Business Module Architecture (RC1)

- **Status:** APPROVED
- **Tanggal:** 27 Juli 2026
- **Pengambil Keputusan:** Lead Software Architect & Product Development Team

---

## 1. Context & Business Rationale

**Modul Keluarga RC1 (Family Domain)** dibangun untuk mengelompokkan data Jamaah individual menjadi unit keluarga/rumah tangga (Kartu Keluarga). Modul ini menjadi fondasi bagi perhitungan Zakat Fitrah, penyaluran hewan Qurban, serta program bantuan sosial (Mustahik/Bansos).

---

## 2. Decision Outcomes & Architectural Justifications

- **Foreign Reference on Jamaah (`jamaahs.family_id`):** Dipilih berdasarkan hasil analisis di `docs/FAMILY_DOMAIN_ANALYSIS.md` untuk kecepatan kueri dan keselarasan dengan konsep Kartu Keluarga (KK) tunggal di Indonesia.
- **Explicit Head Pointer (`families.head_jamaah_id`):** Menunjuk secara langsung Jamaah yang bertindak sebagai Kepala Keluarga.
- **Head Transfer Workflow (`transferHead`):** Mendukung pemindahan peran Kepala Keluarga jika terjadi kematian (`DECEASED`) atau perpindahan domisili (`MOVED`).
- **Generic Events & Audit Engine:** Memicu `EntityCreatedEvent`, `EntityUpdatedEvent`, dan `EntityDeletedEvent` untuk log audit otomatis.
