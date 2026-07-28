<?= $this->extend('layouts/public') ?>

<?= $this->section('title') ?>Transparansi Keuangan Publik<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="portal-container" style="max-width: 900px;">
    <h1 style="font-size: 28px; font-weight: 800; margin-bottom: 8px;">Transparansi Keuangan Publik</h1>
    <p style="color: var(--text-muted); margin-bottom: 24px;">Laporan real-time akuntabilitas pengelolaan dana infaq, zakat, dan donasi jamaah.</p>

    <!-- Financial Overview Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 16px; margin-bottom: 32px;">
        <div style="background: white; border: 1px solid var(--border-light); border-radius: 12px; padding: 20px;">
            <span style="font-size: 13px; color: var(--text-muted); font-weight: 600;">Total Infaq & Pemasukan</span>
            <h3 style="font-size: 22px; font-weight: 800; color: #16a34a; margin-top: 8px; margin-bottom: 0;">Rp <?= number_format((float)$totalIncome, 0, ',', '.') ?></h3>
        </div>
        <div style="background: white; border: 1px solid var(--border-light); border-radius: 12px; padding: 20px;">
            <span style="font-size: 13px; color: var(--text-muted); font-weight: 600;">Total Pengeluaran Operational</span>
            <h3 style="font-size: 22px; font-weight: 800; color: #dc2626; margin-top: 8px; margin-bottom: 0;">Rp <?= number_format((float)$totalExpense, 0, ',', '.') ?></h3>
        </div>
        <div style="background: white; border: 1px solid var(--border-light); border-radius: 12px; padding: 20px;">
            <span style="font-size: 13px; color: var(--text-muted); font-weight: 600;">Saldo Kas Neto</span>
            <h3 style="font-size: 22px; font-weight: 800; color: var(--primary-600); margin-top: 8px; margin-bottom: 0;">Rp <?= number_format((float)($totalIncome - $totalExpense), 0, ',', '.') ?></h3>
        </div>
    </div>

    <!-- Public Transactions Table -->
    <div style="background: white; border: 1px solid var(--border-light); border-radius: 12px; overflow: hidden; padding: 24px;">
        <h3 style="font-size: 16px; font-weight: 700; margin-bottom: 16px;">Catatan Transaksi Terakhir</h3>
        <table class="data-table" style="width: 100%;">
            <thead>
                <tr>
                    <th>No. Transaksi</th>
                    <th>Tanggal</th>
                    <th>Keterangan</th>
                    <th>Tipe</th>
                    <th style="text-align: right;">Jumlah (Rp)</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($transactions)): ?>
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 24px; color: var(--text-muted);">
                            Belum ada catatan transaksi publik.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($transactions as $t): ?>
                        <tr>
                            <td class="stat-mono"><?= esc($t['transaction_no']) ?></td>
                            <td><?= esc(substr($t['transaction_date'], 0, 10)) ?></td>
                            <td><?= esc($t['description']) ?></td>
                            <td><span class="badge <?= $t['transaction_type'] === 'INCOME' ? 'badge-green' : 'badge-red' ?>"><?= esc($t['transaction_type']) ?></span></td>
                            <td class="stat-mono" style="text-align: right;">Rp <?= number_format((float)$t['amount'], 0, ',', '.') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>
