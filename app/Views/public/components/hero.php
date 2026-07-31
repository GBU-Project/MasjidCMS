<!-- Hero Banner Section UI 2.0 -->
<?php $sectionSettings = $settings ?? []; ?>
<section class="hero-banner-section">
    <div class="hero-bg-overlay" style="background-image: url('<?= !empty($donationSettings['donation_bg_image']) ? esc($donationSettings['donation_bg_image']) : base_url('assets/images/hero-bg.svg') ?>');"></div>
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

        <?php
            $prayerLabels = ['imsak' => 'Imsak', 'fajr' => 'Subuh', 'sunrise' => 'Terbit', 'dhuhr' => 'Dzuhur', 'asr' => 'Ashar', 'maghrib' => 'Maghrib', 'isha' => 'Isya'];
            $times = $prayerTimes ?? [];
            $mainFive = ['fajr', 'dhuhr', 'asr', 'maghrib', 'isha'];

            // Determine the next upcoming prayer
            $now = date('H:i');
            $nextKey = null;
            if (is_array($times)) {
                foreach ($mainFive as $key) {
                    if (!empty($times[$key]) && is_string($times[$key]) && $times[$key] > $now) {
                        $nextKey = $key;
                        break;
                    }
                }
            }
            $nextKey = $nextKey ?? $mainFive[0];
        ?>
        <a href="<?= site_url('jadwal-shalat') ?>" class="prayer-hero-card" style="text-decoration: none; color: inherit; display: block; cursor: pointer;">
            <div class="prayer-card-head">
                <div>
                    <span class="prayer-card-label">Jadwal Ibadah Hari Ini</span>
                    <h3>📍 <?= esc($prayerCity ?? ($masjid['city'] ?? 'Kota Masjid')) ?></h3>
                </div>
                <div class="prayer-card-date">
                    <span><?= date('d M Y') ?></span>
                </div>
            </div>

            <div class="prayer-card-highlight">
                <span>Waktu Sholat Berikutnya</span>
                <h2><?= esc(strtoupper($prayerLabels[$nextKey] ?? 'SUBUH')) ?> — <?= esc(is_array($times) && isset($times[$nextKey]) ? $times[$nextKey] : '--:--') ?> WIB</h2>
                <span class="prayer-card-note">Lihat jadwal lengkap &amp; bulanan →</span>
            </div>

            <div class="prayer-time-grid">
                <?php foreach ($mainFive as $key): ?>
                    <div class="prayer-time-box <?= $key === $nextKey ? 'active' : '' ?>">
                        <div><?= esc($prayerLabels[$key]) ?></div>
                        <div><?= esc(is_array($times) && isset($times[$key]) ? $times[$key] : '--:--') ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </a>
    </div>
</section>
