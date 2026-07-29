<!-- Pengurus DKM Section UI 2.0 -->
<?php $sectionSettings = $settings ?? []; ?>
<section class="section-wrapper" id="pengurus" style="background: var(--slate-50);">
    <div class="container">
        <div class="section-header-center">
            <span class="section-tag"><?= esc($sectionSettings['pengurus_tag'] ?? 'STRUKTUR DKM') ?></span>
            <h2 class="section-title"><?= esc($sectionSettings['pengurus_title'] ?? 'Pengurus & Tokoh Masjid') ?></h2>
            <p class="section-subtitle"><?= esc($sectionSettings['pengurus_subtitle'] ?? 'Struktur kepengurusan DKM yang amanah dan berdedikasi mengabdi untuk kemakmuran masjid.') ?></p>
        </div>

        <?php if (!empty($pengurusList)): ?>
            <div class="card-grid-ui2 card-grid-profiles">
                <?php foreach (array_slice($pengurusList, 0, 4) as $peng): ?>
                    <div class="card-ui2 card-profile">
                        <div class="profile-avatar">👔</div>
                        <h4><?= esc($peng['nama'] ?? 'Pengurus DKM') ?></h4>
                        <span class="profile-role"><?= esc($peng['jabatan'] ?? 'Pengurus') ?></span>
                        <?php if (!empty($peng['bidang_name'])): ?>
                            <span class="profile-badge"><?= esc($peng['bidang_name']) ?></span>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
