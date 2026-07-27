<?php
$createUrl = $createUrl ?? '#';
$createLabel = $createLabel ?? '+ Tambah Data';
?>
<div class="quick-action-bar" style="margin-bottom: 16px;">
    <a href="<?= esc($createUrl) ?>" class="btn btn-primary"><?= esc($createLabel) ?></a>
    <button class="btn btn-secondary">📥 Import Data</button>
    <button class="btn btn-secondary">📤 Export</button>
    <button class="btn btn-secondary" onclick="window.location.reload();">🔄 Refresh</button>
</div>
