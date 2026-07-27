<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Step 5: Super Admin Account — MasjidCMS</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/admin-dashboard.css') ?>">
</head>
<body style="display: flex; align-items: center; justify-content: center; min-height: 100vh; background: var(--bg-app); padding: 24px;">
    <div class="panel-card" style="max-width: 600px; width: 100%; padding: 32px;">
        <div style="font-size: 13px; font-weight: 600; color: var(--primary-600); margin-bottom: 4px;">STEP 5 OF 6</div>
        <h2 style="font-size: 20px; font-weight: 700; margin-bottom: 20px;">Pembuatan Akun Utama Super Admin</h2>

        <?php if (!empty($message)): ?>
            <div style="padding: 12px; border-radius: 6px; margin-bottom: 16px; font-size: 13px; background: var(--status-danger-bg); color: var(--status-danger-text);">
                <?= esc($message) ?>
            </div>
        <?php endif; ?>

        <form action="<?= site_url('install/admin') ?>" method="POST" style="display: flex; flex-direction: column; gap: 14px;">
            <div>
                <label style="display: block; font-size: 13px; font-weight: 500; margin-bottom: 4px;">Nama Lengkap Pengurus</label>
                <input type="text" name="name" value="Super Administrator DKM" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;" required>
            </div>

            <div>
                <label style="display: block; font-size: 13px; font-weight: 500; margin-bottom: 4px;">Username</label>
                <input type="text" name="username" value="superadmin" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;" required>
            </div>

            <div>
                <label style="display: block; font-size: 13px; font-weight: 500; margin-bottom: 4px;">Email</label>
                <input type="email" name="email" value="admin@masjidcms.org" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;" required>
            </div>

            <div>
                <label style="display: block; font-size: 13px; font-weight: 500; margin-bottom: 4px;">Password (Min 8 Karakter)</label>
                <input type="password" name="password" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;" required>
            </div>

            <div>
                <label style="display: block; font-size: 13px; font-weight: 500; margin-bottom: 4px;">Konfirmasi Password</label>
                <input type="password" name="confirm_password" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;" required>
            </div>

            <div style="display: flex; justify-content: space-between; margin-top: 12px;">
                <a href="<?= site_url('install/application') ?>" class="btn btn-secondary">‹ Kembali</a>
                <button type="submit" class="btn btn-primary">Selesaikan & Buat Akun ›</button>
            </div>
        </form>
    </div>
</body>
</html>
