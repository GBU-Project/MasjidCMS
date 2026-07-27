# MASJIDCMS ZERO-CONFIGURATION WEB INSTALLER GUIDE (v1.0.0-rc1)

---

## 1. Web Installer Overview
MasjidCMS features a 6-step zero-configuration Web Installation Wizard (`/install`) allowing system administrators to set up database connections, schema/seeders, environment files, and Super Admin accounts directly through any web browser.

---

## 2. 6-Step Installation Flow

```text
[Step 1: Welcome]
       ↓
[Step 2: Requirements Check] — (PHP 8.2+, MySQL Extensions, Writable Folders)
       ↓
[Step 3: Database Config] — (Host, Port, User, Password, DB Name + Connection Test)
       ↓
[Step 4: Application Setup] — (App Name, BaseURL, Timezone + .env Generator)
       ↓
[Step 5: Super Admin Account] — (Name, Username, Email, Password + DDL/DML Seeder)
       ↓
[Step 6: Finish & Lock] — (Creates installed.lock & Redirects to /admin/dashboard)
```

---

## 3. Security Lock Behavior (`installed.lock`)
Upon completion of Step 6, the system automatically creates `writable/installed.lock`. When `installed.lock` exists, the installer wizard automatically disables, and any attempt to access `/install/*` routes immediately redirects to `/admin/dashboard`.
