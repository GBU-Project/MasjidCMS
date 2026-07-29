<?= $this->extend('layouts/public') ?>

<?= $this->section('title') ?>Portal Digital Mosque Experience — <?= esc($masjid['name'] ?? $masjidName ?? 'MasjidCMS') ?><?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- 1. Header & Navigation -->
<?= view('public/components/header', ['activePage' => 'home', 'masjid' => $masjid, 'settings' => $settings ?? []]) ?>

<!-- 2. Hero Banner Section -->
<?= view('public/components/hero', ['masjid' => $masjid, 'donationSettings' => $donationSettings, 'settings' => $settings ?? []]) ?>

<!-- 3. Quick Access Toolbar -->
<?= view('public/components/quick_access') ?>

<!-- 4. Highlight Kajian & Agenda Taklim -->
<?= view('public/components/kajian_section', ['kajianList' => $kajianList, 'settings' => $settings ?? []]) ?>

<!-- 5. Program Unggulan DKM -->
<?= view('public/components/program_section', ['programList' => $activePrograms, 'settings' => $settings ?? []]) ?>

<!-- 6. Layanan Masjid & Jamaah -->
<?= view('public/components/layanan_section', ['layananList' => $activeServices, 'settings' => $settings ?? []]) ?>

<!-- 7. Pengurus & Tokoh DKM -->
<?= view('public/components/pengurus_section', ['pengurusList' => $pengurusList, 'settings' => $settings ?? []]) ?>

<!-- 8. Berita & Warta Jamaah -->
<?= view('public/components/berita_section', ['postsList' => $latestPosts, 'settings' => $settings ?? []]) ?>

<!-- 9. Transparansi Keuangan Realtime -->
<?= view('public/components/financial_section', ['financialSummary' => $financialSummary, 'programCount' => count($activePrograms), 'settings' => $settings ?? []]) ?>

<!-- 10. Donasi & Infaq CTA Banner -->
<?= view('public/components/donation_section', ['donationSettings' => $donationSettings, 'masjid' => $masjid, 'settings' => $settings ?? []]) ?>

<!-- 11. Rich Footer -->
<?= view('public/components/footer', ['masjid' => $masjid]) ?>

<?= $this->endSection() ?>
