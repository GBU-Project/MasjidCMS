<!-- Berita & Warta Jamaah Section UI 2.0 -->
<?php $sectionSettings = $settings ?? []; ?>
<section class="section-wrapper" id="berita" style="background: linear-gradient(180deg, #fff 0%, var(--slate-50) 100%);">
    <div class="container">
        <div class="section-header-center">
            <span class="section-tag"><?= esc($sectionSettings['news_tag'] ?? 'WARTA JAMAAH') ?></span>
            <h2 class="section-title"><?= esc($sectionSettings['news_title'] ?? 'Berita & Informasi Terbaru') ?></h2>
            <p class="section-subtitle"><?= esc($sectionSettings['news_subtitle'] ?? 'Kabar terbaru seputar kegiatan, artikel keislaman, dan pengumuman DKM masjid.') ?></p>
        </div>

        <?php if (!empty($postsList)): ?>
            <div class="card-grid-ui2 card-grid-news">
                <?php foreach ($postsList as $post): ?>
                    <article class="card-ui2 card-news">
                        <div class="news-thumb">
                            <span>📰</span>
                        </div>
                        <div class="card-body">
                            <div class="news-meta-row">
                                <span class="news-category">Berita Masjid</span>
                                <span class="news-date">📅 <?= esc(substr($post['created_at'] ?? date('Y-m-d'), 0, 10)) ?></span>
                            </div>
                            <h3><?= esc($post['title'] ?? 'Judul Berita') ?></h3>
                            <p><?= esc(strip_tags($post['content'] ?? '')) ?></p>
                            <a href="<?= site_url('berita') ?>" class="news-link">Baca Artikel →</a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">Belum ada warta berita yang dipublikasikan.</div>
        <?php endif; ?>
    </div>
</section>
