<?php
$placeholder = $placeholder ?? 'Cari data master...';
?>
<div class="panel-card" style="padding: 16px; margin-bottom: 16px;">
    <div style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
        <div style="flex: 1; min-width: 240px; position: relative;">
            <input type="text" placeholder="<?= esc($placeholder) ?>" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: var(--radius-md); font-size: 14px;">
        </div>
        <select style="padding: 8px 12px; border: 1px solid var(--border-light); border-radius: var(--radius-md); font-size: 14px; background-color: white;">
            <option value="">Semua Status</option>
            <option value="ACTIVE">Aktif</option>
            <option value="INACTIVE">Non-Aktif</option>
        </select>
        <select style="padding: 8px 12px; border: 1px solid var(--border-light); border-radius: var(--radius-md); font-size: 14px; background-color: white;">
            <option value="10">10 per halaman</option>
            <option value="25">25 per halaman</option>
            <option value="50">50 per halaman</option>
        </select>
        <button class="btn btn-secondary">Bulk Actions ▾</button>
    </div>
</div>
