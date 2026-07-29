<?= $this->extend('layouts/public') ?>

<?= $this->section('title') ?>Profil & Visi Misi Masjid<?= $this->endSection() ?>

<?php
$historyText = trim($masjid['history_text'] ?? '') ?: 'Sejarah masjid belum diisi oleh pengurus. Silakan lengkapi melalui menu Profil Masjid di dashboard admin.';
$visionText  = trim($masjid['vision_text'] ?? '') ?: 'Visi masjid belum diisi oleh pengurus.';
$missionText = trim($masjid['mission_text'] ?? '');
$missionPoints = $missionText !== '' ? array_values(array_filter(array_map('trim', explode("\n", $missionText)))) : [];
?>

<?= $this->section('content') ?>
<div class="portal-container" style="max-width: 800px;">
    <h1 style="font-size: 28px; font-weight: 800; margin-bottom: 16px;">Profil & Sejarah Masjid</h1>
    <div style="background: white; border: 1px solid var(--border-light); border-radius: var(--radius-lg); padding: 32px; margin-bottom: 32px;">
        <h2 style="font-size: 20px; font-weight: 700; color: var(--primary-700); margin-bottom: 12px;">Sejarah Singkat</h2>
        <p style="margin-bottom: 16px; white-space: pre-line;"><?= esc($historyText) ?></p>

        <h2 style="font-size: 20px; font-weight: 700; color: var(--primary-700); margin-top: 24px; margin-bottom: 12px;">Visi & Misi</h2>
        <p style="font-weight: 600;">Visi:</p>
        <p style="margin-bottom: 12px; white-space: pre-line;"><?= esc($visionText) ?></p>

        <?php if (!empty($missionPoints)): ?>
            <p style="font-weight: 600;">Misi:</p>
            <ul style="margin-left: 20px; margin-bottom: 16px;">
                <?php foreach ($missionPoints as $point): ?>
                    <li><?= esc($point) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p style="font-weight: 600;">Misi:</p>
            <p style="margin-bottom: 16px; color: var(--text-muted);">Misi masjid belum diisi oleh pengurus.</p>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>
