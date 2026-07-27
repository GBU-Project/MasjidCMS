<?php
$name = $name ?? '';
$label = $label ?? '';
$value = $value ?? date('Y-m-d');
$required = $required ?? false;
?>
<div class="form-group mb-3">
    <?php if ($label): ?>
        <label style="display: block; font-size: 14px; font-weight: 500; margin-bottom: 6px;">
            <?= esc($label) ?> <?php if ($required): ?><span style="color: red;">*</span><?php endif; ?>
        </label>
    <?php endif; ?>
    <input type="date" name="<?= esc($name) ?>" value="<?= esc($value) ?>" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border, #E5E7EB); border-radius: 6px; font-size: 14px;" <?= $required ? 'required' : '' ?>>
</div>
