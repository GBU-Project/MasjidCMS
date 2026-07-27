<?php
$name = $name ?? '';
$label = $label ?? '';
$checked = $checked ?? false;
?>
<div class="form-group mb-3" style="display: flex; align-items: center; gap: 8px;">
    <input type="checkbox" name="<?= esc($name) ?>" <?= $checked ? 'checked' : '' ?> style="width: 16px; height: 16px; border-radius: 4px;">
    <label style="font-size: 14px; font-weight: 400; cursor: pointer;"><?= esc($label) ?></label>
</div>
