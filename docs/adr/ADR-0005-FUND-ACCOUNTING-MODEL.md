# ADR-0005: Fund Accounting Model for Mosque Financial Domain

- **Status:** Accepted
- **Tanggal:** 27 Juli 2026
- **Pengambil Keputusan:** Lead Software Architect & Financial Systems Analyst

---

## 1. Context

Pengelolaan keuangan masjid di Indonesia memerlukan pemisahan peruntukan dana yang ketat (*Restricted Funds* vs *Unrestricted Funds*), seperti memisahkan Kas Operasional, Dana Pembangunan, Dana Zakat, Dana Wakaf, dan Dana Qurban. Akuntansi komersial berbasis laba/rugi tidak mampu menjamin pemisahan ini secara sistemis, sehingga berisiko menimbulkan pencampuran dana terikat (*restricted funds*) yang melanggar prinsip syariat maupun amanah donatur.

---

## 2. Decision

MasjidCMS mengadopsi **Fund Accounting Model (Akuntansi Dana Nirlaba Keagamaan)** sebagai standar utama pembukuan keuangan masjid.

Struktur pembukuan dispesifikasikan sebagai berikut:
1. **Fund (Kantong Dana):** Setiap kantong dana bertindak sebagai entitas pembukuan independen dengan saldo dan laporan terpisah.
2. **Chart of Accounts (COA):** Setiap *Fund* memetakan akun Aset, Kewajiban, Saldo Dana, Penerimaan, dan Pengeluaran.
3. **Double-Entry Journaling:** Setiap transaksi penerimaan, pengeluaran, dan transfer antar-fund wajib menghasilkan entri jurnal ganda (*Double-Entry Journaling*) yang seimbang (Debit = Kredit).
4. **Primary Key Indexing Strategy (Option A):** Menggunakan `BIGINT AUTO_INCREMENT` sebagai Primary Key fisik pada tabel transaksi ber-volume tinggi, dikombinasikan dengan `uuid CHAR(36) UNIQUE` untuk UUID publik.

---

## 3. Consequences

### Positive
- **Syariat & Legal Compliance:** Terjaminnya pemisahan dana Zakat, Wakaf, Qurban, dan Operasional secara sistemik.
- **High-Performance Insert:** Primary Key `BIGINT AUTO_INCREMENT` mencegah fragmentasi B-Tree Index pada transaksi keuangan ber-volume tinggi.
- **Auditability:** Catatan jurnal ganda (*double-entry*) memudahkan pemeriksaan audit internal maupun eksternal.

### Negative / Trade-offs
- Memerlukan logika validasi tambahan pada Service Layer (`BusinessRuleException`) untuk membatasi transfer antar-fund yang dilarang syariat.
- Kueri pelaporan memerlukan pengelompokan berbasis `fund_id` dan `account_id`.

---

## 4. Alternatives Considered

1. **Standard Commercial Accounting (Profit & Loss Model):**
   - *Alasan Ditolak:* Berfokus pada profitabilitas dan ekuitas tunggal. Mengaburkan batasan peruntukan dana terikat.
2. **Single Cashbook (Single-Entry Cash Logging):**
   - *Alasan Ditolak:* Tidak mendukung pembukuan berimbang (*double-entry*) dan rentan terhadap ketidakseimbangan pencatatan aset/kewajiban.
3. **Pure UUID v4 Primary Key:**
   - *Alasan Ditolak:* Penyisipan acak UUID v4 pada Primary Key clustered index menyebabkan fragmentasi B-Tree page split dan penurunan kinerja insert pada tabel transaksi besar.

---

## 5. Reasons for Decision

Pendekatan Fund Accounting dengan `BIGINT AUTO_INCREMENT` + `uuid` memberikan keseimbangan sempurna antara kepatuhan syariat, integritas laporan keuangan nirlaba, dan performa tinggi pada lapisan basis data.
