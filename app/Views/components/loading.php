<?php
$type = $type ?? 'spinner'; // 'spinner', 'skeleton-card', 'skeleton-table'
?>
<?php if ($type === 'skeleton-table'): ?>
    <div style="padding: 16px; display: flex; flex-direction: column; gap: 12px;">
        <div style="height: 24px; background: #E5E7EB; border-radius: 4px; animation: pulse 1.5s infinite;"></div>
        <div style="height: 24px; background: #E5E7EB; border-radius: 4px; animation: pulse 1.5s infinite;"></div>
        <div style="height: 24px; background: #E5E7EB; border-radius: 4px; animation: pulse 1.5s infinite;"></div>
    </div>
<?php else: ?>
    <div style="display: flex; align-items: center; justify-content: center; padding: 24px; gap: 12px; font-size: 14px; color: var(--text-muted, #4B5563);">
        <div style="width: 20px; height: 20px; border: 2px solid #E5E7EB; border-top: 2px solid var(--color-primary, #059669); border-radius: 50%; animation: spin 1s linear infinite;"></div>
        <span>Memuat data...</span>
    </div>
<?php endif; ?>
