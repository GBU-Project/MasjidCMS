# Installation & Setup Guide — MasjidCMS v1.0.0-rc1

This guide covers setup and deployment instructions for **MasjidCMS v1.0.0-rc1**.

---

## 1. System Requirements

- **PHP**: 8.2 or higher
- **PHP Extensions**: `intl`, `mbstring`, `json`, `pdo_mysql`, `curl`
- **Database**: MySQL 8.0+ or MariaDB 10.5+
- **Composer**: 2.5+

---

## 2. Quick Start Installation

### Step 1: Clone Repository
```bash
git clone https://github.com/MasjidCMS/MasjidCMS.git
cd MasjidCMS
git checkout tags/v1.0.0-rc1
```

### Step 2: Install Dependencies
```bash
composer install --no-dev --optimize-autoloader
```

### Step 3: Environment Configuration
Copy the `.env` template and set your database connection credentials:
```bash
cp env .env
```
Edit `.env`:
```ini
CI_ENVIRONMENT = production
app.baseURL = 'https://masjid.domain.org/'

database.default.hostname = localhost
database.default.database = masjidcms_db
database.default.username = masjid_user
database.default.password = secret_password
database.default.DBDriver = MySQLi
```

### Step 4: Run Database Migrations
Run CodeIgniter 4 database migrations:
```bash
php spark migrate
```

### Step 5: Seed Master Data & COA Accounts
Seed initial Master Data, RBAC permissions, Master COA, and Fund accounts:
```bash
php spark db:seed FinancialSeeder
```

---

## 3. Verification & Test Execution

Run the complete automated PHPUnit test suite to verify system integrity:
```bash
vendor/bin/phpunit --no-coverage
```

Expected Output:
```text
OK (88 tests, 287 assertions)
```
