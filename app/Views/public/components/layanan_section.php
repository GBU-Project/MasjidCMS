<!-- Layanan Masjid Section UI 2.0 -->
<?php helper('icon'); $sectionSettings = $settings ?? []; ?>
<section class="section-wrapper" id="layanan" style="background: #fff;">
    <div class="container">
        <div class="section-header-center">
            <span class="section-tag"><?= esc($sectionSettings['layanan_tag'] ?? 'PELAYANAN JAMAAH') ?></span>
            <h2 class="section-title"><?= esc($sectionSettings['layanan_title'] ?? 'Layanan Utama Masjid') ?></h2>
            <p class="section-subtitle"><?= esc($sectionSettings['layanan_subtitle'] ?? 'Kemudahan akses fasilitas dan pelayanan ibadah bagi seluruh jamaah dan warga sekitar masjid.') ?></p>
        </div>

        <?php if (!empty($layananList)): ?>
            <div class="card-grid-ui2 card-grid-services">
                <?php foreach (array_slice($layananList, 0, 4) as $l): ?>
                    <div class="card-ui2 card-service">
                        <div class="service-icon"><?= render_icon($l['icon'] ?? null, '🚑') ?></div>
                        <h3><?= esc($l['nama'] ?? 'Layanan Masjid') ?></h3>
                        <p><?= esc($l['deskripsi'] ?? 'Fasilitas & pelayanan jamaah.') ?></p>
                        <div class="service-meta">⏰ <?= esc($l['jam_layanan'] ?? '24 Jam') ?></div>

                        <?php if (!empty($l['kontak'])): ?>
                            <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $l['kontak']) ?>" target="_blank" class="btn-ui2 btn-primary-ui2 full-width-btn">
                                💬 Hubungi WhatsApp
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
