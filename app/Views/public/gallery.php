<?= $this->extend('layouts/public') ?>

<?= $this->section('title') ?>Galeri Foto & Dokumentasi Kegiatan<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="portal-container" style="max-width: 1000px;">
    <h1 style="font-size: 28px; font-weight: 800; margin-bottom: 8px;">Galeri & Dokumentasi Kegiatan</h1>
    <p style="color: var(--text-muted); margin-bottom: 24px;">Dokumentasi foto kegiatan ibadah, kajian, dan sosial di Masjid.</p>

    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px;">
        <?php if (empty($gallery)): ?>
            <div style="grid-column: 1 / -1; background: white; border: 1px solid var(--border-light); border-radius: 12px; padding: 40px; text-align: center; color: var(--text-muted);">
                Belum ada foto galeri kegiatan yang dipublikasikan.
            </div>
        <?php else: ?>
            <?php foreach ($gallery as $item): ?>
                <div style="background: white; border: 1px solid var(--border-light); border-radius: 12px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.04);">
                    <div style="height: 180px; background: #e2e8f0; display: flex; align-items: center; justify-content: center; font-size: 32px;">
                        🖼️
                    </div>
                    <div style="padding: 16px;">
                        <p style="font-weight: 600; font-size: 14px; margin: 0; color: var(--text-main);"><?= esc($item['caption'] ?? 'Foto Kegiatan Masjid') ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>
