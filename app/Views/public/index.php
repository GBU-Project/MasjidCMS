<?= $this->extend('layouts/public') ?>

<?= $this->section('title') ?>Portal Digital Mosque Experience — <?= esc($masjid['name'] ?? $masjidName ?? 'MasjidCMS') ?><?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Header is now rendered once by layouts/public.php for every page (TASK-022A) -->

<!-- 2. Hero Banner Section -->
<?= view('public/components/hero', ['masjid' => $masjid, 'prayerTimes' => $prayerTimes ?? [], 'prayerCity' => $prayerCity ?? 'Kota Masjid', 'donationSettings' => $donationSettings, 'settings' => $settings ?? []]) ?>

<!-- 3. Quick Access Toolbar -->
<?= view('public/components/quick_access') ?>

<?php
// Sections managed by Homepage Manager & Section Control (single source of truth
// for visibility + ordering). 'hero'/'prayer' are rendered above as part of the
// hero block and are not independently toggleable; 'profile' has no dedicated
// public block yet, so it is intentionally skipped here.
$visibility = $sectionVisibility ?? [];
$sectionRenderers = [
    'program'   => fn () => view('public/components/program_section', ['programList' => $activePrograms, 'settings' => $settings ?? []]),
    'layanan'   => fn () => view('public/components/layanan_section', ['layananList' => $activeServices, 'settings' => $settings ?? []]),
    'pengurus'  => fn () => view('public/components/pengurus_section', ['pengurusList' => $pengurusList, 'settings' => $settings ?? []]),
    'bidang'    => fn () => view('public/components/bidang_section', ['bidangList' => $bidangList ?? [], 'settings' => $settings ?? []]),
    'kajian'    => fn () => view('public/components/kajian_section', ['kajianList' => $kajianList, 'settings' => $settings ?? []]),
    'agenda'    => fn () => view('public/components/agenda_section', ['agendaList' => $agendaList, 'settings' => $settings ?? []]),
    'berita'    => fn () => view('public/components/berita_section', ['postsList' => $latestPosts, 'settings' => $settings ?? []]),
    'financial' => fn () => view('public/components/financial_section', ['financialSummary' => $financialSummary, 'programCount' => count($activePrograms), 'settings' => $settings ?? []]),
    'donation'  => fn () => view('public/components/donation_section', ['donationSettings' => $donationSettings, 'masjid' => $masjid, 'settings' => $settings ?? []]),
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

<!-- Footer is now rendered once by layouts/public.php for every page (TASK-022A) -->

<?= $this->endSection() ?>