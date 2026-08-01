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

        Before this task, the header and footer each had TWO separate,
        independently maintained implementations:
          - components/header.php / components/footer.php ("UI 2.0" style)
            were only ever included manually by public/index.php (the
            homepage).
          - This layout file ALSO had its own completely separate, fully
            hardcoded header/footer (gated by a $showLayoutHeader flag so
            it only showed on every OTHER page), with a static mosque name
            ("Masjid Agung Darussalam"), fake contact info, no logo, and a
            different, incomplete nav.
        That's why earlier bug reports (logo not appearing on inner pages,
        inconsistent nav) kept resurfacing: fixing one implementation never
        touched the other. This layout now includes components/header.php
        and components/footer.php ONCE, unconditionally, so every page that
        extends this layout automatically gets the exact same header/nav/
        footer -- single source of truth, per TASK-022A.
    -->
    <style>
        /* ===== Unified Header UI 2.0 ===== */
        .site-header-v2 { background: #fff; border-bottom: 1px solid var(--border-light, #e2e8f0); position: sticky; top: 0; z-index: 500; }
        .header-v2-inner { display: flex; align-items: center; justify-content: space-between; padding: 12px 20px; gap: 16px; }
        .site-logo-v2 { display: flex; align-items: center; gap: 10px; text-decoration: none; flex-shrink: 0; }
        .site-logo-v2-img { height: 38px; max-width: 150px; width: auto; object-fit: contain; border-radius: 6px; }
        .site-logo-v2-fallback { font-size: 26px; }
        .site-logo-v2-text { font-size: 17px; font-weight: 800; color: var(--text-primary, #1e293b); white-space: nowrap; }

        .nav-hamburger-btn { display: none; flex-direction: column; justify-content: center; gap: 4px; background: none; border: none; cursor: pointer; padding: 8px; }
        .nav-hamburger-btn span { display: block; width: 22px; height: 2px; background: var(--text-primary, #1e293b); transition: transform .2s, opacity .2s; }
        .nav-hamburger-btn.open span:nth-child(1) { transform: translateY(6px) rotate(45deg); }
        .nav-hamburger-btn.open span:nth-child(2) { opacity: 0; }
        .nav-hamburger-btn.open span:nth-child(3) { transform: translateY(-6px) rotate(-45deg); }

        .nav-v2 { display: flex; align-items: center; gap: 20px; flex: 1; justify-content: flex-end; flex-wrap: wrap; }
        .nav-v2-list { list-style: none; display: flex; align-items: center; gap: 4px; margin: 0; padding: 0; }
        .nav-v2-list > li > a, .nav-v2-dropdown-trigger { display: inline-flex; align-items: center; gap: 4px; padding: 8px 12px; font-size: 14px; font-weight: 600; color: var(--text-secondary, #475569); text-decoration: none; background: none; border: none; cursor: pointer; border-radius: 6px; font-family: inherit; }
        .nav-v2-list > li > a.active, .nav-v2-dropdown-trigger.active, .nav-v2-list > li > a:hover, .nav-v2-dropdown-trigger:hover { color: var(--primary-700, #15803d); background: var(--primary-50, #f0fdf4); }

        .nav-v2-dropdown { position: relative; }
        .nav-v2-dropdown-menu { display: none; position: absolute; top: 100%; left: 0; background: #fff; border: 1px solid var(--border-light, #e2e8f0); border-radius: 10px; box-shadow: 0 12px 24px -8px rgba(0,0,0,.15); list-style: none; padding: 6px; margin: 6px 0 0; min-width: 200px; z-index: 600; }
        .nav-v2-dropdown.open .nav-v2-dropdown-menu { display: block; }
        .nav-v2-dropdown-menu li a { display: block; padding: 9px 12px; font-size: 13.5px; font-weight: 500; color: var(--text-primary, #1e293b); text-decoration: none; border-radius: 6px; }
        .nav-v2-dropdown-menu li a:hover { background: var(--primary-50, #f0fdf4); color: var(--primary-700, #15803d); }

        .nav-v2-utilities { display: flex; align-items: center; gap: 10px; }
        .nav-v2-search { display: flex; align-items: center; background: var(--slate-50, #f8fafc); border: 1px solid var(--border-light, #e2e8f0); border-radius: 20px; padding: 4px 4px 4px 12px; }
        .nav-v2-search input { border: none; background: none; outline: none; font-size: 13px; width: 120px; }
        .nav-v2-search button { border: none; background: none; cursor: pointer; padding: 6px 10px; font-size: 13px; border-radius: 50%; }

        .nav-v2-prayer-widget { display: flex; align-items: center; gap: 8px; text-decoration: none; background: linear-gradient(135deg, var(--primary-600, #16a34a), var(--primary-700, #15803d)); color: #fff; padding: 6px 14px; border-radius: 20px; font-size: 12px; white-space: nowrap; }
        .nav-v2-prayer-icon { font-size: 15px; }
        .nav-v2-prayer-text { display: flex; flex-direction: column; line-height: 1.2; }
        .nav-v2-prayer-label { font-size: 10px; opacity: .85; }
        .nav-v2-prayer-time { font-size: 13px; font-weight: 800; }

        .nav-v2-login-btn { background: var(--primary-600, #16a34a); color: #fff; text-decoration: none; padding: 8px 18px; border-radius: 20px; font-size: 13px; font-weight: 700; white-space: nowrap; }
        .nav-v2-login-btn:hover { background: var(--primary-700, #15803d); }

        @media (max-width: 900px) {
            .nav-hamburger-btn { display: flex; }
            .nav-v2 { display: none; position: absolute; top: 100%; left: 0; right: 0; background: #fff; flex-direction: column; align-items: stretch; border-bottom: 1px solid var(--border-light, #e2e8f0); padding: 12px; box-shadow: 0 12px 24px -8px rgba(0,0,0,.1); }
            .nav-v2.nav-v2-open { display: flex; }
            .nav-v2-list { flex-direction: column; align-items: stretch; gap: 2px; }
            .nav-v2-dropdown-menu { position: static; box-shadow: none; border: none; padding-left: 12px; margin-top: 0; }
            .nav-v2-dropdown.open .nav-v2-dropdown-menu { display: block; }
            .nav-v2-utilities { flex-direction: column; align-items: stretch; margin-top: 12px; gap: 8px; }
            .nav-v2-search input { width: auto; flex: 1; }
            .site-header-v2 { position: static; }
        }

        /* ===== Unified Footer UI 2.0 ===== */
        .site-footer-v2 { background: var(--slate-900, #0f172a); color: var(--slate-400, #94a3b8); padding: 56px 0 24px; font-size: 14px; margin-top: 48px; }
        .footer-v2-grid { display: grid; grid-template-columns: 1.4fr 1fr 1fr 1fr; gap: 32px; margin-bottom: 40px; }
        .footer-v2-logo { display: flex; align-items: center; gap: 10px; margin-bottom: 14px; }
        .footer-v2-logo img { height: 34px; max-width: 130px; object-fit: contain; border-radius: 6px; }
        .footer-v2-logo-fallback { font-size: 24px; }
        .footer-v2-logo-text { font-size: 18px; font-weight: 800; color: #fff; }
        .footer-v2-desc { line-height: 1.6; margin-bottom: 16px; }
        .footer-v2-social { display: flex; gap: 10px; }
        .footer-v2-social a { display: inline-flex; align-items: center; justify-content: center; width: 34px; height: 34px; border-radius: 50%; background: var(--slate-800, #1e293b); text-decoration: none; font-size: 15px; }
        .footer-v2-col h4 { color: #fff; font-size: 15px; margin-bottom: 14px; }
        .footer-v2-col ul { list-style: none; margin: 0; padding: 0; display: flex; flex-direction: column; gap: 9px; }
        .footer-v2-col ul li a { color: var(--slate-400, #94a3b8); text-decoration: none; font-size: 13.5px; }
        .footer-v2-col ul li a:hover { color: #fff; }
        .footer-v2-contact li { font-size: 13.5px; line-height: 1.5; }
        .footer-v2-prayer-shortcut { display: inline-block; margin-top: 12px; color: var(--primary-400, #4ade80); text-decoration: none; font-weight: 700; font-size: 13px; }
        .footer-v2-bottom { border-top: 1px solid var(--slate-800, #1e293b); padding-top: 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; font-size: 12.5px; }
        .footer-v2-powered strong { color: #fff; }
        @media (max-width: 900px) {
            .footer-v2-grid { grid-template-columns: 1fr 1fr; }
        }
        @media (max-width: 560px) {
            .footer-v2-grid { grid-template-columns: 1fr; }
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
