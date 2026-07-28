<?= $this->extend('layouts/admin') ?>

<?= $this->section('title') ?>Financial Workspace<?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- Breadcrumb -->
<div class="breadcrumb-container">
    <span>Admin</span>
    <span>/</span>
    <span>Keuangan</span>
    <span>/</span>
    <span class="breadcrumb-active"><?= esc($activeModuleLabel ?? 'Daftar Transaksi') ?></span>
</div>

<!-- Header -->
<div class="content-header-title">
    <div>
        <h1>Financial Transaction Workspace</h1>
        <p style="font-size: 14px; color: var(--text-muted);">Pengelolaan transaksi keuangan, persetujuan DKM, transfer kantong dana, dan penayangan buku jurnal seimbang.</p>
    </div>
</div>

<!-- Workspace Tab Navigation -->
<div style="border-bottom: 1px solid var(--border-light); margin-bottom: 20px; display: flex; gap: 8px;">
    <a href="/admin/financial?tab=transactions" class="nav-item-link <?= ($activeTab === 'transactions') ? 'active' : '' ?>">💳 Daftar Transaksi</a>
    <a href="/admin/financial?tab=coa" class="nav-item-link <?= ($activeTab === 'coa') ? 'active' : '' ?>">📋 Chart of Accounts (COA)</a>
    <a href="/admin/financial?tab=budget" class="nav-item-link <?= ($activeTab === 'budget') ? 'active' : '' ?>">📊 Anggaran / RAB</a>
    <a href="/admin/financial?tab=periods" class="nav-item-link <?= ($activeTab === 'periods') ? 'active' : '' ?>">📅 Periode & Tutup Buku</a>
    <a href="/admin/financial?tab=approvals" class="nav-item-link <?= ($activeTab === 'approvals') ? 'active' : '' ?>">⏳ Queue Persetujuan</a>
    <a href="/admin/financial?tab=transfer" class="nav-item-link <?= ($activeTab === 'transfer') ? 'active' : '' ?>">🔄 Transfer Kantong Dana</a>
    <a href="/admin/financial?tab=journal" class="nav-item-link <?= ($activeTab === 'journal') ? 'active' : '' ?>">📖 Buku Jurnal (Ledger)</a>
</div>

<?php if ($activeTab === 'transfer'): ?>
    <!-- Dedicated Fund Transfer Workspace -->
    <div class="panel-card" style="max-width: 680px; margin-left: 0;">
        <div class="panel-header">
            <span>Form Transfer Antar Kantong Dana (Fund Transfer)</span>
            <span class="badge badge-green">Syariah Compliant</span>
        </div>
        <form style="display: flex; flex-direction: column; gap: 16px;">
            <div>
                <label style="display: block; font-size: 14px; font-weight: 500; margin-bottom: 6px;">Kantong Dana Asal (Source Fund) <span style="color: red;">*</span></label>
                <select style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: var(--radius-md); font-size: 14px;">
                    <option value="1">Kas Umum (UNRESTRICTED) - Saldo: Rp 25.000.000</option>
                    <option value="2">Kas Pembangunan (UNRESTRICTED) - Saldo: Rp 15.000.000</option>
                </select>
            </div>
            <div>
                <label style="display: block; font-size: 14px; font-weight: 500; margin-bottom: 6px;">Kantong Dana Tujuan (Destination Fund) <span style="color: red;">*</span></label>
                <select style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: var(--radius-md); font-size: 14px;">
                    <option value="2">Kas Pembangunan (UNRESTRICTED)</option>
                    <option value="1">Kas Umum (UNRESTRICTED)</option>
                </select>
                <p style="font-size: 12px; color: var(--text-subtle); margin-top: 4px;">Aturan Syariah BR-FIN-01: Transfer dari Dana Zakat/Terikat ke Kas Umum dilarang.</p>
            </div>
            <div>
                <label style="display: block; font-size: 14px; font-weight: 500; margin-bottom: 6px;">Nominal Transfer (Rp) <span style="color: red;">*</span></label>
                <input type="number" placeholder="500000" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: var(--radius-md); font-size: 14px;">
            </div>
            <div>
                <label style="display: block; font-size: 14px; font-weight: 500; margin-bottom: 6px;">Alasan / Catatan Transfer <span style="color: red;">*</span></label>
                <textarea rows="3" placeholder="Alokasi dana kas umum untuk biaya renovasi atap..." style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: var(--radius-md); font-size: 14px;"></textarea>
            </div>
            <div>
                <button type="button" class="btn btn-primary">Proses Transfer Dana</button>
            </div>
        </form>
    </div>

<?php elseif ($activeTab === 'journal'): ?>
    <!-- Ledger Style Journal Viewer -->
    <div class="panel-card" style="padding: 0; margin-bottom: 16px; overflow: hidden;">
        <div class="panel-header" style="padding: 16px 20px; border-bottom: 1px solid var(--border-light);">
            <span>Buku Jurnal Umum (General Journal Ledger)</span>
            <span class="badge badge-green">Balanced (Debit == Credit)</span>
        </div>
        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>No. Jurnal</th>
                        <th>Tanggal</th>
                        <th>Kode Akun</th>
                        <th>Nama Akun COA</th>
                        <th style="text-align: right;">Debit (Rp)</th>
                        <th style="text-align: right;">Kredit (Rp)</th>
                        <th style="text-align: right;">Running Balance (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="stat-mono">JRN-202607-00080</td>
                        <td>27 Jul 2026</td>
                        <td class="stat-mono">10001</td>
                        <td>Kas Tunai Utama</td>
                        <td class="stat-mono" style="text-align: right;">500.000</td>
                        <td class="stat-mono" style="text-align: right;">0</td>
                        <td class="stat-mono" style="text-align: right;">45.850.000</td>
                    </tr>
                    <tr>
                        <td class="stat-mono">JRN-202607-00080</td>
                        <td>27 Jul 2026</td>
                        <td class="stat-mono">40001</td>
                        <td>Pendapatan Infaq Kotak Jumat</td>
                        <td class="stat-mono" style="text-align: right;">0</td>
                        <td class="stat-mono" style="text-align: right;">500.000</td>
                        <td class="stat-mono" style="text-align: right;">45.850.000</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

<?php else: ?>
    <!-- Standard Reusable Financial Workspace -->
    <?= view('components/toolbar', [
        'createUrl' => '/admin/financial/create',
        'createLabel' => '+ Buat Transaksi Baru'
    ]) ?>

    <?= view('components/search_filter', [
        'placeholder' => 'Cari berdasarkan no transaksi, deskripsi, atau nominal...'
    ]) ?>

    <?= view('components/table', [
        'headers' => $headers,
        'rows'    => $rows
    ]) ?>

    <?= view('components/pagination') ?>
<?php endif; ?>

<?= $this->endSection() ?>
