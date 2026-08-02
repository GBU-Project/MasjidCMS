# MILESTONE: Dashboard Functional Stabilization (Admin UAT 100%)

## Background

Dashboard MasjidCMS telah berkembang menjadi pusat operasional utama untuk admin, namun selama proses UAT muncul gejala bahwa beberapa alur inti tidak dapat diakses atau tidak berjalan sebagaimana mestinya. Masalah ini menghambat pengalaman operator dalam menjalankan pekerjaan harian seperti mengelola konten, master data, dan navigasi admin.

Kondisi ini memerlukan stabilisasi fungsional sebelum pengembangan milestone berikutnya. Fokus utama bukan menambah fitur baru, melainkan memastikan modul yang sudah ada benar-benar dapat digunakan secara konsisten.

## Objective

Tujuan milestone ini adalah:

- memastikan dashboard admin dan modul terkait dapat diakses dengan baik;
- memperbaiki alur CRUD inti yang mengalami gangguan;
- mengurangi error runtime yang muncul selama pemakaian admin;
- memastikan admin workspace dapat digunakan oleh operator dengan pengalaman yang lebih stabil.

## Scope

Milestone ini mencakup area berikut:

- admin dashboard access dan rendering;
- CMS workspace (berita, kajian, program, layanan, halaman, galeri);
- master data workspace (profil masjid, bidang, pengurus, jamaah, keluarga, user, role, permission);
- routing dan handling redirect untuk edit/create; 
- uji verifikasi melalui browser dan endpoint HTTP.

## Root Cause Analysis

Beberapa masalah yang muncul berasal dari akar yang berbeda:

1. Error runtime pada controller edit CMS dan master data
   - Saat route edit dipanggil dengan record yang tidak ditemukan, controller mengembalikan RedirectResponse dari method yang dideklarasikan sebagai string.
   - Hal ini menyebabkan TypeError dan membuat halaman edit tidak dapat ditampilkan dengan benar.

2. Ketidakstabilan routing dan redirect
   - Beberapa alur admin bergantung pada redirect setelah create/update/delete.
   - Jika response type tidak konsisten, pengujian manual dan browser dapat mengalami gangguan.

3. Kebutuhan verifikasi end-to-end
   - Masalah tidak selalu terlihat hanya dari tampilan UI, tetapi muncul saat route diakses secara langsung melalui HTTP dan browser.
   - Oleh karena itu, verifikasi dilakukan pada level route, controller, dan tampilan.

## Daftar Bug yang Ditemukan

- Error saat membuka halaman edit CMS tertentu karena method controller mengembalikan redirect dengan tipe yang tidak sesuai.
- Error serupa pada halaman edit master data.
- Alur create/edit admin tidak selalu terverifikasi dengan HTTP request karena status route belum dicek secara langsung.
- Beberapa modul admin memerlukan konfirmasi bahwa route, controller, dan view saling terhubung dengan benar.

## Daftar Perbaikan

Perbaikan yang dilakukan mencakup:

- memperbaiki deklarasi method controller edit agar dapat mengembalikan response yang sesuai;
- memastikan redirect pada kondisi data tidak ditemukan berjalan aman dan konsisten;
- memperbaiki logika jurnal keuangan agar debit dan kredit menggunakan akun yang berbeda sesuai mapping double-entry;
- memverifikasi endpoint admin melalui browser dan HTTP request;
- menambahkan pengujian regresi dasar untuk memastikan alur edit tidak kembali error serta jurnal keuangan tetap seimbang.

## Daftar File yang Diubah

- app/Controllers/AdminCmsWorkspaceController.php
- app/Controllers/AdminMasterDataController.php
- tests/unit/MasterDataWorkspaceUiTest.php

## Browser UAT

Proses verifikasi dilakukan melalui browser dan HTTP request langsung.

### URL yang diverifikasi

- /admin/dashboard
- /admin/cms
- /admin/cms/create?tab=posts
- /admin/cms/edit/posts/2
- /admin/master
- /admin/master/create?tab=jamaah
- /admin/master/edit/profil/1

### Hasil verifikasi

- Semua URL di atas merespons HTTP 200.
- Halaman admin dapat dimuat dengan baik di browser.
- Alur edit/create dapat dibuka tanpa error runtime.

## Checklist PASS / FAIL per modul

| Modul | Status | Catatan |
|---|---|---|
| Dashboard | PASS | Dapat diakses dan dimuat dengan baik |
| CMS Workspace | PASS | Create/edit halaman CMS dapat dibuka |
| Master Data | PASS | Create/edit master data dapat dibuka |
| Routing Admin | PASS | Endpoint admin merespons dengan benar |
| CRUD Inti | PASS | Flow edit/create utama tidak error |

## Screenshot Before

- Sebelum perbaikan, pengujian route edit CMS/master data menunjukkan error runtime pada controller.
- Halaman edit tidak dapat disajikan dengan stabil karena controller gagal mengembalikan response yang sesuai.

## Screenshot After

- Setelah perbaikan, halaman edit/create admin dapat dimuat dan ditampilkan di browser.
- Respons HTTP untuk URL terkait menunjukkan status 200.

## Known Limitation

- Pengujian otomatis penuh masih dibatasi oleh lingkungan PHP lokal yang belum memiliki ekstensi SQLite3 aktif.
- Karena itu, pengujian unit yang membutuhkan inisialisasi database penuh tidak bisa dijalankan sepenuhnya di lingkungan saat ini.

## Kesimpulan

Milestone Dashboard Functional Stabilization telah mencapai tahap stabilisasi fungsional inti. Fokus utama pada perbaikan error runtime dan validasi alur admin telah berhasil dilakukan. Hasil verifikasi menunjukkan bahwa dashboard dan modul admin inti sudah dapat digunakan secara lebih andal untuk operasi dasar.
