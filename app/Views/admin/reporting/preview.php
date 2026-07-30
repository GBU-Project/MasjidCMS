<?= $this->extend('layouts/admin') ?>

<?= $this->section('title') ?>Preview Laporan — <?= esc($reportTitle) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- Breadcrumb -->
<div class="breadcrumb-container">
    <span>Admin</span>
    <span>/</span>
    <span>Laporan</span>
    <span>/</span>
    <span class="breadcrumb-active">Preview Laporan</span>
</div>

<!-- Header Title -->
<div class="content-header-title">
    <div>
        <h1>Preview Laporan: <?= esc($reportTitle) ?></h1>
        <p style="font-size: 14px; color: var(--text-muted);">Hasil komputasi read-model tervalidasi periode <?= date('Y-m-01') ?> s/d <?= date('Y-m-d') ?></p>
    </div>
    <div style="display: flex; gap: 8px;">
        <button class="btn btn-secondary" onclick="window.location.reload();">🔄 Refresh</button>
        <a href="<?= site_url('admin/reporting') ?>" class="btn btn-secondary">Clear Filter</a>
        <button class="btn btn-primary" title="Export Placeholder">📤 Export (Placeholder)</button>
    </div>
</div>

<!-- Summary Cards (4 Cards) -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-card-top"><span class="stat-label">Total Records</span><div class="stat-icon">📄</div></div>
        <div class="stat-figure">2 Transaksi</div>
        <span class="stat-delta">Data Tervalidasi</span>
    </div>
    <div class="stat-card">
        <div class="stat-card-top"><span class="stat-label">Total Income</span><div class="stat-icon">📥</div></div>
        <div class="stat-figure stat-mono" style="color: var(--primary-600);">Rp 1.000.000</div>
        <span class="stat-delta">+ Infaq & Donasi</span>
    </div>
    <div class="stat-card">
        <div class="stat-card-top"><span class="stat-label">Total Expense</span><div class="stat-icon">📤</div></div>
        <div class="stat-figure stat-mono" style="color: var(--status-danger-text);">Rp 500.000</div>
        <span class="stat-delta">- Beban Operasional</span>
    </div>
    <div class="stat-card">
        <div class="stat-card-top"><span class="stat-label">Net Balance (Surplus)</span><div class="stat-icon">💰</div></div>
        <div class="stat-figure stat-mono" style="color: var(--primary-600);">Rp 500.000</div>
        <span class="stat-delta">Surplus Bersih</span>
    </div>
</div>

<!-- Table Preview Component Reused -->
<?= view('components/table', [
    'headers' => ['Kode / Ref', 'Deskripsi / Akun', 'Tanggal', 'Debit (Rp)', 'Kredit (Rp)', 'Status'],
    'rows'    => [
        [
            'columns' => [
                '<span class="stat-mono">10001</span>',
                '<strong>Kas Tunai Utama</strong>',
                '27 Jul 2026',
                '<span class="stat-mono">1.000.000</span>',
                '<span class="stat-mono">0</span>',
                '<span class="badge badge-green">POSTED</span>'
            ]
        ],
        [
            'columns' => [
                '<span class="stat-mono">50001</span>',
                '<strong>Beban Kebersihan & Air</strong>',
                '27 Jul 2026',
                '<span class="stat-mono">0</span>',
                '<span class="stat-mono">500.000</span>',
                '<span class="badge badge-green">POSTED</span>'
            ]
        ],
    ]
]) ?>

<?= view('components/pagination') ?>

<?= $this->endSection() ?>
