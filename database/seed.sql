-- ==============================================================================
-- MasjidCMS Database DML Initial Seed Dump (v1.0.0-rc1)
-- Default Super Admin Credentials:
-- Username: superadmin
-- Password: SuperAdminSecretPassword2026! (IMMEDIATELY CHANGE ON FIRST LOGIN)
-- ==============================================================================

-- 1. Seed Funds
INSERT INTO `funds` (`id`, `uuid`, `masjid_id`, `fund_code`, `name`, `fund_type`, `description`) VALUES
(1, 'f8c05763-7186-4f4c-bc67-000000000001', 'm-1', 'GENERAL', 'Kas Tunai Umum', 'UNRESTRICTED', 'Dana operasional utama masjid'),
(2, 'f8c05763-7186-4f4c-bc67-000000000002', 'm-1', 'BUILDING', 'Kas Pembangunan', 'UNRESTRICTED', 'Dana renovasi & pembangunan fisik'),
(3, 'f8c05763-7186-4f4c-bc67-000000000003', 'm-1', 'ZAKAT', 'Dana Zakat Maal & Fitrah', 'RESTRICTED', 'Dana zakat khusus 8 asnaf');

-- 2. Seed COA Accounts
INSERT INTO `coa_accounts` (`id`, `uuid`, `masjid_id`, `account_code`, `name`, `account_type`, `is_active`) VALUES
(1, 'c8c05763-7186-4f4c-bc67-000000000101', 'm-1', '10001', 'Kas Tunai Utama', 'ASSET', 1),
(2, 'c8c05763-7186-4f4c-bc67-000000000401', 'm-1', '40001', 'Infaq Kotak Jumat', 'REVENUE', 1),
(3, 'c8c05763-7186-4f4c-bc67-000000000501', 'm-1', '50001', 'Beban Kebersihan', 'EXPENSE', 1);

-- 3. Seed Financial Accounts
INSERT INTO `financial_accounts` (`id`, `uuid`, `masjid_id`, `code`, `name`, `bank_name`, `account_number`, `cached_balance`) VALUES
(100, 'a8c05763-7186-4f4c-bc67-000000000100', 'm-1', 'KAS_UTAMA', 'Kas Tunai Utama', 'BSI', '700-1234-567', 45850000.00);

-- 4. Seed Programs
INSERT INTO `programs` (`id`, `uuid`, `masjid_id`, `name`, `target_amount`, `is_active`) VALUES
(1, 'p8c05763-7186-4f4c-bc67-000000000001', 'm-1', 'Renovasi Menara Masjid', 100000000.00, 1);
