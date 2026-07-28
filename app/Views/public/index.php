<?= $this->extend('layouts/public') ?>

<?= $this->section('title') ?>Beranda Portal Utama — <?= esc($masjidName) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>

<?php
helper('url');
// Section rendering map
$order = $sectionOrder ?? ['hero', 'prayer', 'profile', 'program', 'layanan', 'pengurus', 'kajian', 'gallery', 'donation'];
?>

<div class="homepage-sections-container">
    <?php foreach ($order as $secKey): ?>
        <?php if ($secKey === 'hero'): ?>
            <!-- Hero Section -->
            <section class="hero-banner" style="margin-bottom: 32px;">
                <h1>Selamat Datang di <?= esc($masjidName) ?></h1>
                <p>Pusat informasi kegiatan ibadah, jadwal sholat, kajian syariah, serta pengelolaan donasi transparan berbasis Akuntansi Syariah.</p>
                <a href="<?= site_url('donasi') ?>" class="btn-portal btn-portal-primary" style="padding: 12px 28px; font-size: 16px;">Salurkan Donasi / Infaq Online ›</a>
            </section>

        <?php elseif ($secKey === 'prayer'): ?>
            <!-- Prayer Schedule Cards Widget -->
            <section style="max-width: 1120px; margin: 0 auto 40px;">
                <div class="prayer-grid">
                    <div class="prayer-card"><div class="prayer-name">SUBUH</div><div class="prayer-time">04:38</div></div>
                    <div class="prayer-card"><div class="prayer-name">DZUHUR</div><div class="prayer-time">12:02</div></div>
                    <div class="prayer-card"><div class="prayer-name">ASHAR</div><div class="prayer-time">15:24</div></div>
                    <div class="prayer-card"><div class="prayer-name">MAGHRIB</div><div class="prayer-time">18:05</div></div>
                    <div class="prayer-card"><div class="prayer-name">ISYA</div><div class="prayer-time">19:18</div></div>
                </div>
            </section>

        <?php elseif ($secKey === 'profile' && ($settings['show_profile_section'] ?? '1') === '1'): ?>
            <!-- Profile Section -->
            <section style="max-width: 1100px; margin: 0 auto 48px; padding: 0 24px;">
                <div class="panel-card" style="padding: 32px; border-radius: 12px; background: linear-gradient(135deg, var(--bg-surface), white);">
                    <h2 style="font-size: 24px; font-weight: 800; color: var(--text-primary); margin-bottom: 12px;">🕌 Profil & Sejarah <?= esc($masjidName) ?></h2>
                    <p style="font-size: 15px; color: var(--text-secondary); line-height: 1.7; margin-bottom: 16px;">
                        Masjid Agung Darussalam berdiri sebagai pusat dakwah Islam, pendidikan generasi muda Rabbani, serta simpul pemberdayaan ekonomi dan sosial keumatan.
                    </p>
                    <a href="<?= site_url('profil') ?>" class="btn-portal btn-portal-primary" style="padding: 8px 18px; font-size: 13px; display: inline-block;">Selengkapnya Tentang Kami ›</a>
                </div>
            </section>

        <?php elseif ($secKey === 'program' && ($settings['show_program_section'] ?? '1') === '1' && !empty($activePrograms)): ?>
            <!-- Program Section -->
            <section style="max-width: 1100px; margin: 0 auto 48px; padding: 0 24px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                    <div>
                        <h2 style="font-size: 24px; font-weight: 800; color: var(--text-primary); margin-bottom: 4px;">🚩 Program & Kegiatan Masjid</h2>
                        <p style="font-size: 14px; color: var(--text-muted);">Program unggulan peribadatan, pendidikan, dan sosial keumatan.</p>
                    </div>
                    <a href="<?= site_url('program') ?>" class="btn-portal btn-portal-primary" style="padding: 8px 16px; font-size: 13px;">Lihat Semua Program ›</a>
                </div>

                <div class="card-grid">
                    <?php foreach ($activePrograms as $pr): ?>
                        <div class="portal-card">
                            <span style="font-size: 12px; font-weight: 600; color: var(--primary-600); text-transform: uppercase;">
                                <?= esc($pr['bidang_name'] ?? 'PROGRAM MASJID') ?>
                            </span>
                            <h3 style="font-size: 18px; margin: 8px 0; font-weight: 700; color: var(--text-primary);"><?= esc($pr['nama']) ?></h3>
                            <p style="font-size: 14px; color: var(--text-muted); margin-bottom: 12px; line-height: 1.5;">
                                <?= esc($pr['ringkasan'] ?? substr($pr['deskripsi'] ?? '', 0, 100)) ?>
                            </p>
                            <div style="font-size: 13px; color: var(--text-secondary); margin-bottom: 12px;">
                                📍 <strong>Lokasi:</strong> <?= esc($pr['lokasi'] ?? 'Masjid Utama') ?><br>
                                👤 <strong>PJ:</strong> <?= esc($pr['penanggung_jawab'] ?? 'DKM') ?>
                            </div>
                            <a href="<?= site_url('program') ?>" class="btn-portal btn-portal-primary" style="padding: 6px 14px; font-size: 13px; display: inline-block;">Detail Program</a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>

        <?php elseif ($secKey === 'layanan' && ($settings['show_layanan_section'] ?? '1') === '1' && !empty($activeServices)): ?>
            <!-- Layanan Section -->
            <section style="max-width: 1100px; margin: 0 auto 48px; padding: 0 24px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                    <div>
                        <h2 style="font-size: 24px; font-weight: 800; color: var(--text-primary); margin-bottom: 4px;">🤝 Katalog Layanan Masjid</h2>
                        <p style="font-size: 14px; color: var(--text-muted);">Pelayanan sosial kemasyarakatan gratis dan siaga untuk jamaah.</p>
                    </div>
                    <a href="<?= site_url('layanan') ?>" class="btn-portal btn-portal-primary" style="padding: 8px 16px; font-size: 13px;">Lihat Semua Layanan ›</a>
                </div>

                <div class="card-grid">
                    <?php foreach ($activeServices as $ls): ?>
                        <div class="portal-card">
                            <div style="font-size: 32px; margin-bottom: 8px;"><?= esc($ls['icon'] ?? '🤝') ?></div>
                            <h3 style="font-size: 18px; margin: 4px 0 8px; font-weight: 700; color: var(--text-primary);"><?= esc($ls['nama']) ?></h3>
                            <p style="font-size: 14px; color: var(--text-muted); margin-bottom: 12px; line-height: 1.5;">
                                <?= esc($ls['deskripsi'] ?? 'Layanan siaga DKM.') ?>
                            </p>
                            <div style="font-size: 13px; color: var(--text-secondary); margin-bottom: 12px;">
                                ⏰ <strong>Jam:</strong> <?= esc($ls['jam_layanan'] ?? '24 Jam') ?><br>
                                📞 <strong>Kontak:</strong> <?= esc($ls['kontak'] ?? '-') ?>
                            </div>
                            <a href="<?= site_url('layanan') ?>" class="btn-portal btn-portal-primary" style="padding: 6px 14px; font-size: 13px; display: inline-block;">Info Layanan</a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>

        <?php elseif ($secKey === 'pengurus' && ($settings['show_pengurus_section'] ?? '1') === '1' && !empty($pengurusList)): ?>
            <!-- Pengurus Section -->
            <section style="max-width: 1100px; margin: 0 auto 48px; padding: 0 24px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                    <div>
                        <h2 style="font-size: 24px; font-weight: 800; color: var(--text-primary); margin-bottom: 4px;">👔 Susunan Pengurus DKM</h2>
                        <p style="font-size: 14px; color: var(--text-muted);">Jajaran pimpinan dan pengelola operasional masjid.</p>
                    </div>
                    <a href="<?= site_url('struktur-organisasi') ?>" class="btn-portal btn-portal-primary" style="padding: 8px 16px; font-size: 13px;">Struktur Lengkap ›</a>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px;">
                    <?php foreach ($pengurusList as $p): ?>
                        <div class="panel-card" style="padding: 20px; text-align: center; border-radius: 12px;">
                            <div style="width: 64px; height: 64px; border-radius: 50%; background: var(--primary-100); color: var(--primary-700); font-weight: 700; font-size: 22px; display: flex; align-items: center; justify-content: center; margin: 0 auto 12px;">
                                <?= esc(substr($p['nama'], 0, 2)) ?>
                            </div>
                            <h3 style="font-size: 16px; font-weight: 700; margin-bottom: 2px; color: var(--text-primary);"><?= esc($p['nama']) ?></h3>
                            <span class="badge badge-blue" style="font-size: 11px; margin-bottom: 8px; inline-block;"><?= esc($p['jabatan']) ?></span>
                            <p style="font-size: 12px; color: var(--text-tertiary);"><?= esc($p['bidang_name'] ?? 'Umum') ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>

        <?php elseif ($secKey === 'kajian' && ($settings['show_kajian_section'] ?? '1') === '1' && !empty($latestPosts)): ?>
            <!-- Kajian & Berita Section -->
            <section style="max-width: 1100px; margin: 0 auto 48px; padding: 0 24px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                    <div>
                        <h2 style="font-size: 24px; font-weight: 800; color: var(--text-primary); margin-bottom: 4px;">📰 Berita & Jadwal Kajian</h2>
                        <p style="font-size: 14px; color: var(--text-muted);">Informasi warta keumatan dan jadwal taklim rutin.</p>
                    </div>
                    <a href="<?= site_url('berita') ?>" class="btn-portal btn-portal-primary" style="padding: 8px 16px; font-size: 13px;">Warta Lengkap ›</a>
                </div>

                <div class="card-grid">
                    <?php foreach ($latestPosts as $post): ?>
                        <div class="portal-card">
                            <span style="font-size: 11px; color: var(--text-tertiary);"><?= esc(date('d M Y', strtotime($post['created_at']))) ?></span>
                            <h3 style="font-size: 16px; font-weight: 700; margin: 6px 0; color: var(--text-primary);"><?= esc($post['title']) ?></h3>
                            <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 12px; line-height: 1.5;"><?= esc($post['excerpt'] ?? substr(strip_tags($post['content'] ?? ''), 0, 100)) ?></p>
                            <a href="<?= site_url('berita') ?>" class="btn-portal btn-portal-primary" style="padding: 4px 10px; font-size: 12px; display: inline-block;">Baca Artikel</a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>

        <?php elseif ($secKey === 'donation' && ($settings['show_donation_section'] ?? '1') === '1'): ?>
            <?php
                $donTitleRaw = $settings['donation_title'] ?? '💰 Mari Infaq & Sedekah Melalui {masjidName}';
                $donTitle = str_replace('{masjidName}', esc($masjidName), esc($donTitleRaw));
                $donSubtitle = esc($settings['donation_subtitle'] ?? 'Bantu operasional masjid & program sosial keumatan');
                $donDesc = esc($settings['donation_description'] ?? 'Setiap rupiah donasi Anda disalurkan secara aman, akuntabel, dan terdaftar dalam Laporan Keuangan Transparan Masjid.');
                $donBtnText = esc($settings['donation_btn_text'] ?? 'Salurkan Donasi Sekarang ›');
                $rawUrl = $settings['donation_btn_url'] ?? 'donasi';
                $donBtnUrl = (!empty($rawUrl) && (str_starts_with($rawUrl, 'http://') || str_starts_with($rawUrl, 'https://'))) ? esc($rawUrl) : site_url(esc($rawUrl));
                $bgStyle = !empty($settings['donation_bg_image']) ? "background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('" . esc($settings['donation_bg_image']) . "') center/cover no-repeat;" : "background: linear-gradient(135deg, var(--primary-800), var(--primary-900));";
            ?>
            <!-- Donation CTA Section -->
            <section style="max-width: 1100px; margin: 0 auto 48px; padding: 0 24px;">
                <div style="<?= $bgStyle ?> color: white; padding: 40px; border-radius: 16px; text-align: center;">
                    <h2 style="font-size: 28px; font-weight: 800; margin-bottom: 8px;"><?= $donTitle ?></h2>
                    <?php if (!empty($donSubtitle)): ?>
                        <div style="font-size: 14px; opacity: 0.85; margin-bottom: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;"><?= $donSubtitle ?></div>
                    <?php endif; ?>
                    <p style="font-size: 15px; opacity: 0.9; max-width: 700px; margin: 0 auto 24px;">
                        <?= $donDesc ?>
                    </p>
                    <a href="<?= $donBtnUrl ?>" class="btn-portal btn-portal-primary" style="padding: 12px 32px; font-size: 16px; background: white; color: var(--primary-900); font-weight: 800; border-radius: 8px; display: inline-block; text-decoration: none; box-shadow: 0 4px 12px rgba(0,0,0,0.15);"><?= $donBtnText ?></a>
                </div>
            </section>
        <?php endif; ?>
    <?php endforeach; ?>
</div>
<?= $this->endSection() ?>
