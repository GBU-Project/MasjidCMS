<?php
/**
 * Standardized Description Editor — Reusable TinyMCE wrapper component.
 *
 * Usage:
 *   <?= view('admin/components/description_editor', [
 *       'name'        => 'description',
 *       'label'       => 'Deskripsi',
 *       'value'       => old('description', $item['description'] ?? ''),
 *       'editorClass' => 'tinymce-medium',    // tinymce-full | tinymce-medium | tinymce-simple
 *       'placeholder' => 'Tulis deskripsi...',
 *       'required'    => false,
 *       'rows'        => 4,
 *       'helpText'    => '💡 Gunakan editor untuk memformat teks.',
 *       'showCounter' => true,
 *   ])
 * ?>
 */
$editorClass = $editorClass ?? 'tinymce-medium';
$name        = $name ?? 'description';
$label       = $label ?? 'Deskripsi';
$value       = $value ?? '';
$placeholder = $placeholder ?? 'Tulis deskripsi di sini...';
$required    = $required ?? false;
$rows        = $rows ?? 4;
$helpText    = $helpText ?? '';
$showCounter = $showCounter ?? false;
$editorId    = $editorId ?? str_replace(['[', ']', '.'], '_', $name) . '_editor';
$counterId   = $counterId ?? $editorId . '_counter';
$maxLength   = $maxLength ?? 0;
?>

<div class="editor-wrapper" style="margin-bottom: 16px;">
    <?php if ($label): ?>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
        <label for="<?= esc($editorId) ?>" style="display: block; font-size: 13px; font-weight: 700; color: var(--text-primary, #1e293b);">
            <?= esc($label) ?>
            <?php if ($required): ?>
                <span style="color: #ef4444;">*</span>
            <?php endif; ?>
        </label>
        <?php if ($showCounter): ?>
        <span id="<?= esc($counterId) ?>" style="font-size: 12px; color: var(--text-tertiary, #64748b); font-weight: 600;">
            <?= strlen($value) ?> karakter
        </span>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <textarea
        id="<?= esc($editorId) ?>"
        name="<?= esc($name) ?>"
        class="<?= esc($editorClass) ?> form-control"
        rows="<?= (int) $rows ?>"
        placeholder="<?= esc($placeholder) ?>"
        <?= $required ? 'required' : '' ?>
        <?= $maxLength > 0 ? 'maxlength="' . (int) $maxLength . '"' : '' ?>
        style="width: 100%; min-height: <?= $editorClass === 'tinymce-full' ? '300px' : ($editorClass === 'tinymce-medium' ? '200px' : '120px') ?>; padding: 12px 14px; font-size: 14px; line-height: 1.6; border-radius: 8px; border: 1px solid var(--border-light, #e2e8f0); background: white; resize: vertical;"
    ><?= esc($value) ?></textarea>

    <?php if ($helpText): ?>
    <span style="font-size: 11px; color: var(--text-tertiary, #64748b); margin-top: 4px; display: block;"><?= $helpText ?></span>
    <?php endif; ?>
</div>

<?php if ($showCounter): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var editorEl = document.getElementById('<?= esc($editorId, 'js') ?>');
    var counterEl = document.getElementById('<?= esc($counterId, 'js') ?>');
    if (editorEl && counterEl) {
        editorEl.addEventListener('input', function() {
            counterEl.textContent = this.value.length + ' karakter';
        });
    }
});
</script>
<?php endif; ?>