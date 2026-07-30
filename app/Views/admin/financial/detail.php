<?= $this->extend('layouts/admin') ?>

<?php
    $trxNo = esc($transaction['transaction_no'] ?? $transactionId);
    $amount = isset($transaction['amount']) ? number_format((float)$transaction['amount'], 0, ',', '.') : '0';
    $type = esc($transaction['transaction_type'] ?? 'EXPENSE');
    $status = esc($transaction['status'] ?? 'DRAFT');
    $desc = esc($transaction['description'] ?? 'Detail Transaksi');
    $date = esc($transaction['transaction_date'] ?? date('Y-m-d H:i:s'));
?>

<?= $this->section('title') ?>Detail Transaksi <?= $trxNo ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- Breadcrumb -->
<div class="breadcrumb-container">
    <span>Admin</span>
    <span>/</span>
    <span>Keuangan</span>
    <span>/</span>
    <span class="breadcrumb-active"><?= $trxNo ?></span>
</div>

<!-- Header Title -->
<div class="content-header-title">
    <div>
        <h1>Detail Transaksi <span class="stat-mono"><?= $trxNo ?></span></h1>
        <p style="font-size: 14px; color: var(--text-muted);">Informasi lengkap transaksi, jurnal double-entry, log persetujuan, dan audit trail.</p>
    </div>
    <div style="display: flex; gap: 8px;">
        <a href="<?= site_url('admin/financial') ?>" class="btn btn-secondary">← Kembali</a>
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
        <span class="badge <?= $status === 'POSTED' ? 'badge-green' : 'badge-amber' ?>"><?= $status ?></span>
    </div>
    <table class="data-table">
        <tbody>
            <tr>
                <td style="width: 200px; font-weight: 600;">No. Transaksi</td>
                <td class="stat-mono"><?= $trxNo ?></td>
            </tr>
            <tr>
                <td style="font-weight: 600;">Jenis Transaksi</td>
                <td><span class="badge <?= $type === 'INCOME' ? 'badge-green' : 'badge-red' ?>"><?= $type ?></span></td>
            </tr>
            <tr>
                <td style="font-weight: 600;">Nominal Transaksi</td>
                <td class="stat-mono" style="font-size: 18px; font-weight: 700; color: var(--text-main);">Rp <?= $amount ?></td>
            </tr>
            <tr>
                <td style="font-weight: 600;">Deskripsi</td>
                <td><?= $desc ?></td>
            </tr>
            <tr>
                <td style="font-weight: 600;">Tanggal Input</td>
                <td><?= $date ?></td>
            </tr>
        </tbody>
    </table>
</div>
<?= $this->endSection() ?>
