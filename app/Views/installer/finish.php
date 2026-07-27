<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Step 6: Installation Finished — MasjidCMS</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/admin-dashboard.css') ?>">
</head>
<body style="display: flex; align-items: center; justify-content: center; min-height: 100vh; background: var(--bg-app); padding: 24px;">
    <div class="panel-card" style="max-width: 580px; width: 100%; text-align: center; padding: 40px;">
        <div style="font-size: 64px; margin-bottom: 12px;">🎉</div>
        <h1 style="font-size: 24px; font-weight: 800; color: var(--primary-700); margin-bottom: 8px;">Instalasi Berhasil Dikeluarkan!</h1>
        <p style="font-size: 14px; color: var(--text-muted); margin-bottom: 24px;">MasjidCMS v1.0.0-rc1 telah terinstal sempurna. Berkas pengunci <code>installed.lock</code> telah dibuat untuk mengamankan wizard ini.</p>

        <div style="background: var(--surface-card); border: 1px solid var(--border-light); border-radius: 8px; padding: 16px; text-align: left; font-size: 13px; margin-bottom: 32px;">
            <p><strong>Login URL:</strong> <code>/admin/dashboard</code></p>
            <p><strong>Username:</strong> <code><?= esc($username ?? 'superadmin') ?></code></p>
            <p style="color: var(--status-warning-text); margin-top: 8px;">* Harap simpan kredensial Anda dan ganti password secara berkala.</p>
        </div>

        <a href="<?= site_url('admin/dashboard') ?>" class="btn btn-primary" style="padding: 12px 32px; font-size: 15px; width: 100%; justify-content: center;">Buka Admin Dashboard Sekarang ›</a>
    </div>
</body>
</html>
