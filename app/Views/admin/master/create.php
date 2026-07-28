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
                <input type="text" name="code" required style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;" placeholder="Contoh: MSJ-001">
            </div>
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Nama Masjid *</label>
                <input type="text" name="name" required style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;" placeholder="Nama resmi masjid...">
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 6px;">Kota / Lokasi *</label>
                    <input type="text" name="city" required style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;">
                </div>
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 6px;">No. Telepon</label>
                    <input type="text" name="phone" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;">
                </div>
            </div>
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Email Resmi</label>
                <input type="email" name="email" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;">
            </div>
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Alamat Lengkap</label>
                <textarea name="address" rows="3" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;"></textarea>
            </div>

        <?php elseif ($tab === 'bidang'): ?>
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Nama Bidang / Departemen *</label>
                <input type="text" name="name" required style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;" placeholder="Contoh: Bidang Dakwah & Peribadatan">
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 6px;">Icon Emoji / Simbol</label>
                    <input type="text" name="icon" value="🏛️" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;">
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
