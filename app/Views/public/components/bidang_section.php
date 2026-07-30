<!-- Bidang / Departemen Section UI 2.0 -->
<?php $sectionSettings = $settings ?? []; ?>
<section class="section-wrapper" id="bidang">
    <div class="container">
        <div class="section-header-center">
            <span class="section-tag"><?= esc($sectionSettings['bidang_tag'] ?? 'STRUKTUR ORGANISASI') ?></span>
            <h2 class="section-title"><?= esc($sectionSettings['bidang_title'] ?? 'Bidang & Departemen') ?></h2>
            <p class="section-subtitle"><?= esc($sectionSettings['bidang_subtitle'] ?? 'Bidang-bidang yang menjalankan program dan pelayanan masjid sehari-hari.') ?></p>
        </div>

        <?php if (!empty($bidangList)): ?>
            <div class="card-grid-ui2 card-grid-profiles">
                <?php foreach ($bidangList as $bid): ?>
                    <div class="card-ui2 card-profile">
                        <div class="profile-avatar"><?= esc($bid['icon'] ?? '🏛️') ?></div>
                        <h4><?= esc($bid['name'] ?? 'Bidang') ?></h4>
                        <?php if (!empty($bid['description'])): ?>
                            <span class="profile-role"><?= esc($bid['description']) ?></span>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
