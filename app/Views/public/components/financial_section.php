<!-- Transparansi Keuangan Section UI 2.0 -->
<?php $sectionSettings = $settings ?? []; $programCount = (int) ($programCount ?? 0); ?>
<section class="section-wrapper" id="keuangan" style="background: linear-gradient(135deg, var(--emerald-950), var(--emerald-900)); color: #fff;">
    <div class="container">
        <div class="section-header-center">
            <span class="section-tag" style="background: rgba(255, 255, 255, 0.1); color: var(--emerald-100); border-color: rgba(255, 255, 255, 0.15);"><?= esc($sectionSettings['financial_tag'] ?? 'TRANSPARANSI KEUANGAN') ?></span>
            <h2 class="section-title" style="color: #fff;"><?= esc($sectionSettings['financial_title'] ?? 'Akuntabilitas & Saldo Kas Masjid') ?></h2>
            <p class="section-subtitle" style="color: var(--slate-400);"><?= esc($sectionSettings['financial_subtitle'] ?? 'Laporan saldo kas dan penerimaan infaq secara real-time yang dapat dipertanggungjawabkan kepada jamaah.') ?></p>
        </div>

        <div class="financial-showcase">
            <div class="financial-summary-card">
                <div class="financial-summary-head">
                    <span class="financial-label">Total Saldo Kas</span>
                    <h3>Rp <?= number_format($financialSummary['total_balance'] ?? 0, 0, ',', '.') ?></h3>
                    <p>Bank & Kas Tunai DKM</p>
                </div>
                <div class="financial-summary-foot">
                    <span>Program aktif berjalan</span>
                    <strong><?= $programCount ?> program</strong>
                </div>
            </div>

            <div class="financial-metrics-grid">
                <div class="financial-metric-card positive">
                    <span>Saldo Tersedia</span>
                    <h3>Rp <?= number_format($financialSummary['total_balance'] ?? 0, 0, ',', '.') ?></h3>
                    <p>Terpantau aman dan transparan</p>
                </div>
                <div class="financial-metric-card accent">
                    <span>Pemasukan Infaq</span>
                    <h3>Rp <?= number_format($financialSummary['total_income'] ?? 0, 0, ',', '.') ?></h3>
                    <p>Komitmen akuntabilitas terbuka</p>
                </div>
                <div class="financial-metric-card neutral">
                    <span>Pengeluaran Operasional</span>
                    <h3>Rp <?= number_format($financialSummary['total_expense'] ?? 0, 0, ',', '.') ?></h3>
                    <p>Penyaluran untuk program dan perawatan</p>
                </div>
            </div>
        </div>

        <div class="financial-progress-row">
            <p style="color: var(--slate-400); max-width: 480px; margin: 0;">Aktivitas program DKM terus tumbuh dan terpantau secara berkala. Lihat rincian lengkap realisasi anggaran di laporan keuangan.</p>
            <a href="<?= site_url('transparansi') ?>" class="btn-ui2 btn-primary-ui2">
                📊 <?= esc($sectionSettings['financial_cta'] ?? 'Laporan Keuangan Lengkap') ?>
            </a>
        </div>
    </div>
</section>
