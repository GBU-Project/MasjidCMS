<?= $this->extend('layouts/admin') ?>

<?= $this->section('title') ?>Hero Slider Management — Homepage CMS<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div style="padding: 24px; max-width: 1200px; margin: 0 auto;">
    <!-- Flash Notifications -->
    <?php if (session()->getFlashdata('success')): ?>
        <div style="padding: 12px 16px; background: #ecfdf5; border: 1px solid #6ee7b7; color: #065f46; border-radius: 8px; margin-bottom: 20px; font-size: 14px;">
            ✅ <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div style="padding: 12px 16px; background: #fef2f2; border: 1px solid #fca5a5; color: #991b1b; border-radius: 8px; margin-bottom: 20px; font-size: 14px;">
            ⚠️ <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <!-- Page Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <div>
            <div style="font-size: 13px; color: var(--text-tertiary); margin-bottom: 4px;">
                <a href="<?= site_url('admin/dashboard') ?>" style="color: var(--primary-600); text-decoration: none;">Dashboard</a> /
                <a href="<?= site_url('admin/homepage-manager') ?>" style="color: var(--primary-600); text-decoration: none;">Homepage Manager</a> / Hero Slider
            </div>
            <h1 style="font-size: 22px; font-weight: 800; color: var(--text-primary); margin: 0;">🖼️ Homepage Hero Management</h1>
            <p style="font-size: 13px; color: var(--text-muted); margin-top: 4px;">Kelola slide banner utama (Hero Section) di halaman depan portal masjid.</p>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="<?= site_url('admin/homepage-manager') ?>" class="btn btn-secondary" style="padding: 10px 16px; font-size: 13px;">⚙️ Homepage Manager</a>
            <a href="<?= site_url('admin/hero-slides/create') ?>" class="btn btn-primary" style="padding: 10px 16px; font-size: 13px; background: var(--primary-600); color: white; border-radius: 8px; text-decoration: none;">➕ Tambah Slide Hero</a>
        </div>
    </div>

    <!-- Main Card & Slides List -->
    <div class="panel-card" style="background: white; border: 1px solid var(--border-light); border-radius: 12px; padding: 20px;">
        <?php if (empty($slides)): ?>
            <div style="text-align: center; padding: 48px 16px;">
                <span style="font-size: 48px; display: block; margin-bottom: 12px;">🌄</span>
                <h3 style="font-size: 16px; font-weight: 700; color: var(--text-primary); margin-bottom: 6px;">Belum Ada Slide Hero Custom</h3>
                <p style="font-size: 13px; color: var(--text-muted); max-width: 480px; margin: 0 auto 20px;">
                    Halaman depan saat ini menampilkan tampilan Hero standar (default). Tambahkan slide baru untuk menyesuaikan gambar latar, judul, dan tombol CTA.
                </p>
                <a href="<?= site_url('admin/hero-slides/create') ?>" class="btn btn-primary" style="padding: 10px 18px; font-size: 13px; background: var(--primary-600); color: white; border-radius: 8px; text-decoration: none;">➕ Buat Slide Pertama</a>
            </div>
        <?php else: ?>
            <div style="margin-bottom: 14px; font-size: 12px; color: var(--text-muted); display: flex; align-items: center; gap: 6px;">
                💡 <span>Geser baris (Drag &amp; Drop) atau ubah status aktif untuk menentukan slide yang tampil di homepage.</span>
            </div>

            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                    <thead>
                        <tr style="border-bottom: 2px solid var(--border-light); text-align: left; background: #f8fafc;">
                            <th style="padding: 12px; width: 40px; text-align: center;">Urutan</th>
                            <th style="padding: 12px; width: 90px;">Background</th>
                            <th style="padding: 12px;">Judul &amp; Subtitle</th>
                            <th style="padding: 12px;">Tombol CTA</th>
                            <th style="padding: 12px; width: 100px;">Status</th>
                            <th style="padding: 12px; width: 120px;">Jadwal Tayang</th>
                            <th style="padding: 12px; width: 130px; text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="hero-slides-table-body">
                        <?php foreach ($slides as $index => $slide): ?>
                            <tr draggable="true" data-slide-id="<?= $slide['id'] ?>" style="border-bottom: 1px solid var(--border-light); cursor: move; transition: background 0.2s;" ondragstart="handleDragStart(event)" ondragover="handleDragOver(event)" ondrop="handleDrop(event)">
                                <td style="padding: 12px; text-align: center; font-weight: 700; color: var(--text-muted);">
                                    ☰ <span class="order-number"><?= $index + 1 ?></span>
                                </td>
                                <td style="padding: 12px;">
                                    <?php if (!empty($slide['bg_image_path'])): ?>
                                        <img src="<?= base_url($slide['bg_image_path']) ?>" style="width: 72px; height: 48px; object-fit: cover; border-radius: 6px; border: 1px solid var(--border-light);">
                                    <?php else: ?>
                                        <div style="width: 72px; height: 48px; background: #f1f5f9; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 10px; color: #94a3b8; border: 1px dashed #cbd5e1;">
                                            Default BG
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 12px;">
                                    <div style="font-weight: 700; color: var(--text-primary); margin-bottom: 4px;"><?= esc($slide['title']) ?></div>
                                    <div style="font-size: 12px; color: var(--text-secondary); max-width: 320px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?= esc($slide['subtitle'] ?? '-') ?></div>
                                </td>
                                <td style="padding: 12px;">
                                    <?php if (!empty($slide['primary_btn_text'])): ?>
                                        <span class="badge" style="background: #e0f2fe; color: #0369a1; font-size: 11px; margin-bottom: 3px; display: inline-block;">
                                            <?= esc($slide['primary_btn_text']) ?>
                                            <?php if ($slide['primary_btn_new_tab']): ?>↗<?php endif; ?>
                                        </span><br>
                                    <?php endif; ?>
                                    <?php if (!empty($slide['secondary_btn_text'])): ?>
                                        <span class="badge" style="background: #fef3c7; color: #b45309; font-size: 11px; display: inline-block;">
                                            <?= esc($slide['secondary_btn_text']) ?>
                                            <?php if ($slide['secondary_btn_new_tab']): ?>↗<?php endif; ?>
                                        </span>
                                    <?php endif; ?>
                                    <?php if (empty($slide['primary_btn_text']) && empty($slide['secondary_btn_text'])): ?>
                                        <span style="color: var(--text-tertiary); font-size: 12px;">-</span>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 12px;">
                                    <?php
                                        $badgeStyle = 'background: #f1f5f9; color: #475569;';
                                        if ($slide['status'] === 'ACTIVE') $badgeStyle = 'background: #dcfce7; color: #15803d; border: 1px solid #86efac;';
                                        elseif ($slide['status'] === 'DRAFT') $badgeStyle = 'background: #fef9c3; color: #a16207; border: 1px solid #fde047;';
                                        elseif ($slide['status'] === 'INACTIVE') $badgeStyle = 'background: #fef2f2; color: #b91c1c; border: 1px solid #fca5a5;';
                                    ?>
                                    <button type="button" onclick="toggleSlideStatus(<?= $slide['id'] ?>, this)" style="border: none; background: none; cursor: pointer; padding: 0;">
                                        <span class="badge status-badge-<?= $slide['id'] ?>" style="<?= $badgeStyle ?> font-size: 11px; padding: 4px 8px; border-radius: 6px;">
                                            <?= esc($slide['status']) ?>
                                        </span>
                                    </button>
                                </td>
                                <td style="padding: 12px; font-size: 11px; color: var(--text-secondary);">
                                    <?php if ($slide['publish_at'] || $slide['expire_at']): ?>
                                        <div>📅 Muka: <?= $slide['publish_at'] ? esc(substr($slide['publish_at'], 0, 10)) : 'Sekarang' ?></div>
                                        <div>⌛ Akhir: <?= $slide['expire_at'] ? esc(substr($slide['expire_at'], 0, 10)) : 'Selamanya' ?></div>
                                    <?php else: ?>
                                        <span style="color: var(--text-tertiary);">Selalu Tayang</span>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 12px; text-align: center;">
                                    <div style="display: flex; gap: 6px; justify-content: center;">
                                        <a href="<?= site_url('admin/hero-slides/edit/' . $slide['id']) ?>" class="btn btn-secondary" style="padding: 4px 10px; font-size: 12px; border-radius: 6px; text-decoration: none;">✏️ Edit</a>
                                        <form action="<?= site_url('admin/hero-slides/delete/' . $slide['id']) ?>" method="POST" onsubmit="return confirm('Hapus slide hero ini? (File di Media Library tidak akan terhapus)')" style="display: inline;">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-secondary" style="padding: 4px 10px; font-size: 12px; border-radius: 6px; color: #dc2626;">🗑️ Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
let dragSrcEl = null;

function handleDragStart(e) {
    dragSrcEl = e.currentTarget;
    e.dataTransfer.effectAllowed = 'move';
    e.dataTransfer.setData('text/html', e.currentTarget.innerHTML);
    e.currentTarget.style.opacity = '0.4';
}

function handleDragOver(e) {
    if (e.preventDefault) e.preventDefault();
    e.dataTransfer.dropEffect = 'move';
    return false;
}

function handleDrop(e) {
    if (e.stopPropagation) e.stopPropagation();
    let targetRow = e.currentTarget;
    if (dragSrcEl !== targetRow) {
        let tbody = document.getElementById('hero-slides-table-body');
        let rows = Array.from(tbody.querySelectorAll('tr'));
        let srcIndex = rows.indexOf(dragSrcEl);
        let targetIndex = rows.indexOf(targetRow);

        if (srcIndex < targetIndex) {
            tbody.insertBefore(dragSrcEl, targetRow.nextSibling);
        } else {
            tbody.insertBefore(dragSrcEl, targetRow);
        }

        saveNewSlideOrder();
    }
    dragSrcEl.style.opacity = '1';
    return false;
}

function saveNewSlideOrder() {
    let tbody = document.getElementById('hero-slides-table-body');
    let rows = Array.from(tbody.querySelectorAll('tr'));
    let orderData = [];

    rows.forEach((row, idx) => {
        let slideId = row.getAttribute('data-slide-id');
        orderData.push(slideId);
        let orderBadge = row.querySelector('.order-number');
        if (orderBadge) orderBadge.textContent = idx + 1;
    });

    let formData = new FormData();
    orderData.forEach(id => formData.append('order[]', id));
    formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

    fetch('<?= site_url('admin/hero-slides/save-order') ?>', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.status !== 'success') {
            alert('Gagal menyimpan urutan slide.');
        }
    })
    .catch(err => console.error(err));
}

function toggleSlideStatus(slideId, btnEl) {
    let formData = new FormData();
    formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

    fetch('<?= site_url('admin/hero-slides/toggle/') ?>' + slideId, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            let badge = document.querySelector('.status-badge-' + slideId);
            if (badge) {
                badge.textContent = data.new_status;
                if (data.new_status === 'ACTIVE') {
                    badge.style = 'background: #dcfce7; color: #15803d; border: 1px solid #86efac; font-size: 11px; padding: 4px 8px; border-radius: 6px;';
                } else if (data.new_status === 'DRAFT') {
                    badge.style = 'background: #fef9c3; color: #a16207; border: 1px solid #fde047; font-size: 11px; padding: 4px 8px; border-radius: 6px;';
                } else {
                    badge.style = 'background: #fef2f2; color: #b91c1c; border: 1px solid #fca5a5; font-size: 11px; padding: 4px 8px; border-radius: 6px;';
                }
            }
        } else {
            alert(data.message || 'Gagal merubah status.');
        }
    })
    .catch(err => console.error(err));
}
</script>
<?= $this->endSection() ?>
