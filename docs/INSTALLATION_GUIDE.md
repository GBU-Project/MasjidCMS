# MASJIDCMS FRESH INSTALLATION GUIDE (v1.0.0-rc1)

---

## 1. Prerequisites & Environment Requirements
- **OS**: Windows 10/11 / Linux (Ubuntu 22.04 LTS)
- **Web Server**: Apache 2.4+ or Nginx 1.20+ with URL rewriting (`mod_rewrite`)
- **PHP**: PHP 8.2+ (`php -v`)
- **PHP Extensions**: `intl`, `mbstring`, `json`, `pdo_mysql`, `curl`, `fileinfo`
- **Database**: MySQL 8.0+ or MariaDB 10.5+ (InnoDB Engine)
- **Package Manager**: Composer 2.5+

---

## 2. Step-by-Step Installation Instructions

### Step 1: Clone Repository
```bash
git clone https://github.com/MasjidCMS/MasjidCMS.git
cd MasjidCMS
git checkout tags/v1.0.0-rc1
```

### Step 2: Install Composer Dependencies
```bash
composer install --no-dev --optimize-autoloader
```

### Step 3: Copy Environment Configuration File
```bash
cp .env.example .env
```

### Step 4: Configure `.env` Settings
Edit `.env` and set your production environment credentials:
```ini
CI_ENVIRONMENT = production
app.baseURL = 'https://masjid.domain.org/'

database.default.hostname = localhost
database.default.database = masjidcms_db
database.default.username = masjid_user
database.default.password = secret_db_password
```

### Step 5: Create Database in MySQL
```sql
CREATE DATABASE masjidcms_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### Step 6: Import Database Schema (Option A or Option B)

- **Option A (Via Spark CLI Migrations)**:
  ```bash
  php spark migrate
  ```

- **Option B (Via Direct SQL Import)**:
  ```bash
  mysql -u masjid_user -p masjidcms_db < database/schema.sql
  ```

### Step 7: Run Database Seeders

- **Via Spark CLI**:
  ```bash
  php spark db:seed FinancialSeeder
  ```

- **Via SQL Import**:
  ```bash
  mysql -u masjid_user -p masjidcms_db < database/seed.sql
  ```

### Step 8: Set Folder Permissions
Ensure directory `writable/` is writable by web server:
```bash
chmod -R 775 writable/
```

### Step 9: Launch Application
Start local development server (or access via Apache/Nginx vhost):
```bash
php spark serve
```

### Step 10: Initial Login Credentials

> [!CAUTION]
> **DEFAULT SUPER ADMIN CREDENTIALS**  
> **Username**: `superadmin`  
> **Initial Password**: `SuperAdminSecretPassword2026!`  
> **Action Required**: **IMMEDIATELY CHANGE PASSWORD UPON FIRST LOGIN** via `/admin/settings`.
