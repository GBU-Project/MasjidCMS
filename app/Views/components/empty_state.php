<?php
$icon = $icon ?? '📭';
$title = $title ?? 'Belum Ada Data Terdaftar';
$description = $description ?? 'Silakan tambahkan data baru menggunakan tombol di bawah ini.';
$actionLabel = $actionLabel ?? '+ Tambah Data Baru';
$actionUrl = $actionUrl ?? '#';
?>
<div style="padding: 36px 16px; text-align: center;">
    <div style="font-size: 48px; margin-bottom: 12px;"><?= $icon ?></div>
    <h3 style="font-size: 16px; font-weight: 600; color: var(--text-main); margin-bottom: 4px;"><?= esc($title) ?></h3>
    <p style="font-size: 13px; color: var(--text-subtle); margin-bottom: 16px; max-width: 360px; margin-left: auto; margin-right: auto;"><?= esc($description) ?></p>
    <a href="<?= esc($actionUrl) ?>" class="btn btn-primary"><?= esc($actionLabel) ?></a>
</div>
