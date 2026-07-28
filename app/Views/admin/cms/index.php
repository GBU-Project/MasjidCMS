<?= $this->extend('layouts/admin') ?>

<?= $this->section('title') ?>CMS & Portal Management<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="workspace-header">
    <div>
        <div style="font-size: 12px; font-weight: 500; color: var(--text-tertiary); margin-bottom: 4px;">
            <a href="<?= site_url('admin/dashboard') ?>" style="color: var(--primary-600); text-decoration: none;">Dashboard</a> / CMS & Content Portal
        </div>
        <h1 class="page-title">CMS & Portal Management</h1>
        <p class="page-subtitle">Kelola konten berita, jadwal kajian rutin, halaman statis, dan galeri foto portal publik masjid.</p>
    </div>
    <div style="display: flex; gap: 8px;">
        <a href="<?= site_url('admin/cms/create?tab=' . esc($activeTab)) ?>" class="btn btn-primary">+ Tambah Konten Baru (<?= esc(strtoupper($activeTab)) ?>)</a>
    </div>
</div>

<!-- Tab Navigation Bar -->
<div style="display: flex; border-bottom: 1px solid var(--border-light); margin-bottom: 20px; gap: 24px;">
    <a href="<?= site_url('admin/cms?tab=posts') ?>" style="padding: 12px 0; text-decoration: none; font-size: 14px; font-weight: 600; color: <?= ($activeTab === 'posts') ? 'var(--primary-600)' : 'var(--text-secondary)' ?>; border-bottom: 2px solid <?= ($activeTab === 'posts') ? 'var(--primary-600)' : 'transparent' ?>;">📰 Berita & Artikel</a>
    <a href="<?= site_url('admin/cms?tab=kajian') ?>" style="padding: 12px 0; text-decoration: none; font-size: 14px; font-weight: 600; color: <?= ($activeTab === 'kajian') ? 'var(--primary-600)' : 'var(--text-secondary)' ?>; border-bottom: 2px solid <?= ($activeTab === 'kajian') ? 'var(--primary-600)' : 'transparent' ?>;">🕌 Jadwal Kajian</a>
    <a href="<?= site_url('admin/cms?tab=pages') ?>" style="padding: 12px 0; text-decoration: none; font-size: 14px; font-weight: 600; color: <?= ($activeTab === 'pages') ? 'var(--primary-600)' : 'var(--text-secondary)' ?>; border-bottom: 2px solid <?= ($activeTab === 'pages') ? 'var(--primary-600)' : 'transparent' ?>;">📄 Halaman Statis</a>
    <a href="<?= site_url('admin/cms?tab=gallery') ?>" style="padding: 12px 0; text-decoration: none; font-size: 14px; font-weight: 600; color: <?= ($activeTab === 'gallery') ? 'var(--primary-600)' : 'var(--text-secondary)' ?>; border-bottom: 2px solid <?= ($activeTab === 'gallery') ? 'var(--primary-600)' : 'transparent' ?>;">🖼️ Galeri Foto</a>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div style="background: #f0fdf4; border: 1px solid #86efac; color: #166534; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-weight: 500;">
        ✅ <?= session()->getFlashdata('success') ?>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div style="background: #fef2f2; border: 1px solid #fca5a5; color: #991b1b; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-weight: 500;">
        ⚠️ <?= session()->getFlashdata('error') ?>
    </div>
<?php endif; ?>

<!-- Module Table Panel -->
<div class="panel-card" style="padding: 24px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
        <h3 style="font-size: 16px; font-weight: 700; color: var(--text-primary);"><?= esc($activeModuleLabel) ?></h3>
        <span class="badge badge-green">PERSISTENT REAL DATABASE</span>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <?php foreach ($headers as $h): ?>
                    <th><?= esc($h) ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($rows)): ?>
                <tr>
                    <td colspan="<?= count($headers) ?>" style="text-align: center; padding: 24px; color: var(--text-tertiary);">
                        Belum ada data konten CMS / Kajian di database.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($rows as $r): ?>
                    <tr>
                        <?php foreach ($r['columns'] as $col): ?>
                            <td><?= $col ?></td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?= $this->endSection() ?>
