<!-- Agenda & Jadwal Kegiatan Masjid Section (separate from Kajian — finding H) -->
<?php $sectionSettings = $settings ?? []; ?>
<section class="section-wrapper" id="agenda" style="background: #f8fafc;">
    <div class="container">
        <div class="section-header-center">
            <span class="section-tag"><?= esc($sectionSettings['agenda_tag'] ?? 'AGENDA MASJID') ?></span>
            <h2 class="section-title"><?= esc($sectionSettings['agenda_title'] ?? 'Agenda & Jadwal Kegiatan') ?></h2>
            <p class="section-subtitle"><?= esc($sectionSettings['agenda_subtitle'] ?? 'Ikuti berbagai kegiatan dan acara masjid, mulai dari gotong royong, rapat DKM, hingga peringatan hari besar Islam.') ?></p>
        </div>

        <?php if (!empty($agendaList)): ?>
            <div class="card-grid-ui2 card-grid-events">
                <?php foreach ($agendaList as $a): ?>
                    <div class="card-ui2 card-event">
                        <div class="card-body">
                            <div class="event-head">
                                <div class="event-icon">📅</div>
                                <div>
                                    <h4><?= esc($a['title'] ?? 'Agenda Kegiatan') ?></h4>
                                    <span>Agenda Masjid</span>
                                </div>
                            </div>

                            <?php if (!empty($a['description'])): ?>
                                <p style="font-size: 13px; color: var(--text-muted, #64748b); margin: 8px 0;"><?= esc(mb_strimwidth($a['description'], 0, 120, '...')) ?></p>
                            <?php endif; ?>

                            <div class="card-meta-stack">
                                <span>📅 <?= esc($a['event_date'] ?? date('Y-m-d')) ?></span>
                                <?php if (!empty($a['event_time'])): ?>
                                    <span>⏰ <?= esc(substr($a['event_time'], 0, 5)) ?> WIB</span>
                                <?php endif; ?>
                                <?php if (!empty($a['location'])): ?>
                                    <span>📍 <?= esc($a['location']) ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">Belum ada agenda kegiatan mendatang.</div>
        <?php endif; ?>
    </div>
</section>
