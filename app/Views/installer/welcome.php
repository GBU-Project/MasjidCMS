<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Web Installation Wizard — MasjidCMS</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/admin-dashboard.css') ?>">
</head>
<body style="display: flex; align-items: center; justify-content: center; min-height: 100vh; background: var(--bg-app); padding: 24px;">
    <div class="panel-card" style="max-width: 580px; width: 100%; text-align: center; padding: 40px;">
        <div style="font-size: 54px; margin-bottom: 12px;">🕌</div>
        <h1 style="font-size: 26px; font-weight: 800; color: var(--primary-700); margin-bottom: 8px;">Selamat Datang di MasjidCMS</h1>
        <p style="font-size: 14px; color: var(--text-muted); margin-bottom: 24px;">Zero-Configuration Installation Wizard (v1.0.0-rc1)</p>
        
        <div style="background: var(--primary-50); border: 1px solid var(--primary-100); border-radius: 8px; padding: 16px; text-align: left; font-size: 13px; color: var(--primary-900); margin-bottom: 32px;">
            <p style="font-weight: 600; margin-bottom: 4px;">Tahapan Instalasi Otomatis (6 Steps):</p>
            <ol style="margin-left: 20px; line-height: 1.6;">
                <li>Pemeriksaan Persyaratan Server & File Writable</li>
                <li>Konfigurasi & Verifikasi Koneksi Database</li>
                <li>Import DDL Schema & DML Seeders</li>
                <li>Generate Kunci Enkripsi & Penulisan File .env</li>
                <li>Pembuatan Akun Utama Super Admin</li>
            </ol>
        </div>

        <a href="<?= site_url('install/requirements') ?>" class="btn btn-primary" style="padding: 12px 32px; font-size: 15px; width: 100%; justify-content: center;">Mulai Instalasi Otomatis ›</a>
    </div>
</body>
</html>
