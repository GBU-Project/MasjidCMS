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
    <a href="<?= site_url('admin/financial?tab=transactions') ?>" class="nav-item-link <?= ($activeTab === 'transactions') ? 'active' : '' ?>">💳 Daftar Transaksi</a>
    <a href="<?= site_url('admin/financial?tab=coa') ?>" class="nav-item-link <?= ($activeTab === 'coa') ? 'active' : '' ?>">📋 Chart of Accounts (COA)</a>
    <a href="<?= site_url('admin/financial?tab=budget') ?>" class="nav-item-link <?= ($activeTab === 'budget') ? 'active' : '' ?>">📊 Anggaran / RAB</a>
    <a href="<?= site_url('admin/financial?tab=periods') ?>" class="nav-item-link <?= ($activeTab === 'periods') ? 'active' : '' ?>">📅 Periode & Tutup Buku</a>
    <a href="<?= site_url('admin/financial?tab=approvals') ?>" class="nav-item-link <?= ($activeTab === 'approvals') ? 'active' : '' ?>">⏳ Queue Persetujuan</a>
    <a href="<?= site_url('admin/financial?tab=transfer') ?>" class="nav-item-link <?= ($activeTab === 'transfer') ? 'active' : '' ?>">🔄 Transfer Kantong Dana</a>
    <a href="<?= site_url('admin/financial?tab=journal') ?>" class="nav-item-link <?= ($activeTab === 'journal') ? 'active' : '' ?>">📖 Buku Jurnal (Ledger)</a>
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

<?php if ($activeTab === 'coa'): ?>
    <div class="panel-card" style="padding: 20px; margin-bottom: 20px; max-width: 600px;">
        <h4 style="font-size: 14px; font-weight: 700; margin-bottom: 12px;">+ Tambah Akun COA Baru</h4>
        <form action="<?= site_url('admin/financial/coa/store') ?>" method="POST" style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
            <?= csrf_field() ?>
            <div>
                <label style="display: block; font-size: 12px; font-weight: 600;">Kode COA *</label>
                <input type="text" name="account_code" required placeholder="Contoh: 10001" style="width: 100%; padding: 6px 10px; border: 1px solid var(--border-light); border-radius: 6px;">
            </div>
            <div>
                <label style="display: block; font-size: 12px; font-weight: 600;">Nama Akun *</label>
                <input type="text" name="name" required placeholder="Kas Tunai Utama" style="width: 100%; padding: 6px 10px; border: 1px solid var(--border-light); border-radius: 6px;">
            </div>
            <div>
                <label style="display: block; font-size: 12px; font-weight: 600;">Tipe Akun</label>
                <select name="account_type" style="width: 100%; padding: 6px 10px; border: 1px solid var(--border-light); border-radius: 6px;">
                    <option value="ASSET">ASSET (Aset/Kas)</option>
                    <option value="LIABILITY">LIABILITY (Kewajiban)</option>

                    <option value="EQUITY">EQUITY (Modal/Ekuitas)</option>
                    <option value="REVENUE">REVENUE (Pendapatan/Infaq)</option>
                    <option value="EXPENSE">EXPENSE (Beban/Pengeluaran)</option>
                </select>
            </div>
            <div style="display: flex; align-items: flex-end;">
                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 6px;">💾 Simpan COA</button>
            </div>
        </form>
    </div>

<?php elseif ($activeTab === 'budget'): ?>
    <div class="panel-card" style="padding: 20px; margin-bottom: 20px; max-width: 600px;">
        <h4 style="font-size: 14px; font-weight: 700; margin-bottom: 12px;">+ Tambah Alokasi Budget / RAB</h4>
        <form action="<?= site_url('admin/financial/budget/store') ?>" method="POST" style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
            <?= csrf_field() ?>
            <div>
                <label style="display: block; font-size: 12px; font-weight: 600;">Nominal Alokasi (Rp) *</label>
                <input type="number" name="allocated_amount" required placeholder="10000000" style="width: 100%; padding: 6px 10px; border: 1px solid var(--border-light); border-radius: 6px;">
            </div>
            <div>
                <label style="display: block; font-size: 12px; font-weight: 600;">ID Periode</label>
                <input type="number" name="period_id" value="1" style="width: 100%; padding: 6px 10px; border: 1px solid var(--border-light); border-radius: 6px;">
            </div>
            <div style="grid-column: span 2; text-align: right;">
                <button type="submit" class="btn btn-primary" style="padding: 6px 16px;">💾 Simpan Budget</button>
            </div>
        </form>
    </div>

<?php elseif ($activeTab === 'periods'): ?>
    <div class="panel-card" style="padding: 20px; margin-bottom: 20px; max-width: 650px;">
        <h4 style="font-size: 14px; font-weight: 700; margin-bottom: 12px;">+ Tambah Periode Akuntansi Baru</h4>
        <form action="<?= site_url('admin/financial/periods/store') ?>" method="POST" style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
            <?= csrf_field() ?>
            <div>
                <label style="display: block; font-size: 12px; font-weight: 600;">Kode Periode *</label>
                <input type="text" name="period_code" required placeholder="PER-2026" style="width: 100%; padding: 6px 10px; border: 1px solid var(--border-light); border-radius: 6px;">
            </div>
            <div>
                <label style="display: block; font-size: 12px; font-weight: 600;">Nama Periode *</label>
                <input type="text" name="name" required placeholder="Tahun Anggaran 2026" style="width: 100%; padding: 6px 10px; border: 1px solid var(--border-light); border-radius: 6px;">
            </div>
            <div>
                <label style="display: block; font-size: 12px; font-weight: 600;">Tanggal Mulai</label>
                <input type="date" name="start_date" value="<?= date('Y-01-01') ?>" style="width: 100%; padding: 6px 10px; border: 1px solid var(--border-light); border-radius: 6px;">
            </div>
            <div>
                <label style="display: block; font-size: 12px; font-weight: 600;">Tanggal Selesai</label>
                <input type="date" name="end_date" value="<?= date('Y-12-31') ?>" style="width: 100%; padding: 6px 10px; border: 1px solid var(--border-light); border-radius: 6px;">
            </div>
            <div style="grid-column: span 2; text-align: right;">
                <button type="submit" class="btn btn-primary" style="padding: 6px 16px;">💾 Simpan Periode</button>
            </div>
        </form>
    </div>

<?php elseif ($activeTab === 'journal'): ?>
    <div class="panel-card" style="padding: 20px; margin-bottom: 20px; max-width: 600px;">
        <h4 style="font-size: 14px; font-weight: 700; margin-bottom: 12px;">+ Input Catatan Jurnal Manual</h4>
        <form action="<?= site_url('admin/financial/journal/store') ?>" method="POST" style="display: flex; flex-direction: column; gap: 12px;">
            <?= csrf_field() ?>
            <div>
                <label style="display: block; font-size: 12px; font-weight: 600;">Keterangan / Memotext *</label>
                <input type="text" name="description" required placeholder="Pencatatan penyesuaian kas..." style="width: 100%; padding: 6px 10px; border: 1px solid var(--border-light); border-radius: 6px;">
            </div>
            <div>
                <button type="submit" class="btn btn-primary" style="padding: 6px 16px;">📖 Posting Jurnal Manual</button>
            </div>
        </form>
    </div>
<?php endif; ?>

<?php if ($activeTab === 'transfer'): ?>
    <!-- Dedicated Fund Transfer Workspace -->
    <div class="panel-card" style="max-width: 680px; margin-left: 0;">
        <div class="panel-header">
            <span>Form Transfer Antar Kantong Dana (Fund Transfer)</span>
            <span class="badge badge-green">Syariah Compliant</span>
        </div>
        <form style="display: flex; flex-direction: column; gap: 16px; padding: 20px;">
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

<?php else: ?>
    <!-- Standard Reusable Financial Workspace -->
    <?php if ($activeTab === 'transactions'): ?>
        <?= view('components/toolbar', [
            'createUrl' => site_url('admin/financial/create'),
            'createLabel' => '+ Buat Transaksi Baru'
        ]) ?>
    <?php endif; ?>

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
