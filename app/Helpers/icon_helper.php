<?php

/**
 * TASK-022 finding D (Icon Picker).
 *
 * The 'icon' column on bidang / layanan_masjid can now hold one of three
 * kinds of value, chosen via the Icon Picker widget
 * (public/assets/js/icon-picker.js):
 *   - a raw emoji character, e.g. '🏛️'                (stored as-is)
 *   - a FontAwesome class string, e.g. 'fa-solid fa-mosque'
 *   - a Bootstrap Icons class string, e.g. 'bi-building'
 *
 * render_icon() is the single place that knows how to turn any of those
 * back into safe HTML, so every view (admin cards, public sections) stays
 * consistent and none of them need to know the encoding rules themselves.
 */
if (!function_exists('render_icon')) {
    function render_icon(?string $value, string $fallbackEmoji = '⭐'): string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return esc($fallbackEmoji);
        }

        if (str_starts_with($value, 'fa-')) {
            return '<i class="' . esc($value, 'attr') . '" aria-hidden="true"></i>';
        }

        if (str_starts_with($value, 'bi-')) {
            return '<i class="bi ' . esc($value, 'attr') . '" aria-hidden="true"></i>';
        }

        // Anything else is treated as a raw emoji / plain text glyph.
        return esc($value);
    }
}
