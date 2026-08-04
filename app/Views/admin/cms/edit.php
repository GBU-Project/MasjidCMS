<?= $this->extend('layouts/admin') ?>

<?= $this->section('title') ?>Sunting Konten CMS<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="breadcrumb-container">
    <span>Admin</span>
    <span>/</span>
    <span>CMS & Content</span>
    <span>/</span>
    <span class="breadcrumb-active">Sunting Data</span>
</div>

<div class="content-header-title">
    <div>
        <h1>Sunting Konten (<?= esc(strtoupper($tab)) ?>)</h1>
        <p style="font-size: 14px; color: var(--text-muted);">Form pengubahan data konten portal publik masjid.</p>
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
    <form action="<?= site_url('admin/cms/update') ?>" method="POST">
        <?= csrf_field() ?>
        <input type="hidden" name="tab" value="<?= esc($tab) ?>">
        <input type="hidden" name="id" value="<?= esc($id) ?>">

        <?php if ($tab === 'posts'): ?>
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Judul Berita / Artikel *</label>
                <input type="text" name="title" required value="<?= esc(old('title', $item['title'] ?? '')) ?>" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;">
            </div>
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Foto Sampul / Thumbnail</label>
                <div style="display:flex; align-items:center; gap:10px;">
                    <?php $currentUrl = !empty($item['filepath']) ? (str_starts_with($item['filepath'], 'http') ? $item['filepath'] : base_url($item['filepath'])) : ''; ?>
                    <img data-preview-for="posts_featured_media_id" src="<?= esc($currentUrl) ?>" style="width:64px; height:64px; object-fit:cover; border-radius:8px; border:1px solid var(--border-light); <?= empty($currentUrl) ? 'display:none;' : '' ?>">
                    <input type="hidden" name="featured_media_id" id="posts_featured_media_id" data-picker-value="id" value="<?= esc($item['featured_media_id'] ?? '') ?>">
                    <button type="button" class="btn btn-secondary" onclick="selectFromMediaLibrary('posts_featured_media_id')" style="padding: 8px 14px; font-size: 13px;">🖼️ Ganti dari Media Library</button>
                </div>
                <p style="font-size: 12px; color: var(--text-muted); margin-top: 6px;">Opsional. Tanpa foto, kartu berita akan tampil dengan ikon default.</p>
            </div>
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Slug (URL Friendly)</label>
                <input type="text" name="slug" value="<?= esc(old('slug', $item['slug'] ?? '')) ?>" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;">
            </div>
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Isi Berita / Konten *</label>
                <textarea name="content" class="tinymce-standard" rows="5"><?= esc(old('content', $item['content'] ?? '')) ?></textarea>
            </div>
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Status Publikasi</label>
                <select name="is_published" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;">
                    <option value="1" <?= ($item['is_published'] ?? 1) == 1 ? 'selected' : '' ?>>Publish Langsung</option>
                    <option value="0" <?= ($item['is_published'] ?? 1) == 0 ? 'selected' : '' ?>>Simpan Draf</option>
                </select>
            </div>

        <?php elseif ($tab === 'kajian'): ?>
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Nama Penceramah / Ustadz *</label>
                <input type="text" name="speaker_name" required value="<?= esc(old('speaker_name', $item['speaker_name'] ?? '')) ?>" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;">
            </div>
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Foto Penceramah</label>
                <div style="display:flex; align-items:center; gap:10px;">
                    <?php $currentSpeakerUrl = !empty($item['filepath']) ? (str_starts_with($item['filepath'], 'http') ? $item['filepath'] : base_url($item['filepath'])) : ''; ?>
                    <img data-preview-for="kajian_speaker_photo_media_id" src="<?= esc($currentSpeakerUrl) ?>" style="width:56px; height:56px; object-fit:cover; border-radius:50%; border:1px solid var(--border-light); <?= empty($currentSpeakerUrl) ? 'display:none;' : '' ?>">
                    <input type="hidden" name="speaker_photo_media_id" id="kajian_speaker_photo_media_id" data-picker-value="id" value="<?= esc($item['speaker_photo_media_id'] ?? '') ?>">
                    <button type="button" class="btn btn-secondary" onclick="selectFromMediaLibrary('kajian_speaker_photo_media_id')" style="padding: 8px 14px; font-size: 13px;">🖼️ Ganti dari Media Library</button>
                </div>
                <p style="font-size: 12px; color: var(--text-muted); margin-top: 6px;">Opsional. Tanpa foto, akan tampil ikon default.</p>
            </div>
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Tema / Topik Kajian *</label>
                <input type="text" name="topic" required value="<?= esc(old('topic', $item['topic'] ?? '')) ?>" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;">
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 6px;">Tanggal *</label>
                    <input type="date" name="schedule_date" required value="<?= esc(old('schedule_date', $item['schedule_date'] ?? '')) ?>" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;">
                </div>
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 6px;">Jam *</label>
                    <input type="time" name="schedule_time" required value="<?= esc(old('schedule_time', substr($item['schedule_time'] ?? '', 0, 5))) ?>" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;">
                </div>
            </div>
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Lokasi / Ruang *</label>
                <input type="text" name="location" required value="<?= esc(old('location', $item['location'] ?? '')) ?>" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;">
            </div>

        <?php elseif ($tab === 'agenda'): ?>
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Judul Agenda *</label>
                <input type="text" name="title" required value="<?= esc(old('title', $item['title'] ?? '')) ?>" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;">
            </div>
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Deskripsi</label>
                <textarea name="description" class="tinymce-standard" rows="5"><?= esc(old('description', $item['description'] ?? '')) ?></textarea>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 6px;">Tanggal *</label>
                    <input type="date" name="event_date" required value="<?= esc(old('event_date', $item['event_date'] ?? '')) ?>" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;">
                </div>
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 6px;">Jam</label>
                    <input type="time" name="event_time" value="<?= esc(old('event_time', substr($item['event_time'] ?? '', 0, 5))) ?>" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;">
                </div>
            </div>
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Lokasi</label>
                <input type="text" name="location" value="<?= esc(old('location', $item['location'] ?? '')) ?>" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;">
            </div>

        <?php elseif ($tab === 'program'): ?>
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Nama Program / Kegiatan *</label>
                <input type="text" name="nama" required value="<?= esc(old('nama', $item['nama'] ?? '')) ?>" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;">
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 6px;">Penanggung Jawab</label>
                    <input type="text" name="penanggung_jawab" value="<?= esc(old('penanggung_jawab', $item['penanggung_jawab'] ?? '')) ?>" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;">
                </div>
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 6px;">Lokasi Kegiatan</label>
                    <input type="text" name="lokasi" value="<?= esc(old('lokasi', $item['lokasi'] ?? '')) ?>" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;">
                </div>
            </div>
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Ringkasan Singkat</label>
                <input type="text" name="ringkasan" value="<?= esc(old('ringkasan', $item['ringkasan'] ?? '')) ?>" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;">
            </div>
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Deskripsi Lengkap Program</label>
                <textarea name="deskripsi" class="tinymce-standard" rows="5"><?= esc(old('deskripsi', $item['deskripsi'] ?? '')) ?></textarea>
            </div>

        <?php elseif ($tab === 'layanan'): ?>
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Nama Layanan *</label>
                <input type="text" name="nama" required value="<?= esc(old('nama', $item['nama'] ?? '')) ?>" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;">
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 6px;">Icon</label>
                    <?php helper('icon'); $layananIconVal = old('icon', $item['icon'] ?? '🤝'); ?>
                    <div style="display:flex; align-items:center; gap:10px;">
                        <span id="icon_preview_layanan_edit" style="font-size: 22px; width: 36px; text-align: center;"><?= render_icon($layananIconVal) ?></span>
                        <input type="text" name="icon" id="icon_input_layanan_edit" value="<?= esc($layananIconVal) ?>" style="flex:1; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;">
                        <button type="button" class="btn btn-secondary" onclick="selectIconFor('icon_input_layanan_edit', '#icon_preview_layanan_edit')" style="padding: 8px 12px; font-size: 12px;">🎨 Pilih Icon</button>
                    </div>
                </div>
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 6px;">Kontak / WhatsApp</label>
                    <input type="text" name="kontak" value="<?= esc(old('kontak', $item['kontak'] ?? '')) ?>" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;">
                </div>
            </div>
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Jam Operasional Layanan</label>
                <input type="text" name="jam_layanan" value="<?= esc(old('jam_layanan', $item['jam_layanan'] ?? '')) ?>" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;">
            </div>
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Deskripsi & Persyaratan</label>
                <textarea name="deskripsi" class="tinymce-standard" rows="5"><?= esc(old('deskripsi', $item['deskripsi'] ?? '')) ?></textarea>
            </div>

        <?php elseif ($tab === 'pages'): ?>
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Judul Halaman Statis *</label>
                <input type="text" name="title" required value="<?= esc(old('title', $item['title'] ?? '')) ?>" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;">
            </div>
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Slug (URL Path)</label>
                <input type="text" name="slug" value="<?= esc(old('slug', $item['slug'] ?? '')) ?>" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;">
            </div>
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Isi Halaman *</label>
                <textarea name="content" class="tinymce-standard" rows="5"><?= esc(old('content', $item['content'] ?? '')) ?></textarea>
            </div>
            </div>
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Status Halaman</label>
                <select name="is_published" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;">
                    <option value="1" <?= ($item['is_published'] ?? 1) == 1 ? 'selected' : '' ?>>Aktif / Publish</option>
                    <option value="0" <?= ($item['is_published'] ?? 1) == 0 ? 'selected' : '' ?>>Draf</option>
                </select>
            </div>

        <?php elseif ($tab === 'gallery'): ?>
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Caption Foto / Keterangan *</label>
                <input type="text" name="caption" required value="<?= esc(old('caption', $item['caption'] ?? '')) ?>" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;">
            </div>
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Gambar *</label>
                <div style="display:flex; align-items:center; gap:10px;">
                    <?php $currentUrl = !empty($item['filepath']) ? (str_starts_with($item['filepath'], 'http') ? $item['filepath'] : base_url($item['filepath'])) : ''; ?>
                    <img data-preview-for="gallery_media_id" src="<?= esc($currentUrl) ?>" style="width:64px; height:64px; object-fit:cover; border-radius:8px; border:1px solid var(--border-light); <?= empty($currentUrl) ? 'display:none;' : '' ?>">
                    <input type="hidden" name="media_id" id="gallery_media_id" data-picker-value="id" value="<?= esc($item['media_id'] ?? '') ?>">
                    <button type="button" class="btn btn-secondary" onclick="selectFromMediaLibrary('gallery_media_id')" style="padding: 8px 14px; font-size: 13px;">🖼️ Ganti dari Media Library</button>
                </div>
            </div>
        <?php endif; ?>

        <div style="display: flex; gap: 8px; justify-content: flex-end;">
            <a href="<?= site_url('admin/cms?tab=' . esc($tab)) ?>" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">💾 Perbarui Data</button>
        </div>
    </form>
</div>
<?= $this->endSection() ?>
