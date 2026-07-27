<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->renderSection('title') ?> — MasjidCMS Admin</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/app-theme.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/admin-dashboard.css') ?>">
</head>
<body>
    <!-- Top Header Nav Bar -->
    <header class="app-header">
        <div class="brand-container">
            <div class="brand-logo">🕌</div>
            <span class="brand-name">MasjidCMS</span>
        </div>

        <div class="search-bar-header">
            <input type="text" placeholder="Cari Jamaah, Transaksi, atau Dokumen... (Press '/' to search)" aria-label="Global Search">
        </div>

        <div class="user-nav-profile">
            <button class="btn btn-secondary" style="padding: 6px 12px; font-size: 12px;">🔔 Notifikasi</button>
            <div class="avatar-circle" title="Administrator DKM">AD</div>
        </div>
    </header>

    <!-- Collapsible Sidebar Nav Bar -->
    <aside class="app-sidebar">
        <ul class="nav-menu-list">
            <li style="padding: 8px 16px; font-size: 11px; font-weight: 700; color: var(--text-tertiary); text-transform: uppercase;">DASHBOARD</li>
            <li>
                <a href="<?= site_url('admin/dashboard') ?>" class="nav-item-link">
                    <span>📊</span>
                    <span class="nav-text">Dashboard Utama</span>
                </a>
            </li>

            <li style="padding: 12px 16px 4px; font-size: 11px; font-weight: 700; color: var(--text-tertiary); text-transform: uppercase;">MASTER DATA</li>
            <li>
                <a href="<?= site_url('admin/masjid') ?>" class="nav-item-link">
                    <span>🕌</span>
                    <span class="nav-text">Profil Masjid</span>
                </a>
            </li>
            <li>
                <a href="<?= site_url('admin/jamaah') ?>" class="nav-item-link">
                    <span>👥</span>
                    <span class="nav-text">Data Jamaah</span>
                </a>
            </li>
            <li>
                <a href="<?= site_url('admin/family') ?>" class="nav-item-link">
                    <span>👨‍👩‍👧</span>
                    <span class="nav-text">Data Keluarga</span>
                </a>
            </li>

            <li style="padding: 12px 16px 4px; font-size: 11px; font-weight: 700; color: var(--text-tertiary); text-transform: uppercase;">KEUANGAN</li>
            <li>
                <a href="<?= site_url('admin/financial') ?>" class="nav-item-link">
                    <span>💰</span>
                    <span class="nav-text">Keuangan & Kas</span>
                </a>
            </li>
            <li>
                <a href="<?= site_url('admin/financial/create') ?>" class="nav-item-link">
                    <span>➕</span>
                    <span class="nav-text">Input Transaksi</span>
                </a>
            </li>

            <li style="padding: 12px 16px 4px; font-size: 11px; font-weight: 700; color: var(--text-tertiary); text-transform: uppercase;">LAPORAN</li>
            <li>
                <a href="<?= site_url('admin/reporting') ?>" class="nav-item-link">
                    <span>📈</span>
                    <span class="nav-text">Laporan Keuangan</span>
                </a>
            </li>

            <li style="padding: 12px 16px 4px; font-size: 11px; font-weight: 700; color: var(--text-tertiary); text-transform: uppercase;">CMS & SYSTEM</li>
            <li>
                <a href="<?= site_url('admin/cms') ?>" class="nav-item-link">
                    <span>📰</span>
                    <span class="nav-text">CMS & Portal Berita</span>
                </a>
            </li>
            <li>
                <a href="<?= site_url('admin/users') ?>" class="nav-item-link">
                    <span>👤</span>
                    <span class="nav-text">Pengguna (Users)</span>
                </a>
            </li>
            <li>
                <a href="<?= site_url('admin/rbac') ?>" class="nav-item-link">
                    <span>🛡️</span>
                    <span class="nav-text">Role & Permission</span>
                </a>
            </li>
            <li>
                <a href="<?= site_url('admin/settings') ?>" class="nav-item-link">
                    <span>⚙️</span>
                    <span class="nav-text">Pengaturan System</span>
                </a>
            </li>
        </ul>
    </aside>

    <!-- Main Workspace Area -->
    <main class="app-main-content">
        <?= $this->renderSection('content') ?>
    </main>
</body>
</html>
