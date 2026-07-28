<?= $this->extend('layouts/admin') ?>

<?= $this->section('title') ?>Master Data Workspace<?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- Breadcrumb -->
<div class="breadcrumb-container">
    <span>Admin</span>
    <span>/</span>
    <span>Master Data</span>
    <span>/</span>
    <span class="breadcrumb-active"><?= esc($activeModuleLabel ?? 'Data Jamaah') ?></span>
</div>

<!-- Header -->
<div class="content-header-title">
    <div>
        <h1>Unified Master Data Workspace</h1>
        <p style="font-size: 14px; color: var(--text-muted);">Pengelolaan data institusi, jamaah, keluarga, akun user, dan otorisasi role platform.</p>
    </div>
</div>

<!-- Master Module Tab Navigation Bar -->
<div style="border-bottom: 1px solid var(--border-light); margin-bottom: 20px; display: flex; gap: 8px; flex-wrap: wrap;">
    <a href="<?= site_url('admin/master?tab=profil') ?>" class="nav-item-link <?= ($activeTab === 'profil' || $activeTab === 'masjid') ? 'active' : '' ?>">🕌 Profil Masjid</a>
    <a href="<?= site_url('admin/master?tab=bidang') ?>" class="nav-item-link <?= ($activeTab === 'bidang') ? 'active' : '' ?>">🏛️ Data Bidang</a>
    <a href="<?= site_url('admin/master?tab=pengurus') ?>" class="nav-item-link <?= ($activeTab === 'pengurus') ? 'active' : '' ?>">👔 Pengurus Masjid</a>
    <a href="<?= site_url('admin/master?tab=jamaah') ?>" class="nav-item-link <?= ($activeTab === 'jamaah') ? 'active' : '' ?>">👥 Data Jamaah</a>
    <a href="<?= site_url('admin/master?tab=family') ?>" class="nav-item-link <?= ($activeTab === 'family') ? 'active' : '' ?>">👨‍👩‍👧 Data Keluarga</a>
    <a href="<?= site_url('admin/master?tab=user') ?>" class="nav-item-link <?= ($activeTab === 'user') ? 'active' : '' ?>">👤 User Accounts</a>
    <a href="<?= site_url('admin/master?tab=role') ?>" class="nav-item-link <?= ($activeTab === 'role') ? 'active' : '' ?>">🔑 Role Access</a>
    <a href="<?= site_url('admin/master?tab=permission') ?>" class="nav-item-link <?= ($activeTab === 'permission') ? 'active' : '' ?>">🛡️ Permissions</a>
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

<!-- Reusable Toolbar -->
<?= view('components/toolbar', [
    'createUrl' => site_url('admin/master/create?tab=' . $activeTab),
    'createLabel' => '+ Tambah ' . ($activeModuleLabel ?? 'Data')
]) ?>

<!-- Reusable Search & Filter Bar -->
<?= view('components/search_filter', [
    'placeholder' => 'Cari ' . ($activeModuleLabel ?? 'Data') . ' berdasarkan nama, nomor, atau kode...'
]) ?>

<!-- Reusable Data Table Component -->
<?= view('components/table', [
    'headers' => $headers,
    'rows'    => $rows
]) ?>

<!-- Reusable Pagination Component -->
<?= view('components/pagination') ?>

<?= $this->endSection() ?>
