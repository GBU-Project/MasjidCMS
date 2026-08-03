<?= $this->extend('layouts/admin') ?>

<?= $this->section('title') ?>Edit Slide Hero #<?= $slide['id'] ?> — Homepage CMS<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div style="padding: 24px; max-width: 900px; margin: 0 auto;">
    <!-- Breadcrumb & Title -->
    <div style="margin-bottom: 24px;">
        <div style="font-size: 13px; color: var(--text-tertiary); margin-bottom: 4px;">
            <a href="<?= site_url('admin/dashboard') ?>" style="color: var(--primary-600); text-decoration: none;">Dashboard</a> /
            <a href="<?= site_url('admin/hero-slides') ?>" style="color: var(--primary-600); text-decoration: none;">Hero Slider</a> / Edit Slide #<?= $slide['id'] ?>
        </div>
        <h1 style="font-size: 22px; font-weight: 800; color: var(--text-primary); margin: 0;">✏️ Edit Slide Hero</h1>
    </div>

    <!-- Error Flash -->
    <?php if (session()->getFlashdata('error')): ?>
        <div style="padding: 12px 16px; background: #fef2f2; border: 1px solid #fca5a5; color: #991b1b; border-radius: 8px; margin-bottom: 20px; font-size: 14px;">
            ⚠️ <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <!-- Form Container -->
    <form action="<?= site_url('admin/hero-slides/update/' . $slide['id']) ?>" method="POST">
        <?= csrf_field() ?>

        <div class="panel-card" style="background: white; border: 1px solid var(--border-light); border-radius: 12px; padding: 24px; margin-bottom: 24px;">
            <h3 style="font-size: 16px; font-weight: 700; color: var(--text-primary); margin: 0 0 16px; padding-bottom: 12px; border-bottom: 1px solid var(--border-light);">
                📝 Informasi Konten Slide
            </h3>

            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 700; font-size: 13px; margin-bottom: 6px;">Judul Utama (Title) *</label>
                <input type="text" name="title" required value="<?= esc(old('title', $slide['title'])) ?>" placeholder="Contoh: Pusat Ibadah, Dakwah & Pemberdayaan Umat" style="width: 100%; padding: 10px 14px; border: 1px solid var(--border-light); border-radius: 8px; font-size: 14px;">
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 700; font-size: 13px; margin-bottom: 6px;">Sub-Judul / Deskripsi Singkat</label>
                <textarea name="subtitle" rows="3" placeholder="Contoh: Mewujudkan kemakmuran masjid melalui pelayanan jamaah yang transparan, modern, dan berkemajuan." style="width: 100%; padding: 10px 14px; border: 1px solid var(--border-light); border-radius: 8px; font-size: 14px;"><?= esc(old('subtitle', $slide['subtitle'])) ?></textarea>
            </div>

            <!-- Background Image Selection via Media Library -->
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 700; font-size: 13px; margin-bottom: 6px;">Gambar Latar (Background Image)</label>
                <p style="font-size: 12px; color: var(--text-muted); margin-bottom: 8px;">Pilih gambar resolusi tinggi dari Media Library.</p>
                <div style="display: flex; align-items: center; gap: 12px;">
                    <?php $hasMedia = !empty($slide['bg_image_path']); ?>
                    <img data-preview-for="bg_image_media_id" src="<?= $hasMedia ? base_url($slide['bg_image_path']) : '' ?>" style="width: 100px; height: 60px; object-fit: cover; border-radius: 8px; border: 1px solid var(--border-light); <?= $hasMedia ? '' : 'display: none;' ?>">
                    <input type="hidden" name="bg_image_media_id" id="bg_image_media_id" data-picker-value="id" value="<?= esc(old('bg_image_media_id', $slide['bg_image_media_id'])) ?>">
                    <button type="button" class="btn btn-secondary" onclick="selectFromMediaLibrary('bg_image_media_id')" style="padding: 10px 16px; font-size: 13px; border-radius: 8px;">🖼️ Pilih / Ganti Gambar dari Media Library</button>
                </div>
            </div>
        </div>

        <!-- CTA Buttons Container -->
        <div class="panel-card" style="background: white; border: 1px solid var(--border-light); border-radius: 12px; padding: 24px; margin-bottom: 24px;">
            <h3 style="font-size: 16px; font-weight: 700; color: var(--text-primary); margin: 0 0 16px; padding-bottom: 12px; border-bottom: 1px solid var(--border-light);">
                🔗 Tombol Aksi (Call To Action Buttons)
            </h3>

            <!-- Primary Button -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 12px;">
                <div>
                    <label style="display: block; font-weight: 700; font-size: 13px; margin-bottom: 6px;">Tombol Utama — Teks</label>
                    <input type="text" name="primary_btn_text" value="<?= esc(old('primary_btn_text', $slide['primary_btn_text'])) ?>" placeholder="Contoh: Jelajahi Program DKM" style="width: 100%; padding: 10px 14px; border: 1px solid var(--border-light); border-radius: 8px; font-size: 14px;">
                </div>
                <div>
                    <label style="display: block; font-weight: 700; font-size: 13px; margin-bottom: 6px;">Tombol Utama — URL / Link</label>
                    <input type="text" name="primary_btn_url" value="<?= esc(old('primary_btn_url', $slide['primary_btn_url'])) ?>" placeholder="Contoh: program atau https://..." style="width: 100%; padding: 10px 14px; border: 1px solid var(--border-light); border-radius: 8px; font-size: 14px;">
                </div>
            </div>
            <div style="margin-bottom: 20px;">
                <label style="font-size: 13px; color: var(--text-primary); cursor: pointer; display: flex; align-items: center; gap: 8px;">
                    <input type="checkbox" name="primary_btn_new_tab" value="1" <?= old('primary_btn_new_tab', $slide['primary_btn_new_tab']) ? 'checked' : '' ?>>
                    Buka tautan Tombol Utama di Tab Baru (`target="_blank"`)
                </label>
            </div>

            <!-- Secondary Button -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 12px;">
                <div>
                    <label style="display: block; font-weight: 700; font-size: 13px; margin-bottom: 6px;">Tombol Sekunder — Teks</label>
                    <input type="text" name="secondary_btn_text" value="<?= esc(old('secondary_btn_text', $slide['secondary_btn_text'])) ?>" placeholder="Contoh: Infaq & Zakat Online" style="width: 100%; padding: 10px 14px; border: 1px solid var(--border-light); border-radius: 8px; font-size: 14px;">
                </div>
                <div>
                    <label style="display: block; font-weight: 700; font-size: 13px; margin-bottom: 6px;">Tombol Sekunder — URL / Link</label>
                    <input type="text" name="secondary_btn_url" value="<?= esc(old('secondary_btn_url', $slide['secondary_btn_url'])) ?>" placeholder="Contoh: donasi atau https://..." style="width: 100%; padding: 10px 14px; border: 1px solid var(--border-light); border-radius: 8px; font-size: 14px;">
                </div>
            </div>
            <div>
                <label style="font-size: 13px; color: var(--text-primary); cursor: pointer; display: flex; align-items: center; gap: 8px;">
                    <input type="checkbox" name="secondary_btn_new_tab" value="1" <?= old('secondary_btn_new_tab', $slide['secondary_btn_new_tab']) ? 'checked' : '' ?>>
                    Buka tautan Tombol Sekunder di Tab Baru (`target="_blank"`)
                </label>
            </div>
        </div>

        <!-- Styling & Layout Config Container -->
        <div class="panel-card" style="background: white; border: 1px solid var(--border-light); border-radius: 12px; padding: 24px; margin-bottom: 24px;">
            <h3 style="font-size: 16px; font-weight: 700; color: var(--text-primary); margin: 0 0 16px; padding-bottom: 12px; border-bottom: 1px solid var(--border-light);">
                🎨 Tampilan, Penjadwalan & Status Slide
            </h3>

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; font-weight: 700; font-size: 13px; margin-bottom: 6px;">Kegelapan Overlay (0 - 100%)</label>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <input type="range" name="overlay_opacity" min="0" max="100" value="<?= esc(old('overlay_opacity', $slide['overlay_opacity'])) ?>" oninput="document.getElementById('opacityVal').textContent = this.value + '%'" style="flex: 1;">
                        <span id="opacityVal" style="font-weight: 700; font-size: 13px; min-width: 40px; text-align: right;"><?= esc(old('overlay_opacity', $slide['overlay_opacity'])) ?>%</span>
                    </div>
                </div>

                <div>
                    <label style="display: block; font-weight: 700; font-size: 13px; margin-bottom: 6px;">Rataan Teks (Alignment)</label>
                    <?php $align = old('text_alignment', $slide['text_alignment']); ?>
                    <select name="text_alignment" style="width: 100%; padding: 10px 14px; border: 1px solid var(--border-light); border-radius: 8px; font-size: 14px;">
                        <option value="left" <?= $align === 'left' ? 'selected' : '' ?>>Kiri (Default)</option>
                        <option value="center" <?= $align === 'center' ? 'selected' : '' ?>>Tengah (Center)</option>
                        <option value="right" <?= $align === 'right' ? 'selected' : '' ?>>Kanan (Right)</option>
                    </select>
                </div>

                <div>
                    <label style="display: block; font-weight: 700; font-size: 13px; margin-bottom: 6px;">Status Publikasi</label>
                    <?php $st = old('status', $slide['status']); ?>
                    <select name="status" style="width: 100%; padding: 10px 14px; border: 1px solid var(--border-light); border-radius: 8px; font-size: 14px;">
                        <option value="ACTIVE" <?= $st === 'ACTIVE' ? 'selected' : '' ?>>🟢 ACTIVE (Tampil di Public)</option>
                        <option value="DRAFT" <?= $st === 'DRAFT' ? 'selected' : '' ?>>🟡 DRAFT (Konsep)</option>
                        <option value="INACTIVE" <?= $st === 'INACTIVE' ? 'selected' : '' ?>>🔴 INACTIVE (Nonaktif)</option>
                    </select>
                </div>
            </div>

            <!-- Schedule Window -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div>
                    <label style="display: block; font-weight: 700; font-size: 13px; margin-bottom: 6px;">Mulai Tayang (Publish At — Opsional)</label>
                    <?php $pubVal = $slide['publish_at'] ? date('Y-m-d\TH:i', strtotime($slide['publish_at'])) : ''; ?>
                    <input type="datetime-local" name="publish_at" value="<?= esc(old('publish_at', $pubVal)) ?>" style="width: 100%; padding: 10px 14px; border: 1px solid var(--border-light); border-radius: 8px; font-size: 14px;">
                </div>
                <div>
                    <label style="display: block; font-weight: 700; font-size: 13px; margin-bottom: 6px;">Selesai Tayang (Expire At — Opsional)</label>
                    <?php $expVal = $slide['expire_at'] ? date('Y-m-d\TH:i', strtotime($slide['expire_at'])) : ''; ?>
                    <input type="datetime-local" name="expire_at" value="<?= esc(old('expire_at', $expVal)) ?>" style="width: 100%; padding: 10px 14px; border: 1px solid var(--border-light); border-radius: 8px; font-size: 14px;">
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div style="display: flex; justify-content: flex-end; gap: 12px;">
            <a href="<?= site_url('admin/hero-slides') ?>" class="btn btn-secondary" style="padding: 10px 20px; font-size: 14px; text-decoration: none; border-radius: 8px;">Batal</a>
            <button type="submit" class="btn btn-primary" style="padding: 10px 24px; font-size: 14px; background: var(--primary-600); color: white; border: none; border-radius: 8px; font-weight: 700; cursor: pointer;">💾 Update Slide Hero</button>
        </div>
    </form>
</div>
<?= $this->endSection() ?>
