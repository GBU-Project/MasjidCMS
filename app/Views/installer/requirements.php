<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Step 2: System Requirements — MasjidCMS</title>
    <link rel="stylesheet" href="/assets/css/admin-dashboard.css">
</head>
<body style="display: flex; align-items: center; justify-content: center; min-height: 100vh; background: var(--bg-app); padding: 24px;">
    <div class="panel-card" style="max-width: 640px; width: 100%; padding: 32px;">
        <div style="font-size: 13px; font-weight: 600; color: var(--primary-600); margin-bottom: 4px;">STEP 2 OF 6</div>
        <h2 style="font-size: 20px; font-weight: 700; margin-bottom: 20px;">Pemeriksaan Persyaratan Server (System Requirements)</h2>

        <table class="data-table" style="margin-bottom: 24px;">
            <thead>
                <tr>
                    <th>Komponen Server</th>
                    <th>Status System</th>
                    <th>Hasil</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>PHP Version >= 8.2</td>
                    <td><?= esc($phpCheck['current']) ?></td>
                    <td><span class="badge <?= $phpCheck['pass'] ? 'badge-green' : 'badge-red' ?>"><?= $phpCheck['pass'] ? 'PASS' : 'FAIL' ?></span></td>
                </tr>
                <?php foreach ($extChecks as $ext): ?>
                    <tr>
                        <td><?= esc($ext['name']) ?></td>
                        <td><?= esc($ext['current']) ?></td>
                        <td><span class="badge <?= $ext['pass'] ? 'badge-green' : 'badge-red' ?>"><?= $ext['pass'] ? 'PASS' : 'FAIL' ?></span></td>
                    </tr>
                <?php endforeach; ?>
                <?php foreach ($permChecks as $perm): ?>
                    <tr>
                        <td><?= esc($perm['name']) ?></td>
                        <td><?= esc($perm['current']) ?></td>
                        <td><span class="badge <?= $perm['pass'] ? 'badge-green' : 'badge-red' ?>"><?= $perm['pass'] ? 'PASS' : 'FAIL' ?></span></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div style="display: flex; justify-content: space-between;">
            <a href="/install" class="btn btn-secondary">‹ Kembali</a>
            <?php if ($allPass): ?>
                <a href="/install/database" class="btn btn-primary">Lanjut ke Step 3 (Database) ›</a>
            <?php else: ?>
                <button class="btn btn-primary" disabled>Perbaiki Error Sebelum Lanjut</button>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
