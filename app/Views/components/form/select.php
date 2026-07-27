<?php
$name = $name ?? '';
$label = $label ?? '';
$options = $options ?? [];
$selected = $selected ?? '';
$required = $required ?? false;
?>
<div class="form-group mb-3">
    <?php if ($label): ?>
        <label style="display: block; font-size: 14px; font-weight: 500; margin-bottom: 6px;">
            <?= esc($label) ?> <?php if ($required): ?><span style="color: red;">*</span><?php endif; ?>
        </label>
    <?php endif; ?>
    <select name="<?= esc($name) ?>" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border, #E5E7EB); border-radius: 6px; font-size: 14px; background: white;" <?= $required ? 'required' : '' ?>>
        <?php foreach ($options as $val => $optLabel): ?>
            <option value="<?= esc($val) ?>" <?= ($val == $selected) ? 'selected' : '' ?>><?= esc($optLabel) ?></option>
        <?php endforeach; ?>
    </select>
</div>
