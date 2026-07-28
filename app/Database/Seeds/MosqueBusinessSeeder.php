<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MosqueBusinessSeeder extends Seeder
{
    public function run(): void
    {
        $db = \Config\Database::connect();

        // 1. Bidang
        if ($db->tableExists('bidang') && $db->table('bidang')->countAllResults() === 0) {
            $bidangData = [
                ['name' => 'Bidang Dakwah & Peribadatan', 'slug' => 'dakwah-peribadatan', 'icon' => '📖', 'description' => 'Pengelolaan kegiatan dakwah, kajian, khutbah, dan jadwal ibadah harian.', 'sort_order' => 1, 'status' => 'ACTIVE', 'created_at' => date('Y-m-d H:i:s')],
                ['name' => 'Bidang Pendidikan & TPQ', 'slug' => 'pendidikan-tpq', 'icon' => '🎓', 'description' => 'Pendidikan Al-Qur\'an, madrasah, tahfidz, dan keilmuan Islam.', 'sort_order' => 2, 'status' => 'ACTIVE', 'created_at' => date('Y-m-d H:i:s')],
                ['name' => 'Bidang Sosial & Kemasyarakatan', 'slug' => 'sosial-kemasyarakatan', 'icon' => '🤝', 'description' => 'Pelayanan ambulans, tanggap bencana, dan santunan anak yatim.', 'sort_order' => 3, 'status' => 'ACTIVE', 'created_at' => date('Y-m-d H:i:s')],
                ['name' => 'Bidang ZISWAF (Zakat & Wakaf)', 'slug' => 'ziswaf', 'icon' => '💰', 'description' => 'Pengelolaan Unit Pengumpul Zakat (UPZ), zakat fitrah, infaq, dan wakaf.', 'sort_order' => 4, 'status' => 'ACTIVE', 'created_at' => date('Y-m-d H:i:s')],
                ['name' => 'Bidang Humas & Publikasi', 'slug' => 'humas-publikasi', 'icon' => '📢', 'description' => 'Pengelolaan portal media, website, dan komunikasi publik jamaah.', 'sort_order' => 5, 'status' => 'ACTIVE', 'created_at' => date('Y-m-d H:i:s')],
                ['name' => 'Bidang Sarana & Prasarana', 'slug' => 'sarana-prasarana', 'icon' => '🏛️', 'description' => 'Pemeliharaan gedung masjid, kebersihan, dan perlengkapan sarana.', 'sort_order' => 6, 'status' => 'ACTIVE', 'created_at' => date('Y-m-d H:i:s')],
                ['name' => 'Bidang Remaja Masjid (RISMA)', 'slug' => 'remaja-masjid', 'icon' => '⚡', 'description' => 'Pembinaan pemuda dan remaja masjid dalam kegiatan kreatif keislaman.', 'sort_order' => 7, 'status' => 'ACTIVE', 'created_at' => date('Y-m-d H:i:s')],
                ['name' => 'Bidang Ekonomi & Koperasi', 'slug' => 'ekonomi-koperasi', 'icon' => '🛒', 'description' => 'Pengembangan ekonomi keumatan, koperasi syariah, dan pemberdayaan usaha.', 'sort_order' => 8, 'status' => 'ACTIVE', 'created_at' => date('Y-m-d H:i:s')],
            ];
            $db->table('bidang')->insertBatch($bidangData);
        }

        // 2. Pengurus
        if ($db->tableExists('pengurus') && $db->table('pengurus')->countAllResults() === 0) {
            $pengurusData = [
                ['bidang_id' => 1, 'nama' => 'H. Ahmad Abdullah, S.Ag', 'jabatan' => 'Ketua Bidang Dakwah', 'jenis_kelamin' => 'L', 'telepon' => '081234567890', 'email' => 'dakwah@masjid.id', 'alamat' => 'Jakarta Selatan', 'bio' => 'Ustadz & Pengasuh Kajian Tafsir.', 'urutan' => 1, 'status' => 'ACTIVE', 'created_at' => date('Y-m-d H:i:s')],
                ['bidang_id' => 2, 'nama' => 'Dr. Hj. Siti Rahmah, M.Pd', 'jabatan' => 'Kepala Pengelola TPQ', 'jenis_kelamin' => 'P', 'telepon' => '081298765432', 'email' => 'tpq@masjid.id', 'alamat' => 'Jakarta Selatan', 'bio' => 'Pendidik & Praktisi Pendidikan Anak.', 'urutan' => 2, 'status' => 'ACTIVE', 'created_at' => date('Y-m-d H:i:s')],
                ['bidang_id' => 4, 'nama' => 'Ir. Muhammad Ridwan', 'jabatan' => 'Ketua Amil ZISWAF', 'jenis_kelamin' => 'L', 'telepon' => '081311223344', 'email' => 'ziswaf@masjid.id', 'alamat' => 'Jakarta Selatan', 'bio' => 'Praktisi Keuangan Syariah.', 'urutan' => 3, 'status' => 'ACTIVE', 'created_at' => date('Y-m-d H:i:s')],
            ];
            $db->table('pengurus')->insertBatch($pengurusData);
        }

        // 3. Program
        if ($db->tableExists('program_kegiatan') && $db->table('program_kegiatan')->countAllResults() === 0) {
            $programData = [
                ['bidang_id' => 2, 'nama' => 'Taman Pendidikan Al-Qur\'an (TPQ) Darussalam', 'slug' => 'tpq-darussalam', 'ringkasan' => 'Program pembelajaran membaca Al-Qur\'an dan tajwid anak-anak.', 'deskripsi' => 'TPQ Darussalam menyelenggarakan kelas iqra, tajwid, hafalan surat pendek, dan adab harian setiap sore.', 'penanggung_jawab' => 'Dr. Hj. Siti Rahmah, M.Pd', 'lokasi' => 'Gedung Madrasah Lt. 2', 'status' => 'ACTIVE', 'featured' => 1, 'created_at' => date('Y-m-d H:i:s')],
                ['bidang_id' => 2, 'nama' => 'Program Tahfidz Al-Qur\'an Intensif', 'slug' => 'tahfidz-intensif', 'ringkasan' => 'Program bimbingan hafalan Al-Qur\'an 30 Juz bagi remaja dan umum.', 'deskripsi' => 'Bimbingan tasmi dan murajaah bersama hafiz 30 juz setiap harinya pasca Subuh dan Maghrib.', 'penanggung_jawab' => 'Ustadz Zulkifli, Al-Hafiz', 'lokasi' => 'Ruang Utama Masjid', 'status' => 'ACTIVE', 'featured' => 1, 'created_at' => date('Y-m-d H:i:s')],
                ['bidang_id' => 3, 'nama' => 'Santunan Anak Yatim & Dhuafa Bulanan', 'slug' => 'santunan-yatim', 'ringkasan' => 'Program penyaluran bantuan pendidikan dan sembako rutin.', 'deskripsi' => 'Penyaluran beasiswa dan paket sembako bulanan untuk 100+ anak yatim dan dhuafa di lingkungan sekitar masjid.', 'penanggung_jawab' => 'H. Bambang Setiawan', 'lokasi' => 'Aula Masjid', 'status' => 'ACTIVE', 'featured' => 1, 'created_at' => date('Y-m-d H:i:s')],
                ['bidang_id' => 4, 'nama' => 'Pelaksanaan Qurban Idul Adha Hujjah', 'slug' => 'qurban-idul-adha', 'ringkasan' => 'Pengelolaan pendaftaran, pemotongan, dan distribusi hewan qurban.', 'deskripsi' => 'Layanan qurban terpercaya dengan standar syariah dan higienis serta penyaluran tepat sasaran.', 'penanggung_jawab' => 'Ir. Muhammad Ridwan', 'lokasi' => 'Area Parkir Masjid', 'status' => 'ACTIVE', 'featured' => 0, 'created_at' => date('Y-m-d H:i:s')],
            ];
            $db->table('program_kegiatan')->insertBatch($programData);
        }

        // 4. Layanan
        if ($db->tableExists('layanan_masjid') && $db->table('layanan_masjid')->countAllResults() === 0) {
            $layananData = [
                ['nama' => 'Layanan Ambulans Gratis 24 Jam', 'slug' => 'ambulans-gratis', 'icon' => '🚑', 'deskripsi' => 'Pelayanan armada ambulans gratis untuk antar jemput pasien dan jenazah jamaah.', 'persyaratan' => 'Menunjukkan KTP/KK jamaah.', 'jam_layanan' => '24 Jam Nonstop', 'kontak' => '0812-9999-0000', 'lokasi' => 'Pos Siaga Ambulans', 'status' => 'ACTIVE', 'urutan' => 1, 'created_at' => date('Y-m-d H:i:s')],
                ['nama' => 'Perpustakaan & Rumah Baca Islam', 'slug' => 'perpustakaan-islam', 'icon' => '📚', 'deskripsi' => 'Koleksi 2.000+ buku kitab klasik, sejarah Islam, dan literatur umum keumatan.', 'persyaratan' => 'Kartu anggota perpustakaan gratis.', 'jam_layanan' => '08:00 - 20:00 WIB', 'kontak' => '0813-8888-1111', 'lokasi' => 'Gedung Perpustakaan Lt. 1', 'status' => 'ACTIVE', 'urutan' => 2, 'created_at' => date('Y-m-d H:i:s')],
                ['nama' => 'Layanan Pemulasaraan Jenazah Syari', 'slug' => 'pemulasaraan-jenazah', 'icon' => '🕋', 'deskripsi' => 'Layanan lengkap memandikan, mengkafani, menyalatkan, hingga pengantaran ke pemakaman.', 'persyaratan' => 'Konfirmasi via telepon DKM.', 'jam_layanan' => '24 Jam', 'kontak' => '0812-7777-2222', 'lokasi' => 'Gedung Pemulasaraan', 'status' => 'ACTIVE', 'urutan' => 3, 'created_at' => date('Y-m-d H:i:s')],
                ['nama' => 'Penyelenggaraan akad Nikah & Aula', 'slug' => 'layanan-akad-nikah', 'icon' => '💍', 'deskripsi' => 'Fasilitas tempat akad nikah di ruang utama masjid dan sewa aula resepsi syariah.', 'persyaratan' => 'Surat rekomendasi KUA.', 'jam_layanan' => 'Sabtu & Minggu', 'kontak' => '0815-6666-3333', 'lokasi' => 'Ruang Utama & Aula', 'status' => 'ACTIVE', 'urutan' => 4, 'created_at' => date('Y-m-d H:i:s')],
            ];
            $db->table('layanan_masjid')->insertBatch($layananData);
        }

        // 5. Insert new Permissions
        if ($db->tableExists('permissions')) {
            $newPerms = [
                ['id' => 'p-bidang-01', 'permission_code' => 'bidang.manage', 'module_name' => 'bidang', 'description' => 'Kelola Master Data Bidang', 'created_at' => date('Y-m-d H:i:s')],
                ['id' => 'p-pengurus-01', 'permission_code' => 'pengurus.manage', 'module_name' => 'pengurus', 'description' => 'Kelola Data Pengurus Masjid', 'created_at' => date('Y-m-d H:i:s')],
                ['id' => 'p-program-01', 'permission_code' => 'program.manage', 'module_name' => 'program', 'description' => 'Kelola Program & Kegiatan Masjid', 'created_at' => date('Y-m-d H:i:s')],
                ['id' => 'p-layanan-01', 'permission_code' => 'layanan.manage', 'module_name' => 'layanan', 'description' => 'Kelola Layanan Masjid', 'created_at' => date('Y-m-d H:i:s')],
            ];
            foreach ($newPerms as $np) {
                $check = $db->table('permissions')->where('permission_code', $np['permission_code'])->get()->getRowArray();
                if (!$check) {
                    $db->table('permissions')->insert($np);
                }
            }
        }

        // 6. Insert Homepage Visibility Settings
        if ($db->tableExists('settings')) {
            $settingsList = [
                'show_pengurus_section' => '1',
                'show_program_section'  => '1',
                'show_layanan_section'  => '1',
                'show_bidang_section'   => '1',
            ];
            foreach ($settingsList as $key => $val) {
                $check = $db->table('settings')->where('setting_key', $key)->get()->getRowArray();
                if (!$check) {
                    $insertData = [
                        'setting_key'   => $key,
                        'setting_value' => $val,
                    ];
                    if ($db->fieldExists('setting_group', 'settings')) {
                        $insertData['setting_group'] = 'homepage';
                    }
                    $db->table('settings')->insert($insertData);
                }
            }
        }
    }
}
