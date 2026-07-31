<!-- Bidang Masjid Section -->
<?php $sectionSettings = $settings ?? []; ?>
<section class="section-wrapper" id="bidang" style="background: var(--white);">
    <div class="container">
        <div class="section-header-center">
            <span class="section-tag"><?= esc($sectionSettings['bidang_tag'] ?? 'BIDANG MASJID') ?></span>
            <h2 class="section-title"><?= esc($sectionSettings['bidang_title'] ?? 'Bidang & Divisi Masjid') ?></h2>
            <p class="section-subtitle"><?= esc($sectionSettings['bidang_subtitle'] ?? 'Berbagai bidang kegiatan yang dikelola DKM untuk kemakmuran masjid dan pemberdayaan umat.') ?></p>
        </div>

        <?php if (!empty($bidangList)): ?>
            <div class="card-grid-ui2 card-grid-profiles">
                <?php foreach (array_slice($bidangList, 0, 6) as $bidang): ?>
                    <div class="card-ui2 card-profile">
                        <div class="profile-avatar">🏛️</div>
                        <h4><?= esc($bidang['name'] ?? 'Bidang') ?></h4>
                        <?php if (!empty($bidang['description'])): ?>
                            <span class="profile-role"><?= esc(substr($bidang['description'], 0, 80)) ?><?= strlen($bidang['description']) > 80 ? '...' : '' ?></span>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>