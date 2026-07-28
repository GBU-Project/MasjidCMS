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
        <span class="stat-delta">Transaksi Hari Ini</span>
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
    <a href="/admin/financial" class="btn btn-primary">+ Transaksi Baru</a>
    <a href="/admin/master?tab=jamaah" class="btn btn-secondary">+ Tambah Jamaah</a>
    <a href="/admin/master?tab=family" class="btn btn-secondary">+ Registrasi Keluarga</a>
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
                                    <td><a href="/admin/financial/detail/<?= esc($rt['transaction_no']) ?>" class="btn btn-primary" style="padding: 4px 10px; font-size: 12px;">Detail</a></td>
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
                <span>Aktivitas Terbaru (Audit Log)</span>
            </div>
            <ul class="timeline-list">
                <li class="timeline-item">
                    <div class="timeline-dot"></div>
                    <div class="timeline-content">
                        <strong>Bendahara (Ahmad)</strong> memposting jurnal <code>JRN-202607-00080</code>
                        <div class="timeline-time">5 menit yang lalu</div>
                    </div>
                </li>
                <li class="timeline-item">
                    <div class="timeline-dot"></div>
                    <div class="timeline-content">
                        <strong>Ketua DKM (H. Usman)</strong> menyetujui transaksi <code>TRX-202607-00085</code>
                        <div class="timeline-time">20 menit yang lalu</div>
                    </div>
                </li>
                <li class="timeline-item">
                    <div class="timeline-dot"></div>
                    <div class="timeline-content">
                        <strong>Staff (Budi)</strong> mendaftarkan Jamaah baru <code>Bpk. Ridwan</code>
                        <div class="timeline-time">1 jam yang lalu</div>
                    </div>
                </li>
            </ul>
        </div>

        <!-- System Operational Status Panel -->
        <div class="panel-card">
            <div class="panel-header">
                <span>Status Sistem Operational</span>
                <span class="badge badge-green">Healthy</span>
            </div>
            <ul class="status-list">
                <li class="status-item">
                    <span>PHP Engine</span>
                    <strong>v8.2.12</strong>
                </li>
                <li class="status-item">
                    <span>Database Connection</span>
                    <strong style="color: var(--primary-600);">Connected (MySQL 8.0)</strong>
                </li>
                <li class="status-item">
                    <span>Storage Health</span>
                    <strong>12.5 GB / 100 GB (Writable OK)</strong>
                </li>
                <li class="status-item">
                    <span>CSRF & Rate Limit</span>
                    <strong style="color: var(--primary-600);">Active (Protected)</strong>
                </li>
            </ul>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
