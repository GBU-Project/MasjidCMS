<?= $this->extend('layouts/public') ?>

<?= $this->section('title') ?>Program Unggulan Masjid<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="portal-container">
    <h1 style="font-size: 28px; font-weight: 800; margin-bottom: 24px;">Program Unggulan & Dakwah</h1>
    <div class="card-grid">
        <?php if (empty($programs)): ?>
            <div class="portal-card" style="grid-column: 1 / -1; text-align: center; color: var(--text-muted);">
                Belum ada program kegiatan yang terdaftar.
            </div>
        <?php else: ?>
            <?php foreach ($programs as $p): ?>
                <div class="portal-card">
                    <span style="font-size: 12px; font-weight: 600; color: var(--primary-600);"><?= esc($p['bidang_name'] ?? 'PROGRAM DKM') ?></span>
                    <h3 style="font-size: 18px; margin: 8px 0;"><?= esc($p['nama'] ?? 'Program Masjid') ?></h3>
                    <p style="font-size: 14px; color: var(--text-muted);"><?= esc($p['ringkasan'] ?? $p['deskripsi'] ?? 'Program keumatan DKM Masjid.') ?></p>
                    <?php if (!empty($p['target_dana'])): ?>
                        <?php
                            $target = (float) $p['target_dana'];
                            $terkumpul = (float) ($p['terkumpul_dana'] ?? 0);
                            $percent = $target > 0 ? min(100, round(($terkumpul / $target) * 100)) : 0;
                        ?>
                        <div class="progress-bar-bg"><div class="progress-bar-fill" style="width: <?= $percent ?>%;"></div></div>
                        <div style="display: flex; justify-content: space-between; font-size: 13px; font-weight: 600;">
                            <span>Terkumpul: Rp <?= esc(number_format($terkumpul, 0, ',', '.')) ?></span>
                            <span>Target: Rp <?= esc(number_format($target, 0, ',', '.')) ?></span>
                        </div>
                    <?php else: ?>
                        <div style="font-size: 12px; color: var(--text-subtle); margin-top: 8px;">
                            📍 <?= esc($p['lokasi'] ?? 'Area Masjid') ?> &bull; 👤 <?= esc($p['penanggung_jawab'] ?? 'Pengurus DKM') ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>
