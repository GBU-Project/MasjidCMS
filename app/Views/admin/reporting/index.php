<?= $this->extend('layouts/admin') ?>

<?= $this->section('title') ?>Reporting Workspace<?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- Breadcrumb -->
<div class="breadcrumb-container">
    <span>Admin</span>
    <span>/</span>
    <span>Laporan</span>
    <span>/</span>
    <span class="breadcrumb-active"><?= esc($activeModuleLabel ?? 'Katalog Laporan') ?></span>
</div>

<!-- Header Title -->
<div class="content-header-title">
    <div>
        <h1>Unified Reporting Workspace</h1>
        <p style="font-size: 14px; color: var(--text-muted);">Pusat pelaporan operasional masjid, keuangan, data jamaah, audit trail, dan rekapitulasi program.</p>
    </div>
</div>

<!-- Workspace Tab Navigation -->
<div style="border-bottom: 1px solid var(--border-light); margin-bottom: 20px; display: flex; gap: 8px;">
    <a href="<?= site_url('admin/reporting?tab=catalog') ?>" class="nav-item-link <?= ($activeTab === 'catalog') ? 'active' : '' ?>">📊 Katalog Laporan</a>
    <a href="<?= site_url('admin/reporting?tab=preview') ?>" class="nav-item-link <?= ($activeTab === 'preview') ? 'active' : '' ?>">👁️ Preview Laporan</a>
    <a href="<?= site_url('admin/reporting?tab=history') ?>" class="nav-item-link <?= ($activeTab === 'history') ? 'active' : '' ?>">📜 Riwayat Generate</a>
</div>

<?php if ($activeTab === 'history'): ?>
    <!-- Report Generation History -->
    <?= view('components/table', [
        'headers' => ['Nama Laporan', 'Tanggal Generate', 'User Peminta', 'Parameter Filter', 'Status'],
        'rows'    => [
            [
                'columns' => [
                    '<strong>Neraca Saldo (Trial Balance)</strong>',
                    '27 Jul 2026 14:00',
                    'Bendahara (Ahmad)',
                    '<span class="stat-mono">Periode: 2026-07</span>',
                    '<span class="badge badge-green">COMPLETED</span>'
                ]
            ],
            [
                'columns' => [
                    '<strong>Buku Kas Utama</strong>',
                    '27 Jul 2026 12:30',
                    'Ketua DKM (H. Usman)',
                    '<span class="stat-mono">Fund: KAS_UTAMA</span>',
                    '<span class="badge badge-green">COMPLETED</span>'
                ]
            ],
        ]
    ]) ?>
    <?= view('components/pagination') ?>

<?php else: ?>
    <!-- Filter Panel Form Card -->
    <div class="panel-card" style="margin-bottom: 24px;">
        <div class="panel-header">
            <span>Filter Parameter Laporan</span>
            <span class="badge badge-green">Read-Only Engine</span>
        </div>
        <form action="<?= site_url('admin/reporting/preview') ?>" method="GET" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
            <div>
                <label style="display: block; font-size: 13px; font-weight: 500; margin-bottom: 6px;">Tanggal Awal</label>
                <input type="date" name="startDate" value="<?= date('Y-m-01') ?>" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: var(--radius-md); font-size: 14px;">
            </div>
            <div>
                <label style="display: block; font-size: 13px; font-weight: 500; margin-bottom: 6px;">Tanggal Akhir</label>
                <input type="date" name="endDate" value="<?= date('Y-m-d') ?>" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: var(--radius-md); font-size: 14px;">
            </div>
            <div>
                <label style="display: block; font-size: 13px; font-weight: 500; margin-bottom: 6px;">Jenis Laporan</label>
                <select name="type" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: var(--radius-md); font-size: 14px; background: white;">
                    <option value="TRIAL_BALANCE">Neraca Saldo (Trial Balance)</option>
                    <option value="GENERAL_LEDGER">Buku Besar (General Ledger)</option>
                    <option value="CASH_BOOK">Buku Kas (Cash Book)</option>
                    <option value="FUND_BALANCE">Saldo Per Kantong Dana</option>
                    <option value="INCOME_EXPENSE">Pendapatan & Beban</option>
                </select>
            </div>
            <div>
                <label style="display: block; font-size: 13px; font-weight: 500; margin-bottom: 6px;">Kantong Dana (Fund)</label>
                <select name="fundId" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: var(--radius-md); font-size: 14px; background: white;">
                    <option value="">Semua Kantong Dana</option>
                    <option value="1">Kas Umum (UNRESTRICTED)</option>
                    <option value="2">Kas Pembangunan (UNRESTRICTED)</option>
                    <option value="3">Dana Zakat (RESTRICTED)</option>
                </select>
            </div>
            <div style="grid-column: 1 / -1; display: flex; gap: 8px; justify-content: flex-end;">
                <a href="<?= site_url('admin/reporting') ?>" class="btn btn-secondary">Clear Filter</a>
                <button type="submit" class="btn btn-primary">Generate Preview Laporan ›</button>
            </div>
        </form>
    </div>

    <!-- 12 Report Catalog Cards Grid -->
    <h2 style="font-size: 18px; font-weight: 600; margin-bottom: 16px;">Katalog Laporan Tersedia (12 Modules)</h2>
    <div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px;">
        <div class="stat-card">
            <div class="stat-card-top"><span class="stat-label">Keuangan</span><div class="stat-icon">💰</div></div>
            <div style="font-size: 16px; font-weight: 700; margin-bottom: 4px;">Buku Kas (Cash Book)</div>
            <p style="font-size: 12px; color: var(--text-muted); margin-bottom: 12px;">Rincian saldo awal, mutasi masuk/keluar, dan saldo akhir kas.</p>
            <a href="<?= site_url('admin/reporting/preview?type=CASH_BOOK') ?>" class="btn btn-secondary" style="padding: 4px 10px; font-size: 12px;">Generate Preview</a>
        </div>

        <div class="stat-card">
            <div class="stat-card-top"><span class="stat-label">Keuangan</span><div class="stat-icon">📖</div></div>
            <div style="font-size: 16px; font-weight: 700; margin-bottom: 4px;">Buku Besar (General Ledger)</div>
            <p style="font-size: 12px; color: var(--text-muted); margin-bottom: 12px;">Penayangan mutasi jurnal per kode akun COA.</p>
            <a href="<?= site_url('admin/reporting/preview?type=GENERAL_LEDGER') ?>" class="btn btn-secondary" style="padding: 4px 10px; font-size: 12px;">Generate Preview</a>
        </div>

        <div class="stat-card">
            <div class="stat-card-top"><span class="stat-label">Keuangan</span><div class="stat-icon">⚖️</div></div>
            <div style="font-size: 16px; font-weight: 700; margin-bottom: 4px;">Neraca Saldo (Trial Balance)</div>
            <p style="font-size: 12px; color: var(--text-muted); margin-bottom: 12px;">Verifikasi keseimbangan Debit == Kredit seluruh akun.</p>
            <a href="<?= site_url('admin/reporting/preview?type=TRIAL_BALANCE') ?>" class="btn btn-secondary" style="padding: 4px 10px; font-size: 12px;">Generate Preview</a>
        </div>

        <div class="stat-card">
            <div class="stat-card-top"><span class="stat-label">Keuangan</span><div class="stat-icon">👜</div></div>
            <div style="font-size: 16px; font-weight: 700; margin-bottom: 4px;">Saldo Per Kantong Dana</div>
            <p style="font-size: 12px; color: var(--text-muted); margin-bottom: 12px;">Pemisahan saldo terikat Zakat/Wakaf & kas umum.</p>
            <a href="<?= site_url('admin/reporting/preview?type=FUND_BALANCE') ?>" class="btn btn-secondary" style="padding: 4px 10px; font-size: 12px;">Generate Preview</a>
        </div>

        <div class="stat-card">
            <div class="stat-card-top"><span class="stat-label">Operasional</span><div class="stat-icon">📈</div></div>
            <div style="font-size: 16px; font-weight: 700; margin-bottom: 4px;">Pendapatan & Beban</div>
            <p style="font-size: 12px; color: var(--text-muted); margin-bottom: 12px;">Laporan operasional surplus/defisit masjid.</p>
            <a href="<?= site_url('admin/reporting/preview?type=INCOME_EXPENSE') ?>" class="btn btn-secondary" style="padding: 4px 10px; font-size: 12px;">Generate Preview</a>
        </div>

        <div class="stat-card">
            <div class="stat-card-top"><span class="stat-label">Audit</span><div class="stat-icon">📜</div></div>
            <div style="font-size: 16px; font-weight: 700; margin-bottom: 4px;">Audit Log Activity</div>
            <p style="font-size: 12px; color: var(--text-muted); margin-bottom: 12px;">Rekapitulasi riwayat aktivitas user platform.</p>
            <a href="<?= site_url('admin/reporting/preview?type=AUDIT_LOG') ?>" class="btn btn-secondary" style="padding: 4px 10px; font-size: 12px;">Generate Preview</a>
        </div>
    </div>
<?php endif; ?>

<?= $this->endSection() ?>
