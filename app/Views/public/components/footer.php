<!-- Rich Footer Component UI 2.0 -->
<footer style="background: var(--slate-900); color: var(--slate-400); border-top: 1px solid var(--slate-800); padding: 64px 0 32px; font-size: 14px;">
    <div class="container" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 40px; margin-bottom: 48px;">
        <div>
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
                <div style="width: 40px; height: 40px; border-radius: var(--radius-md); background: var(--emerald-700); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 20px;">🕌</div>
                <span style="font-size: 20px; font-weight: 800; color: #fff; font-family: var(--font-heading);"><?= esc($masjid['name'] ?? 'MasjidCMS') ?></span>
            </div>
            <p style="line-height: 1.6; margin-bottom: 20px;">
                <?= esc($masjid['address'] ?? 'Pusat ibadah dan pelayanan jamaah terpadu.') ?>
            </p>
            <div style="color: var(--emerald-500); font-weight: 600;">
                📍 <?= esc($masjid['city'] ?? 'Kota') ?>, <?= esc($masjid['province'] ?? 'Provinsi') ?>
            </div>
        </div>

        <div>
            <h4 style="color: #fff; font-size: 16px; margin-bottom: 16px;">Navigasi Cepat</h4>
            <ul style="list-style: none; display: flex; flex-direction: column; gap: 10px;">
                <li><a href="<?= site_url('/') ?>" style="color: var(--slate-400); text-decoration: none;">Beranda Portal</a></li>
                <li><a href="<?= site_url('profil') ?>" style="color: var(--slate-400); text-decoration: none;">Profil & Sejarah</a></li>
                <li><a href="<?= site_url('berita') ?>" style="color: var(--slate-400); text-decoration: none;">Berita & Warta</a></li>
                <li><a href="<?= site_url('program') ?>" style="color: var(--slate-400); text-decoration: none;">Program DKM</a></li>
                <li><a href="<?= site_url('layanan') ?>" style="color: var(--slate-400); text-decoration: none;">Layanan Jamaah</a></li>
            </ul>
        </div>

        <div>
            <h4 style="color: #fff; font-size: 16px; margin-bottom: 16px;">Transparansi & Donasi</h4>
            <ul style="list-style: none; display: flex; flex-direction: column; gap: 10px;">
                <li><a href="<?= site_url('donasi') ?>" style="color: var(--slate-400); text-decoration: none;">Infaq & Sedekah</a></li>
                <li><a href="<?= site_url('transparansi') ?>" style="color: var(--slate-400); text-decoration: none;">Laporan Keuangan Realtime</a></li>
                <li><a href="<?= site_url('galeri') ?>" style="color: var(--slate-400); text-decoration: none;">Galeri Dokumentasi</a></li>
                <li><a href="<?= site_url('admin/dashboard') ?>" style="color: var(--slate-400); text-decoration: none;">Login Operator DKM</a></li>
            </ul>
        </div>

        <div>
            <h4 style="color: #fff; font-size: 16px; margin-bottom: 16px;">Hubungi DKM</h4>
            <div style="display: flex; flex-direction: column; gap: 12px;">
                <div>📞 <strong>Telepon:</strong> <?= esc($masjid['phone'] ?? '08xxxxxxxxxx') ?></div>
                <div>📧 <strong>Email:</strong> <?= esc($masjid['email'] ?? 'info@masjid.id') ?></div>
                <div>🌐 <strong>Website:</strong> <?= base_url() ?></div>
            </div>
        </div>
    </div>

    <div class="container" style="border-top: 1px solid var(--slate-800); padding-top: 24px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; font-size: 13px;">
        <div>
            © <?= date('Y') ?> <strong><?= esc($masjid['name'] ?? 'MasjidCMS') ?></strong>. All Rights Reserved. Built with <span style="color: var(--emerald-500);">Digital Mosque Platform</span>.
        </div>
        <div style="display: flex; gap: 16px;">
            <a href="<?= site_url('profil') ?>" style="color: var(--slate-400);">Privacy Policy</a>
            <a href="<?= site_url('kontak') ?>" style="color: var(--slate-400);">Contact Support</a>
        </div>
    </div>
</footer>
