<?= $this->extend('layouts/public') ?>

<?= $this->section('title') ?>Berita & Jadwal Kajian<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="portal-container">
    <h1 style="font-size: 28px; font-weight: 800; margin-bottom: 24px;">Berita & Jadwal Kajian Syariah</h1>
    <div class="card-grid">
        <div class="portal-card">
            <span style="font-size: 12px; font-weight: 600; color: var(--primary-600);">KAJIAN RUTIN</span>
            <h3 style="font-size: 18px; margin: 8px 0;">Kajian Tematik Fiqih Muamalah</h3>
            <p style="font-size: 14px; color: var(--text-muted);">Pembahasan prinsip-prinsip transaksi syariah dan kehalalan harta bersama Ustadz Dr. H. Abdurrahman.</p>
            <div style="font-size: 12px; color: var(--text-subtle); margin-top: 12px;">📅 Setiap Ahad Malam (Ba'da Maghrib)</div>
        </div>

        <div class="portal-card">
            <span style="font-size: 12px; font-weight: 600; color: var(--primary-600);">BERITA MASJID</span>
            <h3 style="font-size: 18px; margin: 8px 0;">Laporan Transparansi Keuangan Bulan Juli</h3>
            <p style="font-size: 14px; color: var(--text-muted);">Pengurus masjid mempublikasikan laporan neraca kas dan realisasi program infaq periode Juli 2026.</p>
            <div style="font-size: 12px; color: var(--text-subtle); margin-top: 12px;">📅 27 Juli 2026</div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
