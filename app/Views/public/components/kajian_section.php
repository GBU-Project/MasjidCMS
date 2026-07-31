<!-- Highlight Kajian Section UI 2.0 -->
<?php $sectionSettings = $settings ?? []; ?>
<section class="section-wrapper" id="kajian" style="background: #fff;">
    <div class="container">
        <div class="section-header-center">
            <span class="section-tag"><?= esc($sectionSettings['kajian_tag'] ?? 'KAJIAN RUTIN') ?></span>
            <h2 class="section-title"><?= esc($sectionSettings['kajian_title'] ?? 'Highlight Kajian & Jadwal Taklim') ?></h2>
            <p class="section-subtitle"><?= esc($sectionSettings['kajian_subtitle'] ?? 'Tingkatkan keilmuan dan ketakwaan melalui jadwal kajian rutin bersama ustadz dan ulama terpilih.') ?></p>
        </div>

        <?php if (!empty($kajianList)): ?>
            <div class="card-grid-ui2 card-grid-events">
                <?php foreach ($kajianList as $k): ?>
                    <div class="card-ui2 card-event">
                        <div class="card-body">
                            <div class="event-head">
                                <div class="event-icon">☪️</div>
                                <div>
                                    <h4><?= esc($k['speaker_name'] ?? 'Ustadz Penceramah') ?></h4>
                                    <span>Penceramah Utama</span>
                                </div>
                            </div>

                            <h3><?= esc($k['topic'] ?? 'Tema Kajian Rutin') ?></h3>

                            <div class="card-meta-stack">
                                <span>📅 <?= esc($k['schedule_date'] ?? date('Y-m-d')) ?></span>
                                <span>⏰ <?= esc(substr($k['schedule_time'] ?? '19:30', 0, 5)) ?> WIB</span>
                                <span>📍 <?= esc($k['location'] ?? 'Ruang Utama Masjid') ?></span>
                            </div>

                            <a href="<?= site_url('berita') ?>" class="btn-ui2 btn-secondary-ui2 full-width-btn">
                                Detail & Catatan Kajian
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">Belum ada agenda kajian mendatang.</div>
        <?php endif; ?>
    </div>
</section>
