<?= $this->extend('layouts/public') ?>

<?= $this->section('title') ?>Jadwal Sholat<?= $this->endSection() ?>

<?php
$prayerLabels = [
    'imsak'   => 'Imsak',
    'fajr'    => 'Subuh',
    'sunrise' => 'Terbit',
    'dhuhr'   => 'Dzuhur',
    'asr'     => 'Ashar',
    'maghrib' => 'Maghrib',
    'isha'    => 'Isya',
];
$monthNames = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];
?>

<?= $this->section('content') ?>
<div class="portal-container" style="max-width: 900px;">
    <h1 style="font-size: 28px; font-weight: 800; margin-bottom: 4px;">🕌 Jadwal Sholat</h1>
    <p style="color: var(--text-tertiary); margin-bottom: 24px;">
        <?= esc($masjid['name'] ?? 'Masjid') ?> — <?= esc($masjid['city'] ?? '') ?>
        <br>Dihitung secara lokal berdasarkan lintang/bujur & metode perhitungan yang dikonfigurasi di Profil Masjid. Tidak menggunakan API eksternal.
    </p>

    <!-- Today's schedule -->
    <div style="background: white; border: 1px solid var(--border-light); border-radius: var(--radius-lg); padding: 24px; margin-bottom: 32px;">
        <h2 style="font-size: 18px; font-weight: 700; margin-bottom: 16px;">Hari Ini — <?= date('d M Y') ?></h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(100px, 1fr)); gap: 12px; text-align: center;">
            <?php foreach ($prayerLabels as $key => $label): ?>
                <div style="background: var(--primary-50, #f0fdf4); border-radius: 10px; padding: 12px 8px;">
                    <div style="font-size: 12px; color: var(--text-tertiary); font-weight: 600;"><?= esc($label) ?></div>
                    <div style="font-size: 18px; font-weight: 800; color: var(--primary-700);"><?= esc($today[$key] ?? '-') ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Monthly schedule -->
    <div style="background: white; border: 1px solid var(--border-light); border-radius: var(--radius-lg); padding: 24px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; flex-wrap: wrap; gap: 8px;">
            <h2 style="font-size: 18px; font-weight: 700;">Jadwal Bulanan — <?= esc($monthNames[$month] ?? $month) ?> <?= esc($year) ?></h2>
            <div style="display: flex; gap: 8px;">
                <a href="<?= site_url('jadwal-shalat') ?>?month=<?= $prevMonth ?>&year=<?= $prevYear ?>" class="btn-ui2 btn-secondary-ui2" style="padding: 6px 14px; font-size: 13px;">← Bulan Sebelumnya</a>
                <a href="<?= site_url('jadwal-shalat') ?>?month=<?= $nextMonth ?>&year=<?= $nextYear ?>" class="btn-ui2 btn-secondary-ui2" style="padding: 6px 14px; font-size: 13px;">Bulan Berikutnya →</a>
            </div>
        </div>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                <thead>
                    <tr style="border-bottom: 2px solid var(--border-light); text-align: left;">
                        <th style="padding: 8px;">Tanggal</th>
                        <?php foreach ($prayerLabels as $label): ?>
                            <th style="padding: 8px; text-align: center;"><?= esc($label) ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($monthlySchedule as $row): ?>
                        <tr style="border-bottom: 1px solid var(--border-light); <?= $row['date'] === date('Y-m-d') ? 'background: var(--primary-50, #f0fdf4); font-weight: 700;' : '' ?>">
                            <td style="padding: 8px;"><?= date('d/m', strtotime($row['date'])) ?></td>
                            <?php foreach (array_keys($prayerLabels) as $key): ?>
                                <td style="padding: 8px; text-align: center;"><?= esc($row[$key] ?? '-') ?></td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
