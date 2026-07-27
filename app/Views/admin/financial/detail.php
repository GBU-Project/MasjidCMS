<?= $this->extend('layouts/admin') ?>

<?= $this->section('title') ?>Detail Transaksi TRX-202607-00088<?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- Breadcrumb -->
<div class="breadcrumb-container">
    <span>Admin</span>
    <span>/</span>
    <span>Keuangan</span>
    <span>/</span>
    <span class="breadcrumb-active">TRX-202607-00088</span>
</div>

<!-- Header Title -->
<div class="content-header-title">
    <div>
        <h1>Detail Transaksi <span class="stat-mono">TRX-202607-00088</span></h1>
        <p style="font-size: 14px; color: var(--text-muted);">Informasi lengkap transaksi, jurnal double-entry, log persetujuan, dan audit trail.</p>
    </div>
    <div style="display: flex; gap: 8px;">
        <button class="btn btn-primary">Approve Transaksi</button>
        <button class="btn btn-secondary" style="color: var(--status-danger-text);">Reject</button>
    </div>
</div>

<!-- Detail View Tabs Navigation -->
<div style="border-bottom: 1px solid var(--border-light); margin-bottom: 20px; display: flex; gap: 8px;">
    <a href="#" class="nav-item-link active">📄 Informasi Umum</a>
    <a href="#" class="nav-item-link">📖 Jurnal Double Entry</a>
    <a href="#" class="nav-item-link">⏳ Riwayat Persetujuan</a>
    <a href="#" class="nav-item-link">📜 Audit Log Trail</a>
</div>

<!-- General Info Card -->
<div class="panel-card" style="max-width: 800px; margin-left: 0;">
    <div class="panel-header">
        <span>Informasi Detail Transaksi</span>
        <span class="badge badge-amber">PENDING APPROVAL</span>
    </div>
    <table class="data-table">
        <tbody>
            <tr>
                <td style="width: 200px; font-weight: 600;">No. Transaksi</td>
                <td class="stat-mono">TRX-202607-00088</td>
            </tr>
            <tr>
                <td style="font-weight: 600;">Jenis Transaksi</td>
                <td><span class="badge badge-amber">EXPENSE</span></td>
            </tr>
            <tr>
                <td style="font-weight: 600;">Nominal Transaksi</td>
                <td class="stat-mono" style="font-size: 18px; font-weight: 700; color: var(--text-main);">Rp 250.000</td>
            </tr>
            <tr>
                <td style="font-weight: 600;">Kantong Dana (Fund)</td>
                <td>Kas Tunai Umum (UNRESTRICTED)</td>
            </tr>
            <tr>
                <td style="font-weight: 600;">Kode Akun COA</td>
                <td class="stat-mono">50001 — Beban Kebersihan</td>
            </tr>
            <tr>
                <td style="font-weight: 600;">Deskripsi</td>
                <td>Pembelian alat kebersihan dan perlengkapan jumat masjid</td>
            </tr>
            <tr>
                <td style="font-weight: 600;">Tanggal Input</td>
                <td>27 Juli 2026 10:15:00</td>
            </tr>
        </tbody>
    </table>
</div>
<?= $this->endSection() ?>
