<?= $this->extend('layouts/admin') ?>

<?= $this->section('title') ?><?= $activeTab === 'theme' ? 'Theme & Tampilan' : 'Pengaturan System & Security' ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php if ($activeTab === 'theme'): ?>
    <div class="workspace-header">
        <div>
            <div style="font-size: 12px; font-weight: 500; color: var(--text-tertiary); margin-bottom: 4px;">
                <a href="<?= site_url('admin/dashboard') ?>" style="color: var(--primary-600); text-decoration: none;">Dashboard</a> / Theme
            </div>
            <h1 class="page-title">Theme & Tampilan</h1>
            <p class="page-subtitle">Pilihan tema, warna, layout, dan tampilan portal. Untuk identitas masjid (logo, favicon, kontak, sosial media), lihat <a href="<?= site_url('admin/master?tab=profil') ?>">Website Settings</a>.</p>
        </div>
    </div>

    <div class="panel-card" style="max-width: 700px; padding: 24px;">
        <form action="<?= site_url('admin/theme/store') ?>" method="POST">
            <?= csrf_field() ?>
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">🎨 Warna Utama (Primary Color)</label>
                <input type="color" name="theme_color_primary" value="<?= esc($themeSettings['theme_color_primary'] ?? '#16a34a') ?>" style="width: 100px; height: 40px; border: 1px solid var(--border-light); border-radius: 6px; padding: 2px;">
            </div>
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">📐 Layout</label>
                <select name="theme_layout" style="width: 100%; max-width: 300px; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;">
                    <?php $currentLayout = $themeSettings['theme_layout'] ?? 'default'; ?>
                    <option value="default" <?= $currentLayout === 'default' ? 'selected' : '' ?>>Default</option>
                    <option value="compact" <?= $currentLayout === 'compact' ? 'selected' : '' ?>>Compact</option>
                    <option value="wide" <?= $currentLayout === 'wide' ? 'selected' : '' ?>>Wide</option>
                </select>
            </div>
            <div style="margin-bottom: 24px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">🌓 Mode Tampilan</label>
                <select name="theme_appearance_mode" style="width: 100%; max-width: 300px; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;">
                    <?php $currentMode = $themeSettings['theme_appearance_mode'] ?? 'light'; ?>
                    <option value="light" <?= $currentMode === 'light' ? 'selected' : '' ?>>Light</option>
                    <option value="dark" <?= $currentMode === 'dark' ? 'selected' : '' ?>>Dark</option>
                    <option value="auto" <?= $currentMode === 'auto' ? 'selected' : '' ?>>Auto (Mengikuti Sistem)</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">💾 Simpan Pengaturan Tema</button>
        </form>
    </div>
<?php else: ?>
<div class="workspace-header">
    <div>
        <div style="font-size: 12px; font-weight: 500; color: var(--text-tertiary); margin-bottom: 4px;">
            <a href="<?= site_url('admin/dashboard') ?>" style="color: var(--primary-600); text-decoration: none;">Dashboard</a> / System Workspace
        </div>
        <h1 class="page-title">System & Infrastructure Workspace</h1>
        <p class="page-subtitle">Kelola konfigurasi platform, menu navigasi, media asset, notifikasi, dan audit log.</p>
    </div>
</div>


<!-- Tab Navigation Bar -->
<div style="display: flex; border-bottom: 1px solid var(--border-light); margin-bottom: 20px; gap: 20px; flex-wrap: wrap;">
    <a href="<?= site_url('admin/settings?tab=settings') ?>" style="padding: 10px 0; text-decoration: none; font-size: 14px; font-weight: 600; color: <?= ($activeTab === 'settings') ? 'var(--primary-600)' : 'var(--text-secondary)' ?>; border-bottom: 2px solid <?= ($activeTab === 'settings') ? 'var(--primary-600)' : 'transparent' ?>;">⚙️ General Settings</a>
    <a href="<?= site_url('admin/menu?tab=menu') ?>" style="padding: 10px 0; text-decoration: none; font-size: 14px; font-weight: 600; color: <?= ($activeTab === 'menu') ? 'var(--primary-600)' : 'var(--text-secondary)' ?>; border-bottom: 2px solid <?= ($activeTab === 'menu') ? 'var(--primary-600)' : 'transparent' ?>;">🧭 Menu Manager</a>
    <a href="<?= site_url('admin/media?tab=media') ?>" style="padding: 10px 0; text-decoration: none; font-size: 14px; font-weight: 600; color: <?= ($activeTab === 'media') ? 'var(--primary-600)' : 'var(--text-secondary)' ?>; border-bottom: 2px solid <?= ($activeTab === 'media') ? 'var(--primary-600)' : 'transparent' ?>;">🖼️ Media Manager</a>
    <a href="<?= site_url('admin/notification?tab=notification') ?>" style="padding: 10px 0; text-decoration: none; font-size: 14px; font-weight: 600; color: <?= ($activeTab === 'notification') ? 'var(--primary-600)' : 'var(--text-secondary)' ?>; border-bottom: 2px solid <?= ($activeTab === 'notification') ? 'var(--primary-600)' : 'transparent' ?>;">🔔 Notifications</a>
    <a href="<?= site_url('admin/settings?tab=advanced') ?>" style="padding: 10px 0; text-decoration: none; font-size: 14px; font-weight: 600; color: <?= ($activeTab === 'advanced') ? 'var(--primary-600)' : 'var(--text-secondary)' ?>; border-bottom: 2px solid <?= ($activeTab === 'advanced') ? 'var(--primary-600)' : 'transparent' ?>;">🛠️ Advanced Configuration (Dev)</a>
    <a href="<?= site_url('admin/settings?tab=audit') ?>" style="padding: 10px 0; text-decoration: none; font-size: 14px; font-weight: 600; color: <?= ($activeTab === 'audit') ? 'var(--primary-600)' : 'var(--text-secondary)' ?>; border-bottom: 2px solid <?= ($activeTab === 'audit') ? 'var(--primary-600)' : 'transparent' ?>;">📜 Audit Log</a>
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

<?php if ($activeTab === 'settings'): ?>
    <!-- Homepage section visibility & ordering has moved to Homepage Manager (single source of truth). -->
    <div class="panel-card" style="padding: 16px 20px; margin-bottom: 24px; max-width: 900px; border-left: 4px solid var(--primary-600);">
        <p style="font-size: 13px; color: var(--text-muted); margin: 0;">
            🌐 Kontrol visibilitas & urutan section Homepage kini dikelola sepenuhnya di
            <a href="<?= site_url('homepage-manager') ?>">Homepage Manager &amp; Section Control</a>.
        </p>
    </div>

    <!-- Add / Update Setting Form -->
    <div class="panel-card" style="padding: 20px; margin-bottom: 24px; max-width: 600px;">
        <h4 style="font-size: 14px; font-weight: 700; margin-bottom: 12px;">+ Tambah / Ubah Konfigurasi Custom</h4>
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
                <input type="text" name="setting_group" value="general" style="width: 100%; padding: 6px 10px; border: 1px solid var(--border-light); border-radius: 6px; font-size: 13px;">
            </div>
            <div>
                <button type="submit" class="btn btn-primary" style="padding: 6px 14px; font-size: 13px;">💾 Simpan Pengaturan</button>
            </div>
        </form>
    </div>

<?php elseif ($activeTab === 'menu'): ?>
    <div class="panel-card" style="padding: 20px; margin-bottom: 24px; max-width: 600px;">
        <h4 style="font-size: 14px; font-weight: 700; margin-bottom: 12px;">+ Tambah Item Menu Navigasi</h4>
        <form action="<?= site_url('admin/menu/store') ?>" method="POST" style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
            <?= csrf_field() ?>
            <div>
                <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 4px;">Nama Menu *</label>
                <input type="text" name="title" required placeholder="Profil Masjid" style="width: 100%; padding: 6px 10px; border: 1px solid var(--border-light); border-radius: 6px;">
            </div>
            <div>
                <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 4px;">URL / Target Link *</label>
                <input type="text" name="url" required placeholder="/profile" style="width: 100%; padding: 6px 10px; border: 1px solid var(--border-light); border-radius: 6px;">
            </div>
            <div>
                <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 4px;">Urutan (Sort Order)</label>
                <input type="number" name="sort_order" value="1" style="width: 100%; padding: 6px 10px; border: 1px solid var(--border-light); border-radius: 6px;">
            </div>
            <div style="display: flex; align-items: flex-end;">
                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 6px;">💾 Simpan Menu</button>
            </div>
        </form>
    </div>

<?php elseif ($activeTab === 'media'): ?>
    <div class="panel-card" style="padding: 20px; margin-bottom: 24px; max-width: 600px;">
        <h4 style="font-size: 14px; font-weight: 700; margin-bottom: 12px;">+ Unggah Media Asset Baru</h4>
        <form action="<?= site_url('admin/media/store') ?>" method="POST" enctype="multipart/form-data" style="display: flex; flex-direction: column; gap: 12px;">
            <?= csrf_field() ?>
            <div>
                <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 4px;">Pilih File Gambar / Dokumen *</label>
                <input type="file" name="media_file" required style="width: 100%; padding: 6px 10px; border: 1px solid var(--border-light); border-radius: 6px;">
            </div>
            <div>
                <button type="submit" class="btn btn-primary" style="padding: 6px 14px;">📤 Upload Media</button>
            </div>
        </form>
    </div>

<?php elseif ($activeTab === 'notification'): ?>
    <div class="panel-card" style="padding: 20px; margin-bottom: 24px; max-width: 600px;">
        <h4 style="font-size: 14px; font-weight: 700; margin-bottom: 12px;">+ Kirim Notifikasi Baru</h4>
        <form action="<?= site_url('admin/notification/store') ?>" method="POST" style="display: flex; flex-direction: column; gap: 12px;">
            <?= csrf_field() ?>
            <div>
                <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 4px;">Judul Notifikasi *</label>
                <input type="text" name="title" required placeholder="Pengumuman Kajian..." style="width: 100%; padding: 6px 10px; border: 1px solid var(--border-light); border-radius: 6px;">
            </div>
            <div>
                <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 4px;">Pesan / Isi Notifikasi</label>
                <textarea name="message" rows="2" style="width: 100%; padding: 6px 10px; border: 1px solid var(--border-light); border-radius: 6px;"></textarea>
            </div>
            <div>
                <button type="submit" class="btn btn-primary" style="padding: 6px 14px;">🔔 Kirim Notifikasi</button>
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
                        Belum ada data di database.
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
<?php endif; ?>
<?= $this->endSection() ?>
