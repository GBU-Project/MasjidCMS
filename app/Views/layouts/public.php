<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->renderSection('title') ?> — Portal MasjidCMS</title>
    <?php if (!empty($masjid['favicon_url'])): ?>
        <link rel="icon" href="<?= esc($masjid['favicon_url']) ?>">
    <?php endif; ?>
    <!-- TASK-022 finding D: renders icon-class values (FontAwesome /
         Bootstrap Icons) picked for Bidang / Layanan on the public site. -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/css/public-portal.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/portal-ui2.css') ?>">
    <!--
        TASK-022A: Unified Public Header & Footer (UI 2.0).

        Before this task, header/footer each had TWO independent
        implementations (components/header.php + footer.php, only used by
        the homepage, vs. a second hardcoded pair baked directly into this
        layout for every other page). Fixed by including
        components/header.php and components/footer.php exactly once here,
        unconditionally, for every page.

        IMPORTANT (bug found in UAT after the first pass): the first
        version of this fix introduced BRAND NEW CSS classes/variables
        (--primary-*, --text-*, --border-light, .site-header-v2, ...) that
        don't exist in this project's actual public design system --
        portal-ui2.css already defines a complete, polished "UI 2.0" header
        (.site-header, .header-inner, .site-logo, .main-nav, .nav-link,
        .btn-ui2) using --emerald-*/--slate-*/--font-heading tokens. The
        new classes silently fell back to mismatched default colors/fonts
        and broke section layout across the site ("tampilan section
        kacau"). components/header.php and components/footer.php were
        rebuilt to REUSE those existing classes/tokens instead of
        competing with them; only the genuinely new pieces (dropdowns,
        search box, prayer widget, hamburger, footer grid) get new CSS
        below, using the same --emerald-*/--slate-*/--font-heading tokens
        as everything else.
    -->
    <style>
        /* ---- Berita section: explicit 4-column grid (auto-fit made a
             single post stretch to full width, looking like one giant
             card instead of a tidy news grid) ---- */
        .card-grid-news-4col { grid-template-columns: repeat(4, 1fr) !important; }
        @media (max-width: 1024px) { .card-grid-news-4col { grid-template-columns: repeat(2, 1fr) !important; } }
        @media (max-width: 640px) { .card-grid-news-4col { grid-template-columns: 1fr !important; } }
        .card-news .news-thumb { height: 140px; }
        .card-news .card-body p { display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }

        /* ---- Header overflow fix (bug found in UAT): the original
             .header-inner has a fixed height:66px, which clipped/overlapped
             content whenever logo + full nav + search + prayer widget +
             login didn't fit on one line at medium (tablet/small-laptop)
             widths -- looked "berantakan". Let it wrap cleanly instead, and
             push the hamburger breakpoint wider so those widths get the
             clean mobile menu rather than a squished desktop row. ---- */
        .site-header .header-inner { height: auto; min-height: 66px; flex-wrap: wrap; row-gap: 10px; padding-top: 10px; padding-bottom: 10px; }
        .nav-search input { width: 90px; }

        /* ---- Logo image variant (original .site-logo-icon assumed an
             emoji only; this supports an uploaded image too) ---- */
        .site-logo-img { height: 40px; max-width: 150px; width: auto; object-fit: contain; border-radius: var(--radius-md); }

        /* ---- Dropdown menus for Tentang Kami / Direktori / Layanan / Galeri ---- */
        .nav-dropdown { position: relative; }
        .nav-dropdown-trigger { background: none; border: none; cursor: pointer; font-family: inherit; display: inline-flex; align-items: center; gap: 4px; }
        .nav-dropdown-menu { display: none; position: absolute; top: 100%; left: 0; background: #fff; border: 1px solid var(--slate-200); border-radius: var(--radius-md); box-shadow: var(--shadow-lg); list-style: none; padding: 6px; margin-top: 10px; min-width: 200px; z-index: 1100; }
        .nav-dropdown.open .nav-dropdown-menu { display: block; }
        .nav-dropdown-menu a { display: block; padding: 9px 12px; font-size: 13.5px; font-weight: 500; color: var(--slate-700); text-decoration: none; border-radius: var(--radius-sm); }
        .nav-dropdown-menu a:hover { background: var(--emerald-50); color: var(--emerald-700); }

        /* ---- Header utilities: search, prayer widget, login ---- */
        .header-cta { flex-wrap: wrap; }
        .nav-search { display: flex; align-items: center; background: var(--slate-50); border: 1px solid var(--slate-200); border-radius: var(--radius-full); padding: 4px 4px 4px 14px; }
        .nav-search input { border: none; background: none; outline: none; font-size: 13px; width: 110px; font-family: var(--font-body); }
        .nav-search button { border: none; background: none; cursor: pointer; padding: 6px 10px; font-size: 13px; border-radius: var(--radius-full); }
        .nav-prayer-widget { display: flex; align-items: center; gap: 8px; text-decoration: none; background: linear-gradient(135deg, var(--emerald-600), var(--emerald-800)); color: #fff; padding: 7px 14px; border-radius: var(--radius-full); white-space: nowrap; }
        .nav-prayer-icon { font-size: 15px; }
        .nav-prayer-text { display: flex; flex-direction: column; line-height: 1.2; }
        .nav-prayer-label { font-size: 10px; opacity: .85; }
        .nav-prayer-time { font-size: 13px; font-weight: 800; font-family: var(--font-heading); }
        .nav-login-btn { padding: 9px 20px !important; font-size: 13px !important; }

        /* ---- Mobile hamburger nav (site already hides .main-nav <= 768px
             via portal-ui2.css; this adds the toggle behavior it lacked) ---- */
        .nav-hamburger-btn { display: none; flex-direction: column; justify-content: center; gap: 4px; background: none; border: none; cursor: pointer; padding: 8px; }
        .nav-hamburger-btn span { display: block; width: 22px; height: 2px; background: var(--slate-700); transition: transform .2s, opacity .2s; }
        .nav-hamburger-btn.open span:nth-child(1) { transform: translateY(6px) rotate(45deg); }
        .nav-hamburger-btn.open span:nth-child(2) { opacity: 0; }
        .nav-hamburger-btn.open span:nth-child(3) { transform: translateY(-6px) rotate(-45deg); }

        @media (max-width: 1024px) {
            .site-header .main-nav { display: none; }
            .nav-hamburger-btn { display: flex; }
            .site-header .main-nav.main-nav-open {
                display: flex; flex-direction: column; align-items: stretch; gap: 2px;
                position: absolute; top: 100%; left: 0; right: 0; background: #fff;
                border-bottom: 1px solid var(--slate-200); padding: 12px; box-shadow: var(--shadow-lg);
            }
            .main-nav-open .nav-dropdown-menu { position: static; box-shadow: none; border: none; margin-top: 0; padding-left: 12px; }
            .main-nav-open .header-cta { flex-direction: column; align-items: stretch; margin-top: 12px; }
            .main-nav-open .nav-search input { width: auto; flex: 1; }
        }

        /* ---- Footer grid responsiveness ---- */
        @media (max-width: 900px) {
            footer .container[style*="grid-template-columns"] { grid-template-columns: 1fr 1fr !important; }
        }
        @media (max-width: 560px) {
            footer .container[style*="grid-template-columns"] { grid-template-columns: 1fr !important; }
        }
    </style>
</head>
<body>
    <?= view('public/components/header', ['activePage' => $activePage ?? '', 'masjid' => $masjid ?? null, 'settings' => $settings ?? []]) ?>

    <main>
        <?= $this->renderSection('content') ?>
    </main>

    <?= view('public/components/footer', ['masjid' => $masjid ?? null]) ?>
</body>
</html>
