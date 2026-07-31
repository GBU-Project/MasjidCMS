<?= $this->extend('layouts/admin') ?>

<?= $this->section('title') ?>Tambah Konten CMS<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="breadcrumb-container">
    <span>Admin</span>
    <span>/</span>
    <span>CMS & Content</span>
    <span>/</span>
    <span class="breadcrumb-active">Tambah Baru</span>
</div>

<div class="content-header-title">
    <div>
        <h1>Tambah Konten Baru (<?= esc(strtoupper($tab)) ?>)</h1>
        <p style="font-size: 14px; color: var(--text-muted);">Form pembuatan konten portal publik masjid.</p>
    </div>
    <div>
        <a href="<?= site_url('admin/cms?tab=' . esc($tab)) ?>" class="btn btn-secondary">← Batal & Kembali</a>
    </div>
</div>

<?php if (session()->getFlashdata('error')): ?>
    <div style="background: #fef2f2; border: 1px solid #fca5a5; color: #991b1b; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-weight: 500;">
        ⚠️ <?= session()->getFlashdata('error') ?>
    </div>
<?php endif; ?>

<div class="panel-card" style="max-width: 700px; margin-left: 0; padding: 24px;">
    <form action="<?= site_url('admin/cms/store') ?>" method="POST">
        <?= csrf_field() ?>
        <input type="hidden" name="tab" value="<?= esc($tab) ?>">

        <?php if ($tab === 'posts'): ?>
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Judul Berita / Artikel *</label>
                <input type="text" name="title" required class="form-control" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;" placeholder="Masukkan judul berita...">
            </div>
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Slug (URL Friendly)</label>
                <input type="text" name="slug" class="form-control" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;" placeholder="Opsional (otomatis terisi dari judul)">
            </div>
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Isi Berita / Konten *</label>
                <textarea name="content" id="cms_post_content" class="tinymce-standard" rows="5" placeholder="Tulis artikel berita masjid di sini..."></textarea>
            </div>
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Status Publikasi</label>
                <select name="is_published" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;">
                    <option value="1">Publish Langsung</option>
                    <option value="0">Simpan Draf</option>
                </select>
            </div>

        <?php elseif ($tab === 'kajian'): ?>
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Nama Penceramah / Ustadz *</label>
                <input type="text" name="speaker_name" required style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;" placeholder="Contoh: KH. Ahmad Dahlan, Lc">
            </div>
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Tema / Topik Kajian *</label>
                <input type="text" name="topic" required style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;" placeholder="Contoh: Tafsir Surah Al-Kahfi & Fiqih Muamalah">
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 6px;">Tanggal *</label>
                    <input type="date" name="schedule_date" required style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;">
                </div>
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 6px;">Jam *</label>
                    <input type="time" name="schedule_time" required style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;">
                </div>
            </div>
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Lokasi / Ruang *</label>
                <input type="text" name="location" required style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;" value="Ruang Utama Masjid">
            </div>

        <?php elseif ($tab === 'agenda'): ?>
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Judul Agenda *</label>
                <input type="text" name="title" required style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;" placeholder="Contoh: Gotong Royong Bersih Masjid">
            </div>
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Deskripsi</label>
                <textarea name="description" class="tinymce-standard" rows="5" placeholder="Detail kegiatan..."></textarea>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 6px;">Tanggal *</label>
                    <input type="date" name="event_date" required style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;">
                </div>
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 6px;">Jam</label>
                    <input type="time" name="event_time" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;">
                </div>
            </div>
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Lokasi</label>
                <input type="text" name="location" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;" value="Halaman Masjid">
            </div>

        <?php elseif ($tab === 'program'): ?>
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Nama Program / Kegiatan *</label>
                <input type="text" name="nama" required style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;" placeholder="Contoh: TPQ Al-Qur'an Darussalam">
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 6px;">Penanggung Jawab</label>
                    <input type="text" name="penanggung_jawab" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;" placeholder="Nama Ustadz / Pengelola">
                </div>
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 6px;">Lokasi Kegiatan</label>
                    <input type="text" name="lokasi" value="Masjid Darussalam" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;">
                </div>
            </div>
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Ringkasan Singkat</label>
                <input type="text" name="ringkasan" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;" placeholder="Ringkasan 1 kalimat...">
            </div>
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Deskripsi Lengkap Program</label>
                <textarea name="deskripsi" class="tinymce-standard" rows="5" placeholder="Detail program kegiatan..."></textarea>
            </div>

        <?php elseif ($tab === 'layanan'): ?>
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Nama Layanan *</label>
                <input type="text" name="nama" required style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;" placeholder="Contoh: Layanan Ambulans Gratis 24 Jam">
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 6px;">Icon</label>
                    <div style="display:flex; align-items:center; gap:10px;">
                        <span id="icon_preview_layanan_create" style="font-size: 22px; width: 36px; text-align: center;">🚑</span>
                        <input type="text" name="icon" id="icon_input_layanan_create" value="🚑" style="flex:1; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;">
                        <button type="button" class="btn btn-secondary" onclick="selectIconFor('icon_input_layanan_create', '#icon_preview_layanan_create')" style="padding: 8px 12px; font-size: 12px;">🎨 Pilih Icon</button>
                    </div>
                </div>
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 6px;">Kontak / WhatsApp</label>
                    <input type="text" name="kontak" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;" placeholder="08xxxxxxxxxx">
                </div>
            </div>
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Jam Operasional Layanan</label>
                <input type="text" name="jam_layanan" value="08:00 - 17:00 WIB" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;">
            </div>
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Deskripsi & Persyaratan</label>
                <textarea name="deskripsi" class="tinymce-standard" rows="5" placeholder="Deskripsi layanan..."></textarea>
            </div>

        <?php elseif ($tab === 'pages'): ?>
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Judul Halaman Statis *</label>
                <input type="text" name="title" required style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;" placeholder="Contoh: Sejarah & Visi Misi Masjid">
            </div>
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Slug (URL Path)</label>
                <input type="text" name="slug" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;" placeholder="Opsional (otomatis terisi dari judul)">
            </div>
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Isi Halaman *</label>
                <textarea name="content" class="tinymce-standard" rows="5" placeholder="Tulis konten halaman di sini..."></textarea>
            </div>
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Status Halaman</label>
                <select name="is_published" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;">
                    <option value="1">Aktif / Publish</option>
                    <option value="0">Draf</option>
                </select>
            </div>

        <?php elseif ($tab === 'gallery'): ?>
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Caption Foto / Keterangan *</label>
                <input type="text" name="caption" required style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;" placeholder="Contoh: Dokumentasi Pelaksanaan Shalat Idul Adha">
            </div>
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Gambar *</label>
                <div style="display:flex; align-items:center; gap:10px;">
                    <img data-preview-for="gallery_media_id" src="" style="width:64px; height:64px; object-fit:cover; border-radius:8px; border:1px solid var(--border-light); display:none;">
                    <input type="hidden" name="media_id" id="gallery_media_id" data-picker-value="id" required style="display:none;">
                    <button type="button" class="btn btn-secondary" onclick="selectFromMediaLibrary('gallery_media_id')" style="padding: 8px 14px; font-size: 13px;">🖼️ Pilih dari Media Library</button>
                </div>
            </div>
        <?php endif; ?>

        <div style="display: flex; gap: 8px; justify-content: flex-end;">
            <a href="<?= site_url('admin/cms?tab=' . esc($tab)) ?>" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">💾 Simpan Data</button>
        </div>
    </form>
</div>
<?= $this->endSection() ?>
