# MasjidCMS — Platform Manajemen Masjid Berbasis Domain-Driven Design

[![Version](https://img.shields.io/badge/version-v1.0.0--rc1-blue.svg)](https://github.com/MasjidCMS/MasjidCMS/releases/tag/v1.0.0-rc1)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](LICENSE)
[![Build Status](https://img.shields.io/badge/tests-88%20passed-brightgreen.svg)]()

**MasjidCMS** adalah aplikasi manajemen masjid modern berbasis **CodeIgniter 4 (PHP 8.2+)** yang dirancang menggunakan arsitektur **Domain-Driven Design (DDD)**. 

Aplikasi ini mencakup pengelola identitas masjid, data jamaah, keluarga, hak akses (RBAC), serta **Modul Akuntansi Keuangan Masjid (Fund Accounting & Double-Entry Bookkeeping)** yang akurat dan sesuai dengan prinsip Syariah.

---

## 🌟 Fitur Utama (v1.0.0-rc1)

- 🏛️ **Master Data & Profil Institusi**: Pengelolaan identitas masjid (`Organization`), data jamaah, dan susunan keluarga.
- 🔐 **Dynamic RBAC & Auth Security**: Otorisasi berbasis role (`Super Admin`, `Chairman`, `Treasurer`, `Finance Manager`, `Staff`), dilengkapi CSRF token randomization & brute-force rate-limiting lockout (HTTP 429).
- 💰 **Akuntansi Kantong Dana (Fund Accounting - ADR-0005)**: Pemisahan dana terikat (Zakat, Qurban, Wakaf) dan dana bebas (Kas Umum, Pembangunan). Enforcing aturan syariah `BR-FIN-01` s/d `BR-FIN-04`.
- 📖 **Double-Entry Posting Engine (ADR-0006)**: Pembentukan jurnal otomatis seimbang (`Debit == Credit`) dan jurnal pembalik (*reversal journal*).
- 📊 **Read-Only Reporting Engine**: Laporan Neraca Saldo (Trial Balance), Buku Besar (General Ledger), Buku Kas (Cash Book), Saldo Kantong Dana, dan Laporan Operasional Pendapatan/Beban.
- ⚡ **Race Condition Protection**: Penguncian pesimistik (`SELECT ... FOR UPDATE`) menjaga integritas saldo kas saat posting konkuren.

---

## 🚀 Panduan Instalasi & Penggunaan

Silakan baca dokumen [INSTALLATION.md](INSTALLATION.md) untuk langkah-langkah setup server, konfigurasi `.env`, migrasi database, dan seeding master data.

---

## 📄 Dokumentasi Arsitektur

Dokumentasi lengkap arsitektur sistem dapat ditemukan di direktori `docs/`:
- [SAD v1.1](docs/architecture/SAD_V1.1.md) — Software Architecture Document
- [ADR-0005](docs/adr/ADR-0005-FUND-ACCOUNTING-MODEL.md) — Architectural Decision Record: Fund Accounting Model
- [ADR-0006](docs/adr/ADR-0006-FINANCIAL-POSTING-AND-BALANCE.md) — Architectural Decision Record: Financial Posting & Balance Strategy
- [UAT Checklist](docs/UAT_RC1_CHECKLIST.md) — User Acceptance Test Results
- [Release Notes RC1](RELEASE_NOTES_RC1.md) — Catatan Rilis RC1

---

## 📝 Lisensi

Proyek ini dilisensikan di bawah [MIT License](LICENSE).
