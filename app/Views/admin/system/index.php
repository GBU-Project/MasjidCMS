<?= $this->extend('layouts/admin') ?>

<?= $this->section('title') ?>Pengaturan System & Security<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="workspace-header">
    <div>
        <div style="font-size: 12px; font-weight: 500; color: var(--text-tertiary); margin-bottom: 4px;">
            <a href="<?= site_url('admin/dashboard') ?>" style="color: var(--primary-600); text-decoration: none;">Dashboard</a> / Pengaturan System
        </div>
        <h1 class="page-title">Pengaturan System & Audit Log</h1>
        <p class="page-subtitle">Kelola konfigurasi platform, variabel sistem, dan audit trail aktivitas pengguna.</p>
    </div>
</div>

<!-- Tab Navigation Bar -->
<div style="display: flex; border-bottom: 1px solid var(--border-light); margin-bottom: 20px; gap: 24px;">
    <a href="<?= site_url('admin/settings?tab=settings') ?>" style="padding: 12px 0; text-decoration: none; font-size: 14px; font-weight: 600; color: <?= ($activeTab === 'settings') ? 'var(--primary-600)' : 'var(--text-secondary)' ?>; border-bottom: 2px solid <?= ($activeTab === 'settings') ? 'var(--primary-600)' : 'transparent' ?>;">⚙️ General Settings</a>
    <a href="<?= site_url('admin/settings?tab=audit') ?>" style="padding: 12px 0; text-decoration: none; font-size: 14px; font-weight: 600; color: <?= ($activeTab === 'audit') ? 'var(--primary-600)' : 'var(--text-secondary)' ?>; border-bottom: 2px solid <?= ($activeTab === 'audit') ? 'var(--primary-600)' : 'transparent' ?>;">📜 Audit Activity Log</a>
</div>

<?php if ($activeTab === 'settings'): ?>
    <!-- Add / Update Setting Form -->
    <div class="panel-card" style="padding: 20px; margin-bottom: 24px; max-width: 600px;">
        <h4 style="font-size: 14px; font-weight: 700; margin-bottom: 12px;">+ Tambah / Ubah Pengaturan System</h4>
        <form action="<?= site_url('admin/settings/store') ?>" method="POST" style="display: flex; flex-direction: column; gap: 12px;">
            <?= csrf_field() ?>
            <div>
                <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 4px;">Setting Key *</label>
                <input type="text" name="setting_key" required placeholder="Contoh: site_title atau mosque_name" style="width: 100%; padding: 6px 10px; border: 1px solid var(--border-light); border-radius: 6px; font-size: 13px;">
            </div>
            <div>
                <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 4px;">Setting Value *</label>
                <input type="text" name="setting_value" required placeholder="Nilai konfigurasi..." style="width: 100%; padding: 6px 10px; border: 1px solid var(--border-light); border-radius: 6px; font-size: 13px;">
            </div>
            <div>
                <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 4px;">Grup Konfigurasi</label>
                <input type="text" name="setting_group" value="general" placeholder="general" style="width: 100%; padding: 6px 10px; border: 1px solid var(--border-light); border-radius: 6px; font-size: 13px;">
            </div>
            <div>
                <button type="submit" class="btn btn-primary" style="padding: 6px 14px; font-size: 13px;">💾 Simpan Pengaturan</button>
            </div>
        </form>
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
                        Belum ada data pengaturan di database.
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
