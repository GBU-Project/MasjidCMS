<!-- Unified Public Footer UI 2.0 (TASK-022A) -->
<footer class="site-footer-v2">
    <div class="container footer-v2-grid">
        <div class="footer-v2-brand">
            <div class="footer-v2-logo">
                <?php if (!empty($masjid['logo_url'])): ?>
                    <img src="<?= esc($masjid['logo_url']) ?>" alt="<?= esc($masjid['name'] ?? 'Logo Masjid') ?>">
                <?php else: ?>
                    <span class="footer-v2-logo-fallback">🕌</span>
                <?php endif; ?>
                <span class="footer-v2-logo-text"><?= esc($masjid['name'] ?? 'MasjidCMS') ?></span>
            </div>
            <p class="footer-v2-desc">
                <?= esc($masjid['history_text'] ?? $masjid['address'] ?? 'Pusat ibadah dan pelayanan jamaah terpadu.') ?>
            </p>
            <?php
                $socialLinks = [
                    'facebook_url'  => ['icon' => '📘', 'label' => 'Facebook'],
                    'instagram_url' => ['icon' => '📷', 'label' => 'Instagram'],
                    'youtube_url'   => ['icon' => '📺', 'label' => 'YouTube'],
                ];
                $hasSocial = false;
                foreach ($socialLinks as $field => $meta) {
                    if (!empty($masjid[$field])) { $hasSocial = true; break; }
                }
            ?>
            <?php if ($hasSocial): ?>
                <div class="footer-v2-social">
                    <?php foreach ($socialLinks as $field => $meta): ?>
                        <?php if (!empty($masjid[$field])): ?>
                            <a href="<?= esc($masjid[$field]) ?>" target="_blank" rel="noopener" title="<?= esc($meta['label']) ?>"><?= $meta['icon'] ?></a>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <?php if (!empty($masjid['whatsapp_number'])): ?>
                        <a href="https://wa.me/<?= esc(preg_replace('/[^0-9]/', '', $masjid['whatsapp_number'])) ?>" target="_blank" rel="noopener" title="WhatsApp">💬</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="footer-v2-col">
            <h4>Tautan Cepat</h4>
            <ul>
                <li><a href="<?= site_url('/') ?>">Beranda</a></li>
                <li><a href="<?= site_url('profil') ?>">Profil Masjid</a></li>
                <li><a href="<?= site_url('berita') ?>">Berita</a></li>
                <li><a href="<?= site_url('agenda') ?>">Agenda</a></li>
                <li><a href="<?= site_url('program') ?>">Program</a></li>
                <li><a href="<?= site_url('galeri') ?>">Galeri</a></li>
            </ul>
        </div>

        <div class="footer-v2-col">
            <h4>Layanan</h4>
            <ul>
                <li><a href="<?= site_url('layanan') ?>">Ambulans</a></li>
                <li><a href="<?= site_url('layanan') ?>">Layanan Jenazah</a></li>
                <li><a href="<?= site_url('layanan') ?>">Pernikahan</a></li>
                <li><a href="<?= site_url('layanan') ?>">Mualaf</a></li>
                <li><a href="<?= site_url('layanan') ?>">ZISWAF</a></li>
                <li><a href="<?= site_url('donasi') ?>">Donasi Online</a></li>
            </ul>
        </div>

        <div class="footer-v2-col">
            <h4>Kontak</h4>
            <ul class="footer-v2-contact">
                <li>📍 <?= esc(trim(($masjid['address'] ?? '') . (!empty($masjid['city']) ? ', ' . $masjid['city'] : '')) ?: 'Alamat belum diisi') ?></li>
                <?php if (!empty($masjid['phone'])): ?><li>📞 <?= esc($masjid['phone']) ?></li><?php endif; ?>
                <?php if (!empty($masjid['email'])): ?><li>📧 <?= esc($masjid['email']) ?></li><?php endif; ?>
            </ul>
            <a href="<?= site_url('jadwal-shalat') ?>" class="footer-v2-prayer-shortcut">🕌 Lihat Jadwal Sholat →</a>
        </div>
    </div>

    <div class="container footer-v2-bottom">
        <span>&copy; <?= date('Y') ?> <?= esc($masjid['name'] ?? 'MasjidCMS') ?>. All Rights Reserved.</span>
        <span class="footer-v2-powered">Powered by <strong>MasjidCMS</strong></span>
    </div>
</footer>
