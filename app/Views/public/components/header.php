<!-- Site Header Component UI 2.0 -->
<?php $sectionSettings = $settings ?? []; ?>
<header class="site-header">
    <div class="container header-inner">
        <a href="<?= site_url('/') ?>" class="site-logo">
            <div class="site-logo-icon">🕌</div>
            <span class="site-logo-text"><?= esc($masjid['name'] ?? 'MasjidCMS') ?></span>
        </a>

        <nav class="main-nav">
            <a href="<?= site_url('/') ?>" class="nav-link <?= ($activePage ?? '') === 'home' ? 'active' : '' ?>"><?= esc($sectionSettings['nav_home_label'] ?? 'Beranda') ?></a>
            <a href="<?= site_url('profil') ?>" class="nav-link <?= ($activePage ?? '') === 'profil' ? 'active' : '' ?>"><?= esc($sectionSettings['nav_profile_label'] ?? 'Profil') ?></a>
            <a href="<?= site_url('berita') ?>" class="nav-link <?= ($activePage ?? '') === 'berita' ? 'active' : '' ?>"><?= esc($sectionSettings['nav_news_label'] ?? 'Berita') ?></a>
            <a href="<?= site_url('program') ?>" class="nav-link <?= ($activePage ?? '') === 'program' ? 'active' : '' ?>"><?= esc($sectionSettings['nav_program_label'] ?? 'Program') ?></a>
            <a href="<?= site_url('layanan') ?>" class="nav-link <?= ($activePage ?? '') === 'layanan' ? 'active' : '' ?>"><?= esc($sectionSettings['nav_service_label'] ?? 'Layanan') ?></a>
            <a href="<?= site_url('transparansi') ?>" class="nav-link <?= ($activePage ?? '') === 'transparansi' ? 'active' : '' ?>"><?= esc($sectionSettings['nav_finance_label'] ?? 'Keuangan') ?></a>
            <a href="<?= site_url('kontak') ?>" class="nav-link <?= ($activePage ?? '') === 'kontak' ? 'active' : '' ?>"><?= esc($sectionSettings['nav_contact_label'] ?? 'Kontak') ?></a>
        </nav>

        <div class="header-cta">
            <a href="<?= site_url('donasi') ?>" class="btn-ui2 btn-amber-ui2">
                <span>💚</span> <?= esc($sectionSettings['header_cta_text'] ?? 'Infaq & Donasi') ?>
            </a>
        </div>
    </div>
</header>
