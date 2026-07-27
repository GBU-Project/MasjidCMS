<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Step 3: Database Setup — MasjidCMS</title>
    <link rel="stylesheet" href="/assets/css/admin-dashboard.css">
</head>
<body style="display: flex; align-items: center; justify-content: center; min-height: 100vh; background: var(--bg-app); padding: 24px;">
    <div class="panel-card" style="max-width: 600px; width: 100%; padding: 32px;">
        <div style="font-size: 13px; font-weight: 600; color: var(--primary-600); margin-bottom: 4px;">STEP 3 OF 6</div>
        <h2 style="font-size: 20px; font-weight: 700; margin-bottom: 20px;">Konfigurasi & Verifikasi Database</h2>

        <?php if (!empty($message)): ?>
            <div style="padding: 12px; border-radius: 6px; margin-bottom: 16px; font-size: 13px; background: <?= $success ? 'var(--status-success-bg)' : 'var(--status-danger-bg)' ?>; color: <?= $success ? 'var(--status-success-text)' : 'var(--status-danger-text)' ?>;">
                <?= esc($message) ?>
            </div>
        <?php endif; ?>

        <form action="/install/database" method="POST" style="display: flex; flex-direction: column; gap: 14px;">
            <div style="display: grid; grid-template-columns: 3fr 1fr; gap: 12px;">
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 500; margin-bottom: 4px;">Database Host</label>
                    <input type="text" name="db_host" value="<?= esc($dbHost ?? 'localhost') ?>" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;">
                </div>
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 500; margin-bottom: 4px;">Port</label>
                    <input type="number" name="db_port" value="<?= esc($dbPort ?? '3306') ?>" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;">
                </div>
            </div>

            <div>
                <label style="display: block; font-size: 13px; font-weight: 500; margin-bottom: 4px;">Nama Database</label>
                <input type="text" name="db_name" value="<?= esc($dbName ?? 'masjidcms_db') ?>" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;">
            </div>

            <div>
                <label style="display: block; font-size: 13px; font-weight: 500; margin-bottom: 4px;">Database Username</label>
                <input type="text" name="db_user" value="<?= esc($dbUser ?? 'root') ?>" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;">
            </div>

            <div>
                <label style="display: block; font-size: 13px; font-weight: 500; margin-bottom: 4px;">Database Password</label>
                <input type="password" name="db_pass" value="<?= esc($dbPass ?? '') ?>" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;">
            </div>

            <div style="display: flex; justify-content: space-between; margin-top: 12px;">
                <a href="/install/requirements" class="btn btn-secondary">‹ Kembali</a>
                <div style="display: flex; gap: 8px;">
                    <button type="submit" name="action" value="test" class="btn btn-secondary">Test Connection</button>
                    <?php if (!empty($success) && $success): ?>
                        <a href="/install/application" class="btn btn-primary">Lanjut ke Step 4 (App Config) ›</a>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    </div>
</body>
</html>
