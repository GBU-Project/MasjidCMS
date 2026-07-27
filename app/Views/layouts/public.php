<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->renderSection('title') ?> — Portal MasjidCMS</title>
    <link rel="stylesheet" href="/assets/css/public-portal.css">
</head>
<body>
    <!-- Top Public Header Nav -->
    <header class="public-header">
        <div class="brand-title">
            <span>🕌</span>
            <span>MasjidCMS Portal</span>
        </div>

        <ul class="public-nav-links">
            <li><a href="/" class="<?= ($activePage === 'home') ? 'active' : '' ?>">Beranda</a></li>
            <li><a href="/profil" class="<?= ($activePage === 'profile') ? 'active' : '' ?>">Profil Masjid</a></li>
            <li><a href="/berita" class="<?= ($activePage === 'news') ? 'active' : '' ?>">Berita & Kajian</a></li>
            <li><a href="/program" class="<?= ($activePage === 'programs') ? 'active' : '' ?>">Program</a></li>
            <li><a href="/donasi" class="<?= ($activePage === 'donation') ? 'active' : '' ?>">Donasi Online</a></li>
            <li><a href="/kontak" class="<?= ($activePage === 'contact') ? 'active' : '' ?>">Kontak</a></li>
        </ul>

        <div>
            <a href="/admin/dashboard" class="btn-portal btn-portal-primary">Login Pengurus</a>
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
                <h4 style="color: white; margin-bottom: 12px;">Navigasi Cepat</h4>
                <p><a href="/profil">Profil & Sejarah</a></p>
                <p><a href="/berita">Jadwal Kajian</a></p>
                <p><a href="/donasi">Donasi Infaq Online</a></p>
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
