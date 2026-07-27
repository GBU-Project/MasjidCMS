<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Step 4: Application Setup — MasjidCMS</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/admin-dashboard.css') ?>">
</head>
<body style="display: flex; align-items: center; justify-content: center; min-height: 100vh; background: var(--bg-app); padding: 24px;">
    <div class="panel-card" style="max-width: 600px; width: 100%; padding: 32px;">
        <div style="font-size: 13px; font-weight: 600; color: var(--primary-600); margin-bottom: 4px;">STEP 4 OF 6</div>
        <h2 style="font-size: 20px; font-weight: 700; margin-bottom: 20px;">Konfigurasi Aplikasi & Generate File .env</h2>

        <form action="<?= site_url('install/application') ?>" method="POST" style="display: flex; flex-direction: column; gap: 14px;">
            <?= csrf_field() ?>
            <div>
                <label style="display: block; font-size: 13px; font-weight: 500; margin-bottom: 4px;">Nama Aplikasi / Masjid</label>
                <input type="text" name="app_name" value="MasjidCMS — Masjid Agung" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;">
            </div>

            <div>
                <label style="display: block; font-size: 13px; font-weight: 500; margin-bottom: 4px;">Application URL (BaseURL)</label>
                <input type="text" name="app_url" value="http://localhost:8080/" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;">
            </div>

            <div>
                <label style="display: block; font-size: 13px; font-weight: 500; margin-bottom: 4px;">Timezone</label>
                <select name="app_timezone" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px; background: white;">
                    <option value="Asia/Jakarta">Asia/Jakarta (WIB)</option>
                    <option value="Asia/Makassar">Asia/Makassar (WITA)</option>
                    <option value="Asia/Jayapura">Asia/Jayapura (WIT)</option>
                </select>
            </div>

            <div style="display: flex; justify-content: space-between; margin-top: 12px;">
                <a href="<?= site_url('install/database') ?>" class="btn btn-secondary">‹ Kembali</a>
                <button type="submit" class="btn btn-primary">Simpan & Generate .env ›</button>
            </div>
        </form>
    </div>
</body>
</html>
