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
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Slug (URL Friendly)</label>
                <input type="text" name="slug" value="<?= esc(old('slug', $item['slug'] ?? '')) ?>" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;">
            </div>
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Isi Berita / Konten *</label>
                <textarea name="content" rows="6" required style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;"><?= esc(old('content', $item['content'] ?? '')) ?></textarea>
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
                <textarea name="content" rows="8" required style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;"><?= esc(old('content', $item['content'] ?? '')) ?></textarea>
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
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">URL / Path File Gambar *</label>
                <input type="text" name="filepath" required value="<?= esc(old('filepath', $item['filepath'] ?? '/assets/img/gallery-default.jpg')) ?>" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;">
            </div>
        <?php endif; ?>

        <div style="display: flex; gap: 8px; justify-content: flex-end;">
            <a href="<?= site_url('admin/cms?tab=' . esc($tab)) ?>" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">💾 Perbarui Data</button>
        </div>
    </form>
</div>
<?= $this->endSection() ?>
