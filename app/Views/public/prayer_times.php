<?= $this->extend('layouts/public') ?>

<?= $this->section('title') ?>Jadwal Sholat — <?= esc($masjid['name'] ?? 'MasjidCMS') ?><?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="section-wrapper">
    <div class="container">
        <div class="section-header-center">
            <span class="section-tag">🕌 Jadwal Ibadah</span>
            <h2 class="section-title">Jadwal Sholat Hari Ini</h2>
            <p class="section-subtitle"><?= esc($prayerCity ?? 'Kota Masjid') ?> — <?= date('d F Y') ?></p>
        </div>

        <div class="prayer-times-full-grid">
            <?php foreach ($prayerTimes as $pt):
                $ptName = $pt['prayer_name'] ?? '';
                $ptTime = substr($pt['prayer_time'] ?? '00:00:00', 0, 5);
                $iqamahTime = substr($pt['iqamah_time'] ?? '00:00:00', 0, 5);
            ?>
            <div class="prayer-time-card">
                <div class="prayer-time-icon">
                    <?php
                    $icons = [
                        'Subuh'   => '🌅',
                        'Dzuhur'  => '☀️',
                        'Ashar'   => '🌤️',
                        'Maghrib' => '🌇',
                        'Isya'    => '🌙',
                    ];
                    echo $icons[$ptName] ?? '🕌';
                    ?>
                </div>
                <div class="prayer-time-name"><?= esc($ptName) ?></div>
                <div class="prayer-time-value"><?= esc($ptTime) ?> WIB</div>
                <div class="prayer-time-iqamah">Iqamah: <?= esc($iqamahTime) ?> WIB</div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="prayer-times-info">
            <p>📌 Jadwal sholat dapat berubah sesuai dengan kondisi lokasi. Silakan cek kembali sebelum beribadah.</p>
        </div>
    </div>
</div>

<?= $this->endSection() ?>