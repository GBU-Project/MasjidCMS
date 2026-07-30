<?= $this->extend('layouts/admin') ?>

<?= $this->section('title') ?>Sunting Menu Navigasi<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="breadcrumb-container">
    <span>Admin</span>
    <span>/</span>
    <span>Menu Manager</span>
    <span>/</span>
    <span class="breadcrumb-active">Sunting Menu</span>
</div>

<div class="content-header-title">
    <div>
        <h1>Sunting Item Menu Navigasi</h1>
        <p style="font-size: 14px; color: var(--text-muted);">Perbarui judul, target link, dan urutan tampil menu navigasi portal.</p>
    </div>
    <div>
        <a href="<?= site_url('admin/menu?tab=menu') ?>" class="btn btn-secondary">← Batal & Kembali</a>
    </div>
</div>

<?php if (session()->getFlashdata('error')): ?>
    <div style="background: #fef2f2; border: 1px solid #fca5a5; color: #991b1b; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-weight: 500;">
        ⚠️ <?= session()->getFlashdata('error') ?>
    </div>
<?php endif; ?>

<div class="panel-card" style="max-width: 600px; margin-left: 0; padding: 24px;">
    <form action="<?= site_url('admin/menu/update') ?>" method="POST">
        <?= csrf_field() ?>
        <input type="hidden" name="id" value="<?= esc($id) ?>">

        <div style="margin-bottom: 16px;">
            <label style="display: block; font-weight: 600; margin-bottom: 6px;">Nama Menu *</label>
            <input type="text" name="title" required value="<?= esc(old('title', $item['title'] ?? '')) ?>" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;">
        </div>

        <div style="margin-bottom: 16px;">
            <label style="display: block; font-weight: 600; margin-bottom: 6px;">URL / Target Link *</label>
            <input type="text" name="url" required value="<?= esc(old('url', $item['url'] ?? '')) ?>" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;">
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; font-weight: 600; margin-bottom: 6px;">Urutan (Sort Order)</label>
            <input type="number" name="sort_order" value="<?= esc(old('sort_order', $item['menu_order'] ?? 1)) ?>" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;">
        </div>

        <button type="submit" class="btn btn-primary">💾 Simpan Perubahan Menu</button>
    </form>
</div>
<?= $this->endSection() ?>
