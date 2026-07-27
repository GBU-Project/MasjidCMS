<?php
$headers = $headers ?? [];
$rows = $rows ?? [];
?>
<div class="panel-card" style="padding: 0; margin-bottom: 16px; overflow: hidden;">
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 40px; text-align: center;"><input type="checkbox"></th>
                    <?php foreach ($headers as $header): ?>
                        <th><?= esc($header) ?></th>
                    <?php endforeach; ?>
                    <th style="text-align: right; padding-right: 24px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($rows)): ?>
                    <tr>
                        <td colspan="<?= count($headers) + 2 ?>" style="text-align: center; padding: 32px;">
                            <?= view('components/empty_state') ?>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($rows as $row): ?>
                        <tr>
                            <td style="text-align: center;"><input type="checkbox"></td>
                            <?php foreach ($row['columns'] as $col): ?>
                                <td><?= $col ?></td>
                            <?php endforeach; ?>
                            <td style="text-align: right; padding-right: 16px;">
                                <div style="display: inline-flex; gap: 4px;">
                                    <button class="btn btn-secondary" style="padding: 4px 8px; font-size: 12px;" title="Lihat Detail">👁️ View</button>
                                    <button class="btn btn-secondary" style="padding: 4px 8px; font-size: 12px;" title="Edit Data">✏️ Edit</button>
                                    <button class="btn btn-secondary" style="padding: 4px 8px; font-size: 12px;" title="Lihat Riwayat">📜 History</button>
                                    <button class="btn btn-secondary" style="padding: 4px 8px; font-size: 12px; color: var(--status-danger-text);" title="Hapus Data">🗑️</button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
