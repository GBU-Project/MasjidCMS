<?php
/**
 * Reusable data table component.
 *
 * IMPORTANT: this component renders EXACTLY the headers/columns given to it.
 * Callers are expected to include their own "Aksi" header + action links as
 * the last column (see AdminMasterDataController for the standard pattern).
 * This component must NOT auto-generate a second action column, otherwise
 * every workspace ends up with duplicate "Aksi" columns (TASK-018).
 *
 * @param array $headers    Column headers, e.g. ['Nama', 'Status', 'Aksi']
 * @param array $rows       Each row: ['columns' => [...]] matching $headers
 * @param bool  $enableBulk Show the row-selection checkbox column. Only
 *                          enable this once bulk actions are actually wired
 *                          up for the module (TASK 4: hide if unimplemented).
 */
$headers = $headers ?? [];
$rows = $rows ?? [];
$enableBulk = $enableBulk ?? false;
$colspan = count($headers) + ($enableBulk ? 1 : 0);
?>
<div class="panel-card" style="padding: 0; margin-bottom: 16px; overflow: hidden;">
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <?php if ($enableBulk): ?>
                        <th style="width: 40px; text-align: center;"><input type="checkbox" class="js-bulk-select-all"></th>
                    <?php endif; ?>
                    <?php foreach ($headers as $header): ?>
                        <th><?= esc($header) ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($rows)): ?>
                    <tr>
                        <td colspan="<?= max($colspan, 1) ?>" style="text-align: center; padding: 32px;">
                            <?= view('components/empty_state') ?>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($rows as $row): ?>
                        <tr>
                            <?php if ($enableBulk): ?>
                                <td style="text-align: center;"><input type="checkbox" class="js-bulk-select-row" value="<?= esc($row['id'] ?? '') ?>"></td>
                            <?php endif; ?>
                            <?php foreach ($row['columns'] as $col): ?>
                                <td><?= $col ?></td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
