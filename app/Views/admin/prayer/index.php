<?= $this->extend('layouts/admin') ?>

<?= $this->section('title') ?>Jadwal Sholat Configuration<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
.prayer-time-input {
    width: 80px;
    padding: 6px 8px;
    border: 1px solid var(--border-light, #e2e8f0);
    border-radius: 6px;
    font-size: 14px;
    font-weight: 700;
    text-align: center;
    font-family: 'Courier New', monospace;
}
.prayer-time-row {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 12px 16px;
    background: var(--bg-surface, #f8fafc);
    border: 1px solid var(--border-light, #e2e8f0);
    border-radius: 8px;
    margin-bottom: 8px;
    transition: background 0.2s;
}
.prayer-time-row:hover {
    background: white;
}
.prayer-icon-box {
    width: 44px;
    height: 44px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    background: white;
    border-radius: 8px;
    border: 1px solid var(--border-light, #e2e8f0);
}
</style>

<!-- Workspace Header -->
<div class="workspace-header" style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 16px; margin-bottom: 24px;">
    <div>
        <div style="font-size: 12px; font-weight: 500; color: var(--text-tertiary); margin-bottom: 4px;">
            <a href="<?= site_url('admin/dashboard') ?>" style="color: var(--primary-600); text-decoration: none;">Dashboard</a> / Website Management / Jadwal Sholat
        </div>
        <h1 class="page-title">🕌 Jadwal Sholat Configuration</h1>
        <p class="page-subtitle">Atur jadwal waktu sholat dan iqamah yang ditampilkan di halaman utama portal masjid.</p>
    </div>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div style="background: #f0fdf4; border: 1px solid #86efac; color: #166534; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-weight: 500;">
        ✅ <?= session()->getFlashdata('success') ?>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div style="background: #fef2f2; border: 1px solid #fca5a5; color: #991b1b; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-weight: 500;">
        ⚠️ <?= session()->getFlashdata('error') ?>
    </div>
<?php endif; ?>

<!-- Quick Action Toolbar -->
<div class="panel-card" style="padding: 16px 20px; margin-bottom: 28px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; border-left: 4px solid var(--primary-600);">
    <div style="display: flex; gap: 8px; flex-wrap: wrap; align-items: center;">
        <span style="font-size: 13px; font-weight: 700; color: var(--text-secondary); margin-right: 8px;">⚡ Aksi Cepat:</span>
        <form action="<?= site_url('admin/prayer-time/reset-default') ?>" method="POST" onsubmit="return confirm('Reset semua jadwal sholat ke nilai default?')" style="display: inline;">
            <?= csrf_field() ?>
            <button type="submit" class="btn btn-secondary" style="padding: 6px 12px; font-size: 12px;">↺ Reset Ke Default</button>
        </form>
        <a href="<?= site_url('admin/prayer-time/api') ?>" target="_blank" class="btn btn-secondary" style="padding: 6px 12px; font-size: 12px; text-decoration: none;">🔍 API Preview</a>
    </div>
</div>

<!-- Main Form -->
<form action="<?= site_url('admin/prayer-time/update') ?>" method="POST">
    <?= csrf_field() ?>

    <!-- Prayer Times Editor -->
    <div class="panel-card" style="padding: 24px; margin-bottom: 24px;">
        <h2 style="font-size: 18px; font-weight: 800; color: var(--text-primary); margin-bottom: 16px;">⏰ Waktu Sholat & Iqamah</h2>
        <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 20px;">Atur jadwal sholat lima waktu yang ditampilkan di hero section homepage.</p>

        <?php
        $prayerIcons = [
            'Subuh'   => '🌅',
            'Dzuhur'  => '☀️',
            'Ashar'   => '🌤️',
            'Maghrib' => '🌇',
            'Isya'    => '🌙',
        ];
        $prayerNames = ['Subuh', 'Dzuhur', 'Ashar', 'Maghrib', 'Isya'];
        ?>

        <?php foreach ($prayerNames as $name): ?>
            <?php
            $pt = null;
            foreach ($prayerTimes as $p) {
                if ($p['prayer_name'] === $name) {
                    $pt = $p;
                    break;
                }
            }
            $timeKey = 'prayer_time_' . strtolower($name);
            $iqamahKey = 'iqamah_time_' . strtolower($name);
            $activeKey = 'is_active_' . strtolower($name);
            $icon = $prayerIcons[$name] ?? '🕌';
            $timeVal = $pt ? substr($pt['prayer_time'] ?? '00:00:00', 0, 5) : '00:00';
            $iqamahVal = $pt ? substr($pt['iqamah_time'] ?? '00:00:00', 0, 5) : '';
            $isActive = $pt ? ($pt['is_active'] ?? 1) : 1;
            ?>
            <div class="prayer-time-row">
                <div class="prayer-icon-box"><?= $icon ?></div>
                <div style="flex: 1; min-width: 120px;">
                    <span style="font-size: 15px; font-weight: 700; color: var(--text-primary);"><?= esc($name) ?></span>
                </div>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <label style="font-size: 12px; font-weight: 600; color: var(--text-tertiary);">Waktu Sholat:</label>
                    <input type="time" name="<?= esc($timeKey) ?>" value="<?= esc($timeVal) ?>" class="prayer-time-input">
                </div>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <label style="font-size: 12px; font-weight: 600; color: var(--text-tertiary);">Iqamah:</label>
                    <input type="time" name="<?= esc($iqamahKey) ?>" value="<?= esc($iqamahVal) ?>" class="prayer-time-input">
                </div>
                <div style="display: flex; align-items: center; gap: 6px;">
                    <label class="switch" style="margin: 0;">
                        <input type="hidden" name="<?= esc($activeKey) ?>" value="0">
                        <input type="checkbox" name="<?= esc($activeKey) ?>" value="1" <?= $isActive ? 'checked' : '' ?>>
                        <span class="slider"></span>
                    </label>
                    <span style="font-size: 11px; color: var(--text-tertiary);">Tampil</span>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Location & Method Settings -->
    <div class="panel-card" style="padding: 24px; margin-bottom: 24px;">
        <h2 style="font-size: 18px; font-weight: 800; color: var(--text-primary); margin-bottom: 16px;">📍 Lokasi & Metode Perhitungan</h2>
        <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 20px;">Konfigurasi lokasi masjid untuk kalkulasi waktu sholat otomatis (jika diaktifkan).</p>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px;">
            <div>
                <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-primary); margin-bottom: 6px;">🏙️ Kota / Lokasi</label>
                <input type="text" name="prayer_city" value="<?= esc($settings['prayer_city'] ?? 'Kota Masjid') ?>" class="form-control" style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-light); border-radius: 8px; font-size: 14px;" placeholder="Contoh: Jakarta">
            </div>
            <div>
                <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-primary); margin-bottom: 6px;">🌐 Metode Perhitungan</label>
                <select name="prayer_method" class="form-control" style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-light); border-radius: 8px; font-size: 14px;">
                    <?php $method = $settings['prayer_method'] ?? 'KEMENAG'; ?>
                    <option value="KEMENAG" <?= $method === 'KEMENAG' ? 'selected' : '' ?>>KEMENAG (Indonesia)</option>
                    <option value="MUI" <?= $method === 'MUI' ? 'selected' : '' ?>>MUI</option>
                    <option value="Muhammadiyah" <?= $method === 'Muhammadiyah' ? 'selected' : '' ?>>Muhammadiyah</option>
                    <option value="MWL" <?= $method === 'MWL' ? 'selected' : '' ?>>Muslim World League</option>
                    <option value="ISNA" <?= $method === 'ISNA' ? 'selected' : '' ?>>ISNA (North America)</option>
                    <option value="UmmAlQura" <?= $method === 'UmmAlQura' ? 'selected' : '' ?>>Umm Al-Qura</option>
                    <option value="Manual" <?= $method === 'Manual' ? 'selected' : '' ?>>Manual (Atur Sendiri)</option>
                </select>
            </div>
            <div>
                <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-primary); margin-bottom: 6px;">🌍 Garis Lintang (Latitude)</label>
                <input type="text" name="prayer_latitude" value="<?= esc($settings['prayer_latitude'] ?? '') ?>" class="form-control" style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-light); border-radius: 8px; font-size: 14px;" placeholder="Contoh: -6.200000">
            </div>
            <div>
                <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-primary); margin-bottom: 6px;">🌍 Garis Bujur (Longitude)</label>
                <input type="text" name="prayer_longitude" value="<?= esc($settings['prayer_longitude'] ?? '') ?>" class="form-control" style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-light); border-radius: 8px; font-size: 14px;" placeholder="Contoh: 106.800000">
            </div>
            <div>
                <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-primary); margin-bottom: 6px;">🕐 Zona Waktu (Timezone)</label>
                <select name="prayer_timezone" class="form-control" style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-light); border-radius: 8px; font-size: 14px;">
                    <?php $tz = $settings['prayer_timezone'] ?? 'Asia/Jakarta'; ?>
                    <option value="Asia/Jakarta" <?= $tz === 'Asia/Jakarta' ? 'selected' : '' ?>>WIB (Asia/Jakarta)</option>
                    <option value="Asia/Makassar" <?= $tz === 'Asia/Makassar' ? 'selected' : '' ?>>WITA (Asia/Makassar)</option>
                    <option value="Asia/Jayapura" <?= $tz === 'Asia/Jayapura' ? 'selected' : '' ?>>WIT (Asia/Jayapura)</option>
                </select>
            </div>
            <div>
                <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-primary); margin-bottom: 6px;">🤖 Update Otomatis</label>
                <div style="display: flex; align-items: center; gap: 10px; padding-top: 4px;">
                    <label class="switch">
                        <input type="hidden" name="prayer_auto_update" value="0">
                        <input type="checkbox" name="prayer_auto_update" value="1" <?= (($settings['prayer_auto_update'] ?? '0') === '1') ? 'checked' : '' ?>>
                        <span class="slider"></span>
                    </label>
                    <span style="font-size: 13px; color: var(--text-secondary);">Aktifkan update jadwal otomatis</span>
                </div>
                <span style="font-size: 11px; color: var(--text-tertiary); margin-top: 4px; display: block;">💡 Memerlukan koneksi internet dan API endpoint.</span>
            </div>
        </div>
    </div>

    <!-- Save Button -->
    <div style="display: flex; justify-content: flex-end; gap: 12px; margin-bottom: 32px;">
        <button type="submit" class="btn btn-primary" style="padding: 12px 28px; font-size: 15px; font-weight: 700; box-shadow: 0 4px 12px rgba(22, 163, 74, 0.25);">
            💾 Simpan Jadwal Sholat
        </button>
    </div>
</form>

<?= $this->endSection() ?>