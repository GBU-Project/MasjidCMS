<!-- Hero Banner Section UI 2.0 -->
<?php $sectionSettings = $settings ?? []; ?>
<section class="hero-banner-section">
    <div class="hero-bg-overlay" style="background-image: url('<?= !empty($donationSettings['donation_bg_image']) ? esc($donationSettings['donation_bg_image']) : base_url('assets/images/hero-bg.jpg') ?>');"></div>
    <div class="container hero-grid">
        <div class="hero-copy">
            <div class="hero-pill">
                <span>🕌</span> <?= esc($sectionSettings['hero_badge'] ?? 'Portal Digital Masjid') ?> <?= esc($masjid['name'] ?? 'Masjid') ?>
            </div>

            <h1 class="hero-title">
                <?= esc($sectionSettings['hero_title'] ?? 'Pusat Ibadah, Dakwah & Pemberdayaan Umat') ?>
            </h1>

            <p class="hero-subtitle">
                <?= esc($sectionSettings['hero_subtitle'] ?? ($masjid['address'] ?? 'Mewujudkan kemakmuran masjid melalui pelayanan jamaah yang transparan, modern, dan berkemajuan.')) ?>
            </p>

            <div class="hero-cta-group">
                <a href="<?= site_url('program') ?>" class="btn-ui2 btn-primary-ui2">
                    <span>✨</span> <?= esc($sectionSettings['hero_primary_cta'] ?? 'Jelajahi Program DKM') ?>
                </a>
                <a href="<?= site_url('donasi') ?>" class="btn-ui2 btn-amber-ui2">
                    <span>💳</span> <?= esc($sectionSettings['hero_secondary_cta'] ?? 'Infaq & Zakat Online') ?>
                </a>
            </div>
        </div>

        <div class="prayer-hero-card">
            <div class="prayer-card-head">
                <div>
                    <span class="prayer-card-label">Jadwal Ibadah Hari Ini</span>
                    <h3>📍 <?= esc($masjid['city'] ?? 'Kota Masjid') ?></h3>
                </div>
                <div class="prayer-card-date">
                    <span><?= date('d M Y') ?></span>
                </div>
            </div>

            <div class="prayer-card-highlight">
                <span>Waktu Sholat Berikutnya</span>
                <h2>ASHR — 15:20 WIB</h2>
                <span class="prayer-card-note">-01:45:20 menuju Adzan</span>
            </div>

            <div class="prayer-time-grid">
                <div class="prayer-time-box">
                    <div>Subuh</div>
                    <div>04:38</div>
                </div>
                <div class="prayer-time-box">
                    <div>Dzuhur</div>
                    <div>12:05</div>
                </div>
                <div class="prayer-time-box active">
                    <div>Ashar</div>
                    <div>15:20</div>
                </div>
                <div class="prayer-time-box">
                    <div>Maghrib</div>
                    <div>18:02</div>
                </div>
                <div class="prayer-time-box">
                    <div>Isya</div>
                    <div>19:14</div>
                </div>
            </div>
        </div>
    </div>
</section>
