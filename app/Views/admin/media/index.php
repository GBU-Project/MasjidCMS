<?= $this->extend('layouts/admin') ?>

<?= $this->section('title') ?>Perpustakaan Media (Unified Media Library)<?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- Workspace Header -->
<div class="workspace-header" style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 16px; margin-bottom: 24px;">
    <div>
        <div style="font-size: 12px; font-weight: 500; color: var(--text-tertiary); margin-bottom: 4px;">
            <a href="<?= site_url('admin/dashboard') ?>" style="color: var(--primary-600); text-decoration: none;">Dashboard</a> / Media Library
        </div>
        <h1 class="page-title">📁 Unified Media Library</h1>
        <p class="page-subtitle">Pusat pengelolaan file media terpadu untuk Seluruh Modul (Berita, Kajian, Pages, Program, Layanan, Pengurus, & Donasi CTA).</p>
    </div>

    <div>
        <button type="button" class="btn btn-primary" onclick="document.getElementById('standaloneFileInput').click()" style="padding: 10px 20px; font-weight: 700;">
            📤 Unggah Media Baru
        </button>
        <input type="file" id="standaloneFileInput" multiple accept="image/*,.pdf" style="display: none;" onchange="document.getElementById('standaloneUploadForm').submit()">
    </div>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div style="background: #f0fdf4; border: 1px solid #86efac; color: #166534; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-weight: 500;">
        ✅ <?= session()->getFlashdata('success') ?>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div style="background: #fef2f2; border: 1px solid #fca5a5; color: #991b1b; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-weight: 500;">
        ⚠️ <?= session()->getFlashdata('error') ?>
    </div>
<?php endif; ?>

<!-- Instant Drag & Drop Dropzone Box -->
<form id="standaloneUploadForm" action="<?= site_url('admin/media/upload') ?>" method="POST" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <div id="mediaDropzone" style="border: 2px dashed var(--primary-600, #16a34a); background: var(--bg-surface, #f8fafc); border-radius: 12px; padding: 32px; text-align: center; margin-bottom: 28px; cursor: pointer; transition: all 0.2s;" onclick="document.getElementById('standaloneFileInput').click()">
        <span style="font-size: 40px; display: block; margin-bottom: 8px;">📤</span>
        <h3 style="font-size: 16px; font-weight: 800; color: var(--text-primary); margin-bottom: 4px;">Seret (Drag & Drop) File Gambar di Sini untuk Mengunggah Otomatis</h3>
        <p style="font-size: 13px; color: var(--text-muted); margin: 0;">Mendukung multi-file upload otomatis (Maksimal 10MB per file: JPG, PNG, WEBP, GIF, SVG, PDF)</p>
    </div>
</form>

<!-- Filter & Search Toolbar -->
<div class="panel-card" style="padding: 16px 20px; margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
    <form action="<?= site_url('admin/media') ?>" method="GET" style="display: flex; gap: 10px; flex-wrap: wrap; align-items: center; flex: 1;">
        <input type="text" name="q" value="<?= esc($search) ?>" placeholder="Cari nama file media..." class="form-control" style="width: 240px; padding: 8px 12px; font-size: 13px; border-radius: 6px; border: 1px solid var(--border-light);">
        <select name="type" class="form-control" style="padding: 8px 12px; font-size: 13px; border-radius: 6px; border: 1px solid var(--border-light);" onchange="this.form.submit()">
            <option value="all" <?= $filterType === 'all' ? 'selected' : '' ?>>Semua Tipe Media</option>
            <option value="image" <?= $filterType === 'image' ? 'selected' : '' ?>>🖼️ Gambar Only</option>
            <option value="document" <?= $filterType === 'document' ? 'selected' : '' ?>>📄 Dokumen Only</option>
        </select>
        <button type="submit" class="btn btn-secondary" style="padding: 8px 14px; font-size: 13px;">🔍 Cari</button>
        <?php if (!empty($search) || $filterType !== 'all'): ?>
            <a href="<?= site_url('admin/media') ?>" class="btn btn-secondary" style="padding: 8px 12px; font-size: 13px;">Reset Filter</a>
        <?php endif; ?>
    </form>

    <form action="<?= site_url('admin/media/bulk-delete') ?>" method="POST" id="bulkDeleteForm" onsubmit="return confirm('Hapus seluruh item tercentang secara permanen?')">
        <?= csrf_field() ?>
        <div id="bulkSelectionInputs"></div>
        <button type="submit" id="btnBulkDelete" class="btn btn-secondary" style="padding: 8px 14px; font-size: 12px; color: var(--status-danger-text); display: none;">
            🗑️ Hapus Terpilih (<span id="bulkCount">0</span>)
        </button>
    </form>
</div>

<!-- Media Grid Workspace -->
<?php if (!empty($mediaList)): ?>
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 20px; margin-bottom: 32px;">
        <?php foreach ($mediaList as $m): ?>
            <?php 
                $fileUrl = str_starts_with($m['filepath'], 'http') ? $m['filepath'] : base_url($m['filepath']);
                $isImg = str_starts_with($m['mime_type'], 'image/');
            ?>
            <div class="panel-card" style="padding: 12px; position: relative; border-radius: 10px; display: flex; flex-direction: column; justify-content: space-between;">
                <!-- Checkbox Selection -->
                <input type="checkbox" class="media-bulk-chk" value="<?= $m['id'] ?>" onchange="updateBulkDeleteState()" style="position: absolute; top: 18px; left: 18px; z-index: 10; width: 18px; height: 18px; cursor: pointer;">

                <!-- Thumbnail -->
                <div style="width: 100%; height: 130px; border-radius: 6px; overflow: hidden; background: var(--bg-surface, #f8fafc); border: 1px solid var(--border-light); display: flex; align-items: center; justify-content: center; margin-bottom: 10px;">
                    <?php if ($isImg): ?>
                        <img src="<?= esc($fileUrl) ?>" alt="<?= esc($m['filename']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                    <?php else: ?>
                        <span style="font-size: 36px;">📄</span>
                    <?php endif; ?>
                </div>

                <!-- Info Meta -->
                <div>
                    <h4 style="font-size: 13px; font-weight: 700; color: var(--text-primary); margin-bottom: 4px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?= esc($m['filename']) ?>">
                        <?= esc($m['filename']) ?>
                    </h4>
                    <span style="font-size: 11px; color: var(--text-tertiary); display: block; margin-bottom: 8px;">
                        <?= round($m['filesize'] / 1024, 1) ?> KB
                    </span>

                    <div style="display: flex; gap: 4px;">
                        <button type="button" onclick="copyToClipboard('<?= esc($fileUrl) ?>')" class="btn btn-secondary" style="flex: 1; padding: 4px 6px; font-size: 11px; text-align: center;" title="Salin URL Gambar">📋 URL</button>
                        <form action="<?= site_url('admin/media/delete/' . $m['id']) ?>" method="POST" onsubmit="return confirm('Hapus file media ini secara permanen?')" style="display: inline;">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-secondary" style="padding: 4px 8px; font-size: 11px; color: var(--status-danger-text);" title="Hapus Media">🗑️</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="panel-card" style="padding: 60px; text-align: center; color: var(--text-muted);">
        <span style="font-size: 48px; display: block; margin-bottom: 12px;">🖼️</span>
        <h3 style="font-size: 18px; font-weight: 800; color: var(--text-primary); margin-bottom: 6px;">Perpustakaan Media Kosong</h3>
        <p style="font-size: 14px; margin-bottom: 16px;">Belum ada file gambar yang diunggah ke dalam sistem.</p>
        <button type="button" class="btn btn-primary" onclick="document.getElementById('standaloneFileInput').click()" style="padding: 10px 20px;">
            📤 Unggah Gambar Pertama
        </button>
    </div>
<?php endif; ?>

<script>
function updateBulkDeleteState() {
    var checkboxes = document.querySelectorAll('.media-bulk-chk:checked');
    var btn = document.getElementById('btnBulkDelete');
    var countSpan = document.getElementById('bulkCount');
    var inputsDiv = document.getElementById('bulkSelectionInputs');

    inputsDiv.innerHTML = '';
    if (checkboxes.length > 0) {
        btn.style.display = 'inline-block';
        countSpan.innerText = checkboxes.length;
        checkboxes.forEach(function(chk) {
            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'ids[]';
            input.value = chk.value;
            inputsDiv.appendChild(input);
        });
    } else {
        btn.style.display = 'none';
        countSpan.innerText = '0';
    }
}

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        alert('URL Media berhasil disalin ke clipboard:\n' + text);
    }).catch(function() {
        prompt('Salin URL manual:', text);
    });
}
</script>
<?= $this->endSection() ?>
