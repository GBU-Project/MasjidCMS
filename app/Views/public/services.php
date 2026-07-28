<?= $this->extend('layouts/public') ?>

<?= $this->section('title') ?>Katalog Layanan Masjid<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div style="background: linear-gradient(135deg, var(--primary-800), var(--primary-900)); color: white; padding: 48px 24px; text-align: center;">
    <div style="max-width: 1000px; margin: 0 auto;">
        <h1 style="font-size: 32px; font-weight: 800; margin-bottom: 12px;">🤝 Layanan Kemasyarakatan Masjid</h1>
        <p style="font-size: 16px; opacity: 0.9;">Fasilitas dan pelayanan sosial keumatan gratis & terjangkau untuk jamaah.</p>
    </div>
</div>

<div style="max-width: 1100px; margin: 40px auto; padding: 0 24px;">
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 24px;">
        <?php foreach ($services as $s): ?>
            <div class="panel-card" style="padding: 28px; border-radius: 12px; transition: transform 0.2s;">
                <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 16px;">
                    <div style="font-size: 36px; padding: 12px; background-color: var(--primary-50); border-radius: 12px;"><?= esc($s['icon'] ?? '🤝') ?></div>
                    <div>
                        <h3 style="font-size: 18px; font-weight: 700; color: var(--text-primary); margin-bottom: 4px;"><?= esc($s['nama']) ?></h3>
                        <span class="badge badge-green" style="font-size: 11px;">Aktif Melayani</span>
                    </div>
                </div>
                <p style="font-size: 14px; color: var(--text-secondary); line-height: 1.6; margin-bottom: 16px;"><?= esc($s['deskripsi']) ?></p>
                <div style="background-color: var(--bg-surface); padding: 12px 16px; border-radius: 8px; font-size: 13px; margin-bottom: 16px;">
                    <div style="margin-bottom: 4px;"><strong>⏰ Jam Layanan:</strong> <?= esc($s['jam_layanan'] ?? '24 Jam') ?></div>
                    <div style="margin-bottom: 4px;"><strong>📍 Lokasi:</strong> <?= esc($s['lokasi'] ?? 'Area Masjid') ?></div>
                    <?php if (!empty($s['persyaratan'])): ?>
                        <div><strong>📋 Syarat:</strong> <?= esc($s['persyaratan']) ?></div>
                    <?php endif; ?>
                </div>
                <?php if (!empty($s['kontak'])): ?>
                    <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $s['kontak']) ?>" target="_blank" class="btn btn-primary" style="width: 100%; text-align: center; display: block; text-decoration: none;">
                        💬 Hubungi Layanan (<?= esc($s['kontak']) ?>)
                    </a>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?= $this->endSection() ?>
