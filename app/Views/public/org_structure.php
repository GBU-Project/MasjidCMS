<?= $this->extend('layouts/public') ?>

<?php helper('icon'); ?>
<?= $this->section('title') ?>Struktur Organisasi & Pengurus Masjid<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div style="background: linear-gradient(135deg, var(--primary-800), var(--primary-900)); color: white; padding: 48px 24px; text-align: center;">
    <div style="max-width: 1000px; margin: 0 auto;">
        <h1 style="font-size: 32px; font-weight: 800; margin-bottom: 12px;">Struktur Organisasi DKM</h1>
        <p style="font-size: 16px; opacity: 0.9;">Pengurus & Bidang Operasional Pelayanan MasjidCMS</p>
    </div>
</div>

<div style="max-width: 1100px; margin: 40px auto; padding: 0 24px;">
    <!-- Bidang Departemen Grid -->
    <h2 id="bidang" style="font-size: 22px; font-weight: 700; margin-bottom: 20px; text-align: center; scroll-margin-top: 100px;">🏛️ Bidang & Departemen Kerja</h2>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; margin-bottom: 48px;">
        <?php foreach ($bidangList as $b): ?>
            <div class="panel-card" style="padding: 20px; border-radius: 12px; border-left: 4px solid var(--primary-600);">
                <div style="font-size: 28px; margin-bottom: 8px;"><?= render_icon($b['icon'] ?? null, '🏛️') ?></div>
                <h3 style="font-size: 16px; font-weight: 700; margin-bottom: 6px; color: var(--text-primary);"><?= esc($b['name']) ?></h3>
                <p style="font-size: 13px; color: var(--text-secondary); line-height: 1.5;"><?= esc($b['description'] ?? 'Bidang operasional masjid.') ?></p>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Pengurus Card Grid -->
    <h2 id="struktur-dkm" style="font-size: 22px; font-weight: 700; margin-bottom: 20px; text-align: center; scroll-margin-top: 100px;">👔 Susunan Pengurus DKM</h2>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 24px;">
        <?php foreach ($pengurusList as $p): ?>
            <div class="panel-card" style="padding: 24px; border-radius: 12px; text-align: center; box-shadow: var(--shadow-sm);">
                <div style="width: 80px; height: 80px; border-radius: 50%; background-color: var(--primary-100); color: var(--primary-700); font-size: 28px; font-weight: 700; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
                    <?= esc(substr($p['nama'], 0, 2)) ?>
                </div>
                <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 4px; color: var(--text-primary);"><?= esc($p['nama']) ?></h3>
                <span class="badge badge-blue" style="font-size: 12px; margin-bottom: 12px; display: inline-block;"><?= esc($p['jabatan']) ?></span>
                <p style="font-size: 13px; color: var(--text-tertiary); margin-bottom: 8px;"><strong>Departemen:</strong> <?= esc($p['bidang_name'] ?? 'Umum') ?></p>
                <?php if (!empty($p['telepon'])): ?>
                    <p style="font-size: 13px; color: var(--text-secondary); margin-bottom: 4px;">📞 <?= esc($p['telepon']) ?></p>
                <?php endif; ?>
                <?php if (!empty($p['bio'])): ?>
                    <p style="font-size: 13px; color: var(--text-muted); font-style: italic; margin-top: 12px; border-top: 1px solid var(--border-light); padding-top: 12px;">"<?= esc($p['bio']) ?>"</p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?= $this->endSection() ?>
