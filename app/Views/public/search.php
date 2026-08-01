<?= $this->extend('layouts/public') ?>

<?= $this->section('title') ?>Hasil Pencarian<?= (!empty($query) ? ': ' . esc($query) : '') ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="portal-container">
    <h1 style="font-size: 26px; font-weight: 800; margin-bottom: 8px;">🔍 Hasil Pencarian</h1>
    <p style="color: var(--text-muted); margin-bottom: 24px;">
        <?= $query !== '' ? 'Menampilkan hasil untuk "' . esc($query) . '"' : 'Ketik kata kunci pada kotak pencarian di header.' ?>
    </p>

    <?php $totalResults = count($results['posts']) + count($results['programs']) + count($results['services']); ?>

    <?php if ($query !== '' && $totalResults === 0): ?>
        <div class="portal-card" style="text-align: center; color: var(--text-muted);">Tidak ditemukan hasil untuk "<?= esc($query) ?>".</div>
    <?php endif; ?>

    <?php if (!empty($results['posts'])): ?>
        <h2 style="font-size: 18px; font-weight: 700; margin: 24px 0 12px;">📰 Berita</h2>
        <div style="display: grid; gap: 10px;">
            <?php foreach ($results['posts'] as $p): ?>
                <a href="<?= site_url('berita/' . ($p['slug'] ?? '')) ?>" class="portal-card" style="text-decoration: none; color: inherit; display: block;"><?= esc($p['title']) ?></a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($results['programs'])): ?>
        <h2 style="font-size: 18px; font-weight: 700; margin: 24px 0 12px;">🚩 Program</h2>
        <div style="display: grid; gap: 10px;">
            <?php foreach ($results['programs'] as $p): ?>
                <a href="<?= site_url('program') ?>" class="portal-card" style="text-decoration: none; color: inherit; display: block;"><?= esc($p['nama']) ?></a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($results['services'])): ?>
        <h2 style="font-size: 18px; font-weight: 700; margin: 24px 0 12px;">🤝 Layanan</h2>
        <div style="display: grid; gap: 10px;">
            <?php foreach ($results['services'] as $s): ?>
                <a href="<?= site_url('layanan') ?>" class="portal-card" style="text-decoration: none; color: inherit; display: block;"><?= esc($s['nama']) ?></a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
