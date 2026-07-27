<?= $this->extend('layouts/public') ?>

<?= $this->section('title') ?>Beranda Portal Utama<?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- Hero Section -->
<section class="hero-banner">
    <h1>Selamat Datang di Portal MasjidCMS</h1>
    <p>Pusat informasi kegiatan ibadah, jadwal sholat, kajian syariah, serta pengelolaan donasi transparan berbasis Akuntansi Syariah.</p>
    <a href="/donasi" class="btn-portal btn-portal-primary" style="padding: 12px 28px; font-size: 16px;">Salurkan Donasi / Infaq Online ›</a>
</section>

<!-- Prayer Schedule Cards Widget -->
<section style="max-width: 1120px; margin: 0 auto;">
    <div class="prayer-grid">
        <div class="prayer-card">
            <div class="prayer-name">SUBUH</div>
            <div class="prayer-time">04:38</div>
        </div>
        <div class="prayer-card">
            <div class="prayer-name">DZUHUR</div>
            <div class="prayer-time">12:02</div>
        </div>
        <div class="prayer-card">
            <div class="prayer-name">ASHAR</div>
            <div class="prayer-time">15:24</div>
        </div>
        <div class="prayer-card">
            <div class="prayer-name">MAGHRIB</div>
            <div class="prayer-time">18:05</div>
        </div>
        <div class="prayer-card">
            <div class="prayer-name">ISYA</div>
            <div class="prayer-time">19:18</div>
        </div>
    </div>
</section>

<!-- Main Portal Container -->
<section class="portal-container">
    <h2 style="font-size: 24px; font-weight: 700; margin-bottom: 24px; text-align: center;">Program Unggulan Masjid</h2>
    <div class="card-grid">
        <div class="portal-card">
            <span style="font-size: 12px; font-weight: 600; color: var(--primary-600); text-transform: uppercase;">Pembangunan</span>
            <h3 style="font-size: 18px; margin: 8px 0;">Renovasi Menara & Kanopi Masjid</h3>
            <p style="font-size: 14px; color: var(--text-muted); margin-bottom: 12px;">Program perluasan area sholat outdoor dan renovasi menara masjid.</p>
            <div class="progress-bar-bg"><div class="progress-bar-fill" style="width: 75%;"></div></div>
            <div style="display: flex; justify-content: space-between; font-size: 13px; font-weight: 600;">
                <span>Terkumpul: Rp 75M</span>
                <span>Target: Rp 100M</span>
            </div>
        </div>

        <div class="portal-card">
            <span style="font-size: 12px; font-weight: 600; color: var(--primary-600); text-transform: uppercase;">Sosial & ZIS</span>
            <h3 style="font-size: 18px; margin: 8px 0;">Beasiswa Santri & Anak Yatim</h3>
            <p style="font-size: 14px; color: var(--text-muted); margin-bottom: 12px;">Bantuan pendidikan bulanan untuk santri dan anak yatim dhuafa.</p>
            <div class="progress-bar-bg"><div class="progress-bar-fill" style="width: 60%;"></div></div>
            <div style="display: flex; justify-content: space-between; font-size: 13px; font-weight: 600;">
                <span>Terkumpul: Rp 18M</span>
                <span>Target: Rp 30M</span>
            </div>
        </div>

        <div class="portal-card">
            <span style="font-size: 12px; font-weight: 600; color: var(--primary-600); text-transform: uppercase;">Dakwah & Kajian</span>
            <h3 style="font-size: 18px; margin: 8px 0;">Kajian Rutin Tafsir Al-Quran</h3>
            <p style="font-size: 14px; color: var(--text-muted); margin-bottom: 12px;">Setiap Hari Sabtu Ba'da Subuh bersama Ustadz Pengasuh.</p>
            <a href="/berita" class="btn-portal btn-portal-primary" style="margin-top: 16px; padding: 6px 14px; font-size: 13px;">Lihat Jadwal Kajian</a>
        </div>
    </div>
</section>
<?= $this->endSection() ?>
