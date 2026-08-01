<!-- Unified Public Header UI 2.0 (TASK-022A) -->
<?php
    // Everything here is driven by $masjid (Website Settings + Media
    // Library, via PublicPortalController::resolveMasjidProfile()) --
    // no hardcoded mosque name/logo. Nav labels are structural (routes),
    // not mosque-specific content, so they stay in code like any other
    // site's fixed navigation.
    $activePage = $activePage ?? '';
?>
<header class="site-header-v2">
    <div class="container header-v2-inner">
        <a href="<?= site_url('/') ?>" class="site-logo-v2">
            <?php if (!empty($masjid['logo_url'])): ?>
                <img src="<?= esc($masjid['logo_url']) ?>" alt="<?= esc($masjid['name'] ?? 'Logo Masjid') ?>" class="site-logo-v2-img">
            <?php else: ?>
                <span class="site-logo-v2-fallback">🕌</span>
            <?php endif; ?>
            <span class="site-logo-v2-text"><?= esc($masjid['name'] ?? 'MasjidCMS') ?></span>
        </a>

        <button type="button" class="nav-hamburger-btn" id="navHamburgerBtn" aria-label="Buka menu navigasi" aria-expanded="false">
            <span></span><span></span><span></span>
        </button>

        <nav class="nav-v2" id="navV2">
            <ul class="nav-v2-list">
                <li><a href="<?= site_url('/') ?>" class="<?= $activePage === 'home' ? 'active' : '' ?>">Beranda</a></li>

                <li class="nav-v2-dropdown">
                    <button type="button" class="nav-v2-dropdown-trigger <?= in_array($activePage, ['profile', 'org_structure']) ? 'active' : '' ?>">Tentang Kami <span>▾</span></button>
                    <ul class="nav-v2-dropdown-menu">
                        <li><a href="<?= site_url('profil') ?>">Profil Masjid</a></li>
                        <li><a href="<?= site_url('profil#sejarah') ?>">Sejarah</a></li>
                        <li><a href="<?= site_url('profil#visi-misi') ?>">Visi & Misi</a></li>
                        <li><a href="<?= site_url('struktur-organisasi#struktur-dkm') ?>">Struktur DKM</a></li>
                        <li><a href="<?= site_url('struktur-organisasi#bidang') ?>">Bidang</a></li>
                    </ul>
                </li>

                <li class="nav-v2-dropdown">
                    <button type="button" class="nav-v2-dropdown-trigger <?= in_array($activePage, ['news', 'agenda', 'programs']) ? 'active' : '' ?>">Direktori <span>▾</span></button>
                    <ul class="nav-v2-dropdown-menu">
                        <li><a href="<?= site_url('berita') ?>">Berita</a></li>
                        <li><a href="<?= site_url('agenda') ?>">Agenda</a></li>
                        <li><a href="<?= site_url('berita#kajian') ?>">Jadwal Kajian</a></li>
                        <li><a href="<?= site_url('program') ?>">Program</a></li>
                    </ul>
                </li>

                <li class="nav-v2-dropdown">
                    <button type="button" class="nav-v2-dropdown-trigger <?= $activePage === 'services' ? 'active' : '' ?>">Layanan <span>▾</span></button>
                    <ul class="nav-v2-dropdown-menu">
                        <li><a href="<?= site_url('layanan') ?>">Ambulans</a></li>
                        <li><a href="<?= site_url('layanan') ?>">Layanan Jenazah</a></li>
                        <li><a href="<?= site_url('layanan') ?>">Pernikahan</a></li>
                        <li><a href="<?= site_url('layanan') ?>">Mualaf</a></li>
                        <li><a href="<?= site_url('layanan') ?>">ZISWAF</a></li>
                    </ul>
                </li>

                <li class="nav-v2-dropdown">
                    <button type="button" class="nav-v2-dropdown-trigger <?= $activePage === 'gallery' ? 'active' : '' ?>">Galeri <span>▾</span></button>
                    <ul class="nav-v2-dropdown-menu">
                        <li><a href="<?= site_url('galeri?type=photo') ?>">Foto</a></li>
                        <li><a href="<?= site_url('galeri?type=video') ?>">Video</a></li>
                        <li><a href="<?= site_url('galeri?type=document') ?>">Dokumen</a></li>
                    </ul>
                </li>

                <li><a href="<?= site_url('kontak') ?>" class="<?= $activePage === 'contact' ? 'active' : '' ?>">Kontak</a></li>
            </ul>

            <!-- Right Header Utilities: Search, Prayer Time, Login -->
            <div class="nav-v2-utilities">
                <form action="<?= site_url('cari') ?>" method="GET" class="nav-v2-search">
                    <input type="text" name="q" placeholder="Cari..." aria-label="Cari">
                    <button type="submit" aria-label="Cari">🔍</button>
                </form>

                <a href="<?= site_url('jadwal-shalat') ?>" class="nav-v2-prayer-widget" id="navPrayerWidget" title="Jadwal Sholat Hari Ini">
                    <span class="nav-v2-prayer-icon">🕌</span>
                    <span class="nav-v2-prayer-text">
                        <span class="nav-v2-prayer-label" id="navPrayerLabel">Jadwal Sholat</span>
                        <span class="nav-v2-prayer-time" id="navPrayerTime">--:--</span>
                    </span>
                </a>

                <a href="<?= site_url('login') ?>" class="nav-v2-login-btn">Login</a>
            </div>
        </nav>
    </div>
</header>

<script>
    (function () {
        // Dropdown toggling (click-based so it works on touch devices too).
        document.querySelectorAll('.nav-v2-dropdown-trigger').forEach(function (trigger) {
            trigger.addEventListener('click', function (e) {
                e.stopPropagation();
                var dropdown = trigger.closest('.nav-v2-dropdown');
                var wasOpen = dropdown.classList.contains('open');
                document.querySelectorAll('.nav-v2-dropdown.open').forEach(function (d) { d.classList.remove('open'); });
                if (!wasOpen) dropdown.classList.add('open');
            });
        });
        document.addEventListener('click', function () {
            document.querySelectorAll('.nav-v2-dropdown.open').forEach(function (d) { d.classList.remove('open'); });
        });

        // Mobile hamburger toggle.
        var hamburger = document.getElementById('navHamburgerBtn');
        var nav = document.getElementById('navV2');
        if (hamburger && nav) {
            hamburger.addEventListener('click', function () {
                var isOpen = nav.classList.toggle('nav-v2-open');
                hamburger.classList.toggle('open', isOpen);
                hamburger.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            });
        }

        // Prayer Time utility: fetched client-side so it appears on every
        // page via this one shared header, without every controller action
        // needing to compute/pass prayer times individually.
        fetch('<?= site_url('api/prayer-times-today') ?>')
            .then(function (r) { return r.json(); })
            .then(function (data) {
                var times = data.times || {};
                var order = ['fajr', 'dhuhr', 'asr', 'maghrib', 'isha'];
                var labels = { fajr: 'Subuh', dhuhr: 'Dzuhur', asr: 'Ashar', maghrib: 'Maghrib', isha: 'Isya' };
                var now = new Date();
                var nowHM = String(now.getHours()).padStart(2, '0') + ':' + String(now.getMinutes()).padStart(2, '0');
                var next = null;
                for (var i = 0; i < order.length; i++) {
                    if (times[order[i]] && times[order[i]] > nowHM) { next = order[i]; break; }
                }
                next = next || order[0];
                var labelEl = document.getElementById('navPrayerLabel');
                var timeEl = document.getElementById('navPrayerTime');
                if (labelEl && timeEl && times[next]) {
                    labelEl.textContent = labels[next];
                    timeEl.textContent = times[next];
                }
            })
            .catch(function () { /* Prayer widget stays on its default label if this fails. */ });
    })();
</script>
