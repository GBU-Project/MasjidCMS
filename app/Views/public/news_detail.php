<?= $this->extend('layouts/public') ?>

<?= $this->section('title') ?><?= esc($post['title']) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="portal-container" style="max-width: 800px;">
    <div style="font-size: 12px; font-weight: 500; color: var(--text-tertiary); margin-bottom: 12px;">
        <a href="<?= site_url('/') ?>" style="color: var(--primary-600); text-decoration: none;">Beranda</a> /
        <a href="<?= site_url('berita') ?>" style="color: var(--primary-600); text-decoration: none;">Berita</a> /
        <?= esc($post['title']) ?>
    </div>

    <span style="font-size: 12px; font-weight: 700; color: var(--primary-600); text-transform: uppercase; letter-spacing: 0.5px;">Warta Masjid</span>
    <h1 style="font-size: 30px; font-weight: 800; margin: 8px 0 12px; line-height: 1.3;"><?= esc($post['title']) ?></h1>
    <div style="font-size: 13px; color: var(--text-muted); margin-bottom: 24px; padding-bottom: 20px; border-bottom: 1px solid var(--border-light);">
        📅 <?= esc(date('d F Y', strtotime($post['published_at'] ?? $post['created_at'] ?? 'now'))) ?>
        <?php if (!empty($masjid['name'])): ?> &bull; 🕌 <?= esc($masjid['name']) ?><?php endif; ?>
    </div>

    <div style="font-size: 16px; line-height: 1.8; color: var(--text-primary);">
        <?= nl2br(esc($post['content'])) ?>
    </div>

    <?php if (!empty($relatedPosts)): ?>
        <div style="margin-top: 48px; padding-top: 24px; border-top: 1px solid var(--border-light);">
            <h2 style="font-size: 18px; font-weight: 700; margin-bottom: 16px;">📰 Berita Lainnya</h2>
            <div style="display: grid; gap: 12px;">
                <?php foreach ($relatedPosts as $rp): ?>
                    <a href="<?= site_url('berita/' . $rp['slug']) ?>" style="display: block; padding: 14px 16px; border: 1px solid var(--border-light); border-radius: 8px; text-decoration: none; color: inherit;">
                        <strong style="font-size: 14px;"><?= esc($rp['title']) ?></strong>
                        <div style="font-size: 12px; color: var(--text-muted); margin-top: 4px;">📅 <?= esc(substr($rp['created_at'] ?? '', 0, 10)) ?></div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <div style="margin-top: 32px;">
        <a href="<?= site_url('berita') ?>" class="btn-ui2 btn-secondary-ui2" style="padding: 8px 18px; font-size: 13px;">← Kembali ke Semua Berita</a>
    </div>
</div>
<?= $this->endSection() ?>
