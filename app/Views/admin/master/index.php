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
<div style="border-bottom: 1px solid var(--border-light); margin-bottom: 20px; display: flex; gap: 8px;">
    <a href="/admin/master?tab=masjid" class="nav-item-link <?= ($activeTab === 'masjid') ? 'active' : '' ?>">🕌 Profil Masjid</a>
    <a href="/admin/master?tab=jamaah" class="nav-item-link <?= ($activeTab === 'jamaah') ? 'active' : '' ?>">👥 Data Jamaah</a>
    <a href="/admin/master?tab=family" class="nav-item-link <?= ($activeTab === 'family') ? 'active' : '' ?>">👨‍👩‍👧 Data Keluarga</a>
    <a href="/admin/master?tab=user" class="nav-item-link <?= ($activeTab === 'user') ? 'active' : '' ?>">👤 User Accounts</a>
    <a href="/admin/master?tab=role" class="nav-item-link <?= ($activeTab === 'role') ? 'active' : '' ?>">🔑 Role Access</a>
    <a href="/admin/master?tab=permission" class="nav-item-link <?= ($activeTab === 'permission') ? 'active' : '' ?>">🛡️ Permissions</a>
</div>

<!-- Reusable Toolbar -->
<?= view('components/toolbar', [
    'createUrl' => '/admin/master/create?tab=' . esc($activeTab),
    'createLabel' => '+ Tambah ' . esc($activeModuleLabel ?? 'Data')
]) ?>

<!-- Reusable Search & Filter Bar -->
<?= view('components/search_filter', [
    'placeholder' => 'Cari ' . esc($activeModuleLabel) . ' berdasarkan nama, nomor, atau kode...'
]) ?>

<!-- Reusable Data Table Component -->
<?= view('components/table', [
    'headers' => $headers,
    'rows'    => $rows
]) ?>

<!-- Reusable Pagination Component -->
<?= view('components/pagination') ?>

<?= $this->endSection() ?>
