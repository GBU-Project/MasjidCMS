<?php
$id = $id ?? 'modal-dialog';
$title = $title ?? 'Judul Modal Dialog';
$size = $size ?? 'md'; // sm, md, lg, fullscreen
$content = $content ?? '';
?>
<div id="<?= esc($id) ?>" style="position: fixed; inset: 0; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 100; visibility: hidden;">
    <div style="background: white; border-radius: 8px; max-width: <?= ($size === 'lg') ? '800px' : (($size === 'sm') ? '400px' : '600px') ?>; width: 90%; padding: 24px; box-shadow: 0 10px 15px rgba(0,0,0,0.2);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <h3 style="font-size: 18px; font-weight: 700;"><?= esc($title) ?></h3>
            <button onclick="document.getElementById('<?= esc($id) ?>').style.visibility='hidden';" style="background: none; border: none; font-size: 20px; cursor: pointer;">✕</button>
        </div>
        <div>
            <?= $content ?>
        </div>
    </div>
</div>
