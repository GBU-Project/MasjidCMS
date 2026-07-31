<?= $this->extend('layouts/public') ?>

<?= $this->section('title') ?>Portal Digital Mosque Experience — <?= esc($masjid['name'] ?? $masjidName ?? 'MasjidCMS') ?><?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- 1. Header & Navigation -->
<?= view('public/components/header', ['activePage' => 'home', 'masjid' => $masjid, 'settings' => $settings ?? []]) ?>

<!-- 2. Hero Banner Section -->
<?= view('public/components/hero', ['masjid' => $masjid, 'donationSettings' => $donationSettings, 'settings' => $settings ?? [], 'prayerTimes' => $prayerTimes ?? [], 'prayerCity' => $prayerCity ?? 'Kota Masjid']) ?>

<!-- 3. Quick Access Toolbar -->
<?= view('public/components/quick_access') ?>

<?php
// Sections managed by Homepage Manager & Section Control (single source of truth
// for visibility + ordering). 'hero'/'prayer' are rendered above as part of the
// hero block and are not independently toggleable; 'profile' has no dedicated
// public block yet, so it is intentionally skipped here.
$visibility = $sectionVisibility ?? [];
$sectionRenderers = [
    'program'  => fn () => view('public/components/program_section', ['programList' => $activePrograms, 'settings' => $settings ?? []]),
    'layanan'  => fn () => view('public/components/layanan_section', ['layananList' => $activeServices, 'settings' => $settings ?? []]),
    'bidang'   => fn () => view('public/components/bidang_section', ['bidangList' => $bidangList, 'settings' => $settings ?? []]),
    'pengurus' => fn () => view('public/components/pengurus_section', ['pengurusList' => $pengurusList, 'settings' => $settings ?? []]),
    'kajian'   => fn () => view('public/components/kajian_section', ['kajianList' => $kajianList, 'settings' => $settings ?? []]),
    'agenda'   => fn () => view('public/components/agenda_section', ['agendaList' => $agendaList, 'settings' => $settings ?? []]),
    'donation' => fn () => view('public/components/donation_section', ['donationSettings' => $donationSettings, 'masjid' => $masjid, 'settings' => $settings ?? []]),
];

foreach ($sectionOrder as $sectionKey) {
    if (!isset($sectionRenderers[$sectionKey])) {
        continue; // 'hero', 'prayer', 'profile', 'gallery' (no public block yet), or unknown keys
    }
    if (($visibility[$sectionKey] ?? true) === false) {
        continue; // Hidden via Homepage Manager
    }
    echo $sectionRenderers[$sectionKey]();
}
?>

<!-- Berita & Transparansi Keuangan are always shown; not yet part of Homepage Manager's section list -->
<?= view('public/components/berita_section', ['postsList' => $latestPosts, 'settings' => $settings ?? []]) ?>
<?= view('public/components/financial_section', ['financialSummary' => $financialSummary, 'programCount' => count($activePrograms), 'settings' => $settings ?? []]) ?>

<!-- Rich Footer -->
<?= view('public/components/footer', ['masjid' => $masjid]) ?>

<?= $this->endSection() ?>