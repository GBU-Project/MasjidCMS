<?php
$errors = $errors ?? [];
?>
<?php if (!empty($errors)): ?>
    <div style="padding: 12px 16px; background-color: var(--status-danger-bg, #FEE2E2); border: 1px solid var(--color-danger, #991B1B); border-radius: 6px; margin-bottom: 16px;">
        <h4 style="font-size: 14px; font-weight: 600; color: var(--status-danger-text, #991B1B); margin-bottom: 4px;">Terdapat kesalahan validasi input:</h4>
        <ul style="margin-left: 20px; font-size: 13px; color: var(--status-danger-text, #991B1B);">
            <?php foreach ($errors as $err): ?>
                <li><?= esc($err) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>
