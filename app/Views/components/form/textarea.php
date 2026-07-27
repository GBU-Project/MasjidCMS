<?php
$name = $name ?? '';
$label = $label ?? '';
$value = $value ?? '';
$rows = $rows ?? 3;
$placeholder = $placeholder ?? '';
$required = $required ?? false;
$error = $error ?? '';
?>
<div class="form-group mb-3">
    <?php if ($label): ?>
        <label style="display: block; font-size: 14px; font-weight: 500; margin-bottom: 6px;">
            <?= esc($label) ?> <?php if ($required): ?><span style="color: red;">*</span><?php endif; ?>
        </label>
    <?php endif; ?>
    <textarea name="<?= esc($name) ?>" rows="<?= (int) $rows ?>" placeholder="<?= esc($placeholder) ?>"
              style="width: 100%; padding: 8px 12px; border: 1px solid <?= $error ? 'red' : 'var(--border, #E5E7EB)' ?>; border-radius: 6px; font-size: 14px;"
              <?= $required ? 'required' : '' ?>><?= esc($value) ?></textarea>
    <?php if ($error): ?>
        <p style="color: red; font-size: 12px; margin-top: 4px;"><?= esc($error) ?></p>
    <?php endif; ?>
</div>
