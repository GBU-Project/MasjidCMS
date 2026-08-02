<?= $this->extend('layouts/admin') ?>

<?= $this->section('title') ?>Tambah Master Data<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="breadcrumb-container">
    <span>Admin</span>
    <span>/</span>
    <span>Master Data</span>
    <span>/</span>
    <span class="breadcrumb-active">Tambah Baru</span>
</div>

<div class="content-header-title">
    <div>
        <h1>Tambah Master Data (<?= esc(strtoupper($tab)) ?>)</h1>
        <p style="font-size: 14px; color: var(--text-muted);">Form registrasi data induk platform masjid.</p>
    </div>
    <div>
        <a href="<?= site_url('admin/master?tab=' . esc($tab)) ?>" class="btn btn-secondary">← Batal & Kembali</a>
    </div>
</div>

<div class="panel-card" style="max-width: 700px; margin-left: 0; padding: 24px;">
    <form action="<?= site_url('admin/master/store') ?>" method="POST">
        <?= csrf_field() ?>
        <input type="hidden" name="tab" value="<?= esc($tab) ?>">

        <?php if ($tab === 'profil'): ?>
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Kode Masjid *</label>
                <input type="text" name="code" required value="<?= esc(old('code', '')) ?>" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;" placeholder="Contoh: MSJ-001">
            </div>
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Nama Masjid *</label>
                <input type="text" name="name" required value="<?= esc(old('name', '')) ?>" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;" placeholder="Nama resmi masjid...">
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 6px;">Kota / Lokasi *</label>
                    <input type="text" name="city" required value="<?= esc(old('city', '')) ?>" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;" placeholder="Contoh: Kota Bogor">
                </div>
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 6px;">No. Telepon</label>
                    <input type="text" name="phone" value="<?= esc(old('phone', '')) ?>" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;">
                </div>
            </div>
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Email Resmi</label>
                <input type="email" name="email" value="<?= esc(old('email', '')) ?>" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;">
            </div>
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Alamat Lengkap</label>
                <textarea name="address" rows="3" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;"><?= esc(old('address', '')) ?></textarea>
            </div>
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Sejarah Singkat</label>
                <p style="font-size: 12px; color: var(--text-muted); margin-bottom: 6px;">Ditampilkan di halaman publik "Profil & Visi Misi Masjid".</p>
                <textarea name="history_text" rows="4" placeholder="Ceritakan sejarah singkat berdirinya masjid..." style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;"><?= esc(old('history_text', '')) ?></textarea>
            </div>
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Visi</label>
                <textarea name="vision_text" rows="2" placeholder="Visi masjid..." style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;"><?= esc(old('vision_text', '')) ?></textarea>
            </div>
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Misi</label>
                <p style="font-size: 12px; color: var(--text-muted); margin-bottom: 6px;">Satu poin misi per baris — setiap baris akan tampil sebagai daftar bernomor di halaman publik.</p>
                <textarea name="mission_text" rows="4" placeholder="Menyelenggarakan ibadah dan kegiatan syiar Islam...&#10;Mengelola dana ZISWAF secara transparan..." style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;"><?= esc(old('mission_text', '')) ?></textarea>
            </div>

            <!-- Logo & Favicon Media Pickers -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 6px;">Logo Website</label>
                    <div style="display:flex; align-items:center; gap:10px;">
                        <img data-preview-for="logo_media_id" src="" style="width:48px; height:48px; object-fit:cover; border-radius:8px; border:1px solid var(--border-light); display:none;">
                        <input type="hidden" name="logo_media_id" id="logo_media_id" data-picker-value="id" value="<?= esc(old('logo_media_id', '')) ?>">
                        <button type="button" class="btn btn-secondary" onclick="selectFromMediaLibrary('logo_media_id')" style="padding: 8px 14px; font-size: 13px;">🖼️ Pilih dari Media Library</button>
                    </div>
                </div>
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 6px;">Favicon</label>
                    <div style="display:flex; align-items:center; gap:10px;">
                        <img data-preview-for="favicon_media_id" src="" style="width:32px; height:32px; object-fit:cover; border-radius:6px; border:1px solid var(--border-light); display:none;">
                        <input type="hidden" name="favicon_media_id" id="favicon_media_id" data-picker-value="id" value="<?= esc(old('favicon_media_id', '')) ?>">
                        <button type="button" class="btn btn-secondary" onclick="selectFromMediaLibrary('favicon_media_id')" style="padding: 8px 14px; font-size: 13px;">🖼️ Pilih dari Media Library</button>
                    </div>
                </div>
            </div>

            <!-- Social Media -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 6px;">Facebook URL</label>
                    <input type="text" name="facebook_url" value="<?= esc(old('facebook_url', '')) ?>" placeholder="https://facebook.com/..." style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;">
                </div>
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 6px;">Instagram URL</label>
                    <input type="text" name="instagram_url" value="<?= esc(old('instagram_url', '')) ?>" placeholder="https://instagram.com/..." style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;">
                </div>
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 6px;">YouTube URL</label>
                    <input type="text" name="youtube_url" value="<?= esc(old('youtube_url', '')) ?>" placeholder="https://youtube.com/..." style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;">
                </div>
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 6px;">WhatsApp</label>
                    <input type="text" name="whatsapp_number" value="<?= esc(old('whatsapp_number', '')) ?>" placeholder="6281234567890" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;">
                </div>
            </div>

            <!-- Prayer Time Configuration -->
            <h3 style="font-size: 15px; font-weight: 800; margin: 8px 0 12px; padding-top: 12px; border-top: 1px solid var(--border-light);">🕌 Prayer Time Configuration</h3>
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 6px;">Latitude</label>
                    <input type="number" step="0.00000001" name="latitude" value="<?= esc(old('latitude', '')) ?>" placeholder="-6.56412300" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;">
                </div>
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 6px;">Longitude</label>
                    <input type="number" step="0.00000001" name="longitude" value="<?= esc(old('longitude', '')) ?>" placeholder="106.77234500" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;">
                </div>
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 6px;">Timezone</label>
                    <?php
                        $currentTz = old('timezone', 'Asia/Jakarta');
                        $tzOptions = [
                            'Asia/Jakarta'    => 'Asia/Jakarta (WIB — Jawa, Sumatra, Bogor, dll.)',
                            'Asia/Pontianak'  => 'Asia/Pontianak (WIB — Kalimantan Barat/Tengah)',
                            'Asia/Makassar'   => 'Asia/Makassar (WITA — Kalimantan Timur/Selatan, Sulawesi, Bali, NTB, NTT)',
                            'Asia/Jayapura'   => 'Asia/Jayapura (WIT — Maluku, Papua)',
                        ];
                    ?>
                    <select name="timezone" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;">
                        <?php foreach ($tzOptions as $tzValue => $tzLabel): ?>
                            <option value="<?= esc($tzValue) ?>" <?= $currentTz === $tzValue ? 'selected' : '' ?>><?= esc($tzLabel) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 6px;">Calculation Method</label>
                    <select name="prayer_calc_method" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;">
                        <?php $currentCalc = old('prayer_calc_method', 'KEMENAG'); ?>
                        <?php foreach (\App\Services\Prayer\PrayerTimeCalculator::availableMethods() as $code => $m): ?>
                            <option value="<?= esc($code) ?>" <?= $currentCalc === $code ? 'selected' : '' ?>><?= esc($m['label']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 6px;">Asr Method (Madhab)</label>
                    <select name="prayer_asr_method" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;">
                        <?php $currentAsr = old('prayer_asr_method', 'STANDARD'); ?>
                        <?php foreach (\App\Services\Prayer\PrayerTimeCalculator::availableAsrMethods() as $code => $m): ?>
                            <option value="<?= esc($code) ?>" <?= $currentAsr === $code ? 'selected' : '' ?>><?= esc($m['label']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 6px;">High Latitude Rule</label>
                    <select name="prayer_high_lat_rule" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;">
                        <?php $currentHighLat = old('prayer_high_lat_rule', 'NONE'); ?>
                        <?php foreach (\App\Services\Prayer\PrayerTimeCalculator::HIGH_LAT_RULES as $code => $label): ?>
                            <option value="<?= esc($code) ?>" <?= $currentHighLat === $code ? 'selected' : '' ?>><?= esc($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

        <?php elseif ($tab === 'bidang'): ?>
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Nama Bidang / Departemen *</label>
                <input type="text" name="name" required style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;" placeholder="Contoh: Bidang Dakwah & Peribadatan">
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 6px;">Icon</label>
                    <div style="display:flex; align-items:center; gap:10px;">
                        <span id="icon_preview_bidang_create" style="font-size: 22px; width: 36px; text-align: center;">🏛️</span>
                        <input type="text" name="icon" id="icon_input_bidang_create" value="🏛️" style="flex:1; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;">
                        <button type="button" class="btn btn-secondary" onclick="selectIconFor('icon_input_bidang_create', '#icon_preview_bidang_create')" style="padding: 8px 12px; font-size: 12px;">🎨 Pilih Icon</button>
                    </div>
                </div>
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 6px;">Urutan Tampil (Sort Order)</label>
                    <input type="number" name="sort_order" value="1" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;">
                </div>
            </div>
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Deskripsi Bidang</label>
                <textarea name="description" rows="3" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;" placeholder="Tugas dan fungsi bidang..."></textarea>
            </div>

        <?php elseif ($tab === 'pengurus'): ?>
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Nama Lengkap Pengurus *</label>
                <input type="text" name="nama" required style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;" placeholder="Contoh: H. Ahmad Abdullah, S.Ag">
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 6px;">Jabatan *</label>
                    <input type="text" name="jabatan" required style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;" placeholder="Ketua / Sekretaris / Anggota">
                </div>
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 6px;">ID Bidang Navigasi</label>
                    <input type="number" name="bidang_id" value="1" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;">
                </div>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 6px;">Jenis Kelamin</label>
                    <select name="jenis_kelamin" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;">
                        <option value="L">Laki-laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                </div>
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 6px;">No. Telepon / WA</label>
                    <input type="text" name="telepon" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;">
                </div>
            </div>
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Email Pengurus</label>
                <input type="email" name="email" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;">
            </div>
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Bio / Profil Singkat</label>
                <textarea name="bio" rows="3" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;"></textarea>
            </div>

        <?php elseif ($tab === 'jamaah'): ?>
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Nama Lengkap Jamaah *</label>
                <input type="text" name="full_name" required style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;" placeholder="Nama sesuai KTP...">
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 6px;">NIK (No. KTP)</label>
                    <input type="text" name="nik" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;" placeholder="16 digit NIK">
                </div>
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 6px;">Jenis Kelamin *</label>
                    <select name="gender" required style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;">
                        <option value="L">Laki-laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                </div>
            </div>
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">No. HP / WhatsApp</label>
                <input type="text" name="phone" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;" placeholder="08xxxxxxxxxx">
            </div>
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Alamat Tempat Tinggal</label>
                <textarea name="address" rows="3" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;"></textarea>
            </div>

        <?php elseif ($tab === 'family'): ?>
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">No. Kartu Keluarga (KK) *</label>
                <input type="text" name="kk_number" required style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;" placeholder="16 digit Nomor KK">
            </div>
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Nama Kepala Keluarga *</label>
                <input type="text" name="name" required style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;" placeholder="Nama Kepala Keluarga">
            </div>
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Alamat Domisili</label>
                <textarea name="address" rows="3" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;"></textarea>
            </div>

        <?php elseif ($tab === 'user'): ?>
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Username *</label>
                <input type="text" name="username" required style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;">
            </div>
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Nama Lengkap Pengguna *</label>
                <input type="text" name="full_name" required style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;">
            </div>
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Email *</label>
                <input type="email" name="email" required style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;">
            </div>
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Password *</label>
                <input type="password" name="password" required style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;">
            </div>

        <?php elseif ($tab === 'role' || $tab === 'permission'): ?>
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Nama <?= esc(ucfirst($tab)) ?> *</label>
                <input type="text" name="name" required style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;">
            </div>
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Deskripsi</label>
                <textarea name="description" rows="3" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;"></textarea>
            </div>
        <?php endif; ?>

        <div style="display: flex; gap: 8px; justify-content: flex-end;">
            <a href="<?= site_url('admin/master?tab=' . esc($tab)) ?>" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">💾 Simpan Master Data</button>
        </div>
    </form>
</div>
<?= $this->endSection() ?>
