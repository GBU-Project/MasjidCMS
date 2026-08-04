<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token-name" content="<?= csrf_token() ?>">
    <meta name="csrf-token-value" content="<?= csrf_hash() ?>">
    <!-- TASK-022 finding C: media-picker.js previously called fetch('/admin/media/api')
         and fetch('/admin/media/upload') with hardcoded absolute paths, which 404'd
         ("Failed loading media. Please ensure server is running.") on any install NOT
         at the domain root (e.g. XAMPP subfolder installs like /masjidgbu/). Expose
         base_url() so JS builds these paths correctly, same as PHP-side site_url(). -->
    <meta name="app-base-url" content="<?= rtrim(base_url(), '/') ?>">
    <title><?= $this->renderSection('title') ?> — MasjidCMS Admin</title>
    <!-- TASK-022 finding D (Icon Picker): FontAwesome & Bootstrap Icons are
         referenced via CDN (not vendored locally, unlike TinyMCE) so that
         icon-class values chosen in the picker actually render. This is a
         deliberate trade-off: the admin dashboard already requires network
         access for other things, but a fully offline install will show the
         emoji/text fallback instead of the icon glyph until these are
         self-hosted -- flagged here for a future pass if that matters. -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/css/app-theme.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/admin-dashboard.css') ?>">
</head>
<body>
    <!-- Top Header Nav Bar -->
    <header class="app-header">
        <div class="brand-container">
            <!-- Mobile menu toggle: sidebar used to be display:none below 640px with
                 no way back in. This button opens it as an off-canvas drawer instead. -->
            <button type="button" class="sidebar-toggle-btn" id="sidebarToggleBtn"
                    aria-label="Buka menu navigasi" aria-controls="appSidebar" aria-expanded="false">
                ☰
            </button>
            <div class="brand-logo">🕌</div>
            <span class="brand-name">MasjidCMS</span>
        </div>

        <div class="search-bar-header">
            <input type="text" placeholder="Cari Jamaah, Transaksi, atau Dokumen... (Press '/' to search)" aria-label="Global Search">
        </div>
        <?php
            // TASK-022 finding B: header previously had a hardcoded avatar
            // ("AD" / "Administrator DKM") with no link to the actual logged
            // in user, no dropdown, and no logout entry point from the
            // avatar. SecurityContext::user() is populated by
            // AuthenticationFilter before every admin:: route, so it's
            // reliably available here for any page using this layout.
            $currentUser = \App\Core\Security\SecurityContext::user();
            $profileName = $currentUser ? $currentUser->displayName() : 'Guest';
            $profileRole = $currentUser && !empty($currentUser->roles) ? $currentUser->roles[0] : '-';
            $profileInitials = $currentUser
                ? strtoupper(substr($profileName, 0, 1) . substr($profileName, strpos($profileName, ' ') !== false ? strpos($profileName, ' ') + 1 : 1, 1))
                : 'GU';
        ?>
        <div class="user-nav-profile">
            <button class="btn btn-secondary" style="padding: 6px 12px; font-size: 12px;" aria-label="Lihat notifikasi">🔔 Notifikasi</button>
            <div class="user-profile-dropdown" style="position: relative;">
                <button type="button" class="user-profile-trigger" onclick="toggleUserProfileMenu()" style="display: flex; align-items: center; gap: 8px; background: none; border: none; cursor: pointer; padding: 4px;" aria-haspopup="true" aria-expanded="false" id="userProfileTrigger">
                    <div class="avatar-circle" title="<?= esc($profileName) ?>"><?= esc($profileInitials) ?></div>
                    <span style="font-size: 12px; text-align: left; line-height: 1.3;">
                        <strong style="display: block;"><?= esc($profileName) ?></strong>
                        <span style="color: var(--text-tertiary);"><?= esc($profileRole) ?></span>
                    </span>
                    <span style="font-size: 10px;">▼</span>
                </button>
                <div id="userProfileMenu" class="user-profile-menu" style="display: none; position: absolute; right: 0; top: calc(100% + 8px); min-width: 200px; background: #fff; border: 1px solid var(--border-light); border-radius: 8px; box-shadow: 0 8px 24px rgba(0,0,0,0.12); overflow: hidden; z-index: 1000;">
                    <div style="padding: 10px 14px; border-bottom: 1px solid var(--border-light);">
                        <strong style="display: block; font-size: 13px;"><?= esc($profileName) ?></strong>
                        <span style="font-size: 11px; color: var(--text-tertiary);"><?= esc($currentUser->email ?? '') ?></span>
                    </div>
                    <a href="<?= site_url('admin/profile') ?>" style="display: block; padding: 10px 14px; font-size: 13px; text-decoration: none; color: inherit;">👤 My Profile</a>
                    <a href="<?= site_url('admin/profile/password') ?>" style="display: block; padding: 10px 14px; font-size: 13px; text-decoration: none; color: inherit;">🔒 Change Password</a>
                    <a href="<?= site_url('logout') ?>" style="display: block; padding: 10px 14px; font-size: 13px; text-decoration: none; color: #dc2626; border-top: 1px solid var(--border-light);">🚪 Logout</a>
                </div>
            </div>
        </div>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            var trigger = document.getElementById('userProfileTrigger');
            var dropdown = document.getElementById('userProfileDropdown');
            if (trigger && dropdown) {
                trigger.addEventListener('click', function(e) {
                    e.stopPropagation();
                    dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
                });
                document.addEventListener('click', function() {
                    dropdown.style.display = 'none';
                });
                dropdown.addEventListener('click', function(e) {
                    e.stopPropagation();
                });
            }
        });
        </script>
    </header>

    <!-- Mobile sidebar drawer controls (open/close via hamburger, backdrop, Escape,
         and auto-close after picking a menu item so the drawer doesn't linger). -->
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        var toggleBtn = document.getElementById('sidebarToggleBtn');
        var backdrop = document.getElementById('sidebarBackdrop');
        var sidebar = document.getElementById('appSidebar');

        function openSidebar() {
            document.body.classList.add('sidebar-open');
            if (toggleBtn) toggleBtn.setAttribute('aria-expanded', 'true');
        }
        function closeSidebar() {
            document.body.classList.remove('sidebar-open');
            if (toggleBtn) toggleBtn.setAttribute('aria-expanded', 'false');
        }

        if (toggleBtn) {
            toggleBtn.addEventListener('click', function () {
                document.body.classList.contains('sidebar-open') ? closeSidebar() : openSidebar();
            });
        }
        if (backdrop) {
            backdrop.addEventListener('click', closeSidebar);
        }
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') closeSidebar();
        });
        if (sidebar) {
            sidebar.querySelectorAll('a.nav-item-link').forEach(function (link) {
                link.addEventListener('click', closeSidebar);
            });
        }
    });
    </script>

    <script>
        function toggleUserProfileMenu() {
            var menu = document.getElementById('userProfileMenu');
            var trigger = document.getElementById('userProfileTrigger');
            if (!menu) return;
            var isOpen = menu.style.display === 'block';
            menu.style.display = isOpen ? 'none' : 'block';
            if (trigger) trigger.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
        }
        document.addEventListener('click', function (event) {
            var dropdown = document.querySelector('.user-profile-dropdown');
            var menu = document.getElementById('userProfileMenu');
            if (menu && dropdown && !dropdown.contains(event.target)) {
                menu.style.display = 'none';
            }
        });
    </script>

    <!-- Backdrop shown behind the drawer when the sidebar is opened on mobile -->
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <?php
        // Active-menu highlighting: previously NO sidebar link ever received the
        // `.active` class (the CSS rule existed but nothing applied it), so users
        // had no visual confirmation of where they were in a ~26-item menu.
        // We compare against 'admin/...' onward so this also works on subfolder
        // installs (e.g. /masjidgbu/admin/master) where REQUEST_URI has a prefix.
        $__requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?? '';
        $__requestQuery = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_QUERY) ?? '';
        
        $__pos = strpos($__requestPath, 'admin/');
        $GLOBALS['__currentBase'] = $__pos !== false ? rtrim(substr($__requestPath, $__pos), '/') : trim($__requestPath, '/');
        parse_str($__requestQuery, $GLOBALS['__currentQueryArr']);

        // Named function using $GLOBALS array so it works reliably inside CodeIgniter view scope.
        if (!function_exists('navClass')) {
            function navClass(string $route): string
            {
                [$routePath, $routeQuery] = array_pad(explode('?', $route, 2), 2, null);
                $pos = strpos($routePath, 'admin/');
                $routeBase = $pos !== false ? rtrim(substr($routePath, $pos), '/') : trim($routePath, '/');
                
                if ($routeBase !== ($GLOBALS['__currentBase'] ?? '')) {
                    return 'nav-item-link';
                }
                if ($routeQuery !== null) {
                    parse_str($routeQuery, $routeQueryArr);
                    foreach ($routeQueryArr as $key => $value) {
                        if ((($GLOBALS['__currentQueryArr'] ?? [])[$key] ?? null) !== $value) {
                            return 'nav-item-link';
                        }
                    }
                }
                return 'nav-item-link active';
            }
        }
    ?>

    <!-- Collapsible Sidebar Nav Bar -->
    <aside class="app-sidebar" id="appSidebar">
        <ul class="nav-menu-list">
            <li class="nav-group-label-static">DASHBOARD</li>
            <li>
                <a href="<?= site_url('admin/dashboard') ?>" class="<?= navClass('admin/dashboard') ?>">
                    <span class="nav-icon"><i class="bi bi-speedometer2"></i></span>
                    <span class="nav-text">Dashboard Utama</span>
                </a>
            </li>

            <li class="nav-group">
                <details class="nav-group-details" open>
                    <summary class="nav-group-summary">MASTER DATA</summary>
                    <ul class="nav-group-items">
                        <li>
                                    <a href="<?= site_url('admin/masjid') ?>" class="<?= navClass('admin/masjid') ?>">
                                        <span class="nav-icon"><i class="bi bi-building"></i></span>
                                        <span class="nav-text">Profil Masjid</span>
                                    </a>
                                </li>
                        <li>
                                    <a href="<?= site_url('admin/master?tab=bidang') ?>" class="<?= navClass('admin/master?tab=bidang') ?>">
                                        <span class="nav-icon"><i class="bi bi-diagram-3"></i></span>
                                        <span class="nav-text">Bidang</span>
                                    </a>
                                </li>
                        <li>
                                    <a href="<?= site_url('admin/master?tab=pengurus') ?>" class="<?= navClass('admin/master?tab=pengurus') ?>">
                                        <span class="nav-icon"><i class="bi bi-person-badge"></i></span>
                                        <span class="nav-text">Pengurus</span>
                                    </a>
                                </li>
                        <li>
                                    <a href="<?= site_url('admin/jamaah') ?>" class="<?= navClass('admin/jamaah') ?>">
                                        <span class="nav-icon"><i class="bi bi-people"></i></span>
                                        <span class="nav-text">Jamaah</span>
                                    </a>
                                </li>
                        <li>
                                    <a href="<?= site_url('admin/family') ?>" class="<?= navClass('admin/family') ?>">
                                        <span class="nav-icon"><i class="bi bi-house-heart"></i></span>
                                        <span class="nav-text">Keluarga</span>
                                    </a>
                                </li>
                        <li>
                                    <a href="<?= site_url('admin/users') ?>" class="<?= navClass('admin/users') ?>">
                                        <span class="nav-icon"><i class="bi bi-person-circle"></i></span>
                                        <span class="nav-text">User</span>
                                    </a>
                                </li>
                        <li>
                                    <a href="<?= site_url('admin/master?tab=role') ?>" class="<?= navClass('admin/master?tab=role') ?>">
                                        <span class="nav-icon"><i class="bi bi-key"></i></span>
                                        <span class="nav-text">Role</span>
                                    </a>
                                </li>
                        <li>
                                    <a href="<?= site_url('admin/master?tab=permission') ?>" class="<?= navClass('admin/master?tab=permission') ?>">
                                        <span class="nav-icon"><i class="bi bi-shield-lock"></i></span>
                                        <span class="nav-text">Permission</span>
                                    </a>
                                </li>
                    </ul>
                </details>
            </li>
            <li class="nav-group">
                <details class="nav-group-details" open>
                    <summary class="nav-group-summary">CMS</summary>
                    <ul class="nav-group-items">
                        <li>
                                    <a href="<?= site_url('admin/cms?tab=posts') ?>" class="<?= navClass('admin/cms?tab=posts') ?>">
                                        <span class="nav-icon"><i class="bi bi-newspaper"></i></span>
                                        <span class="nav-text">Berita</span>
                                    </a>
                                </li>
                        <li>
                                    <a href="<?= site_url('admin/cms?tab=kajian') ?>" class="<?= navClass('admin/cms?tab=kajian') ?>">
                                        <span class="nav-icon"><i class="bi bi-book"></i></span>
                                        <span class="nav-text">Kajian</span>
                                    </a>
                                </li>
                        <li>
                                    <a href="<?= site_url('admin/cms?tab=program') ?>" class="<?= navClass('admin/cms?tab=program') ?>">
                                        <span class="nav-icon"><i class="bi bi-flag"></i></span>
                                        <span class="nav-text">Program</span>
                                    </a>
                                </li>
                        <li>
                                    <a href="<?= site_url('admin/cms?tab=layanan') ?>" class="<?= navClass('admin/cms?tab=layanan') ?>">
                                        <span class="nav-icon"><i class="bi bi-briefcase"></i></span>
                                        <span class="nav-text">Layanan</span>
                                    </a>
                                </li>
                        <li>
                                    <a href="<?= site_url('admin/cms?tab=pages') ?>" class="<?= navClass('admin/cms?tab=pages') ?>">
                                        <span class="nav-icon"><i class="bi bi-file-earmark-text"></i></span>
                                        <span class="nav-text">Pages</span>
                                    </a>
                                </li>
                        <li>
                                    <a href="<?= site_url('admin/cms?tab=gallery') ?>" class="<?= navClass('admin/cms?tab=gallery') ?>">
                                        <span class="nav-icon"><i class="bi bi-images"></i></span>
                                        <span class="nav-text">Gallery</span>
                                    </a>
                                </li>
                    </ul>
                </details>
            </li>
            <li class="nav-group">
                <details class="nav-group-details" open>
                    <summary class="nav-group-summary">KEUANGAN</summary>
                    <ul class="nav-group-items">
                        <li>
                                    <a href="<?= site_url('admin/financial') ?>" class="<?= navClass('admin/financial') ?>">
                                        <span class="nav-icon"><i class="bi bi-cash-stack"></i></span>
                                        <span class="nav-text">Keuangan & Kas</span>
                                    </a>
                                </li>
                        <li>
                                    <a href="<?= site_url('admin/financial/create') ?>" class="<?= navClass('admin/financial/create') ?>">
                                        <span class="nav-icon"><i class="bi bi-plus-circle"></i></span>
                                        <span class="nav-text">Input Transaksi</span>
                                    </a>
                                </li>
                    </ul>
                </details>
            </li>
            <li class="nav-group">
                <details class="nav-group-details" open>
                    <summary class="nav-group-summary">LAPORAN</summary>
                    <ul class="nav-group-items">
                        <li>
                                    <a href="<?= site_url('admin/reporting') ?>" class="<?= navClass('admin/reporting') ?>">
                                        <span class="nav-icon"><i class="bi bi-graph-up"></i></span>
                                        <span class="nav-text">Laporan Keuangan</span>
                                    </a>
                                </li>
                    </ul>
                </details>
            </li>
            <li class="nav-group">
                <details class="nav-group-details" open>
                    <summary class="nav-group-summary">WEBSITE MANAGEMENT</summary>
                    <ul class="nav-group-items">
                        <li>
                                    <a href="<?= site_url('admin/settings') ?>" class="<?= navClass('admin/settings') ?>">
                                        <span class="nav-icon"><i class="bi bi-globe"></i></span>
                                        <span class="nav-text">Website Settings</span>
                                    </a>
                                </li>
                        <li>
                                    <a href="<?= site_url('admin/homepage-manager') ?>" class="<?= navClass('admin/homepage-manager') ?>">
                                        <span class="nav-icon"><i class="bi bi-layout-text-window"></i></span>
                                        <span class="nav-text">Homepage Manager</span>
                                    </a>
                                </li>
                        <li>
                                    <a href="<?= site_url('admin/hero-slides') ?>" class="<?= navClass('admin/hero-slides') ?>">
                                        <span class="nav-icon"><i class="bi bi-image"></i></span>
                                        <span class="nav-text">Hero Slider</span>
                                    </a>
                                </li>
                        <li>
                                    <a href="<?= site_url('admin/menu') ?>" class="<?= navClass('admin/menu') ?>">
                                        <span class="nav-icon"><i class="bi bi-compass"></i></span>
                                        <span class="nav-text">Navigation</span>
                                    </a>
                                </li>
                        <li>
                                    <a href="<?= site_url('admin/theme') ?>" class="<?= navClass('admin/theme') ?>">
                                        <span class="nav-icon"><i class="bi bi-palette"></i></span>
                                        <span class="nav-text">Theme</span>
                                    </a>
                                </li>
                        <li>
                                    <a href="<?= site_url('admin/media') ?>" class="<?= navClass('admin/media') ?>">
                                        <span class="nav-icon"><i class="bi bi-folder2-open"></i></span>
                                        <span class="nav-text">Media Library</span>
                                    </a>
                                </li>
                        <li>
                                    <a href="<?= site_url('admin/settings?tab=seo') ?>" class="<?= navClass('admin/settings?tab=seo') ?>">
                                        <span class="nav-icon"><i class="bi bi-search"></i></span>
                                        <span class="nav-text">SEO Settings</span>
                                    </a>
                                </li>
                        <li>
                                    <a href="<?= site_url('admin/settings?tab=advanced') ?>" class="<?= navClass('admin/settings?tab=advanced') ?>">
                                        <span class="nav-icon"><i class="bi bi-gear"></i></span>
                                        <span class="nav-text">Advanced Configuration</span>
                                    </a>
                                </li>
                        <li>
                                    <a href="<?= site_url('admin/notification') ?>" class="<?= navClass('admin/notification') ?>">
                                        <span class="nav-icon"><i class="bi bi-bell"></i></span>
                                        <span class="nav-text">Notifikasi</span>
                                    </a>
                                </li>
                        <li>
                                    <a href="<?= site_url('admin/prayer-time') ?>" class="<?= navClass('admin/prayer-time') ?>">
                                        <span class="nav-icon"><i class="bi bi-clock"></i></span>
                                        <span class="nav-text">Jadwal Sholat</span>
                                    </a>
                                </li>
                    </ul>
                </details>
            </li>
        </ul>
    </aside>

    <!-- Main Workspace Area -->
    <main class="app-main-content">
        <?= $this->renderSection('content') ?>
    </main>

    <!-- Universal Media Picker Modal -->
    <?= $this->include('admin/media/picker_modal') ?>

    <!-- Self-Hosted TinyMCE Community & Unified Media Library JS Engine -->
    <script>
    window.MEDIA_API_URL = '<?= site_url('admin/media/api?type=image') ?>';
    window.MEDIA_UPLOAD_URL = '<?= site_url('admin/media/upload') ?>';
    </script>
    <script src="<?= base_url('assets/vendor/tinymce/tinymce.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/media-picker.js') ?>"></script>
    <script src="<?= base_url('assets/js/icon-picker.js') ?>"></script>
</body>
</html>
