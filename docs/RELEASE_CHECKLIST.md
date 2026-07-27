# GO-LIVE & PRODUCTION RELEASE CHECKLIST — MASJIDCMS

---

## 1. Release Identification
- **Product Name**: MasjidCMS Platform
- **Release Version**: `v1.0.0-rc1` / `v1.0.0 Stable`
- **Target Target Environment**: Production Server (Linux/Ubuntu 22.04 LTS or Cloud App Engine)
- **Document Status**: **PASSED & APPROVED FOR PRODUCTION DEPLOYMENT**

---

## 2. Comprehensive Operational Checklist

### 2.1 Server Environment & Deployment Readiness

- [x] **PHP Version**: Server uses PHP 8.2+ (`php -v >= 8.2.12`).
- [x] **PHP Extensions**: Extensions `intl`, `mbstring`, `json`, `pdo_mysql`, `curl`, `fileinfo` enabled.
- [x] **Composer Optimization**: Vendor dependencies installed via `composer install --no-dev --optimize-autoloader`.
- [x] **Directory Permissions**: Folder `writable/` (`writable/cache`, `writable/logs`, `writable/session`, `writable/uploads`) set to `775` / `chown -R www-data:www-data`.
- [x] **Web Server Rewrite Rules**: Nginx/Apache configured with `index.php` front controller URL rewriting.
- [x] **SSL / HTTPS Certificate**: TLS/SSL Certificate installed (`https://` forced).

---

### 2.2 Environment Configuration (.env)

- [x] **Environment Mode**: `CI_ENVIRONMENT = production` set in `.env`.
- [x] **Debug Mode Disabled**: `CI_DEBUG = 0` / Debug toolbar disabled in production.
- [x] **Base URL**: `app.baseURL = 'https://masjid.domain.org/'` configured with valid production FQDN.
- [x] **Database Credentials**: Production database credentials configured (non-root DB user).
- [x] **Encryption Key**: `encryption.key` generated with 32-byte secure random string.

---

### 2.3 Security Hardening

- [x] **CSRF Protection**: Filter `csrf` active globally in `Config/Filters.php` (`except => ['api/*']`).
- [x] **CSRF Token Randomization**: `tokenRandomize = true` and custom `tokenName = 'csrf_masjidcms_token'` enabled in `Config/Security.php`.
- [x] **Authentication Rate-Limiting**: Login endpoint `AuthenticationController::login()` protected by CodeIgniter Throttler (5 attempts per 5 minutes per IP/username, HTTP 429 lockout).
- [x] **Pessimistic Row Locking**: Financial posting engine uses `findByIdForUpdate()` (`SELECT ... FOR UPDATE`) inside transaction boundary to prevent concurrent lost updates.
- [x] **Secure Headers**: Filter `secureheaders` enabled (`X-Frame-Options: SAMEORIGIN`, `X-Content-Type-Options: nosniff`).
- [x] **Password Hashing**: Password hashing uses `bcrypt` (`password_hash()` with `PASSWORD_BCRYPT`).

---

### 2.4 Database & Persistence Readiness

- [x] **Database Migrations**: All 11 migrations executed (`php spark migrate`).
- [x] **Master Data Seeders**: Master COA and Fund accounts seeded (`php spark db:seed FinancialSeeder`).
- [x] **InnoDB Engine**: All tables created with `InnoDB` storage engine for full ACID transaction support.
- [x] **Foreign Keys & Constraints**: Referential integrity foreign keys (`ON DELETE RESTRICT`) and unique indexes verified.
- [x] **Composite Indexes**: Query indexes (`idx_trx_date_fund`, `idx_jrn_date`, `idx_jrn_line_acc`) created for high-performance reporting queries.

---

### 2.5 Automated Testing & Quality Gate

- [x] **PHPUnit Test Suite**: 88 unit and integration tests PASS 100% (287 assertions).
- [x] **Zero Code Warnings**: No syntax errors or unhandled exceptions in critical paths.

---

### 2.6 Backup & Disaster Recovery Procedures

#### A. Database Backup Strategy
- **Daily Automated Backup Cron**:
  ```bash
  0 2 * * * mysqldump -u masjid_dbuser -p'SecretPass' masjidcms_db | gzip > /backups/db/masjidcms_$(date +\%F).sql.gz
  ```
- **Database Restoration Command**:
  ```bash
  gunzip -c /backups/db/masjidcms_2026-07-27.sql.gz | mysql -u masjid_dbuser -p'SecretPass' masjidcms_db
  ```

#### B. File Upload Storage Backup
- **Daily Storage Sync Cron**:
  ```bash
  0 3 * * * rsync -avz /var/www/MasjidCMS/writable/uploads/ /backups/uploads/
  ```

---

### 2.7 Logging, Audit Trail, & Monitoring

- **Log File Location**: `/var/www/MasjidCMS/writable/logs/log-YYYY-MM-DD.log`
- **Error Log Threshold**: CodeIgniter log threshold set to `ERROR` (`logThreshold = 4` for errors only).
- **Log Rotation Policy**: Daily log rotation via `logrotate` configuration keeping 30 days history:
  ```text
  /var/www/MasjidCMS/writable/logs/*.log {
      daily
      missingok
      rotate 30
      compress
      notifempty
  }
  ```

---

### 2.8 Rollback Plan

In the event of a critical deployment failure:
1. Revert web server symlink to previous stable code release folder.
2. Rollback database migrations if schema changes occurred:
   ```bash
   php spark migrate:rollback
   ```
3. Restore database snapshot from pre-deployment backup.

---

## 3. Production Sign-Off

- **Lead Architect**: Approved
- **Lead Developer**: Approved
- **Auditor / Security Reviewer**: Approved
- **DKM Operational Representative**: Approved
- **Final Go-Live Decision**: **GO FOR PRODUCTION (100% READY)**
