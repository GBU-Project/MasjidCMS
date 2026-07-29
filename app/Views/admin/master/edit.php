<?= $this->extend('layouts/admin') ?>

<?= $this->section('title') ?>Sunting Master Data<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="breadcrumb-container">
    <span>Admin</span>
    <span>/</span>
    <span>Master Data</span>
    <span>/</span>
    <span class="breadcrumb-active">Sunting Data</span>
</div>

<div class="content-header-title">
    <div>
        <h1>Sunting Master Data (<?= esc(strtoupper($tab)) ?>)</h1>
        <p style="font-size: 14px; color: var(--text-muted);">Form pengubahan data induk platform masjid.</p>
    </div>
    <div>
        <a href="<?= site_url('admin/master?tab=' . esc($tab)) ?>" class="btn btn-secondary">← Batal & Kembali</a>
    </div>
</div>

<?php if (session()->getFlashdata('error')): ?>
    <div style="background: #fef2f2; border: 1px solid #fca5a5; color: #991b1b; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-weight: 500;">
        ⚠️ <?= session()->getFlashdata('error') ?>
    </div>
<?php endif; ?>

<div class="panel-card" style="max-width: 700px; margin-left: 0; padding: 24px;">
    <form action="<?= site_url('admin/master/update') ?>" method="POST">
        <?= csrf_field() ?>
        <input type="hidden" name="tab" value="<?= esc($tab) ?>">
        <input type="hidden" name="id" value="<?= esc($id) ?>">

        <?php if ($tab === 'profil'): ?>
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Kode Masjid *</label>
                <input type="text" name="code" required value="<?= esc(old('code', $item['code'] ?? '')) ?>" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;">
            </div>
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Nama Masjid *</label>
                <input type="text" name="name" required value="<?= esc(old('name', $item['name'] ?? '')) ?>" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;">
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 6px;">Kota / Lokasi *</label>
                    <input type="text" name="city" required value="<?= esc(old('city', $item['city'] ?? '')) ?>" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;">
                </div>
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 6px;">No. Telepon</label>
                    <input type="text" name="phone" value="<?= esc(old('phone', $item['phone'] ?? '')) ?>" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;">
                </div>
            </div>
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Email Resmi</label>
                <input type="email" name="email" value="<?= esc(old('email', $item['email'] ?? '')) ?>" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;">
            </div>
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Alamat Lengkap</label>
                <textarea name="address" rows="3" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;"><?= esc(old('address', $item['address'] ?? '')) ?></textarea>
            </div>
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Sejarah Singkat</label>
                <p style="font-size: 12px; color: var(--text-muted); margin-bottom: 6px;">Ditampilkan di halaman publik "Profil & Visi Misi Masjid".</p>
                <textarea name="history_text" rows="4" placeholder="Ceritakan sejarah singkat berdirinya masjid..." style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;"><?= esc(old('history_text', $item['history_text'] ?? '')) ?></textarea>
            </div>
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Visi</label>
                <textarea name="vision_text" rows="2" placeholder="Visi masjid..." style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;"><?= esc(old('vision_text', $item['vision_text'] ?? '')) ?></textarea>
            </div>
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Misi</label>
                <p style="font-size: 12px; color: var(--text-muted); margin-bottom: 6px;">Satu poin misi per baris — setiap baris akan tampil sebagai daftar bernomor di halaman publik.</p>
                <textarea name="mission_text" rows="4" placeholder="Menyelenggarakan ibadah dan kegiatan syiar Islam...&#10;Mengelola dana ZISWAF secara transparan..." style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;"><?= esc(old('mission_text', $item['mission_text'] ?? '')) ?></textarea>
            </div>

        <?php elseif ($tab === 'bidang'): ?>
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Nama Bidang / Departemen *</label>
                <input type="text" name="name" required value="<?= esc(old('name', $item['name'] ?? '')) ?>" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;">
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 6px;">Icon Emoji / Simbol</label>
                    <input type="text" name="icon" value="<?= esc(old('icon', $item['icon'] ?? '🏛️')) ?>" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;">
                </div>
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 6px;">Urutan Tampil (Sort Order)</label>
                    <input type="number" name="sort_order" value="<?= esc(old('sort_order', $item['sort_order'] ?? 1)) ?>" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;">
                </div>
            </div>
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Deskripsi Bidang</label>
                <textarea name="description" rows="3" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;"><?= esc(old('description', $item['description'] ?? '')) ?></textarea>
            </div>

        <?php elseif ($tab === 'pengurus'): ?>
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Nama Lengkap Pengurus *</label>
                <input type="text" name="nama" required value="<?= esc(old('nama', $item['nama'] ?? '')) ?>" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;">
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 6px;">Jabatan *</label>
                    <input type="text" name="jabatan" required value="<?= esc(old('jabatan', $item['jabatan'] ?? '')) ?>" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;">
                </div>
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 6px;">ID Bidang Navigasi</label>
                    <input type="number" name="bidang_id" value="<?= esc(old('bidang_id', $item['bidang_id'] ?? 1)) ?>" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;">
                </div>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 6px;">Jenis Kelamin</label>
                    <select name="jenis_kelamin" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;">
                        <option value="L" <?= ($item['jenis_kelamin'] ?? 'L') === 'L' ? 'selected' : '' ?>>Laki-laki</option>
                        <option value="P" <?= ($item['jenis_kelamin'] ?? 'L') === 'P' ? 'selected' : '' ?>>Perempuan</option>
                    </select>
                </div>
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 6px;">No. Telepon / WA</label>
                    <input type="text" name="telepon" value="<?= esc(old('telepon', $item['telepon'] ?? '')) ?>" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;">
                </div>
            </div>
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Email Pengurus</label>
                <input type="email" name="email" value="<?= esc(old('email', $item['email'] ?? '')) ?>" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;">
            </div>
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Bio / Profil Singkat</label>
                <textarea name="bio" rows="3" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;"><?= esc(old('bio', $item['bio'] ?? '')) ?></textarea>
            </div>

        <?php elseif ($tab === 'jamaah'): ?>
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Nama Lengkap Jamaah *</label>
                <input type="text" name="full_name" required value="<?= esc(old('full_name', $item['full_name'] ?? '')) ?>" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;">
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 6px;">NIK (No. KTP)</label>
                    <input type="text" name="nik" value="<?= esc(old('nik', $item['nik'] ?? '')) ?>" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;">
                </div>
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 6px;">Jenis Kelamin *</label>
                    <select name="gender" required style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;">
                        <option value="L" <?= ($item['gender'] ?? 'L') === 'L' ? 'selected' : '' ?>>Laki-laki</option>
                        <option value="P" <?= ($item['gender'] ?? 'L') === 'P' ? 'selected' : '' ?>>Perempuan</option>
                    </select>
                </div>
            </div>
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">No. HP / WhatsApp</label>
                <input type="text" name="phone" value="<?= esc(old('phone', $item['phone'] ?? '')) ?>" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;">
            </div>
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Alamat Tempat Tinggal</label>
                <textarea name="address" rows="3" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;"><?= esc(old('address', $item['address'] ?? '')) ?></textarea>
            </div>

        <?php elseif ($tab === 'family'): ?>
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">No. Kartu Keluarga (KK) *</label>
                <input type="text" name="kk_number" required value="<?= esc(old('kk_number', $item['kk_number'] ?? $item['family_no'] ?? '')) ?>" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;">
            </div>
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Nama Kepala Keluarga *</label>
                <input type="text" name="name" required value="<?= esc(old('name', $item['name'] ?? '')) ?>" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;">
            </div>
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Alamat Domisili</label>
                <textarea name="address" rows="3" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;"><?= esc(old('address', $item['address'] ?? '')) ?></textarea>
            </div>

        <?php elseif ($tab === 'user'): ?>
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Username *</label>
                <input type="text" name="username" required value="<?= esc(old('username', $item['username'] ?? '')) ?>" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;">
            </div>
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Nama Lengkap Pengguna</label>
                <input type="text" name="full_name" value="<?= esc(old('full_name', $item['full_name'] ?? '')) ?>" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;">
            </div>
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Email *</label>
                <input type="email" name="email" required value="<?= esc(old('email', $item['email'] ?? '')) ?>" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;">
            </div>
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Password Baru (Kosongkan jika tidak diubah)</label>
                <input type="password" name="password" placeholder="Password baru..." style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;">
            </div>

        <?php elseif ($tab === 'role' || $tab === 'permission'): ?>
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Nama <?= esc(ucfirst($tab)) ?> *</label>
                <input type="text" name="name" required value="<?= esc(old('name', $item['name'] ?? $item['permission_code'] ?? '')) ?>" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;">
            </div>
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Deskripsi</label>
                <textarea name="description" rows="3" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;"><?= esc(old('description', $item['description'] ?? '')) ?></textarea>
            </div>
        <?php endif; ?>

        <div style="display: flex; gap: 8px; justify-content: flex-end;">
            <a href="<?= site_url('admin/master?tab=' . esc($tab)) ?>" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">💾 Perbarui Master Data</button>
        </div>
    </form>
</div>
<?= $this->endSection() ?>
