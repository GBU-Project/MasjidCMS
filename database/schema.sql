-- ==============================================================================
-- MasjidCMS Database DDL Schema Dump (v1.0.0-rc1)
-- Target Database Engine: MySQL 8.0+ / MariaDB 10.5+ (InnoDB Engine)
-- ==============================================================================

SET FOREIGN_KEY_CHECKS = 0;

-- 1. Funds Table
CREATE TABLE IF NOT EXISTS `funds` (
  `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
  `uuid` CHAR(36) NOT NULL UNIQUE,
  `masjid_id` VARCHAR(64) NOT NULL,
  `fund_code` VARCHAR(32) NOT NULL,
  `name` VARCHAR(128) NOT NULL,
  `fund_type` ENUM('UNRESTRICTED', 'RESTRICTED', 'ENDOWMENT') NOT NULL DEFAULT 'UNRESTRICTED',
  `description` TEXT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` DATETIME NULL,
  INDEX `idx_fund_masjid_code` (`masjid_id`, `fund_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. COA Accounts Table
CREATE TABLE IF NOT EXISTS `coa_accounts` (
  `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
  `uuid` CHAR(36) NOT NULL UNIQUE,
  `masjid_id` VARCHAR(64) NOT NULL,
  `account_code` VARCHAR(32) NOT NULL,
  `name` VARCHAR(128) NOT NULL,
  `account_type` ENUM('ASSET', 'LIABILITY', 'EQUITY', 'REVENUE', 'EXPENSE') NOT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` DATETIME NULL,
  INDEX `idx_coa_masjid_code` (`masjid_id`, `account_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Financial Accounts Table
CREATE TABLE IF NOT EXISTS `financial_accounts` (
  `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
  `uuid` CHAR(36) NOT NULL UNIQUE,
  `masjid_id` VARCHAR(64) NOT NULL,
  `code` VARCHAR(32) NOT NULL UNIQUE,
  `name` VARCHAR(128) NOT NULL,
  `bank_name` VARCHAR(64) NULL,
  `account_number` VARCHAR(64) NULL,
  `cached_balance` DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_fin_acc_masjid` (`masjid_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Programs Table
CREATE TABLE IF NOT EXISTS `programs` (
  `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
  `uuid` CHAR(36) NOT NULL UNIQUE,
  `masjid_id` VARCHAR(64) NOT NULL,
  `name` VARCHAR(128) NOT NULL,
  `target_amount` DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_program_masjid` (`masjid_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Financial Transactions Table
CREATE TABLE IF NOT EXISTS `financial_transactions` (
  `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
  `uuid` CHAR(36) NOT NULL UNIQUE,
  `masjid_id` VARCHAR(64) NOT NULL,
  `fund_id` BIGINT NOT NULL,
  `category_id` BIGINT NOT NULL,
  `financial_account_id` BIGINT NOT NULL,
  `transaction_number` VARCHAR(64) NOT NULL UNIQUE,
  `transaction_type` ENUM('INCOME', 'EXPENSE', 'TRANSFER', 'ADJUSTMENT') NOT NULL,
  `amount` DECIMAL(15, 2) NOT NULL,
  `transaction_date` DATETIME NOT NULL,
  `description` TEXT NOT NULL,
  `status` ENUM('DRAFT', 'SUBMITTED', 'PENDING_APPROVAL', 'APPROVED', 'REJECTED', 'POSTED', 'VOID', 'CANCELLED') NOT NULL DEFAULT 'DRAFT',
  `submitted_by` BIGINT NULL,
  `approved_by` BIGINT NULL,
  `posted_by` BIGINT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`fund_id`) REFERENCES `funds`(`id`) ON DELETE RESTRICT,
  FOREIGN KEY (`financial_account_id`) REFERENCES `financial_accounts`(`id`) ON DELETE RESTRICT,
  INDEX `idx_trx_date_fund` (`masjid_id`, `fund_id`, `status`, `transaction_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Journal Entries Table
CREATE TABLE IF NOT EXISTS `journal_entries` (
  `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
  `uuid` CHAR(36) NOT NULL UNIQUE,
  `masjid_id` VARCHAR(64) NOT NULL,
  `transaction_id` BIGINT NOT NULL UNIQUE,
  `journal_number` VARCHAR(64) NOT NULL UNIQUE,
  `journal_date` DATETIME NOT NULL,
  `description` TEXT NOT NULL,
  `is_balanced` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`transaction_id`) REFERENCES `financial_transactions`(`id`) ON DELETE RESTRICT,
  INDEX `idx_jrn_date` (`masjid_id`, `journal_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Journal Details Table
CREATE TABLE IF NOT EXISTS `journal_details` (
  `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
  `journal_id` BIGINT NOT NULL,
  `account_id` BIGINT NOT NULL,
  `debit` DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
  `credit` DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
  FOREIGN KEY (`journal_id`) REFERENCES `journal_entries`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`account_id`) REFERENCES `coa_accounts`(`id`) ON DELETE RESTRICT,
  INDEX `idx_jrn_line_acc` (`journal_id`, `account_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. Approval Logs Table
CREATE TABLE IF NOT EXISTS `approval_logs` (
  `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
  `transaction_id` BIGINT NOT NULL,
  `user_id` BIGINT NOT NULL,
  `previous_status` VARCHAR(32) NOT NULL,
  `new_status` VARCHAR(32) NOT NULL,
  `action` VARCHAR(32) NOT NULL,
  `notes` TEXT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`transaction_id`) REFERENCES `financial_transactions`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
