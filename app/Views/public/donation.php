<?= $this->extend('layouts/public') ?>

<?= $this->section('title') ?>Donasi & Infaq Online<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="portal-container" style="max-width: 900px;">
    <h1 style="font-size: 28px; font-weight: 800; margin-bottom: 12px; text-align: center;">Salurkan Donasi, Infaq, & Zakat Anda</h1>
    <p style="text-align: center; color: var(--text-muted); margin-bottom: 32px;">Seluruh dana donasi dikelola secara terpisah per kantong dana (Fund Accounting) dan dilaporkan secara berkala.</p>

    <div class="card-grid">
        <div class="portal-card">
            <h3 style="font-size: 18px; color: var(--primary-700); margin-bottom: 8px;">1. Infaq & Sedekah Kas Umum</h3>
            <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 12px;">Untuk operasional masjid, listrik, air, dan kebersihan.</p>
            <div style="background: var(--bg-app); padding: 12px; border-radius: 6px; font-size: 13px;">
                <p><strong>Bank Syariah Indonesia (BSI)</strong></p>
                <p class="stat-mono" style="font-size: 16px; font-weight: 700; color: var(--text-main);">700-1234-567</p>
                <p>a.n. Masjid Agung Darussalam</p>
            </div>
        </div>

        <div class="portal-card">
            <h3 style="font-size: 18px; color: var(--primary-700); margin-bottom: 8px;">2. Dana Zakat Maal & Fitrah</h3>
            <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 12px;">Khusus disalurkan kepada 8 asnaf penerima zakat (Restricted Fund).</p>
            <div style="background: var(--bg-app); padding: 12px; border-radius: 6px; font-size: 13px;">
                <p><strong>Bank Syariah Indonesia (BSI)</strong></p>
                <p class="stat-mono" style="font-size: 16px; font-weight: 700; color: var(--text-main);">700-9876-543</p>
                <p>a.n. Bazis Masjid Darussalam</p>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
