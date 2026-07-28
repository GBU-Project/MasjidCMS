<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->renderSection('title') ?> — Portal MasjidCMS</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/public-portal.css') ?>">
    <style>
        .dropdown-item { position: relative; display: inline-block; }
        .dropdown-item .dropdown-menu { display: none; position: absolute; top: 100%; left: 0; background: white; border: 1px solid var(--border-light, #e2e8f0); border-radius: 8px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); list-style: none; padding: 8px 0; min-width: 180px; z-index: 1000; }
        .dropdown-item:hover .dropdown-menu,
        .dropdown-item:focus-within .dropdown-menu { display: block !important; }
        .dropdown-menu li a:hover { background-color: var(--primary-50, #f0fdf4); color: var(--primary-700, #15803d) !important; }
    </style>
</head>
<body>
    <!-- Top Public Header Nav -->
    <header class="public-header">
        <div class="brand-title">
            <span>🕌</span>
            <span>MasjidCMS Portal</span>
        </div>

        <ul class="public-nav-links">
            <li><a href="<?= site_url('/') ?>" class="<?= ($activePage === 'home') ? 'active' : '' ?>">Beranda</a></li>
            <li class="dropdown-item">
                <a href="#" class="<?= ($activePage === 'profile' || $activePage === 'org_structure') ? 'active' : '' ?>">Tentang Kami ▾</a>
                <ul class="dropdown-menu">
                    <li><a href="<?= site_url('profil') ?>" style="padding: 8px 16px; display: block; color: var(--text-primary, #1e293b); text-decoration: none;">Profil Masjid</a></li>
                    <li><a href="<?= site_url('struktur-organisasi') ?>" style="padding: 8px 16px; display: block; color: var(--text-primary, #1e293b); text-decoration: none;">Struktur Organisasi</a></li>
                </ul>
            </li>
            <li><a href="<?= site_url('program') ?>" class="<?= ($activePage === 'programs') ? 'active' : '' ?>">Program</a></li>
            <li><a href="<?= site_url('layanan') ?>" class="<?= ($activePage === 'services') ? 'active' : '' ?>">Layanan</a></li>
            <li><a href="<?= site_url('berita') ?>" class="<?= ($activePage === 'news') ? 'active' : '' ?>">Berita & Kajian</a></li>
            <li><a href="<?= site_url('donasi') ?>" class="<?= ($activePage === 'donation') ? 'active' : '' ?>">Donasi Online</a></li>
            <li><a href="<?= site_url('kontak') ?>" class="<?= ($activePage === 'contact') ? 'active' : '' ?>">Kontak</a></li>
        </ul>

        <div>
            <a href="<?= site_url('admin/dashboard') ?>" class="btn-portal btn-portal-primary">Login Pengurus</a>
        </div>
    </header>

    <!-- Page Main Content Area -->
    <main>
        <?= $this->renderSection('content') ?>
    </main>

    <!-- Public Footer -->
    <footer class="public-footer">
        <div class="footer-grid">
            <div>
                <h3 style="color: white; font-size: 18px; margin-bottom: 12px;">Masjid Agung Darussalam</h3>
                <p>Jl. Masjid Raya No. 1, Pusat Kota. Pusat kegiatan ibadah, kajian syariah, serta pengelolaan dana ziswaf terpercaya berbasis MasjidCMS Platform.</p>
            </div>
            <div>
                <h4 style="color: white; margin-bottom: 12px;">Navigasi & Sitemap</h4>
                <p><a href="<?= site_url('profil') ?>">Profil & Sejarah</a></p>
                <p><a href="<?= site_url('struktur-organisasi') ?>">Struktur DKM & Pengurus</a></p>
                <p><a href="<?= site_url('program') ?>">Program & Kegiatan</a></p>
                <p><a href="<?= site_url('layanan') ?>">Katalog Layanan Masjid</a></p>
                <p><a href="<?= site_url('berita') ?>">Warta & Jadwal Kajian</a></p>
                <p><a href="<?= site_url('donasi') ?>">Donasi & Infaq Online</a></p>
            </div>
            <div>
                <h4 style="color: white; margin-bottom: 12px;">Hubungi Kami</h4>
                <p>📧 info@masjidcms.org</p>
                <p>📞 (021) 555-0199</p>
                <p>🕒 Buka Setiap Hari (24 Jam)</p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?= date('Y') ?> MasjidCMS. All Rights Reserved. Built with CodeIgniter 4 & DDD Architecture.</p>
        </div>
    </footer>
</body>
</html>
