<?php
$type = $type ?? 'info'; // success, warning, danger, info
$message = $message ?? '';
?>
<?php if ($message): ?>
    <div style="padding: 12px 16px; border-radius: 6px; margin-bottom: 16px; font-size: 14px; display: flex; align-items: center; justify-content: space-between;
         background-color: <?= ($type === 'success') ? 'var(--status-success-bg, #DCFCE7)' : (($type === 'danger') ? 'var(--status-danger-bg, #FEE2E2)' : (($type === 'warning') ? 'var(--status-warning-bg, #FEF3C7)' : 'var(--status-info-bg, #E0F2FE)')) ?>;
         color: <?= ($type === 'success') ? 'var(--status-success-text, #166534)' : (($type === 'danger') ? 'var(--status-danger-text, #991B1B)' : (($type === 'warning') ? 'var(--status-warning-text, #92400E)' : 'var(--status-info-text, #075985)')) ?>;">
        <span><?= esc($message) ?></span>
    </div>
<?php endif; ?>
