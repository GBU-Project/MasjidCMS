<?= $this->extend('layouts/public') ?>

<?= $this->section('title') ?>Berita & Jadwal Kajian<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="portal-container">
    <h1 style="font-size: 28px; font-weight: 800; margin-bottom: 24px;">Berita Warta Masjid & Jadwal Kajian Syariah</h1>

    <!-- Schedule Section: Kajian Syariah -->
    <div style="margin-bottom: 32px;">
        <h2 id="kajian" style="font-size: 20px; font-weight: 700; margin-bottom: 16px; color: var(--primary-800); scroll-margin-top: 100px;">🕌 Jadwal Kajian Rutin & Tematik</h2>
        <div class="card-grid">
            <?php if (empty($kajianList)): ?>
                <div class="portal-card" style="grid-column: 1 / -1; text-align: center; color: var(--text-muted);">
                    Belum ada agenda jadwal kajian mendatang yang terdaftar.
                </div>
            <?php else: ?>
                <?php foreach ($kajianList as $k): ?>
                    <div class="portal-card">
                        <span style="font-size: 12px; font-weight: 600; color: var(--primary-600); text-transform: uppercase;">KAJIAN SYARIAH</span>
                        <h3 style="font-size: 18px; margin: 8px 0;"><?= esc($k['topic']) ?></h3>
                        <p style="font-size: 14px; color: var(--text-muted);">Penceramah: <strong><?= esc($k['speaker_name']) ?></strong></p>
                        <div style="font-size: 12px; color: var(--text-subtle); margin-top: 12px;">
                            📅 <?= esc($k['schedule_date']) ?> (<?= esc(substr($k['schedule_time'], 0, 5)) ?> WIB) &bull; 📍 <?= esc($k['location']) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- News & Articles Section -->
    <div>
        <h2 style="font-size: 20px; font-weight: 700; margin-bottom: 16px; color: var(--primary-800);">📰 Berita & Pengumuman Terbaru</h2>
        <div class="card-grid">
            <?php if (empty($posts)): ?>
                <div class="portal-card" style="grid-column: 1 / -1; text-align: center; color: var(--text-muted);">
                    Belum ada berita atau warta publikasi yang diterbitkan.
                </div>
            <?php else: ?>
                <?php foreach ($posts as $p): ?>
                    <a href="<?= site_url('berita/' . ($p['slug'] ?? '')) ?>" class="portal-card" style="text-decoration: none; color: inherit; display: block;">
                        <span style="font-size: 12px; font-weight: 600; color: var(--primary-600); text-transform: uppercase;">WARTA MASJID</span>
                        <h3 style="font-size: 18px; margin: 8px 0;"><?= esc($p['title']) ?></h3>
                        <p style="font-size: 14px; color: var(--text-muted);"><?= esc(substr($p['content'], 0, 140)) ?>...</p>
                        <div style="font-size: 12px; color: var(--text-subtle); margin-top: 12px;">
                            📅 <?= esc(substr($p['created_at'], 0, 10)) ?> &bull; Baca selengkapnya →
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
