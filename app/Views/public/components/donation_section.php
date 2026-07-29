<!-- Donation CTA Banner Section UI 2.0 -->
<?php $sectionSettings = $settings ?? []; ?>
<section class="section-wrapper donation-cta-section" id="donasi">
    <div class="donation-overlay" style="background-image: url('<?= !empty($donationSettings['donation_bg_image']) ? esc($donationSettings['donation_bg_image']) : base_url('assets/images/hero-bg.jpg') ?>');"></div>
    <div class="container donation-cta-shell">
        <span class="section-tag donation-tag">
            💳 <?= esc($donationSettings['donation_subtitle'] ?? ($sectionSettings['donation_tag'] ?? 'Infaq & Sedekah Online')) ?>
        </span>

        <h2>
            <?= esc(str_replace('{masjidName}', $masjid['name'] ?? 'Masjid', $donationSettings['donation_title'] ?? ($sectionSettings['donation_title'] ?? 'Mari Berinfaq Melalui {masjidName}'))) ?>
        </h2>

        <p>
            <?= esc(str_replace('{masjidName}', $masjid['name'] ?? 'Masjid', $donationSettings['donation_description'] ?? ($sectionSettings['donation_description'] ?? 'Bantu operasional masjid dan kegiatan sosial keumatan melalui transfer bank atau QRIS resmi.'))) ?>
        </p>

        <div class="donation-actions">
            <a href="<?= site_url('donasi') ?>" class="btn-ui2 btn-amber-ui2 donation-btn">
                <span>💚</span> <?= esc($donationSettings['donation_btn_text'] ?? ($sectionSettings['donation_cta'] ?? 'Salurkan Donasi Sekarang')) ?>
            </a>
            <a href="<?= site_url('transparansi') ?>" class="btn-ui2 btn-secondary-ui2">
                📋 <?= esc($sectionSettings['donation_secondary_cta'] ?? 'Lihat Rekening Resmi') ?>
            </a>
        </div>
    </div>
</section>
