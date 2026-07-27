<?php
$name = $name ?? '';
$label = $label ?? '';
$value = $value ?? '';
$placeholder = $placeholder ?? '0';
$required = $required ?? false;
?>
<div class="form-group mb-3">
    <?php if ($label): ?>
        <label style="display: block; font-size: 14px; font-weight: 500; margin-bottom: 6px;">
            <?= esc($label) ?> <?php if ($required): ?><span style="color: red;">*</span><?php endif; ?>
        </label>
    <?php endif; ?>
    <div style="display: flex; align-items: center;">
        <span style="padding: 8px 12px; background: var(--background, #F9FAFB); border: 1px solid var(--border, #E5E7EB); border-right: none; border-radius: 6px 0 0 6px; font-size: 14px; font-weight: 600;">Rp</span>
        <input type="number" name="<?= esc($name) ?>" value="<?= esc($value) ?>" placeholder="<?= esc($placeholder) ?>"
               style="width: 100%; padding: 8px 12px; border: 1px solid var(--border, #E5E7EB); border-radius: 0 6px 6px 0; font-size: 14px; font-family: var(--font-mono, monospace);" <?= $required ? 'required' : '' ?>>
    </div>
</div>
