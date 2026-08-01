<!-- Unified Public Footer UI 2.0 (TASK-022A) -->
<footer style="background: var(--slate-900); color: var(--slate-400); border-top: 1px solid var(--slate-800); padding: 64px 0 32px; font-size: 14px;">
    <div class="container" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 40px; margin-bottom: 40px;">
        <div>
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
                <?php if (!empty($masjid['logo_url'])): ?>
                    <img src="<?= esc($masjid['logo_url']) ?>" alt="<?= esc($masjid['name'] ?? 'Logo Masjid') ?>" style="height: 40px; max-width: 140px; width: auto; object-fit: contain; border-radius: var(--radius-md);">
                <?php else: ?>
                    <div style="width: 40px; height: 40px; border-radius: var(--radius-md); background: var(--emerald-700); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 20px;">🕌</div>
                <?php endif; ?>
                <span style="font-size: 20px; font-weight: 800; color: #fff; font-family: var(--font-heading);"><?= esc($masjid['name'] ?? 'MasjidCMS') ?></span>
            </div>
            <p style="line-height: 1.6; margin-bottom: 16px;">
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
            <?php if ($hasSocial || !empty($masjid['whatsapp_number'])): ?>
                <div style="display: flex; gap: 10px;">
                    <?php foreach ($socialLinks as $field => $meta): ?>
                        <?php if (!empty($masjid[$field])): ?>
                            <a href="<?= esc($masjid[$field]) ?>" target="_blank" rel="noopener" title="<?= esc($meta['label']) ?>" style="display: inline-flex; align-items: center; justify-content: center; width: 34px; height: 34px; border-radius: var(--radius-full); background: var(--slate-800); text-decoration: none; font-size: 15px;"><?= $meta['icon'] ?></a>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <?php if (!empty($masjid['whatsapp_number'])): ?>
                        <a href="https://wa.me/<?= esc(preg_replace('/[^0-9]/', '', $masjid['whatsapp_number'])) ?>" target="_blank" rel="noopener" title="WhatsApp" style="display: inline-flex; align-items: center; justify-content: center; width: 34px; height: 34px; border-radius: var(--radius-full); background: var(--slate-800); text-decoration: none; font-size: 15px;">💬</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <div>
            <h4 style="color: #fff; font-size: 16px; margin-bottom: 16px; font-family: var(--font-heading);">Tautan Cepat</h4>
            <ul style="list-style: none; display: flex; flex-direction: column; gap: 10px; padding: 0; margin: 0;">
                <li><a href="<?= site_url('/') ?>" style="color: var(--slate-400); text-decoration: none;">Beranda</a></li>
                <li><a href="<?= site_url('profil') ?>" style="color: var(--slate-400); text-decoration: none;">Profil Masjid</a></li>
                <li><a href="<?= site_url('berita') ?>" style="color: var(--slate-400); text-decoration: none;">Berita</a></li>
                <li><a href="<?= site_url('agenda') ?>" style="color: var(--slate-400); text-decoration: none;">Agenda</a></li>
                <li><a href="<?= site_url('program') ?>" style="color: var(--slate-400); text-decoration: none;">Program</a></li>
                <li><a href="<?= site_url('galeri') ?>" style="color: var(--slate-400); text-decoration: none;">Galeri</a></li>
            </ul>
        </div>

        <div>
            <h4 style="color: #fff; font-size: 16px; margin-bottom: 16px; font-family: var(--font-heading);">Layanan</h4>
            <ul style="list-style: none; display: flex; flex-direction: column; gap: 10px; padding: 0; margin: 0;">
                <li><a href="<?= site_url('layanan') ?>" style="color: var(--slate-400); text-decoration: none;">Ambulans</a></li>
                <li><a href="<?= site_url('layanan') ?>" style="color: var(--slate-400); text-decoration: none;">Layanan Jenazah</a></li>
                <li><a href="<?= site_url('layanan') ?>" style="color: var(--slate-400); text-decoration: none;">Pernikahan</a></li>
                <li><a href="<?= site_url('layanan') ?>" style="color: var(--slate-400); text-decoration: none;">Mualaf</a></li>
                <li><a href="<?= site_url('layanan') ?>" style="color: var(--slate-400); text-decoration: none;">ZISWAF</a></li>
                <li><a href="<?= site_url('donasi') ?>" style="color: var(--slate-400); text-decoration: none;">Donasi Online</a></li>
            </ul>
        </div>

        <div>
            <h4 style="color: #fff; font-size: 16px; margin-bottom: 16px; font-family: var(--font-heading);">Kontak</h4>
            <ul style="list-style: none; display: flex; flex-direction: column; gap: 10px; padding: 0; margin: 0 0 16px;">
                <li>📍 <?= esc(trim(($masjid['address'] ?? '') . (!empty($masjid['city']) ? ', ' . $masjid['city'] : '')) ?: 'Alamat belum diisi') ?></li>
                <?php if (!empty($masjid['phone'])): ?><li>📞 <?= esc($masjid['phone']) ?></li><?php endif; ?>
                <?php if (!empty($masjid['email'])): ?><li>📧 <?= esc($masjid['email']) ?></li><?php endif; ?>
            </ul>
            <a href="<?= site_url('jadwal-shalat') ?>" style="display: inline-block; color: var(--emerald-500); text-decoration: none; font-weight: 700;">🕌 Lihat Jadwal Sholat →</a>
        </div>
    </div>

    <div class="container" style="border-top: 1px solid var(--slate-800); padding-top: 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; font-size: 12.5px;">
        <span>&copy; <?= date('Y') ?> <?= esc($masjid['name'] ?? 'MasjidCMS') ?>. All Rights Reserved.</span>
        <span>Powered by <strong style="color: #fff;">GBU-Project</strong></span>
    </div>
</footer>
