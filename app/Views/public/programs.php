<?= $this->extend('layouts/public') ?>

<?= $this->section('title') ?>Program Unggulan Masjid<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="portal-container">
    <h1 style="font-size: 28px; font-weight: 800; margin-bottom: 24px;">Program Unggulan & Dakwah</h1>
    <div class="card-grid">
        <div class="portal-card">
            <span style="font-size: 12px; font-weight: 600; color: var(--primary-600);">PEMBANGUNAN</span>
            <h3 style="font-size: 18px; margin: 8px 0;">Pembangunan Gedung TPQ & Rumah Tahfidz</h3>
            <p style="font-size: 14px; color: var(--text-muted);">Penyediaan fasilitas belajar Al-Quran gratis bagi anak-anak di lingkungan sekitar masjid.</p>
            <div class="progress-bar-bg"><div class="progress-bar-fill" style="width: 50%;"></div></div>
            <div style="display: flex; justify-content: space-between; font-size: 13px; font-weight: 600;">
                <span>Terkumpul: Rp 50M</span>
                <span>Target: Rp 100M</span>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
