<!-- Unified Public Header UI 2.0 (TASK-022A) -->
<?php $activePage = $activePage ?? ''; ?>
<header class="site-header">
    <div class="container header-inner">
        <a href="<?= site_url('/') ?>" class="site-logo">
            <?php if (!empty($masjid['logo_url'])): ?>
                <img src="<?= esc($masjid['logo_url']) ?>" alt="<?= esc($masjid['name'] ?? 'Logo Masjid') ?>" class="site-logo-img">
            <?php else: ?>
                <div class="site-logo-icon">🕌</div>
            <?php endif; ?>
            <span class="site-logo-text"><?= esc($masjid['name'] ?? 'MasjidCMS') ?></span>
        </a>

        <button type="button" class="nav-hamburger-btn" id="navHamburgerBtn" aria-label="Buka menu navigasi" aria-expanded="false">
            <span></span><span></span><span></span>
        </button>

        <nav class="main-nav" id="navV2">
            <a href="<?= site_url('/') ?>" class="nav-link <?= $activePage === 'home' ? 'active' : '' ?>">Beranda</a>

            <div class="nav-dropdown">
                <button type="button" class="nav-link nav-dropdown-trigger <?= in_array($activePage, ['profile', 'org_structure']) ? 'active' : '' ?>">Tentang Kami <span>▾</span></button>
                <div class="nav-dropdown-menu">
                    <a href="<?= site_url('profil') ?>">Profil Masjid</a>
                    <a href="<?= site_url('profil#sejarah') ?>">Sejarah</a>
                    <a href="<?= site_url('profil#visi-misi') ?>">Visi & Misi</a>
                    <a href="<?= site_url('struktur-organisasi#struktur-dkm') ?>">Struktur DKM</a>
                    <a href="<?= site_url('struktur-organisasi#bidang') ?>">Bidang</a>
                </div>
            </div>

            <div class="nav-dropdown">
                <button type="button" class="nav-link nav-dropdown-trigger <?= in_array($activePage, ['news', 'agenda', 'programs']) ? 'active' : '' ?>">Direktori <span>▾</span></button>
                <div class="nav-dropdown-menu">
                    <a href="<?= site_url('berita') ?>">Berita</a>
                    <a href="<?= site_url('agenda') ?>">Agenda</a>
                    <a href="<?= site_url('berita#kajian') ?>">Jadwal Kajian</a>
                    <a href="<?= site_url('program') ?>">Program</a>
                </div>
            </div>

            <div class="nav-dropdown">
                <button type="button" class="nav-link nav-dropdown-trigger <?= $activePage === 'services' ? 'active' : '' ?>">Layanan <span>▾</span></button>
                <div class="nav-dropdown-menu">
                    <a href="<?= site_url('layanan') ?>">Ambulans</a>
                    <a href="<?= site_url('layanan') ?>">Layanan Jenazah</a>
                    <a href="<?= site_url('layanan') ?>">Pernikahan</a>
                    <a href="<?= site_url('layanan') ?>">Mualaf</a>
                    <a href="<?= site_url('layanan') ?>">ZISWAF</a>
                </div>
            </div>

            <div class="nav-dropdown">
                <button type="button" class="nav-link nav-dropdown-trigger <?= $activePage === 'gallery' ? 'active' : '' ?>">Galeri <span>▾</span></button>
                <div class="nav-dropdown-menu">
                    <a href="<?= site_url('galeri?type=photo') ?>">Foto</a>
                    <a href="<?= site_url('galeri?type=video') ?>">Video</a>
                    <a href="<?= site_url('galeri?type=document') ?>">Dokumen</a>
                </div>
            </div>

            <a href="<?= site_url('kontak') ?>" class="nav-link <?= $activePage === 'contact' ? 'active' : '' ?>">Kontak</a>
        </nav>

        <!-- Right Header Utilities: Search, Prayer Time, Login -->
        <div class="header-cta">
            <form action="<?= site_url('cari') ?>" method="GET" class="nav-search">
                <input type="text" name="q" placeholder="Cari..." aria-label="Cari">
                <button type="submit" aria-label="Cari">🔍</button>
            </form>

            <a href="<?= site_url('jadwal-shalat') ?>" class="nav-prayer-widget" id="navPrayerWidget" title="Jadwal Sholat Hari Ini">
                <span class="nav-prayer-icon">🕌</span>
                <span class="nav-prayer-text">
                    <span class="nav-prayer-label" id="navPrayerLabel">Jadwal Sholat</span>
                    <span class="nav-prayer-time" id="navPrayerTime">--:--</span>
                </span>
            </a>

            <a href="<?= site_url('login') ?>" class="btn-ui2 btn-primary-ui2 nav-login-btn">Login</a>
        </div>
    </div>
</header>

<script>
    (function () {
        document.querySelectorAll('.nav-dropdown-trigger').forEach(function (trigger) {
            trigger.addEventListener('click', function (e) {
                e.stopPropagation();
                var dropdown = trigger.closest('.nav-dropdown');
                var wasOpen = dropdown.classList.contains('open');
                document.querySelectorAll('.nav-dropdown.open').forEach(function (d) { d.classList.remove('open'); });
                if (!wasOpen) dropdown.classList.add('open');
            });
        });
        document.addEventListener('click', function () {
            document.querySelectorAll('.nav-dropdown.open').forEach(function (d) { d.classList.remove('open'); });
        });

        var hamburger = document.getElementById('navHamburgerBtn');
        var nav = document.getElementById('navV2');
        if (hamburger && nav) {
            hamburger.addEventListener('click', function () {
                var isOpen = nav.classList.toggle('main-nav-open');
                hamburger.classList.toggle('open', isOpen);
                hamburger.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            });
        }

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
