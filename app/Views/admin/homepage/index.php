<?= $this->extend('layouts/admin') ?>

<?= $this->section('title') ?>Homepage Manager & Section Control Dashboard<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
/* iOS-style Custom Toggle Switch */
.switch-container {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: var(--bg-surface, #f8fafc);
    padding: 10px 14px;
    border-radius: 8px;
    border: 1px solid var(--border-light, #e2e8f0);
}
.switch-label {
    font-size: 13px;
    font-weight: 700;
    color: var(--text-primary, #1e293b);
}
.switch {
    position: relative;
    display: inline-block;
    width: 46px;
    height: 24px;
}
.switch input {
    opacity: 0;
    width: 0;
    height: 0;
}
.slider {
    position: absolute;
    cursor: pointer;
    top: 0; left: 0; right: 0; bottom: 0;
    background-color: #cbd5e1;
    transition: .3s;
    border-radius: 24px;
}
.slider:before {
    position: absolute;
    content: "";
    height: 18px;
    width: 18px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: .3s;
    border-radius: 50%;
}
input:checked + .slider {
    background-color: var(--primary-600, #16a34a);
}
input:checked + .slider:before {
    transform: translateX(22px);
}

/* Card Statistics Grid */
.stat-mini-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 8px;
    margin: 12px 0;
    text-align: center;
}
.stat-mini-box {
    background: white;
    padding: 8px 6px;
    border-radius: 6px;
    border: 1px solid var(--border-light, #e2e8f0);
}
.stat-mini-val {
    font-size: 16px;
    font-weight: 800;
    color: var(--text-primary, #0f172a);
}
.stat-mini-lbl {
    font-size: 10px;
    font-weight: 600;
    color: var(--text-tertiary, #64748b);
    text-transform: uppercase;
}

/* Responsive Grid for Section Cards */
.section-cards-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(360px, 1fr));
    gap: 20px;
    margin-bottom: 32px;
}
.biz-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    border: 1px solid var(--border-light, #e2e8f0);
    box-shadow: 0 2px 4px rgba(0,0,0,0.04);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    transition: transform 0.2s, box-shadow 0.2s;
}
.biz-card:hover {
    box-shadow: 0 8px 16px rgba(0,0,0,0.08);
}
</style>

<!-- Workspace Header -->
<div class="workspace-header" style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 16px; margin-bottom: 24px;">
    <div>
        <div style="font-size: 12px; font-weight: 500; color: var(--text-tertiary); margin-bottom: 4px;">
            <a href="<?= site_url('admin/dashboard') ?>" style="color: var(--primary-600); text-decoration: none;">Dashboard</a> / Website Management / Homepage Manager
        </div>
        <h1 class="page-title">🎨 Homepage Manager & Section Control</h1>
        <p class="page-subtitle">Dashboard bisnis pengelolaan konten, visibilitas section, jumlah penayangan, dan urutan Halaman Utama Portal.</p>
    </div>

    <!-- FEATURE 7: LARGE HOMEPAGE PREVIEW BUTTON -->
    <div style="display: flex; gap: 12px; align-items: center;">
        <button type="button" onclick="openPreviewModal()" class="btn btn-primary" style="padding: 12px 24px; font-size: 15px; font-weight: 700; box-shadow: 0 4px 12px rgba(22, 163, 74, 0.3);">
            👁 Preview Homepage
        </button>
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

<!-- Quick Management Action Toolbar -->
<div class="panel-card" style="padding: 16px 20px; margin-bottom: 28px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; border-left: 4px solid var(--primary-600);">
    <div style="display: flex; gap: 8px; flex-wrap: wrap; align-items: center;">
        <span style="font-size: 13px; font-weight: 700; color: var(--text-secondary); margin-right: 8px;">⚡ Aksi Cepat:</span>
        <form action="<?= site_url('admin/homepage-manager/bulk-action') ?>" method="POST" style="display: inline;">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="show_all">
            <button type="submit" class="btn btn-secondary" style="padding: 6px 12px; font-size: 12px;">👁️ Tampilkan Semua</button>
        </form>
        <form action="<?= site_url('admin/homepage-manager/bulk-action') ?>" method="POST" style="display: inline;">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="hide_all">
            <button type="submit" class="btn btn-secondary" style="padding: 6px 12px; font-size: 12px; color: var(--status-danger-text);">🙈 Sembunyikan Semua</button>
        </form>
        <form action="<?= site_url('admin/homepage-manager/bulk-action') ?>" method="POST" style="display: inline;">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="enable_featured">
            <button type="submit" class="btn btn-secondary" style="padding: 6px 12px; font-size: 12px; color: var(--primary-700);">⭐ Aktifkan Featured All</button>
        </form>
        <form action="<?= site_url('admin/homepage-manager/bulk-action') ?>" method="POST" style="display: inline;">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="reset_featured">
            <button type="submit" class="btn btn-secondary" style="padding: 6px 12px; font-size: 12px;">🔄 Reset Featured</button>
        </form>
    </div>

    <div style="display: flex; gap: 8px;">
        <form action="<?= site_url('admin/homepage-manager/reset-default') ?>" method="POST" onsubmit="return confirm('Reset susunan dan konfigurasi ke standar awal?')">
            <?= csrf_field() ?>
            <button type="submit" class="btn btn-secondary" style="padding: 6px 12px; font-size: 12px;">↺ Reset Standar</button>
        </form>
        <form action="<?= site_url('admin/homepage-manager/clear-cache') ?>" method="POST">
            <?= csrf_field() ?>
            <button type="submit" class="btn btn-primary" style="padding: 6px 12px; font-size: 12px;">⚡ Bersihkan Cache</button>
        </form>
    </div>
</div>

<!-- SECTION 1: HOMEPAGE SUMMARY CARDS -->
<div style="margin-bottom: 16px;">
    <h2 style="font-size: 18px; font-weight: 800; color: var(--text-primary); margin-bottom: 4px;">📊 Homepage Summary Cards</h2>
    <p style="font-size: 13px; color: var(--text-muted);">Kelola statistik ringkas, visibilitas modul, dan limit jumlah item penayangan.</p>
</div>

<div class="section-cards-grid">
    <!-- 1. PROGRAM CARD -->
    <?php $pStat = $sectionStats['program'] ?? []; ?>
    <div class="biz-card">
        <div>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span style="font-size: 28px;">🚩</span>
                    <div>
                        <h3 style="font-size: 16px; font-weight: 800; color: var(--text-primary);">Program & Kegiatan</h3>
                        <span style="font-size: 12px; color: var(--text-tertiary);">Modul Operasional Masjid</span>
                    </div>
                </div>
                <!-- Status Badge -->
                <?php if ($pStat['is_visible'] ?? true): ?>
                    <span class="badge badge-green">🟢 Ditampilkan</span>
                <?php else: ?>
                    <span class="badge badge-red" style="background: #fef2f2; color: #991b1b; border: 1px solid #fca5a5;">⚪ Disembunyikan</span>
                <?php endif; ?>
            </div>

            <!-- Stats Mini Grid -->
            <div class="stat-mini-grid">
                <div class="stat-mini-box">
                    <div class="stat-mini-val"><?= $pStat['total'] ?? 0 ?></div>
                    <div class="stat-mini-lbl">Total</div>
                </div>
                <div class="stat-mini-box">
                    <div class="stat-mini-val" style="color: var(--primary-600);"><?= $pStat['active'] ?? 0 ?></div>
                    <div class="stat-mini-lbl">Aktif</div>
                </div>
                <div class="stat-mini-box">
                    <div class="stat-mini-val" style="color: #d97706;"><?= $pStat['hidden'] ?? 0 ?></div>
                    <div class="stat-mini-lbl">Hidden</div>
                </div>
                <div class="stat-mini-box">
                    <div class="stat-mini-val" style="color: #2563eb;"><?= $pStat['featured'] ?? 0 ?></div>
                    <div class="stat-mini-lbl">Featured</div>
                </div>
            </div>

            <!-- Single Section Save Form -->
            <form action="<?= site_url('admin/homepage-manager/save-settings') ?>" method="POST" style="margin-top: 12px;">
                <?= csrf_field() ?>
                <div class="switch-container" style="margin-bottom: 12px;">
                    <span class="switch-label">Tampilkan di Homepage</span>
                    <label class="switch">
                        <input type="hidden" name="show_program_section" value="0">
                        <input type="checkbox" name="show_program_section" value="1" <?= (($settings['show_program_section'] ?? '1') === '1') ? 'checked' : '' ?> onchange="this.form.submit()">
                        <span class="slider"></span>
                    </label>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-size: 13px; font-weight: 600; color: var(--text-secondary);">Jumlah Tampil Maksimal:</span>
                    <div style="display: flex; gap: 6px;">
                        <input type="number" name="limit_program" min="1" max="100" value="<?= esc($settings['limit_program'] ?? 6) ?>" style="width: 70px; padding: 4px 8px; border: 1px solid var(--border-light); border-radius: 6px; font-size: 13px; font-weight: 700; text-align: center;">
                        <button type="submit" class="btn btn-secondary" style="padding: 4px 10px; font-size: 12px;">Simpan</button>
                    </div>
                </div>
            </form>
        </div>

        <div style="display: flex; gap: 8px; margin-top: 16px; border-top: 1px solid var(--border-light); padding-top: 12px;">
            <a href="<?= site_url('admin/cms/create?tab=program') ?>" class="btn btn-primary" style="flex: 1; text-align: center; padding: 6px; font-size: 12px;">+ Tambah Program</a>
            <a href="<?= site_url('admin/cms?tab=program') ?>" class="btn btn-secondary" style="flex: 1; text-align: center; padding: 6px; font-size: 12px;">⚙️ Kelola</a>
        </div>
    </div>

    <!-- 2. LAYANAN CARD -->
    <?php $lStat = $sectionStats['layanan'] ?? []; ?>
    <div class="biz-card">
        <div>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span style="font-size: 28px;">🤝</span>
                    <div>
                        <h3 style="font-size: 16px; font-weight: 800; color: var(--text-primary);">Katalog Layanan Masjid</h3>
                        <span style="font-size: 12px; color: var(--text-tertiary);">Fasilitas Kemasyarakatan</span>
                    </div>
                </div>
                <?php if ($lStat['is_visible'] ?? true): ?>
                    <span class="badge badge-green">🟢 Ditampilkan</span>
                <?php else: ?>
                    <span class="badge badge-red" style="background: #fef2f2; color: #991b1b; border: 1px solid #fca5a5;">⚪ Disembunyikan</span>
                <?php endif; ?>
            </div>

            <div class="stat-mini-grid">
                <div class="stat-mini-box">
                    <div class="stat-mini-val"><?= $lStat['total'] ?? 0 ?></div>
                    <div class="stat-mini-lbl">Total</div>
                </div>
                <div class="stat-mini-box">
                    <div class="stat-mini-val" style="color: var(--primary-600);"><?= $lStat['active'] ?? 0 ?></div>
                    <div class="stat-mini-lbl">Aktif</div>
                </div>
                <div class="stat-mini-box">
                    <div class="stat-mini-val" style="color: #d97706;"><?= $lStat['hidden'] ?? 0 ?></div>
                    <div class="stat-mini-lbl">Hidden</div>
                </div>
                <div class="stat-mini-box">
                    <div class="stat-mini-val" style="color: #2563eb;"><?= $lStat['featured'] ?? 0 ?></div>
                    <div class="stat-mini-lbl">Featured</div>
                </div>
            </div>

            <form action="<?= site_url('admin/homepage-manager/save-settings') ?>" method="POST" style="margin-top: 12px;">
                <?= csrf_field() ?>
                <div class="switch-container" style="margin-bottom: 12px;">
                    <span class="switch-label">Tampilkan di Homepage</span>
                    <label class="switch">
                        <input type="hidden" name="show_layanan_section" value="0">
                        <input type="checkbox" name="show_layanan_section" value="1" <?= (($settings['show_layanan_section'] ?? '1') === '1') ? 'checked' : '' ?> onchange="this.form.submit()">
                        <span class="slider"></span>
                    </label>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-size: 13px; font-weight: 600; color: var(--text-secondary);">Jumlah Tampil Maksimal:</span>
                    <div style="display: flex; gap: 6px;">
                        <input type="number" name="limit_layanan" min="1" max="100" value="<?= esc($settings['limit_layanan'] ?? 4) ?>" style="width: 70px; padding: 4px 8px; border: 1px solid var(--border-light); border-radius: 6px; font-size: 13px; font-weight: 700; text-align: center;">
                        <button type="submit" class="btn btn-secondary" style="padding: 4px 10px; font-size: 12px;">Simpan</button>
                    </div>
                </div>
            </form>
        </div>

        <div style="display: flex; gap: 8px; margin-top: 16px; border-top: 1px solid var(--border-light); padding-top: 12px;">
            <a href="<?= site_url('admin/cms/create?tab=layanan') ?>" class="btn btn-primary" style="flex: 1; text-align: center; padding: 6px; font-size: 12px;">+ Tambah Layanan</a>
            <a href="<?= site_url('admin/cms?tab=layanan') ?>" class="btn btn-secondary" style="flex: 1; text-align: center; padding: 6px; font-size: 12px;">⚙️ Kelola</a>
        </div>
    </div>

    <!-- 3. PENGURUS CARD -->
    <?php $peStat = $sectionStats['pengurus'] ?? []; ?>
    <div class="biz-card">
        <div>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span style="font-size: 28px;">👔</span>
                    <div>
                        <h3 style="font-size: 16px; font-weight: 800; color: var(--text-primary);">Pengurus DKM Masjid</h3>
                        <span style="font-size: 12px; color: var(--text-tertiary);">Struktur Keorganisasian</span>
                    </div>
                </div>
                <?php if ($peStat['is_visible'] ?? true): ?>
                    <span class="badge badge-green">🟢 Ditampilkan</span>
                <?php else: ?>
                    <span class="badge badge-red" style="background: #fef2f2; color: #991b1b; border: 1px solid #fca5a5;">⚪ Disembunyikan</span>
                <?php endif; ?>
            </div>

            <div class="stat-mini-grid">
                <div class="stat-mini-box">
                    <div class="stat-mini-val"><?= $peStat['total'] ?? 0 ?></div>
                    <div class="stat-mini-lbl">Total</div>
                </div>
                <div class="stat-mini-box">
                    <div class="stat-mini-val" style="color: var(--primary-600);"><?= $peStat['active'] ?? 0 ?></div>
                    <div class="stat-mini-lbl">Aktif</div>
                </div>
                <div class="stat-mini-box">
                    <div class="stat-mini-val" style="color: #d97706;"><?= $peStat['hidden'] ?? 0 ?></div>
                    <div class="stat-mini-lbl">Hidden</div>
                </div>
                <div class="stat-mini-box">
                    <div class="stat-mini-val" style="color: #2563eb;"><?= $peStat['featured'] ?? 0 ?></div>
                    <div class="stat-mini-lbl">Featured</div>
                </div>
            </div>

            <form action="<?= site_url('admin/homepage-manager/save-settings') ?>" method="POST" style="margin-top: 12px;">
                <?= csrf_field() ?>
                <div class="switch-container" style="margin-bottom: 12px;">
                    <span class="switch-label">Tampilkan di Homepage</span>
                    <label class="switch">
                        <input type="hidden" name="show_pengurus_section" value="0">
                        <input type="checkbox" name="show_pengurus_section" value="1" <?= (($settings['show_pengurus_section'] ?? '1') === '1') ? 'checked' : '' ?> onchange="this.form.submit()">
                        <span class="slider"></span>
                    </label>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-size: 13px; font-weight: 600; color: var(--text-secondary);">Jumlah Tampil Maksimal:</span>
                    <div style="display: flex; gap: 6px;">
                        <input type="number" name="limit_pengurus" min="1" max="100" value="<?= esc($settings['limit_pengurus'] ?? 3) ?>" style="width: 70px; padding: 4px 8px; border: 1px solid var(--border-light); border-radius: 6px; font-size: 13px; font-weight: 700; text-align: center;">
                        <button type="submit" class="btn btn-secondary" style="padding: 4px 10px; font-size: 12px;">Simpan</button>
                    </div>
                </div>
            </form>
        </div>

        <div style="display: flex; gap: 8px; margin-top: 16px; border-top: 1px solid var(--border-light); padding-top: 12px;">
            <a href="<?= site_url('admin/master/create?tab=pengurus') ?>" class="btn btn-primary" style="flex: 1; text-align: center; padding: 6px; font-size: 12px;">+ Tambah Pengurus</a>
            <a href="<?= site_url('admin/master?tab=pengurus') ?>" class="btn btn-secondary" style="flex: 1; text-align: center; padding: 6px; font-size: 12px;">⚙️ Kelola</a>
        </div>
    </div>

    <!-- 3b. BIDANG / DEPARTEMEN CARD (TASK-022 finding E) -->
    <?php $biStat = $sectionStats['bidang'] ?? []; ?>
    <div class="biz-card">
        <div>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span style="font-size: 28px;">🏛️</span>
                    <div>
                        <h3 style="font-size: 16px; font-weight: 800; color: var(--text-primary);">Bidang / Departemen</h3>
                        <span style="font-size: 12px; color: var(--text-tertiary);">Struktur Bidang DKM</span>
                    </div>
                </div>
                <?php if ($biStat['is_visible'] ?? true): ?>
                    <span class="badge badge-green">🟢 Ditampilkan</span>
                <?php else: ?>
                    <span class="badge badge-red" style="background: #fef2f2; color: #991b1b; border: 1px solid #fca5a5;">⚪ Disembunyikan</span>
                <?php endif; ?>
            </div>

            <div class="stat-mini-grid">
                <div class="stat-mini-box">
                    <div class="stat-mini-val"><?= $biStat['total'] ?? 0 ?></div>
                    <div class="stat-mini-lbl">Total</div>
                </div>
                <div class="stat-mini-box">
                    <div class="stat-mini-val" style="color: var(--primary-600);"><?= $biStat['active'] ?? 0 ?></div>
                    <div class="stat-mini-lbl">Aktif</div>
                </div>
                <div class="stat-mini-box">
                    <div class="stat-mini-val" style="color: #d97706;"><?= $biStat['hidden'] ?? 0 ?></div>
                    <div class="stat-mini-lbl">Hidden</div>
                </div>
                <div class="stat-mini-box">
                    <div class="stat-mini-val" style="color: #2563eb;"><?= $biStat['featured'] ?? 0 ?></div>
                    <div class="stat-mini-lbl">Featured</div>
                </div>
            </div>

            <form action="<?= site_url('admin/homepage-manager/save-settings') ?>" method="POST" style="margin-top: 12px;">
                <?= csrf_field() ?>
                <div class="switch-container" style="margin-bottom: 12px;">
                    <span class="switch-label">Tampilkan di Homepage</span>
                    <label class="switch">
                        <input type="hidden" name="show_bidang_section" value="0">
                        <input type="checkbox" name="show_bidang_section" value="1" <?= (($settings['show_bidang_section'] ?? '1') === '1') ? 'checked' : '' ?> onchange="this.form.submit()">
                        <span class="slider"></span>
                    </label>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-size: 13px; font-weight: 600; color: var(--text-secondary);">Jumlah Tampil Maksimal:</span>
                    <div style="display: flex; gap: 6px;">
                        <input type="number" name="limit_bidang" min="1" max="100" value="<?= esc($settings['limit_bidang'] ?? 6) ?>" style="width: 70px; padding: 4px 8px; border: 1px solid var(--border-light); border-radius: 6px; font-size: 13px; font-weight: 700; text-align: center;">
                        <button type="submit" class="btn btn-secondary" style="padding: 4px 10px; font-size: 12px;">Simpan</button>
                    </div>
                </div>
            </form>
        </div>

        <div style="display: flex; gap: 8px; margin-top: 16px; border-top: 1px solid var(--border-light); padding-top: 12px;">
            <a href="<?= site_url('admin/master/create?tab=bidang') ?>" class="btn btn-primary" style="flex: 1; text-align: center; padding: 6px; font-size: 12px;">+ Tambah Bidang</a>
            <a href="<?= site_url('admin/master?tab=bidang') ?>" class="btn btn-secondary" style="flex: 1; text-align: center; padding: 6px; font-size: 12px;">⚙️ Kelola</a>
        </div>
    </div>

    <!-- 4. KAJIAN & TAKLIM CARD -->
    <?php $kStat = $sectionStats['kajian'] ?? []; ?>
    <div class="biz-card">
        <div>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span style="font-size: 28px;">📖</span>
                    <div>
                        <h3 style="font-size: 16px; font-weight: 800; color: var(--text-primary);">Kajian & Taklim</h3>
                        <span style="font-size: 12px; color: var(--text-tertiary);">Jadwal Kajian Rutin & Tematik</span>
                    </div>
                </div>
                <?php if ($kStat['is_visible'] ?? true): ?>
                    <span class="badge badge-green">🟢 Ditampilkan</span>
                <?php else: ?>
                    <span class="badge badge-red" style="background: #fef2f2; color: #991b1b; border: 1px solid #fca5a5;">⚪ Disembunyikan</span>
                <?php endif; ?>
            </div>

            <div class="stat-mini-grid">
                <div class="stat-mini-box">
                    <div class="stat-mini-val"><?= $kStat['total'] ?? 0 ?></div>
                    <div class="stat-mini-lbl">Total</div>
                </div>
                <div class="stat-mini-box">
                    <div class="stat-mini-val" style="color: var(--primary-600);"><?= $kStat['active'] ?? 0 ?></div>
                    <div class="stat-mini-lbl">Aktif</div>
                </div>
                <div class="stat-mini-box">
                    <div class="stat-mini-val" style="color: #d97706;"><?= $kStat['hidden'] ?? 0 ?></div>
                    <div class="stat-mini-lbl">Hidden</div>
                </div>
                <div class="stat-mini-box">
                    <div class="stat-mini-val" style="color: #2563eb;"><?= $kStat['featured'] ?? 0 ?></div>
                    <div class="stat-mini-lbl">Featured</div>
                </div>
            </div>

            <form action="<?= site_url('admin/homepage-manager/save-settings') ?>" method="POST" style="margin-top: 12px;">
                <?= csrf_field() ?>
                <div class="switch-container" style="margin-bottom: 12px;">
                    <span class="switch-label">Tampilkan di Homepage</span>
                    <label class="switch">
                        <input type="hidden" name="show_kajian_section" value="0">
                        <input type="checkbox" name="show_kajian_section" value="1" <?= (($settings['show_kajian_section'] ?? '1') === '1') ? 'checked' : '' ?> onchange="this.form.submit()">
                        <span class="slider"></span>
                    </label>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-size: 13px; font-weight: 600; color: var(--text-secondary);">Jumlah Tampil Maksimal:</span>
                    <div style="display: flex; gap: 6px;">
                        <input type="number" name="limit_kajian" min="1" max="100" value="<?= esc($settings['limit_kajian'] ?? 6) ?>" style="width: 70px; padding: 4px 8px; border: 1px solid var(--border-light); border-radius: 6px; font-size: 13px; font-weight: 700; text-align: center;">
                        <button type="submit" class="btn btn-secondary" style="padding: 4px 10px; font-size: 12px;">Simpan</button>
                    </div>
                </div>
            </form>
        </div>

        <div style="display: flex; gap: 8px; margin-top: 16px; border-top: 1px solid var(--border-light); padding-top: 12px;">
            <a href="<?= site_url('admin/cms/create?tab=kajian') ?>" class="btn btn-primary" style="flex: 1; text-align: center; padding: 6px; font-size: 12px;">+ Tambah Kajian</a>
            <a href="<?= site_url('admin/cms?tab=kajian') ?>" class="btn btn-secondary" style="flex: 1; text-align: center; padding: 6px; font-size: 12px;">⚙️ Kelola</a>
        </div>
    </div>

<!-- 4c. BERITA / WARTA JAMAAH CARD (bug fix: this section was hardcoded to
     always show on the homepage, bypassing Homepage Manager entirely) -->
<div class="biz-card">
    <div>
        <?php $brStat = $sectionStats['berita'] ?? []; ?>
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <span style="font-size: 28px;">📰</span>
                <div>
                    <h3 style="font-size: 16px; font-weight: 800; color: var(--text-primary);">Berita (Warta Jamaah)</h3>
                    <span style="font-size: 12px; color: var(--text-tertiary);">Daftar berita terbaru di homepage</span>
                </div>
            </div>
            <?php if ($brStat['is_visible'] ?? true): ?>
                <span class="badge badge-green">🟢 Ditampilkan</span>
            <?php else: ?>
                <span class="badge badge-red" style="background: #fef2f2; color: #991b1b; border: 1px solid #fca5a5;">⚪ Disembunyikan</span>
            <?php endif; ?>
        </div>
        <div class="stat-mini-grid">
            <div class="stat-mini-box"><div class="stat-mini-val"><?= $brStat['total'] ?? 0 ?></div><div class="stat-mini-lbl">Total</div></div>
            <div class="stat-mini-box"><div class="stat-mini-val" style="color: var(--primary-600);"><?= $brStat['active'] ?? 0 ?></div><div class="stat-mini-lbl">Published</div></div>
            <div class="stat-mini-box"><div class="stat-mini-val" style="color: #d97706;"><?= $brStat['hidden'] ?? 0 ?></div><div class="stat-mini-lbl">Hidden</div></div>
            <div class="stat-mini-box"><div class="stat-mini-val" style="color: #2563eb;"><?= $brStat['featured'] ?? 0 ?></div><div class="stat-mini-lbl">Featured</div></div>
        </div>
        <form action="<?= site_url('admin/homepage-manager/save-settings') ?>" method="POST" style="margin-top: 12px;">
            <?= csrf_field() ?>
            <div class="switch-container" style="margin-bottom: 12px;">
                <span class="switch-label">Tampilkan di Homepage</span>
                <label class="switch">
                    <input type="hidden" name="show_berita_section" value="0">
                    <input type="checkbox" name="show_berita_section" value="1" <?= (($settings['show_berita_section'] ?? '1') === '1') ? 'checked' : '' ?> onchange="this.form.submit()">
                    <span class="slider"></span>
                </label>
            </div>
        </form>
    </div>
    <div style="display: flex; gap: 8px; margin-top: 16px; border-top: 1px solid var(--border-light); padding-top: 12px;">
        <a href="<?= site_url('admin/cms/create?tab=posts') ?>" class="btn btn-primary" style="flex: 1; text-align: center; padding: 6px; font-size: 12px;">+ Tambah Berita</a>
        <a href="<?= site_url('admin/cms?tab=posts') ?>" class="btn btn-secondary" style="flex: 1; text-align: center; padding: 6px; font-size: 12px;">⚙️ Kelola</a>
    </div>
</div>

<!-- 4d. TRANSPARANSI KEUANGAN CARD (same bug: hardcoded to always show) -->
<div class="biz-card">
    <div>
        <?php $finStat = $sectionStats['financial'] ?? []; ?>
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <span style="font-size: 28px;">💰</span>
                <div>
                    <h3 style="font-size: 16px; font-weight: 800; color: var(--text-primary);">Transparansi Keuangan</h3>
                    <span style="font-size: 12px; color: var(--text-tertiary);">Ringkasan saldo & kas masjid di homepage</span>
                </div>
            </div>
            <?php if ($finStat['is_visible'] ?? true): ?>
                <span class="badge badge-green">🟢 Ditampilkan</span>
            <?php else: ?>
                <span class="badge badge-red" style="background: #fef2f2; color: #991b1b; border: 1px solid #fca5a5;">⚪ Disembunyikan</span>
            <?php endif; ?>
        </div>
        <form action="<?= site_url('admin/homepage-manager/save-settings') ?>" method="POST" style="margin-top: 12px;">
            <?= csrf_field() ?>
            <div class="switch-container">
                <span class="switch-label">Tampilkan di Homepage</span>
                <label class="switch">
                    <input type="hidden" name="show_financial_section" value="0">
                    <input type="checkbox" name="show_financial_section" value="1" <?= (($settings['show_financial_section'] ?? '1') === '1') ? 'checked' : '' ?> onchange="this.form.submit()">
                    <span class="slider"></span>
                </label>
            </div>
        </form>
    </div>
    <div style="display: flex; gap: 8px; margin-top: 16px; border-top: 1px solid var(--border-light); padding-top: 12px;">
        <a href="<?= site_url('admin/financial') ?>" class="btn btn-secondary" style="flex: 1; text-align: center; padding: 6px; font-size: 12px;">⚙️ Kelola Keuangan</a>
    </div>
</div>

<!-- 4e. AGENDA CARD (finding H: separate module from Kajian) -->
<?php $agStat = $sectionStats['agenda'] ?? []; ?>
<div class="biz-card">
    <div>
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <span style="font-size: 28px;">📅</span>
                <div>
                    <h3 style="font-size: 16px; font-weight: 800; color: var(--text-primary);">Agenda & Jadwal Kegiatan</h3>
                    <span style="font-size: 12px; color: var(--text-tertiary);">Kegiatan & Acara Masjid</span>
                </div>
            </div>
            <?php if ($agStat['is_visible'] ?? true): ?>
                <span class="badge badge-green">🟢 Ditampilkan</span>
            <?php else: ?>
                <span class="badge badge-red" style="background: #fef2f2; color: #991b1b; border: 1px solid #fca5a5;">⚪ Disembunyikan</span>
            <?php endif; ?>
        </div>

        <div class="stat-mini-grid">
            <div class="stat-mini-box">
                <div class="stat-mini-val"><?= $agStat['total'] ?? 0 ?></div>
                <div class="stat-mini-lbl">Total</div>
            </div>
            <div class="stat-mini-box">
                <div class="stat-mini-val" style="color: var(--primary-600);"><?= $agStat['active'] ?? 0 ?></div>
                <div class="stat-mini-lbl">Aktif</div>
            </div>
            <div class="stat-mini-box">
                <div class="stat-mini-val" style="color: #d97706;"><?= $agStat['hidden'] ?? 0 ?></div>
                <div class="stat-mini-lbl">Hidden</div>
            </div>
            <div class="stat-mini-box">
                <div class="stat-mini-val" style="color: #2563eb;"><?= $agStat['featured'] ?? 0 ?></div>
                <div class="stat-mini-lbl">Featured</div>
            </div>
        </div>

        <form action="<?= site_url('admin/homepage-manager/save-settings') ?>" method="POST" style="margin-top: 12px;">
            <?= csrf_field() ?>
            <div class="switch-container" style="margin-bottom: 12px;">
                <span class="switch-label">Tampilkan di Homepage</span>
                <label class="switch">
                    <input type="hidden" name="show_agenda_section" value="0">
                    <input type="checkbox" name="show_agenda_section" value="1" <?= (($settings['show_agenda_section'] ?? '1') === '1') ? 'checked' : '' ?> onchange="this.form.submit()">
                    <span class="slider"></span>
                </label>
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center;">
                <span style="font-size: 13px; font-weight: 600; color: var(--text-secondary);">Jumlah Tampil Maksimal:</span>
                <div style="display: flex; gap: 6px;">
                    <input type="number" name="limit_agenda" min="1" max="100" value="<?= esc($settings['limit_agenda'] ?? 5) ?>" style="width: 70px; padding: 4px 8px; border: 1px solid var(--border-light); border-radius: 6px; font-size: 13px; font-weight: 700; text-align: center;">
                    <button type="submit" class="btn btn-secondary" style="padding: 4px 10px; font-size: 12px;">Simpan</button>
                </div>
            </div>
        </form>
    </div>

    <div style="display: flex; gap: 8px; margin-top: 16px; border-top: 1px solid var(--border-light); padding-top: 12px;">
        <a href="<?= site_url('admin/cms/create?tab=agenda') ?>" class="btn btn-primary" style="flex: 1; text-align: center; padding: 6px; font-size: 12px;">+ Tambah Agenda</a>
        <a href="<?= site_url('admin/cms?tab=agenda') ?>" class="btn btn-secondary" style="flex: 1; text-align: center; padding: 6px; font-size: 12px;">⚙️ Kelola</a>
    </div>
</div>

</div><!-- /.section-cards-grid -->

<!-- 4d. TRANSPARANSI KEUANGAN CARD -- deliberately the only full-width
     card here (per user request), since it's a site-wide financial
     summary rather than a per-item content list like the others. -->
<?php $finStat = $sectionStats['financial'] ?? []; ?>
<div class="biz-card" style="margin-bottom: 24px;">
    <div>
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <span style="font-size: 28px;">💰</span>
                <div>
                    <h3 style="font-size: 16px; font-weight: 800; color: var(--text-primary);">Transparansi Keuangan</h3>
                    <span style="font-size: 12px; color: var(--text-tertiary);">Ringkasan saldo & kas masjid di homepage</span>
                </div>
            </div>
            <?php if ($finStat['is_visible'] ?? true): ?>
                <span class="badge badge-green">🟢 Ditampilkan</span>
            <?php else: ?>
                <span class="badge badge-red" style="background: #fef2f2; color: #991b1b; border: 1px solid #fca5a5;">⚪ Disembunyikan</span>
            <?php endif; ?>
        </div>
        <form action="<?= site_url('admin/homepage-manager/save-settings') ?>" method="POST" style="margin-top: 12px;">
            <?= csrf_field() ?>
            <div class="switch-container">
                <span class="switch-label">Tampilkan di Homepage</span>
                <label class="switch">
                    <input type="hidden" name="show_financial_section" value="0">
                    <input type="checkbox" name="show_financial_section" value="1" <?= (($settings['show_financial_section'] ?? '1') === '1') ? 'checked' : '' ?> onchange="this.form.submit()">
                    <span class="slider"></span>
                </label>
            </div>
        </form>
    </div>
    <div style="display: flex; gap: 8px; margin-top: 16px; border-top: 1px solid var(--border-light); padding-top: 12px;">
        <a href="<?= site_url('admin/financial') ?>" class="btn btn-secondary" style="flex: 1; text-align: center; padding: 6px; font-size: 12px;">⚙️ Kelola Keuangan</a>
    </div>
</div>

<!-- SECTION 2: HOMEPAGE CONTENT EDITOR -->
<div style="margin-bottom: 20px;">
    <h2 style="font-size: 18px; font-weight: 800; color: var(--text-primary); margin-bottom: 4px;">✍️ Homepage Content Editor</h2>
    <p style="font-size: 13px; color: var(--text-muted);">Kelola konten teks, pesan himbauan, tombol CTA, dan latar belakang visual section halaman utama portal.</p>
</div>

<!-- DONATION & INFAQ CTA EDITOR CARD (FULL WIDTH) -->
<?php $dStat = $sectionStats['donation'] ?? []; ?>
<div class="panel-card" style="padding: 24px; margin-bottom: 32px; border-radius: 12px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; flex-wrap: wrap; gap: 12px;">
        <div style="display: flex; align-items: center; gap: 12px;">
            <span style="font-size: 32px;">💰</span>
            <div>
                <h3 style="font-size: 17px; font-weight: 800; color: var(--text-primary); margin-bottom: 2px;">Donasi & Infaq CTA Section Editor</h3>
                <span style="font-size: 12px; color: var(--text-tertiary);">Pesan Himbauan Donasi & Tombol Ajak Infaq Halaman Depan</span>
            </div>
        </div>
        <?php if ($dStat['is_visible'] ?? true): ?>
            <span class="badge badge-green" style="padding: 6px 12px; font-size: 12px;">🟢 Ditampilkan</span>
        <?php else: ?>
            <span class="badge badge-red" style="padding: 6px 12px; font-size: 12px; background: #fef2f2; color: #991b1b; border: 1px solid #fca5a5;">⚪ Disembunyikan</span>
        <?php endif; ?>
    </div>

    <form action="<?= site_url('admin/homepage-manager/save-settings') ?>" method="POST">
        <?= csrf_field() ?>
        <div class="switch-container" style="margin-bottom: 20px; background: #f8fafc; padding: 12px 16px; border-radius: 8px; border: 1px solid var(--border-light, #e2e8f0);">
            <span class="switch-label" style="font-size: 14px;">Tampilkan Section Donasi di Homepage Portal</span>
            <label class="switch">
                <input type="hidden" name="show_donation_section" value="0">
                <input type="checkbox" name="show_donation_section" value="1" <?= (($settings['show_donation_section'] ?? '1') === '1') ? 'checked' : '' ?> onchange="this.form.submit()">
                <span class="slider"></span>
            </label>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 18px; background: var(--bg-surface, #f8fafc); padding: 24px; border-radius: 12px; border: 1px solid var(--border-light, #e2e8f0);">
            <!-- Title Input -->
            <div style="grid-column: 1 / -1;">
                <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-primary); margin-bottom: 6px;">
                    📌 Judul Banner (Title)
                </label>
                <input type="text" name="donation_title" value="<?= esc($settings['donation_title'] ?? '💰 Mari Infaq & Sedekah Melalui {masjidName}') ?>" class="form-control" style="width: 100%; padding: 10px 14px; font-size: 16px; font-weight: 700; border-radius: 8px; border: 1px solid var(--border-light); background: white;" placeholder="Contoh: 💰 Mari Infaq & Sedekah Melalui {masjidName}">
                <span style="font-size: 11px; color: var(--text-tertiary); margin-top: 4px; display: block;">💡 Gunakan placeholder <code>{masjidName}</code> untuk menyisipkan Nama Masjid secara otomatis.</span>
            </div>

            <!-- Subtitle Input -->
            <div style="grid-column: 1 / -1;">
                <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-primary); margin-bottom: 6px;">
                    🏷️ Subjudul Himbauan (Subtitle)
                </label>
                <input type="text" name="donation_subtitle" value="<?= esc($settings['donation_subtitle'] ?? 'Bantu operasional masjid & program sosial keumatan') ?>" class="form-control" style="width: 100%; padding: 10px 14px; font-size: 14px; font-weight: 600; border-radius: 8px; border: 1px solid var(--border-light); background: white;" placeholder="Contoh: Bantu operasional masjid & program sosial keumatan">
            </div>

            <!-- Description Textarea (Min 7 Rows, Auto-Resize, Char Counter) -->
            <div style="grid-column: 1 / -1;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                    <label style="font-size: 13px; font-weight: 700; color: var(--text-primary);">
                        📝 Deskripsi / Pesan Ajak Donasi (Description)
                    </label>
                    <span id="donationDescCounter" style="font-size: 12px; color: var(--text-tertiary); font-weight: 600;">0 karakter</span>
                </div>
                <textarea id="donation_description_input" name="donation_description" rows="7" class="form-control" style="width: 100%; min-height: 160px; padding: 12px 14px; font-size: 14px; line-height: 1.6; border-radius: 8px; border: 1px solid var(--border-light); background: white; resize: vertical;" placeholder="Tuliskan kalimat himbauan donasi yang menyentuh, amanah, dan mengajak jamaah untuk berinfaq..." oninput="updateDonationDescUX(this)"><?= esc($settings['donation_description'] ?? 'Setiap rupiah donasi Anda disalurkan secara aman, akuntabel, dan terdaftar dalam Laporan Keuangan Transparan Masjid.') ?></textarea>
                <span style="font-size: 11px; color: var(--text-tertiary); margin-top: 4px; display: block;">💡 Area teks ini otomatis menyesuaikan tinggi baris pengetikan agar nyaman digunakan untuk pesan panjang.</span>
            </div>

            <!-- Button Text Input -->
            <div>
                <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-primary); margin-bottom: 6px;">
                    🔘 Teks Tombol CTA (Button Text)
                </label>
                <input type="text" name="donation_btn_text" value="<?= esc($settings['donation_btn_text'] ?? 'Salurkan Donasi Sekarang ›') ?>" class="form-control" style="width: 100%; padding: 10px 12px; font-size: 14px; font-weight: 600; border-radius: 8px; border: 1px solid var(--border-light); background: white;" placeholder="Contoh: Salurkan Donasi Sekarang ›">
            </div>

            <!-- Button URL Input -->
            <div>
                <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-primary); margin-bottom: 6px;">
                    🔗 Link Tujuan Tombol (Button URL)
                </label>
                <input type="text" name="donation_btn_url" value="<?= esc($settings['donation_btn_url'] ?? 'donasi') ?>" class="form-control" style="width: 100%; padding: 10px 12px; font-size: 14px; font-weight: 600; border-radius: 8px; border: 1px solid var(--border-light); background: white;" placeholder="Contoh: donasi atau https://masjid.com/donasi">
            </div>

            <!-- Background Image URL Input with Media Library Picker -->
            <div style="grid-column: 1 / -1;">
                <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-primary); margin-bottom: 6px;">
                    🖼️ Latar Belakang / Background Image (Media Library / URL)
                </label>
                <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                    <input type="text" id="donation_bg_image_input" name="donation_bg_image" value="<?= esc($settings['donation_bg_image'] ?? '') ?>" class="form-control" style="flex: 1; min-width: 260px; padding: 10px 12px; font-size: 13px; border-radius: 8px; border: 1px solid var(--border-light); background: white;" placeholder="URL Gambar Latar (misal: /uploads/banners/donasi-bg.jpg)">
                    <button type="button" onclick="selectFromMediaLibrary('donation_bg_image_input')" class="btn btn-secondary" style="padding: 10px 16px; font-size: 13px; font-weight: 700; white-space: nowrap;">
                        📁 Media Library
                    </button>
                </div>
                <span style="font-size: 11px; color: var(--text-tertiary); margin-top: 4px; display: block;">💡 Kosongkan jika ingin menggunakan warna latar belakang bawaan *Syariah Gradient*.</span>
            </div>
        </div>

        <div style="display: flex; justify-content: flex-end; margin-top: 20px;">
            <button type="submit" class="btn btn-primary" style="padding: 12px 28px; font-size: 15px; font-weight: 700; box-shadow: 0 4px 12px rgba(22, 163, 74, 0.25);">💾 Simpan Pengaturan Donasi</button>
        </div>
    </form>
</div>

<script>
function updateDonationDescUX(el) {
    // 1. Auto resize height following content
    el.style.height = 'auto';
    el.style.height = Math.max(160, el.scrollHeight) + 'px';

    // 2. Update character count
    var count = el.value.length;
    var counterEl = document.getElementById('donationDescCounter');
    if (counterEl) {
        counterEl.innerText = count + ' karakter';
    }
}

function selectFromMediaLibrary(inputId) {
    var sampleUrls = [
        'https://images.unsplash.com/photo-1542810634-71277d95dcbb?auto=format&fit=crop&w=1200&q=80',
        'https://images.unsplash.com/photo-1590076175571-4b5459efb08c?auto=format&fit=crop&w=1200&q=80'
    ];
    var picked = prompt('Masukkan URL Gambar dari Media Library (atau gunakan URL sampel):', sampleUrls[0]);
    if (picked !== null && picked.trim() !== '') {
        document.getElementById(inputId).value = picked.trim();
    }
}

// Initial trigger on page load
document.addEventListener('DOMContentLoaded', function() {
    var descEl = document.getElementById('donation_description_input');
    if (descEl) {
        updateDonationDescUX(descEl);
    }
});
</script>

<!-- UAT TASK-022 finding #3: every section template below already reads its
     Tag/Title/Subtitle from settings (see e.g. layanan_section.php,
     program_section.php) -- the hardcoded strings shown were only the
     *fallback* used when no setting exists. This panel is what was
     missing: an actual place to set them, submitting to the same
     save-settings endpoint (now widened to accept *_tag/*_title/*_subtitle
     keys; see AdminHomepageManagerController::saveSettings()). -->
<div class="panel-card" style="padding: 24px; margin-bottom: 32px;">
    <div style="margin-bottom: 16px;">
        <h2 style="font-size: 18px; font-weight: 800; color: var(--text-primary); margin-bottom: 4px;">✏️ Judul &amp; Teks Section Beranda</h2>
        <p style="font-size: 13px; color: var(--text-muted);">Ubah tag, judul, dan subjudul tiap section homepage. Kosongkan untuk memakai teks bawaan.</p>
    </div>
    <form action="<?= site_url('admin/homepage-manager/save-settings') ?>" method="POST">
        <?= csrf_field() ?>
        <?php
            $sectionTextGroups = [
                'hero'     => ['label' => '🕌 Hero / Banner Utama', 'badge' => 'Portal Digital Masjid', 'title' => 'Pusat Ibadah, Dakwah & Pemberdayaan Umat', 'subtitle' => ''],
                'program'  => ['label' => '🚩 Program & Kegiatan', 'tag' => 'PROGRAM DKM', 'title' => 'Program Sosial & Keumatan', 'subtitle' => "Berbagai inisiatif kemakmuran masjid dalam bidang sosial, pendidikan al-qur'an, dan ekonomi keumatan."],
                'layanan'  => ['label' => '🤝 Layanan Masjid', 'tag' => 'PELAYANAN JAMAAH', 'title' => 'Layanan Utama Masjid', 'subtitle' => 'Kemudahan akses fasilitas dan pelayanan ibadah bagi seluruh jamaah dan warga sekitar masjid.'],
                'pengurus' => ['label' => '👔 Pengurus DKM', 'tag' => 'STRUKTUR DKM', 'title' => 'Pengurus & Tokoh Masjid', 'subtitle' => 'Struktur kepengurusan DKM yang amanah dan berdedikasi mengabdi untuk kemakmuran masjid.'],
                'bidang'   => ['label' => '🏛️ Bidang / Departemen', 'tag' => 'STRUKTUR ORGANISASI', 'title' => 'Bidang & Departemen', 'subtitle' => 'Bidang-bidang yang menjalankan program dan pelayanan masjid sehari-hari.'],
                'kajian'   => ['label' => '📖 Kajian & Taklim', 'tag' => 'KAJIAN RUTIN', 'title' => 'Highlight Kajian & Jadwal Taklim', 'subtitle' => 'Tingkatkan keilmuan dan ketakwaan melalui jadwal kajian rutin bersama ustadz dan ulama terpilih.'],
                'agenda'   => ['label' => '📅 Agenda & Jadwal', 'tag' => 'AGENDA MASJID', 'title' => 'Agenda & Jadwal Kegiatan', 'subtitle' => 'Ikuti berbagai kegiatan dan acara masjid, mulai dari gotong royong, rapat DKM, hingga peringatan hari besar Islam.'],
            ];
        ?>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px;">
            <?php foreach ($sectionTextGroups as $secKey => $group): ?>
                <div style="border: 1px solid var(--border-light); border-radius: 10px; padding: 14px;">
                    <h4 style="font-size: 13px; font-weight: 700; margin-bottom: 10px;"><?= esc($group['label']) ?></h4>
                    <?php if ($secKey === 'hero'): ?>
                        <label style="display:block; font-size:11px; font-weight:600; color:var(--text-muted); margin-bottom:3px;">Badge</label>
                        <input type="text" name="hero_badge" value="<?= esc($settings['hero_badge'] ?? '') ?>" placeholder="<?= esc($group['badge']) ?>" style="width:100%; margin-bottom:8px; padding:6px 10px; border:1px solid var(--border-light); border-radius:6px; font-size:12px;">
                    <?php else: ?>
                        <label style="display:block; font-size:11px; font-weight:600; color:var(--text-muted); margin-bottom:3px;">Tag</label>
                        <input type="text" name="<?= esc($secKey) ?>_tag" value="<?= esc($settings[$secKey . '_tag'] ?? '') ?>" placeholder="<?= esc($group['tag']) ?>" style="width:100%; margin-bottom:8px; padding:6px 10px; border:1px solid var(--border-light); border-radius:6px; font-size:12px;">
                    <?php endif; ?>
                    <label style="display:block; font-size:11px; font-weight:600; color:var(--text-muted); margin-bottom:3px;">Judul</label>
                    <input type="text" name="<?= esc($secKey) ?>_title" value="<?= esc($settings[$secKey . '_title'] ?? '') ?>" placeholder="<?= esc($group['title']) ?>" style="width:100%; margin-bottom:8px; padding:6px 10px; border:1px solid var(--border-light); border-radius:6px; font-size:12px;">
                    <label style="display:block; font-size:11px; font-weight:600; color:var(--text-muted); margin-bottom:3px;">Subjudul</label>
                    <textarea name="<?= esc($secKey) ?>_subtitle" rows="2" placeholder="<?= esc($group['subtitle']) ?>" style="width:100%; padding:6px 10px; border:1px solid var(--border-light); border-radius:6px; font-size:12px;"><?= esc($settings[$secKey . '_subtitle'] ?? '') ?></textarea>
                </div>
            <?php endforeach; ?>
        </div>
        <button type="submit" class="btn btn-primary" style="margin-top: 16px;">💾 Simpan Semua Judul & Teks</button>
    </form>
</div>

<!-- FEATURE 6 & 1: DRAG & DROP SECTION ORDERING -->
<div class="panel-card" style="padding: 24px; margin-bottom: 32px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
        <div>
            <h2 style="font-size: 18px; font-weight: 800; color: var(--text-primary); margin-bottom: 4px;">☰ Urutan Penayangan Section Beranda</h2>
            <p style="font-size: 13px; color: var(--text-muted);">Sesuaikan susunan letak section pada halaman utama portal dengan tombol panah atau drag.</p>
        </div>
    </div>

    <form action="<?= site_url('admin/homepage-manager/save-order') ?>" method="POST" id="orderForm">
        <?= csrf_field() ?>
        <div id="sectionList" style="display: flex; flex-direction: column; gap: 10px;">
            <?php foreach ($sectionOrder as $index => $secKey): ?>
                <?php if (isset($sectionStats[$secKey])): ?>
                    <?php $stat = $sectionStats[$secKey]; ?>
                    <div class="section-item-row panel-card" draggable="true" style="padding: 14px 18px; display: flex; align-items: center; justify-content: space-between; background: var(--bg-surface); border: 1px solid var(--border-light); border-radius: 8px; cursor: move;">
                        <div style="display: flex; align-items: center; gap: 14px;">
                            <span style="font-size: 18px; color: var(--text-tertiary);">☰</span>
                            <span style="font-weight: 800; font-size: 14px; color: var(--primary-700); background: var(--primary-100); padding: 2px 8px; border-radius: 6px;">#<?= $index + 1 ?></span>
                            <span style="font-size: 22px;"><?= $stat['meta']['icon'] ?></span>
                            <div>
                                <span style="font-size: 15px; font-weight: 700; color: var(--text-primary);"><?= esc($stat['meta']['name']) ?></span>
                            </div>
                            <input type="hidden" name="section_order[]" value="<?= $secKey ?>">
                        </div>

                        <div style="display: flex; align-items: center; gap: 12px;">
                            <?php if ($stat['is_visible'] ?? true): ?>
                                <span class="badge badge-green">🟢 Visible</span>
                            <?php else: ?>
                                <span class="badge badge-red" style="background: #fef2f2; color: #991b1b; border: 1px solid #fca5a5;">⚪ Hidden</span>
                            <?php endif; ?>

                            <div style="display: flex; gap: 4px;">
                                <button type="button" onclick="moveUp(this)" class="btn btn-secondary" style="padding: 4px 10px; font-size: 12px;">▲ Naik</button>
                                <button type="button" onclick="moveDown(this)" class="btn btn-secondary" style="padding: 4px 10px; font-size: 12px;">▼ Turun</button>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>

        <div style="margin-top: 16px; text-align: right;">
            <button type="submit" class="btn btn-primary" style="padding: 10px 24px; font-size: 14px;">💾 Simpan Urutan Section</button>
        </div>
    </form>
</div>

<!-- FEATURE 7: RESPONSIVE PREVIEW MODAL / DRAWER -->
<div id="previewModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); z-index: 9999; justify-content: center; align-items: center; padding: 20px;">
    <div style="background: white; width: 100%; max-width: 1200px; height: 90vh; border-radius: 16px; display: flex; flex-direction: column; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);">
        <!-- Modal Toolbar -->
        <div style="background: var(--bg-surface); padding: 14px 24px; border-bottom: 1px solid var(--border-light); display: flex; justify-content: space-between; align-items: center;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <h3 style="font-size: 16px; font-weight: 800; color: var(--text-primary); margin: 0;">👁 Responsive Live Preview Homepage</h3>
                <span class="badge badge-green">LIVE PREVIEW</span>
            </div>

            <!-- Device Selector -->
            <div style="display: flex; gap: 8px; background: white; padding: 4px; border-radius: 8px; border: 1px solid var(--border-light);">
                <button type="button" onclick="setPreviewMode('desktop')" id="btnDesk" class="btn btn-secondary" style="padding: 6px 14px; font-size: 12px; background: var(--primary-100); color: var(--primary-700);">🖥️ Desktop (100%)</button>
                <button type="button" onclick="setPreviewMode('tablet')" id="btnTab" class="btn btn-secondary" style="padding: 6px 14px; font-size: 12px;">📱 Tablet (768px)</button>
                <button type="button" onclick="setPreviewMode('mobile')" id="btnMob" class="btn btn-secondary" style="padding: 6px 14px; font-size: 12px;">📱 Mobile (375px)</button>
            </div>

            <button type="button" onclick="closePreviewModal()" class="btn btn-secondary" style="padding: 6px 14px; font-weight: 700; color: var(--status-danger-text);">✕ Tutup Pratinjau</button>
        </div>

        <!-- Iframe Container -->
        <div style="flex: 1; background: #e2e8f0; display: flex; justify-content: center; align-items: center; overflow: hidden; padding: 16px;">
            <div id="previewWrapper" style="width: 100%; height: 100%; transition: width 0.3s ease; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.15); background: white;">
                <iframe id="previewIframe" src="<?= site_url('/') ?>" style="width: 100%; height: 100%; border: none;"></iframe>
            </div>
        </div>
    </div>
</div>

<script>
function openPreviewModal() {
    document.getElementById('previewModal').style.display = 'flex';
    document.getElementById('previewIframe').src = '<?= site_url('/') ?>';
}

function closePreviewModal() {
    document.getElementById('previewModal').style.display = 'none';
}

function moveUp(btn) {
    const row = btn.closest('.section-item-row');
    if (row.previousElementSibling) {
        row.parentNode.insertBefore(row, row.previousElementSibling);
        updateOrderIndexes();
    }
}

function moveDown(btn) {
    const row = btn.closest('.section-item-row');
    if (row.nextElementSibling) {
        row.parentNode.insertBefore(row.nextElementSibling, row);
        updateOrderIndexes();
    }
}

function updateOrderIndexes() {
    const rows = document.querySelectorAll('.section-item-row');
    rows.forEach((r, idx) => {
        const numSpan = r.querySelector('span:nth-child(2)');
        if (numSpan) numSpan.textContent = '#' + (idx + 1);
    });
}

function setPreviewMode(mode) {
    const wrapper = document.getElementById('previewWrapper');
    const btnDesk = document.getElementById('btnDesk');
    const btnTab = document.getElementById('btnTab');
    const btnMob = document.getElementById('btnMob');

    [btnDesk, btnTab, btnMob].forEach(b => {
        b.style.background = 'transparent';
        b.style.color = 'var(--text-secondary)';
    });

    if (mode === 'desktop') {
        wrapper.style.width = '100%';
        btnDesk.style.background = 'var(--primary-100)';
        btnDesk.style.color = 'var(--primary-700)';
    } else if (mode === 'tablet') {
        wrapper.style.width = '768px';
        btnTab.style.background = 'var(--primary-100)';
        btnTab.style.color = 'var(--primary-700)';
    } else if (mode === 'mobile') {
        wrapper.style.width = '375px';
        btnMob.style.background = 'var(--primary-100)';
        btnMob.style.color = 'var(--primary-700)';
    }
}
</script>
<?= $this->endSection() ?>
