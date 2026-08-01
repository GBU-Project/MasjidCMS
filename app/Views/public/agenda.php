<?= $this->extend('layouts/public') ?>

<?= $this->section('title') ?>Agenda & Jadwal Kegiatan<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="portal-container">
    <h1 style="font-size: 28px; font-weight: 800; margin-bottom: 24px;">📅 Agenda & Jadwal Kegiatan</h1>
    <div class="card-grid">
        <?php if (empty($agendaList)): ?>
            <div class="portal-card" style="grid-column: 1 / -1; text-align: center; color: var(--text-muted);">
                Belum ada agenda kegiatan yang terdaftar.
            </div>
        <?php else: ?>
            <?php foreach ($agendaList as $a): ?>
                <div class="portal-card">
                    <span style="font-size: 12px; font-weight: 600; color: var(--primary-600); text-transform: uppercase;"><?= esc($a['status'] ?? 'AGENDA') ?></span>
                    <h3 style="font-size: 18px; margin: 8px 0;"><?= esc($a['title'] ?? $a['name'] ?? 'Kegiatan Masjid') ?></h3>
                    <?php if (!empty($a['description'])): ?>
                        <p style="font-size: 14px; color: var(--text-muted);"><?= esc(strip_tags($a['description'])) ?></p>
                    <?php endif; ?>
                    <div style="font-size: 12px; color: var(--text-subtle); margin-top: 12px;">
                        📅 <?= esc(substr($a['event_date'] ?? '', 0, 10)) ?>
                        <?php if (!empty($a['location'])): ?> &bull; 📍 <?= esc($a['location']) ?><?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>
