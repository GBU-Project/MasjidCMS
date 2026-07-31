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
                    <h3>📍 <?= esc($prayerCity ?? ($masjid['city'] ?? 'Kota Masjid')) ?></h3>
                </div>
                <div class="prayer-card-date">
                    <span><?= date('d M Y') ?></span>
                </div>
            </div>

            <?php
            $prayerTimes = $prayerTimes ?? [];
            $now = time();
            $nextPrayer = null;
            $nextPrayerTime = null;
            $prayerIcons = [
                'Subuh'   => '🌅',
                'Dzuhur'  => '☀️',
                'Ashar'   => '🌤️',
                'Maghrib' => '🌇',
                'Isya'    => '🌙',
            ];

            // Find next prayer
            foreach ($prayerTimes as $pt) {
                $ptTime = strtotime(substr($pt['prayer_time'] ?? '00:00:00', 0, 5));
                if ($ptTime === false) continue;
                // Adjust to today
                $ptToday = strtotime(date('Y-m-d') . ' ' . substr($pt['prayer_time'] ?? '00:00:00', 0, 5));
                if ($ptToday > $now) {
                    $nextPrayer = $pt;
                    $nextPrayerTime = $ptToday;
                    break;
                }
            }
            // If no next prayer found, first prayer tomorrow
            if (!$nextPrayer && !empty($prayerTimes)) {
                $nextPrayer = $prayerTimes[0];
                $nextPrayerTime = strtotime('+1 day ' . date('Y-m-d') . ' ' . substr($prayerTimes[0]['prayer_time'] ?? '00:00:00', 0, 5));
            }
            ?>

            <?php if ($nextPrayer): ?>
            <div class="prayer-card-highlight">
                <span>Waktu Sholat Berikutnya</span>
                <h2><?= esc($nextPrayer['prayer_name']) ?> — <?= esc(substr($nextPrayer['prayer_time'] ?? '00:00', 0, 5)) ?> WIB</h2>
                <?php if ($nextPrayerTime): ?>
                <span class="prayer-card-note">⏱ <?= gmdate('H:i:s', max(0, $nextPrayerTime - $now)) ?> menuju Adzan</span>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <div class="prayer-time-grid">
                <?php foreach ($prayerTimes as $pt):
                    $ptName = $pt['prayer_name'] ?? '';
                    $ptTime = substr($pt['prayer_time'] ?? '00:00:00', 0, 5);
                    $isActive = ($nextPrayer && $nextPrayer['prayer_name'] === $ptName);
                ?>
                <div class="prayer-time-box <?= $isActive ? 'active' : '' ?>">
                    <div><?= esc($ptName) ?></div>
                    <div><?= esc($ptTime) ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
