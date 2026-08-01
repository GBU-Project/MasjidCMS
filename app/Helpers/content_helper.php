<?php

/**
 * Bug fix (UAT): article content authored in TinyMCE (Berita and other
 * CMS modules) was being displayed via nl2br(esc($content)) on the public
 * detail page. esc() HTML-encodes every tag, so the browser showed the
 * raw markup as literal text ("<p class="MsoNormal">...") instead of
 * rendering it -- and content pasted from Microsoft Word additionally
 * carries mso-* classes/styles and <o:p> tags that TinyMCE doesn't always
 * strip on its own.
 *
 * render_rich_content() renders the stored HTML directly (this is
 * trusted admin-authored CMS content, not public user input) after
 * stripping Word-specific cruft and anything genuinely dangerous
 * (script/style/iframe/on* attributes), so both the escaping bug and the
 * "MsoTitle/MsoNormal" clutter are fixed at the same time.
 */
if (!function_exists('render_rich_content')) {
    function render_rich_content(?string $html): string
    {
        $html = (string) $html;
        if (trim($html) === '') {
            return '';
        }

        // Strip script/style/iframe blocks entirely.
        $html = preg_replace('#<(script|style|iframe)\b[^>]*>.*?</\1>#is', '', $html);

        // Strip inline on* event handler attributes (onclick=, onerror=, ...).
        $html = preg_replace('/\son\w+\s*=\s*"[^"]*"/i', '', $html);
        $html = preg_replace("/\son\w+\s*=\s*'[^']*'/i", '', $html);

        // Strip Word/Office paste artifacts: mso-* inline styles, Mso*
        // class names, and empty <o:p></o:p> markers.
        $html = preg_replace('/mso-[^:;"]+:[^;"]+;?/i', '', $html);
        $html = preg_replace('/\bMso\w+\b/i', '', $html);
        $html = preg_replace('#<o:p>\s*</o:p>#i', '', $html);
        $html = preg_replace('#</?o:p>#i', '', $html);
        // Clean up now-empty class/style attributes left behind by the above.
        $html = preg_replace('/\sclass=""/i', '', $html);
        $html = preg_replace('/\sstyle=""/i', '', $html);

        return $html;
    }
}
