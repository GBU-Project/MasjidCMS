# Laporan UAT (User Acceptance Testing)
## MasjidCMS — Temuan: Modul CMS & Portal Management Tidak Bisa Input Data

**Tanggal Pengujian:** 28 Juli 2026
**Referensi commit:** `f8dda70` (branch `develop`, pasca-remediasi Audit #1)
**Pemicu Pengujian:** Laporan UAT internal — modul CMS & Portal Management tidak bisa input data, beberapa tab tidak memiliki tombol tambah (contoh: Halaman Statis, Galeri)
**Metodologi:** Verifikasi langsung ke source code (Controller, Routes, View) untuk setiap modul admin — memeriksa keberadaan route create/store, method controller, dan wiring form ke backend.

---

## 1. Ringkasan Temuan

Laporan UAT **dikonfirmasi benar**. Setelah ditelusuri lebih dalam, cakupan masalah ternyata **lebih luas dari yang dilaporkan** — bukan hanya modul CMS, tapi **seluruh panel admin (Master Data, CMS, Financial, System) saat ini bersifat read-only**. Belum ada satu pun modul di web UI yang memiliki jalur input data yang benar-benar berfungsi end-to-end (form → validasi → simpan ke database).

Satu-satunya jalur penulisan data yang berfungsi di seluruh proyek adalah JSON API (`/api/financial/transactions` dan sejenisnya), namun ini **tidak terhubung ke dashboard/web form manapun**.

---

## 2. Detail Temuan per Modul

### 2.1 Modul CMS & Portal Management — Nol Kemampuan Input (sesuai laporan UAT)

| Item Pemeriksaan | Hasil |
|---|---|
| Method controller (`AdminCmsWorkspaceController`) | Hanya `index()`. Tidak ada `create()`, `store()`, `edit()`, `update()`, `delete()` |
| Route terdaftar | Hanya `GET admin/cms` (index). Tidak ada route POST/create/edit apa pun |
| File view di `app/Views/admin/cms/` | Hanya `index.php`. Tidak ada `create.php` atau form input |
| Tombol "📝 Tulis Berita Baru" | **Bukan tombol tambah** — hanya link berpindah tab (`admin/cms?tab=posts`), tidak membuka form |
| Tombol "📅 Tambah Jadwal Kajian" | **Bukan tombol tambah** — hanya link berpindah tab (`admin/cms?tab=kajian`), tidak membuka form |
| Tab "📄 Halaman Statis" (pages) | **Tidak ada tombol tambah sama sekali** |
| Tab "🖼️ Galeri Foto" (gallery) | **Tidak ada tombol tambah sama sekali** |

**Kesimpulan:** Tidak ada satu pun jalur untuk menambah, mengedit, atau menghapus Berita, Jadwal Kajian, Halaman Statis, atau Galeri Foto dari dashboard admin. Modul ini murni tampilan daftar (read-only), sesuai dengan yang dilaporkan tim UAT.

### 2.2 Modul Master Data — Tombol Tambah Ada, Tapi Rusak (404)

| Item Pemeriksaan | Hasil |
|---|---|
| Tombol "+ Tambah Data" | Ada secara visual, mengarah ke `/admin/master/create?tab={tab}` |
| Route `admin/master/create` | **Tidak terdaftar di `Routes.php`** |
| Dampak | Tombol terlihat berfungsi, tapi saat diklik menghasilkan **404 Page Not Found** |

**Catatan:** Ini lebih berisiko dibanding CMS — pengguna akan mengira fitur tersedia (tombolnya ada), tapi gagal saat digunakan, berpotensi menimbulkan laporan bug berulang dari end user/DKM.

### 2.3 Modul Financial — Form Create Berupa Mockup Statis, Belum Wired

| Item Pemeriksaan | Hasil |
|---|---|
| Route `admin/financial/create` | Terdaftar (GET), berhasil membuka halaman `create.php` |
| Atribut `action` pada `<form>` | **Tidak ada** |
| Atribut `name` pada input/select/textarea | **Tidak ada satu pun** |
| Tombol submit | `<button type="button">Lanjut ke Step 2 ›</button>` — `type="button"`, bukan `submit`, dan tidak ada JavaScript/handler untuk memprosesnya |
| Step 2/3 (lanjutan wizard) | Tidak pernah dibangun — form berhenti di "Step 1" |

**Kesimpulan:** Modul ini secara visual paling "lengkap" di antara semua modul admin (satu-satunya yang punya halaman create), tapi secara fungsional **sama seperti yang lain — tidak bisa menyimpan data apa pun**. Ini murni wireframe/mockup UI.

### 2.4 Modul System/Settings — Tidak Ada Tombol Tambah

Tidak ditemukan tombol tambah maupun form input untuk Pengaturan Platform. Konsisten dengan pola di seluruh dashboard.

---

## 3. Tabel Rekap Status Input Data — Seluruh Modul Admin

| Modul | Tombol Tambah? | Route Create Ada? | View Form Ada? | Form Bisa Disubmit? |
|---|---|---|---|---|
| CMS — Berita/Artikel | ⚠️ Palsu (ganti tab saja) | ❌ | ❌ | ❌ |
| CMS — Jadwal Kajian | ⚠️ Palsu (ganti tab saja) | ❌ | ❌ | ❌ |
| CMS — Halaman Statis | ❌ Tidak ada | ❌ | ❌ | ❌ |
| CMS — Galeri Foto | ❌ Tidak ada | ❌ | ❌ | ❌ |
| Master Data (Masjid/Jamaah/Keluarga/User/Role/Permission) | ⚠️ Ada, tapi 404 | ❌ | ❌ | ❌ |
| Financial — Transaksi | ✅ Ada | ✅ (GET saja) | ✅ (mockup) | ❌ (form tidak wired) |
| System/Settings | ❌ Tidak ada | ❌ | ❌ | ❌ |

**Total: 0 dari 7 area modul admin memiliki jalur input data yang berfungsi penuh (form → simpan ke database).**

---

## 4. Dampak terhadap Operasional

Dalam kondisi saat ini, aplikasi **tidak dapat digunakan untuk operasional harian pengurus masjid (DKM)** melalui dashboard admin:
- Tidak bisa menambahkan data jamaah/keluarga baru lewat UI
- Tidak bisa mempublikasikan berita, jadwal kajian, halaman statis, atau galeri foto
- Tidak bisa mencatat transaksi keuangan lewat form web (hanya lewat API langsung, yang memerlukan integrasi teknis terpisah, bukan untuk pengguna akhir non-teknis)
- Tidak bisa mengubah pengaturan sistem

Aplikasi dalam status rilis ini **hanya berfungsi sebagai panel monitoring/pelaporan (read-only)**, bukan sistem manajemen operasional yang lengkap.

---

## 5. Rekomendasi

| Prioritas | Rekomendasi |
|---|---|
| 🔴 Kritis | Implementasikan form create/store untuk seluruh modul CMS (Posts, Kajian, Pages, Gallery) — method controller, route POST, dan view form yang benar-benar terhubung ke database |
| 🔴 Kritis | Daftarkan route `admin/master/create` (dan `edit`/`update`/`delete`) agar tombol "+ Tambah Data" di Master Data tidak lagi 404 |
| 🔴 Kritis | Lengkapi form `admin/financial/create` — tambahkan atribut `name` pada input, `action` pada form, ubah tombol jadi `type="submit"`, dan hubungkan ke endpoint penyimpanan (baik lewat POST langsung atau memanggil `FinancialApiController` yang sudah ada dan berfungsi) |
| 🟠 Sedang | Terapkan pola CRUD yang konsisten di semua modul (create/store/edit/update/delete), mengikuti standar routing RESTful CodeIgniter 4 |
| 🟠 Sedang | Tambahkan pengujian otomatis (feature test) untuk memastikan setiap tombol "Tambah"/"Edit" benar-benar mengarah ke route yang valid, sehingga regresi seperti ini tidak lolos ke rilis berikutnya |
| 🟡 Rendah | Perjelas di release notes bahwa RC saat ini adalah "read-only preview" jika memang belum ditargetkan untuk mendukung input data penuh pada v1.0.0 |

---

## 6. Kesimpulan

Laporan UAT internal tim — bahwa modul CMS & Portal Management belum bisa input data dan beberapa tab (Halaman Statis, Galeri) tidak memiliki tombol tambah — **terverifikasi akurat 100%**. Investigasi lanjutan menunjukkan bahwa masalah ini bukan terbatas pada CMS saja, melainkan **pola yang konsisten di seluruh dashboard admin**: aplikasi saat ini adalah panel read-only, dengan satu-satunya jalur tulis data (JSON API Financial) yang belum terhubung ke antarmuka pengguna mana pun. Ini merupakan gap fungsional signifikan yang perlu menjadi prioritas utama sebelum rilis v1.0.0 final, karena tanpa kemampuan input data, aplikasi tidak dapat memenuhi tujuan dasarnya sebagai sistem manajemen masjid.
