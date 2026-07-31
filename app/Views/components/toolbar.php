<?php
/**
 * Capability-based workspace toolbar (TASK-018).
 *
 * Each button is opt-in and must be explicitly enabled by the caller.
 * This prevents "dummy" buttons from appearing for capabilities a module
 * hasn't actually implemented yet (e.g. Import/Export/Bulk).
 *
 * @param string      $createUrl    Href for the "Tambah" button.
 * @param string      $createLabel  Label for the "Tambah" button.
 * @param bool        $showCreate   Show the "Tambah" button. Default true.
 * @param string|null $importUrl    Href for Import. Only shown if set.
 * @param string|null $exportUrl    Href for Export. Only shown if set.
 * @param bool        $showRefresh  Show the Refresh button. Default true.
 */
$createUrl = $createUrl ?? '#';
$createLabel = $createLabel ?? '+ Tambah Data';
$showCreate = $showCreate ?? true;
$importUrl = $importUrl ?? null;
$exportUrl = $exportUrl ?? null;
$showRefresh = $showRefresh ?? true;
?>
<div class="quick-action-bar" style="margin-bottom: 16px; display:flex; gap:8px; flex-wrap:wrap;">
    <?php if ($showCreate): ?>
        <a href="<?= esc($createUrl) ?>" class="btn btn-primary"><?= esc($createLabel) ?></a>
    <?php endif; ?>
    <?php if ($importUrl): ?>
        <a href="<?= esc($importUrl) ?>" class="btn btn-secondary">📥 Import Data</a>
    <?php endif; ?>
    <?php if ($exportUrl): ?>
        <a href="<?= esc($exportUrl) ?>" class="btn btn-secondary">📤 Export</a>
    <?php endif; ?>
    <?php if ($showRefresh): ?>
        <button type="button" class="btn btn-secondary" onclick="window.location.reload();">🔄 Refresh</button>
    <?php endif; ?>
</div>
