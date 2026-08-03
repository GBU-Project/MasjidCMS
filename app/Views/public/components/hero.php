<!-- Hero Banner Section UI 2.0 with Dynamic Slider Support -->
<?php
    $sectionSettings = $settings ?? [];
    $slides = $heroSlides ?? [];
    $hasDynamicSlides = !empty($slides) && is_array($slides);
    $showPrayerWidget = (($sectionSettings['show_hero_prayer_widget'] ?? '1') === '1');
    $gridClass = $showPrayerWidget ? 'hero-grid' : 'hero-grid hero-grid-no-widget';
?>

<section class="hero-banner-section">
    <?php if ($hasDynamicSlides): ?>
        <!-- Dynamic Hero Slides Container -->
        <div class="hero-slides-wrapper" id="heroSlidesWrapper">
            <?php foreach ($slides as $idx => $slide): ?>
                <?php
                    $bgUrl = !empty($slide['bg_image_path']) ? base_url($slide['bg_image_path']) : (!empty($donationSettings['donation_bg_image']) ? esc($donationSettings['donation_bg_image']) : base_url('assets/images/hero-bg.svg'));
                    $opacity = isset($slide['overlay_opacity']) ? ((int)$slide['overlay_opacity'] / 100) : 0.4;
                    $align = $slide['text_alignment'] ?? 'left';
                    $alignStyle = $align === 'center' ? 'text-align: center; margin-left: auto; margin-right: auto;' : ($align === 'right' ? 'text-align: right; margin-left: auto;' : 'text-align: left;');
                ?>
                <div class="hero-slide-item <?= $idx === 0 ? 'active' : '' ?>" data-slide-index="<?= $idx ?>">
                    <div class="hero-bg-media-wrap">
                        <img src="<?= $bgUrl ?>" alt="" class="hero-bg-media" loading="<?= $idx === 0 ? 'eager' : 'lazy' ?>">
                        <div class="hero-bg-tint" style="opacity: <?= $opacity ?>;"></div>
                    </div>
                    <div class="container <?= $gridClass ?>">
                        <div class="hero-copy" style="<?= $alignStyle ?>">
                            <div class="hero-pill" style="<?= $align === 'center' ? 'margin: 0 auto 16px;' : ($align === 'right' ? 'margin-left: auto;' : '') ?>">
                                <span>🕌</span> <?= esc($sectionSettings['hero_badge'] ?? 'Portal Digital Masjid') ?> <?= esc($masjid['name'] ?? 'Masjid') ?>
                            </div>

                            <h1 class="hero-title">
                                <?= esc($slide['title']) ?>
                            </h1>

                            <?php if (!empty($slide['subtitle'])): ?>
                                <p class="hero-subtitle">
                                    <?= esc($slide['subtitle']) ?>
                                </p>
                            <?php endif; ?>

                            <div class="hero-cta-group" style="<?= $align === 'center' ? 'justify-content: center;' : ($align === 'right' ? 'justify-content: flex-end;' : '') ?>">
                                <?php if (!empty($slide['primary_btn_text'])): ?>
                                    <?php
                                        $pUrl = $slide['primary_btn_url'] ?? 'program';
                                        if (!str_starts_with($pUrl, 'http://') && !str_starts_with($pUrl, 'https://')) {
                                            $pUrl = site_url($pUrl);
                                        }
                                    ?>
                                    <a href="<?= esc($pUrl) ?>" <?= !empty($slide['primary_btn_new_tab']) ? 'target="_blank" rel="noopener"' : '' ?> class="hero-btn-primary">
                                        <span>✨</span> <?= esc($slide['primary_btn_text']) ?>
                                    </a>
                                <?php endif; ?>

                                <?php if (!empty($slide['secondary_btn_text'])): ?>
                                    <?php
                                        $sUrl = $slide['secondary_btn_url'] ?? 'donasi';
                                        if (!str_starts_with($sUrl, 'http://') && !str_starts_with($sUrl, 'https://')) {
                                            $sUrl = site_url($sUrl);
                                        }
                                    ?>
                                    <a href="<?= esc($sUrl) ?>" <?= !empty($slide['secondary_btn_new_tab']) ? 'target="_blank" rel="noopener"' : '' ?> class="hero-btn-secondary">
                                        <span>💳</span> <?= esc($slide['secondary_btn_text']) ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>

                        <?php if ($showPrayerWidget): ?>
                            <!-- Right Prayer Widget Card -->
                            <?php
                                $prayerLabels = ['imsak' => 'Imsak', 'fajr' => 'Subuh', 'sunrise' => 'Terbit', 'dhuhr' => 'Dzuhur', 'asr' => 'Ashar', 'maghrib' => 'Maghrib', 'isha' => 'Isya'];
                                $times = $prayerTimes ?? [];
                                $mainFive = ['fajr', 'dhuhr', 'asr', 'maghrib', 'isha'];

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
                            <a href="<?= site_url('jadwal-shalat') ?>" class="prayer-hero-card" style="text-decoration: none; color: inherit; display: block; cursor: pointer; position: relative; z-index: 10;">
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
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if (count($slides) > 1): ?>
            <!-- Slider Dots Pagination -->
            <div class="hero-slider-dots" style="position: absolute; bottom: 24px; left: 50%; transform: translateX(-50%); display: flex; gap: 8px; z-index: 20; background: rgba(0, 0, 0, 0.25); backdrop-filter: blur(8px); padding: 6px 14px; border-radius: 999px; border: 1px solid rgba(255, 255, 255, 0.15);">
                <?php foreach ($slides as $idx => $s): ?>
                    <button type="button" onclick="goToHeroSlide(<?= $idx ?>)" class="hero-dot <?= $idx === 0 ? 'active' : '' ?>" style="width: <?= $idx === 0 ? '24px' : '10px' ?>; height: 10px; border-radius: 5px; border: none; background: <?= $idx === 0 ? '#10b981' : 'rgba(255,255,255,0.6)' ?>; cursor: pointer; transition: all 0.3s;"></button>
                <?php endforeach; ?>
            </div>
            <script>
                let currentSlideIdx = 0;
                let totalHeroSlides = <?= count($slides) ?>;
                let slideTimer = null;

                function goToHeroSlide(idx) {
                    let items = document.querySelectorAll('.hero-slide-item');
                    let dots = document.querySelectorAll('.hero-dot');
                    if (!items.length) return;

                    items.forEach((item, i) => {
                        if (i === idx) {
                            item.style.position = 'absolute';
                            item.style.opacity = '1';
                            item.style.pointerEvents = 'auto';
                            item.classList.add('active');
                        } else {
                            item.style.position = 'absolute';
                            item.style.opacity = '0';
                            item.style.pointerEvents = 'none';
                            item.classList.remove('active');
                        }
                    });

                    dots.forEach((dot, i) => {
                        if (i === idx) {
                            dot.style.background = '#10b981';
                            dot.style.width = '24px';
                            dot.classList.add('active');
                        } else {
                            dot.style.background = 'rgba(255,255,255,0.6)';
                            dot.style.width = '10px';
                            dot.classList.remove('active');
                        }
                    });

                    currentSlideIdx = idx;
                }

                function autoNextHeroSlide() {
                    let nextIdx = (currentSlideIdx + 1) % totalHeroSlides;
                    goToHeroSlide(nextIdx);
                }

                if (totalHeroSlides > 1) {
                    slideTimer = setInterval(autoNextHeroSlide, 6000);
                    let wrapper = document.getElementById('heroSlidesWrapper');
                    if (wrapper) {
                        wrapper.addEventListener('mouseenter', () => clearInterval(slideTimer));
                        wrapper.addEventListener('mouseleave', () => slideTimer = setInterval(autoNextHeroSlide, 6000));
                    }
                }
            </script>
        <?php endif; ?>

    <?php else: ?>
        <!-- Static Default Hero Fallback -->
        <div class="hero-bg-media-wrap">
            <img src="<?= !empty($donationSettings['donation_bg_image']) ? esc($donationSettings['donation_bg_image']) : base_url('assets/images/hero-bg.svg') ?>" alt="" class="hero-bg-media" loading="eager">
            <div class="hero-bg-tint" style="opacity: 0.55;"></div>
        </div>
        <div class="container <?= $gridClass ?>">
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
                    <a href="<?= site_url('program') ?>" class="hero-btn-primary">
                        <span>✨</span> <?= esc($sectionSettings['hero_primary_cta'] ?? 'Jelajahi Program DKM') ?>
                    </a>
                    <a href="<?= site_url('donasi') ?>" class="hero-btn-secondary">
                        <span>💳</span> <?= esc($sectionSettings['hero_secondary_cta'] ?? 'Infaq & Zakat Online') ?>
                    </a>
                </div>
            </div>

            <?php if ($showPrayerWidget): ?>
                <?php
                    $prayerLabels = ['imsak' => 'Imsak', 'fajr' => 'Subuh', 'sunrise' => 'Terbit', 'dhuhr' => 'Dzuhur', 'asr' => 'Ashar', 'maghrib' => 'Maghrib', 'isha' => 'Isya'];
                    $times = $prayerTimes ?? [];
                    $mainFive = ['fajr', 'dhuhr', 'asr', 'maghrib', 'isha'];

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
            <?php endif; ?>
        </div>
    <?php endif; ?>
</section>
