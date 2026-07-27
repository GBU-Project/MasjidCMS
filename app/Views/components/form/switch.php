<?php
$name = $name ?? '';
$label = $label ?? '';
$checked = $checked ?? false;
?>
<div class="form-group mb-3" style="display: flex; align-items: center; justify-content: space-between;">
    <span style="font-size: 14px; font-weight: 500;"><?= esc($label) ?></span>
    <label style="position: relative; display: inline-block; width: 44px; height: 24px;">
        <input type="checkbox" name="<?= esc($name) ?>" <?= $checked ? 'checked' : '' ?> style="opacity: 0; width: 0; height: 0;">
        <span style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #ccc; transition: .4s; border-radius: 24px;"></span>
    </label>
</div>
