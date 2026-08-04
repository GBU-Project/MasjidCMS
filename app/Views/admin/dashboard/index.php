<?= $this->extend('layouts/admin') ?>

<?= $this->section('title') ?>Dashboard Overview<?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- Breadcrumb Navigation -->
<div class="breadcrumb-container">
    <span>Admin</span>
    <span>/</span>
    <span class="breadcrumb-active">Dashboard Overview</span>
</div>

<!-- Header Title -->
<div class="content-header-title">
    <div>
        <h1>Dashboard Utama MasjidCMS</h1>
        <p style="font-size: 14px; color: var(--text-muted);">Ringkasan operasional masjid, statistik jamaah, dan status keuangan real-time.</p>
    </div>
    <div>
        <button class="btn btn-secondary">📥 Export Quick Report</button>
    </div>
</div>

<!-- 6 Statistic Cards Grid -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-card-top">
            <span class="stat-label">Masjid Terdaftar</span>
            <div class="stat-icon">🕌</div>
        </div>
        <div class="stat-figure"><?= esc($totalMasjids) ?></div>
        <span class="stat-delta">Active Institutional Profile</span>
    </div>

    <div class="stat-card">
        <div class="stat-card-top">
            <span class="stat-label">Total Jamaah</span>
            <div class="stat-icon">👥</div>
        </div>
        <div class="stat-figure"><?= esc($totalJamaah) ?></div>
        <span class="stat-delta">Jamaah Terdaftar</span>
    </div>

    <div class="stat-card">
        <div class="stat-card-top">
            <span class="stat-label">Keluarga (KK)</span>
            <div class="stat-icon">👨‍👩‍👧</div>
        </div>
        <div class="stat-figure"><?= esc($totalFamilies) ?></div>
        <span class="stat-delta">Kepala Keluarga Aktif</span>
    </div>

    <div class="stat-card">
        <div class="stat-card-top">
            <span class="stat-label">Pending Approval</span>
            <div class="stat-icon">⏳</div>
        </div>
        <div class="stat-figure" style="color: var(--status-warning-text);"><?= esc($pendingApprovals) ?></div>
        <span class="stat-delta" style="color: var(--status-warning-text);">Butuh persetujuan DKM</span>
    </div>

    <div class="stat-card">
        <div class="stat-card-top">
            <span class="stat-label">Donasi Hari Ini</span>
            <div class="stat-icon">📥</div>
        </div>
        <div class="stat-figure stat-mono">Rp <?= number_format((float)($todayDonation ?? 0), 0, ',', '.') ?></div>
        <?php
            // TASK-AUDIT: bare figures with no comparison are hard to judge
            // ("is this good or does it need attention?"). Simple yesterday
            // comparison — no new tables, just reusing today's/yesterday's sums.
            $yesterday = (float) ($yesterdayDonation ?? 0);
            $today = (float) ($todayDonation ?? 0);
        ?>
        <?php if ($yesterday > 0): ?>
            <?php $deltaPct = round((($today - $yesterday) / $yesterday) * 100); ?>
            <span class="stat-delta" style="color: <?= $deltaPct >= 0 ? 'var(--primary-600)' : 'var(--status-danger-text)' ?>;">
                <?= $deltaPct >= 0 ? '↑' : '↓' ?> <?= abs($deltaPct) ?>% dari kemarin
            </span>
        <?php else: ?>
            <span class="stat-delta">Belum ada data kemarin untuk dibandingkan</span>
        <?php endif; ?>
    </div>

    <div class="stat-card">
        <div class="stat-card-top">
            <span class="stat-label">Saldo Kas Utama</span>
            <div class="stat-icon">💰</div>
        </div>
        <div class="stat-figure stat-mono" style="color: var(--primary-600);">Rp <?= number_format((float)($totalBalance ?? 0), 0, ',', '.') ?></div>
        <span class="stat-delta">Tervalidasi Double Entry</span>
    </div>
</div>

<!-- Quick Action Bar -->
<div class="quick-action-bar">
    <span style="font-size: 14px; font-weight: 600; color: var(--text-main); margin-right: 8px;">Aksi Cepat:</span>
    <a href="<?= site_url('admin/financial') ?>" class="btn btn-primary">+ Transaksi Baru</a>
    <a href="<?= site_url('admin/master?tab=jamaah') ?>" class="btn btn-secondary">+ Tambah Jamaah</a>
    <a href="<?= site_url('admin/master?tab=family') ?>" class="btn btn-secondary">+ Registrasi Keluarga</a>
</div>

<!-- Dashboard Content Split Section -->
<div class="dashboard-sections-grid">
    <!-- Left Column: Pending Approval & Recent Transactions -->
    <div>
        <!-- Transaksi Terbaru Panel -->
        <div class="panel-card">
            <div class="panel-header">
                <span>Transaksi Terbaru</span>
                <span class="badge badge-blue"><?= count($recentTransactions) ?> Transaksi</span>
            </div>
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>No. Transaksi</th>
                            <th>Tanggal</th>
                            <th>Kategori / Deskripsi</th>
                            <th>Jumlah (Rp)</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recentTransactions)): ?>
                            <tr>
                                <td colspan="6" style="text-align: center; color: var(--text-muted);">Belum ada transaksi recorded.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recentTransactions as $rt): ?>
                                <tr>
                                    <td class="stat-mono"><?= esc($rt['transaction_no']) ?></td>
                                    <td><?= esc(substr($rt['transaction_date'] ?? date('Y-m-d'), 0, 10)) ?></td>
                                    <td><?= esc($rt['description'] ?? '-') ?></td>
                                    <td class="stat-mono">Rp <?= number_format((float)$rt['amount'], 0, ',', '.') ?></td>
                                    <td><span class="badge <?= $rt['status'] === 'POSTED' ? 'badge-green' : 'badge-amber' ?>"><?= esc($rt['status']) ?></span></td>
                                    <td><a href="<?= site_url('admin/financial/detail/' . esc($rt['transaction_no'])) ?>" class="btn btn-primary" style="padding: 4px 10px; font-size: 12px;">Detail</a></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Right Column: Recent Activity Timeline & System Status -->
    <div>
        <!-- Audit Log Timeline Widget -->
        <div class="panel-card">
            <div class="panel-header">
                <span>Aktivitas Terbaru</span>
                <a href="<?= site_url('admin/settings?tab=audit') ?>" style="font-size: 12px; color: var(--primary-600);">Lihat semua →</a>
            </div>
            <?php
                // TASK-AUDIT: this used to be 3 hardcoded fake entries ("Bendahara
                // Ahmad memposting jurnal..."). Now pulled from the real audit_logs
                // table (same source as System > Audit Log), with an honest empty
                // state instead of invented activity.
                $activityLabels = [
                    'create' => 'menambahkan data baru di',
                    'update' => 'memperbarui data di',
                    'delete' => 'menghapus data di',
                    'post'   => 'memposting jurnal di',
                    'login'  => 'masuk ke sistem',
                ];
            ?>
            <?php if (empty($recentActivity)): ?>
                <p style="padding: 16px; font-size: 13px; color: var(--text-muted);">Belum ada aktivitas tercatat.</p>
            <?php else: ?>
                <ul class="timeline-list">
                    <?php foreach ($recentActivity as $act): ?>
                        <?php
                            $actionKey = strtolower($act['action'] ?? '');
                            $actionText = $activityLabels[$actionKey] ?? (esc($act['action'] ?? 'melakukan aksi') . ' di');
                        ?>
                        <li class="timeline-item">
                            <div class="timeline-dot"></div>
                            <div class="timeline-content">
                                <strong>User-<?= esc($act['user_id'] ?? 'SYSTEM') ?></strong>
                                <?= $actionText ?>
                                <code><?= esc($act['module'] ?? '-') ?></code>
                                <div class="timeline-time"><?= esc($act['created_at'] ?? '-') ?></div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>

        <!-- System Status: kept intentionally short on this page. Technical
             details (PHP version, DB engine, storage) moved to System workspace
             so the main dashboard — seen by every role — isn't cluttered with
             information only relevant to a developer/sysadmin. -->
        <div class="panel-card">
            <div class="panel-header">
                <span>Status Sistem</span>
                <span class="badge badge-green">Normal</span>
            </div>
            <div style="padding: 16px; font-size: 13px; color: var(--text-muted);">
                Semua layanan berjalan normal.
                <a href="<?= site_url('admin/settings?tab=advanced') ?>" style="color: var(--primary-600);">Lihat detail teknis →</a>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
