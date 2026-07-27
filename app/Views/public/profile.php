<?= $this->extend('layouts/public') ?>

<?= $this->section('title') ?>Profil & Visi Misi Masjid<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="portal-container" style="max-width: 800px;">
    <h1 style="font-size: 28px; font-weight: 800; margin-bottom: 16px;">Profil & Sejarah Masjid</h1>
    <div style="background: white; border: 1px solid var(--border-light); border-radius: var(--radius-lg); padding: 32px; margin-bottom: 32px;">
        <h2 style="font-size: 20px; font-weight: 700; color: var(--primary-700); margin-bottom: 12px;">Sejarah Singkat</h2>
        <p style="margin-bottom: 16px;">Masjid Agung Darussalam didirikan pada tahun 1995 di atas tanah wakaf seluas 2.500 m2. Sejak awal berdirinya, masjid ini menjadi pusat keagamaan, sosial, dan ekonomi syariah bagi warga sekitar.</p>
        
        <h2 style="font-size: 20px; font-weight: 700; color: var(--primary-700); margin-top: 24px; margin-bottom: 12px;">Visi & Misi</h2>
        <p style="font-weight: 600;">Visi:</p>
        <p style="margin-bottom: 12px;">Menjadi pusat peradaban Islam yang mandiri, makmur, transparan, dan memberikan maslahat bagi seluruh umat.</p>
        
        <p style="font-weight: 600;">Misi:</p>
        <ul style="margin-left: 20px; margin-bottom: 16px;">
            <li>Menyelenggarakan ibadah dan kegiatan syiar Islam yang sesuai Al-Quran & Sunnah.</li>
            <li>Mengelola dana ZISWAF secara transparan berbasis teknologi MasjidCMS.</li>
            <li>Memberdayakan ekonomi jamaah dan anak yatim dhuafa.</li>
        </ul>
    </div>
</div>
<?= $this->endSection() ?>
