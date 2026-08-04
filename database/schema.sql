-- ==============================================================================
-- MasjidCMS Complete Database DDL Schema Dump (v1.0.0-rc1)
-- Target Database Engine: MySQL 8.0+ / MariaDB 10.5+ (InnoDB Engine)
-- ==============================================================================

SET FOREIGN_KEY_CHECKS = 0;

-- ------------------------------------------------------------------------------
-- 1. SECURITY & RBAC DOMAIN
-- ------------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `users` (
  `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
  `uuid` CHAR(36) NOT NULL UNIQUE,
  `username` VARCHAR(64) NOT NULL UNIQUE,
  `email` VARCHAR(128) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `full_name` VARCHAR(128) NOT NULL,
  `phone` VARCHAR(32) NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` DATETIME NULL,
  INDEX `idx_users_username_email` (`username`, `email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `roles` (
  `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(64) NOT NULL UNIQUE,
  `description` TEXT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `permissions` (
  `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(64) NOT NULL UNIQUE,
  `description` TEXT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `role_permissions` (
  `role_id` BIGINT NOT NULL,
  `permission_id` BIGINT NOT NULL,
  PRIMARY KEY (`role_id`, `permission_id`),
  FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`permission_id`) REFERENCES `permissions`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `user_roles` (
  `user_id` BIGINT NOT NULL,
  `role_id` BIGINT NOT NULL,
  PRIMARY KEY (`user_id`, `role_id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 2. MASJID & ORGANIZATIONAL DOMAIN
-- ------------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `masjids` (
  `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
  `uuid` CHAR(36) NOT NULL UNIQUE,
  `name` VARCHAR(128) NOT NULL,
  `code` VARCHAR(32) NOT NULL UNIQUE,
  `address` TEXT NOT NULL,
  `city` VARCHAR(64) NOT NULL,
  `phone` VARCHAR(32) NULL,
  `email` VARCHAR(128) NULL,
  `legal_status` VARCHAR(128) NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `branches` (
  `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
  `masjid_id` BIGINT NOT NULL,
  `name` VARCHAR(128) NOT NULL,
  `code` VARCHAR(32) NOT NULL UNIQUE,
  `address` TEXT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`masjid_id`) REFERENCES `masjids`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 3. JAMAAH & FAMILY DOMAIN
-- ------------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `families` (
  `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
  `uuid` CHAR(36) NOT NULL UNIQUE,
  `family_card_number` VARCHAR(32) NOT NULL UNIQUE,
  `head_of_family_name` VARCHAR(128) NOT NULL,
  `address` TEXT NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `jamaah` (
  `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
  `uuid` CHAR(36) NOT NULL UNIQUE,
  `family_id` BIGINT NULL,
  `nik` VARCHAR(32) NULL UNIQUE,
  `full_name` VARCHAR(128) NOT NULL,
  `gender` ENUM('MALE', 'FEMALE') NOT NULL,
  `birth_place` VARCHAR(64) NULL,
  `birth_date` DATE NULL,
  `phone` VARCHAR(32) NULL,
  `email` VARCHAR(128) NULL,
  `address` TEXT NOT NULL,
  `status` ENUM('ACTIVE', 'INACTIVE', 'DECEASED', 'MOVED') NOT NULL DEFAULT 'ACTIVE',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`family_id`) REFERENCES `families`(`id`) ON DELETE SET NULL,
  INDEX `idx_jamaah_name_status` (`full_name`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `jamaah_contacts` (
  `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
  `jamaah_id` BIGINT NOT NULL,
  `contact_type` VARCHAR(32) NOT NULL,
  `contact_value` VARCHAR(128) NOT NULL,
  FOREIGN KEY (`jamaah_id`) REFERENCES `jamaah`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `family_members` (
  `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
  `family_id` BIGINT NOT NULL,
  `jamaah_id` BIGINT NOT NULL,
  `relation_status` ENUM('HEAD', 'SPOUSE', 'CHILD', 'OTHER') NOT NULL,
  FOREIGN KEY (`family_id`) REFERENCES `families`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`jamaah_id`) REFERENCES `jamaah`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 4. KEUANGAN (FINANCIAL ENGINE) DOMAIN
-- ------------------------------------------------------------------------------

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
  INDEX `idx_fund_code` (`masjid_id`, `fund_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  INDEX `idx_coa_code` (`masjid_id`, `account_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `financial_periods` (
  `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
  `period_code` VARCHAR(32) NOT NULL UNIQUE,
  `name` VARCHAR(64) NOT NULL,
  `start_date` DATE NOT NULL,
  `end_date` DATE NOT NULL,
  `is_closed` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `budget` (
  `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
  `period_id` BIGINT NOT NULL,
  `account_id` BIGINT NOT NULL,
  `fund_id` BIGINT NOT NULL,
  `allocated_amount` DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
  `used_amount` DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
  FOREIGN KEY (`period_id`) REFERENCES `financial_periods`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`account_id`) REFERENCES `coa_accounts`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`fund_id`) REFERENCES `funds`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  INDEX `idx_trx_number_date` (`transaction_number`, `transaction_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  INDEX `idx_journal_number_date` (`journal_number`, `journal_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `journal_details` (
  `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
  `journal_id` BIGINT NOT NULL,
  `account_id` BIGINT NOT NULL,
  `debit` DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
  `credit` DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
  FOREIGN KEY (`journal_id`) REFERENCES `journal_entries`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`account_id`) REFERENCES `coa_accounts`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 5. APPROVAL WORKFLOW DOMAIN
-- ------------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `approval_requests` (
  `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
  `transaction_id` BIGINT NOT NULL,
  `requester_id` BIGINT NOT NULL,
  `status` ENUM('PENDING', 'APPROVED', 'REJECTED') NOT NULL DEFAULT 'PENDING',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`transaction_id`) REFERENCES `financial_transactions`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `approval_steps` (
  `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
  `request_id` BIGINT NOT NULL,
  `approver_id` BIGINT NOT NULL,
  `step_order` INT NOT NULL DEFAULT 1,
  `status` ENUM('PENDING', 'APPROVED', 'REJECTED') NOT NULL DEFAULT 'PENDING',
  `notes` TEXT NULL,
  `action_at` DATETIME NULL,
  FOREIGN KEY (`request_id`) REFERENCES `approval_requests`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

-- ------------------------------------------------------------------------------
-- 6. PROGRAM & EVENT DOMAIN
-- ------------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `program_categories` (
  `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(64) NOT NULL UNIQUE,
  `description` TEXT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `programs` (
  `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
  `uuid` CHAR(36) NOT NULL UNIQUE,
  `masjid_id` VARCHAR(64) NOT NULL,
  `category_id` BIGINT NULL,
  `name` VARCHAR(128) NOT NULL,
  `target_amount` DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`category_id`) REFERENCES `program_categories`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 7. CONTENT MANAGEMENT (CMS) DOMAIN
-- ------------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `categories` (
  `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(64) NOT NULL,
  `slug` VARCHAR(64) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `pages` (
  `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(128) NOT NULL,
  `slug` VARCHAR(128) NOT NULL UNIQUE,
  `content` TEXT NOT NULL,
  `is_published` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `posts` (
  `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
  `category_id` BIGINT NULL,
  `author_id` BIGINT NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `featured_media_id` BIGINT NULL,
  `slug` VARCHAR(255) NOT NULL UNIQUE,
  `content` TEXT NOT NULL,
  `is_published` TINYINT(1) NOT NULL DEFAULT 1,
  `published_at` DATETIME NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`featured_media_id`) REFERENCES `media`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `menus` (
  `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(64) NOT NULL,
  `url` VARCHAR(255) NOT NULL,
  `menu_order` INT NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `media` (
  `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
  `filename` VARCHAR(255) NOT NULL,
  `filepath` VARCHAR(255) NOT NULL,
  `mime_type` VARCHAR(64) NOT NULL,
  `filesize` INT NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `gallery` (
  `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
  `media_id` BIGINT NOT NULL,
  `caption` VARCHAR(255) NULL,
  FOREIGN KEY (`media_id`) REFERENCES `media`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `kajian` (
  `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
  `uuid` CHAR(36) NOT NULL UNIQUE,
  `masjid_id` VARCHAR(64) NOT NULL,
  `speaker_name` VARCHAR(128) NOT NULL,
  `speaker_photo_media_id` BIGINT NULL,
  `topic` VARCHAR(255) NOT NULL,
  `schedule_date` DATE NOT NULL,
  `schedule_time` TIME NOT NULL,
  `location` VARCHAR(128) NOT NULL,
  `status` ENUM('UPCOMING', 'COMPLETED', 'CANCELLED') NOT NULL DEFAULT 'UPCOMING',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`speaker_photo_media_id`) REFERENCES `media`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 8. SYSTEM & AUDIT LOG DOMAIN
-- ------------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `settings` (
  `setting_key` VARCHAR(64) PRIMARY KEY,
  `setting_value` TEXT NOT NULL,
  `setting_group` VARCHAR(32) NOT NULL DEFAULT 'general'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `audit_logs` (
  `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
  `user_id` BIGINT NULL,
  `action` VARCHAR(64) NOT NULL,
  `module` VARCHAR(64) NOT NULL,
  `description` TEXT NOT NULL,
  `ip_address` VARCHAR(45) NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `notifications` (
  `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
  `user_id` BIGINT NOT NULL,
  `title` VARCHAR(128) NOT NULL,
  `message` TEXT NOT NULL,
  `is_read` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
