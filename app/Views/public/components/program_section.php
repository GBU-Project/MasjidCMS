<!-- Program Unggulan Section UI 2.0 -->
<?php $sectionSettings = $settings ?? []; ?>
<section class="section-wrapper" id="program" style="background: var(--slate-50);">
    <div class="container">
        <div class="section-header-center">
            <span class="section-tag"><?= esc($sectionSettings['program_tag'] ?? 'PROGRAM DKM') ?></span>
            <h2 class="section-title"><?= esc($sectionSettings['program_title'] ?? 'Program Sosial & Keumatan') ?></h2>
            <p class="section-subtitle"><?= esc($sectionSettings['program_subtitle'] ?? "Berbagai inisiatif kemakmuran masjid dalam bidang sosial, pendidikan al-qur'an, dan ekonomi keumatan.") ?></p>
        </div>

        <?php if (!empty($programList)): ?>
            <div class="card-grid-ui2 card-grid-featured">
                <?php foreach (array_slice($programList, 0, 3) as $p): ?>
                    <div class="card-ui2 card-featured">
                        <div class="card-body">
                            <span class="card-chip">🤝 Program Aktif</span>
                            <h3><?= esc($p['nama'] ?? 'Program Masjid') ?></h3>
                            <p><?= esc($p['ringkasan'] ?? $p['deskripsi'] ?? 'Program keumatan DKM Masjid.') ?></p>
                            <div class="card-meta-stack">
                                <span>📍 <?= esc($p['lokasi'] ?? 'Area Masjid') ?></span>
                                <span>👤 <?= esc($p['penanggung_jawab'] ?? 'Pengurus DKM') ?></span>
                            </div>
                            <a href="<?= site_url('program') ?>" class="btn-ui2 btn-primary-ui2 full-width-btn">
                                Selengkapnya & Ikut Serta
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">Belum ada data program kegiatan.</div>
        <?php endif; ?>
    </div>
</section>
