-- ==============================================================================
-- MasjidCMS Complete Database DML Initial Seed Dump (v1.0.0-rc1)
-- ==============================================================================

SET FOREIGN_KEY_CHECKS = 0;

-- 1. Seed Roles
INSERT INTO `roles` (`id`, `name`, `description`) VALUES
(1, 'Super Admin', 'Akses penuh ke seluruh sistem dan konfigurasi platform'),
(2, 'Administrator', 'Pengelola operasional masjid dan master data'),
(3, 'Bendahara', 'Pengelola transaksi keuangan, posting, dan buku besar'),
(4, 'Ketua DKM', 'Penyetuju (Approver) anggaran dan transaksi keuangan');

-- 2. Seed Permissions
INSERT INTO `permissions` (`id`, `name`, `description`) VALUES
(1, 'manage_users', 'Mengelola user, role, dan permission'),
(2, 'manage_financials', 'Menginput transaksi dan membuat kantong dana'),
(3, 'approve_transactions', 'Menyetujui atau menolak draft transaksi keuangan'),
(4, 'view_reports', 'Melihat dan mengekspor laporan keuangan & operasional');

-- 3. Seed Role Permissions
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES
(1, 1), (1, 2), (1, 3), (1, 4),
(3, 2), (3, 4),
(4, 3), (4, 4);

-- 4. Seed Super Admin User
-- Default password: SuperAdminSecretPassword2026! (bcrypt hash)
INSERT INTO `users` (`id`, `uuid`, `username`, `email`, `password_hash`, `full_name`, `phone`, `is_active`) VALUES
(1, 'u8c05763-7186-4f4c-bc67-000000000001', 'superadmin', 'admin@masjidcms.org', '$2y$10$e.wS.c.wZJm6Rk8O2A2e1eYxLg8g7A6G5H4J3K2L1M0N9O8P7Q6R5', 'Super Administrator DKM', '081234567890', 1);

INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES (1, 1);

-- 5. Seed Masjid Profile
INSERT INTO `masjids` (`id`, `uuid`, `name`, `code`, `address`, `city`, `phone`, `email`, `legal_status`) VALUES
(1, 'm8c05763-7186-4f4c-bc67-000000000001', 'Masjid Agung Darussalam', 'MASJID_MAIN', 'Jl. Raya Masjid No. 1, Pusat Kota', 'Jakarta', '021-5550199', 'info@masjidcms.org', 'Yayasan Wakaf Resmi');

-- 6. Seed Family & Jamaah
INSERT INTO `families` (`id`, `uuid`, `family_card_number`, `head_of_family_name`, `address`) VALUES
(1, 'fam8c057-7186-4f4c-bc67-000000000001', '3171000102030001', 'H. Ahmad Syarif', 'Jl. Syariah No. 10');

INSERT INTO `jamaah` (`id`, `uuid`, `family_id`, `nik`, `full_name`, `gender`, `birth_place`, `birth_date`, `phone`, `email`, `address`, `status`) VALUES
(1, 'jam8c057-7186-4f4c-bc67-000000000001', 1, '3171000102030009', 'H. Ahmad Syarif', 'MALE', 'Jakarta', '1975-05-12', '081299998888', 'ahmad@jamaah.org', 'Jl. Syariah No. 10', 'ACTIVE');

-- 7. Seed Funds
INSERT INTO `funds` (`id`, `uuid`, `masjid_id`, `fund_code`, `name`, `fund_type`, `description`) VALUES
(1, 'f8c05763-7186-4f4c-bc67-000000000001', 'm-1', 'GENERAL', 'Kas Tunai Umum', 'UNRESTRICTED', 'Dana operasional utama masjid'),
(2, 'f8c05763-7186-4f4c-bc67-000000000002', 'm-1', 'BUILDING', 'Kas Pembangunan', 'UNRESTRICTED', 'Dana renovasi & pembangunan fisik'),
(3, 'f8c05763-7186-4f4c-bc67-000000000003', 'm-1', 'ZAKAT', 'Dana Zakat Maal & Fitrah', 'RESTRICTED', 'Dana zakat khusus 8 asnaf');

-- 8. Seed COA Accounts
INSERT INTO `coa_accounts` (`id`, `uuid`, `masjid_id`, `account_code`, `name`, `account_type`, `is_active`) VALUES
(1, 'c8c05763-7186-4f4c-bc67-000000000101', 'm-1', '10001', 'Kas Tunai Utama', 'ASSET', 1),
(2, 'c8c05763-7186-4f4c-bc67-000000000401', 'm-1', '40001', 'Infaq Kotak Jumat', 'REVENUE', 1),
(3, 'c8c05763-7186-4f4c-bc67-000000000501', 'm-1', '50001', 'Beban Kebersihan', 'EXPENSE', 1);

-- 9. Seed Financial Accounts
INSERT INTO `financial_accounts` (`id`, `uuid`, `masjid_id`, `code`, `name`, `bank_name`, `account_number`, `cached_balance`) VALUES
(100, 'a8c05763-7186-4f4c-bc67-000000000100', 'm-1', 'KAS_UTAMA', 'Kas Tunai Utama', 'BSI', '700-1234-567', 45850000.00);

-- 10. Seed Program Categories & Programs
INSERT INTO `program_categories` (`id`, `name`, `description`) VALUES
(1, 'Pembangunan', 'Program renovasi dan perbaikan infrastruktur masjid'),
(2, 'Sosial & ZIS', 'Program santunan yatim dhuafa dan pemberdayaan ekonomi');

INSERT INTO `programs` (`id`, `uuid`, `masjid_id`, `category_id`, `name`, `target_amount`, `is_active`) VALUES
(1, 'p8c05763-7186-4f4c-bc67-000000000001', 'm-1', 1, 'Renovasi Menara Masjid', 100000000.00, 1);

-- 11. Seed Kajian Schedule
INSERT INTO `kajian` (`id`, `uuid`, `masjid_id`, `speaker_name`, `topic`, `schedule_date`, `schedule_time`, `location`, `status`) VALUES
(1, 'k8c05763-7186-4f4c-bc67-000000000001', 'm-1', 'Ustadz Dr. H. Abdullah Gymnastiar', 'Tazkiyatun Nufs & Fiqih Muamalah Syariah', '2026-08-01', '18:30:00', 'Ruang Utama Masjid', 'UPCOMING'),
(2, 'k8c05763-7186-4f4c-bc67-000000000002', 'm-1', 'Ustadz Adi Hidayat, Lc., M.A.', 'Pendalaman Kitab Syamail Muhammadiyah', '2026-08-05', '19:45:00', 'Aula Serbaguna Masjid', 'UPCOMING');

-- 12. Seed Posts & Articles
INSERT INTO `posts` (`id`, `author_id`, `title`, `slug`, `content`, `is_published`) VALUES
(1, 1, 'Pengumuman Pelaksanaan Shalat Jumat & Protokol Kebersihan', 'pengumuman-shalat-jumat-protokol-kebersihan', 'Pelaksanaan shalat Jumat pekan ini akan dipimpin oleh Khathib Dr. H. Muhammad Zulkarnain dengan tema Ketakwaan dan Ukhuwah Islamiyah.', 1),
(2, 1, 'Laporan Penyaluran Santunan Anak Yatim & Dhuafa Pekan Ini', 'laporan-penyaluran-santunan-anak-yatim-dhuafa', 'Alhamdulillah telah disalurkan dana bantuan sebesar Rp 15.000.000 kepada 50 anak yatim terdaftar di lingkungan masjid.', 1);

-- 13. Seed System Settings
INSERT INTO `settings` (`setting_key`, `setting_value`, `setting_group`) VALUES
('site_name', 'MasjidCMS Platform', 'general'),
('site_timezone', 'Asia/Jakarta', 'general'),
('currency_symbol', 'Rp', 'finance');

SET FOREIGN_KEY_CHECKS = 1;
