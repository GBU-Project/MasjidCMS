<!-- Quick Access Toolbar Component UI 2.0 (Unified Modern Bar) -->
<?php
    $qaSettings = $settings ?? [];
    $showQuickAccess = (($qaSettings['show_quickaccess_section'] ?? '1') === '1');
    if (!$showQuickAccess) return;

    $items = [
        'show_quickaccess_jadwal'  => ['url' => 'jadwal-shalat', 'label' => 'Jadwal Sholat', 'icon' => 'clock'],
        'show_quickaccess_kajian'  => ['url' => 'berita', 'label' => 'Kajian & Taklim', 'icon' => 'book'],
        'show_quickaccess_program' => ['url' => 'program', 'label' => 'Program DKM', 'icon' => 'users'],
        'show_quickaccess_layanan' => ['url' => 'layanan', 'label' => 'Layanan Jamaah', 'icon' => 'heart-handshake'],
        'show_quickaccess_donasi'  => ['url' => 'donasi', 'label' => 'Infaq & Zakat', 'icon' => 'credit-card', 'accent' => true],
        'show_quickaccess_galeri'  => ['url' => 'galeri', 'label' => 'Galeri Foto', 'icon' => 'image'],
        'show_quickaccess_kontak'  => ['url' => 'kontak', 'label' => 'Kontak DKM', 'icon' => 'phone'],
    ];

    $activeItems = [];
    foreach ($items as $key => $item) {
        if (($qaSettings[$key] ?? '1') === '1') {
            $activeItems[] = $item;
        }
    }

    if (empty($activeItems)) return;
?>

<section class="quick-access-section">
    <div class="container">
        <div class="quick-access-bar">
            <?php foreach ($activeItems as $idx => $item): ?>
                <a href="<?= site_url($item['url']) ?>" class="quick-bar-item <?= !empty($item['accent']) ? 'quick-bar-accent' : '' ?>">
                    <div class="quick-bar-icon">
                        <?php if ($item['icon'] === 'clock'): ?>
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        <?php elseif ($item['icon'] === 'book'): ?>
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                        <?php elseif ($item['icon'] === 'users'): ?>
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        <?php elseif ($item['icon'] === 'heart-handshake'): ?>
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>
                        <?php elseif ($item['icon'] === 'credit-card'): ?>
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                        <?php elseif ($item['icon'] === 'image'): ?>
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                        <?php elseif ($item['icon'] === 'phone'): ?>
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                        <?php endif; ?>
                    </div>
                    <span class="quick-bar-label"><?= esc($item['label']) ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
