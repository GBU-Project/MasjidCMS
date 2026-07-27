<?php
$name = $name ?? '';
$label = $label ?? '';
$value = $value ?? '';
$type = $type ?? 'text';
$placeholder = $placeholder ?? '';
$required = $required ?? false;
$disabled = $disabled ?? false;
$error = $error ?? '';
?>
<div class="form-group mb-3">
    <?php if ($label): ?>
        <label class="form-label" style="display: block; font-size: 14px; font-weight: 500; margin-bottom: 6px;">
            <?= esc($label) ?> <?php if ($required): ?><span style="color: var(--color-danger, red);">*</span><?php endif; ?>
        </label>
    <?php endif; ?>
    <input type="<?= esc($type) ?>" name="<?= esc($name) ?>" value="<?= esc($value) ?>" placeholder="<?= esc($placeholder) ?>"
           style="width: 100%; padding: 8px 12px; border: 1px solid <?= $error ? 'var(--color-danger, red)' : 'var(--border, #E5E7EB)' ?>; border-radius: 6px; font-size: 14px;"
           <?= $required ? 'required' : '' ?> <?= $disabled ? 'disabled' : '' ?>>
    <?php if ($error): ?>
        <p style="color: var(--color-danger, red); font-size: 12px; margin-top: 4px;"><?= esc($error) ?></p>
    <?php endif; ?>
</div>
