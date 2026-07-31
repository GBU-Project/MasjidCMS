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
            <button class="btn btn-secondary" style="padding: 6px 12px; font-size: 12px;">🔔 Notifikasi</button>
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

    <!-- Collapsible Sidebar Nav Bar -->
    <aside class="app-sidebar">
        <ul class="nav-menu-list">
            <li style="padding: 8px 16px; font-size: 11px; font-weight: 700; color: var(--text-tertiary); text-transform: uppercase;">DASHBOARD</li>
            <li>
                <a href="<?= site_url('admin/dashboard') ?>" class="nav-item-link">
                    <span>📊</span>
                    <span class="nav-text">Dashboard Utama</span>
                </a>
            </li>

            <li style="padding: 12px 16px 4px; font-size: 11px; font-weight: 700; color: var(--text-tertiary); text-transform: uppercase;">MASTER DATA</li>
            <li>
                <a href="<?= site_url('admin/masjid') ?>" class="nav-item-link">
                    <span>🕌</span>
                    <span class="nav-text">Profil Masjid</span>
                </a>
            </li>
            <li>
                <a href="<?= site_url('admin/master?tab=bidang') ?>" class="nav-item-link">
                    <span>🏛️</span>
                    <span class="nav-text">Bidang</span>
                </a>
            </li>
            <li>
                <a href="<?= site_url('admin/master?tab=pengurus') ?>" class="nav-item-link">
                    <span>👔</span>
                    <span class="nav-text">Pengurus</span>
                </a>
            </li>
            <li>
                <a href="<?= site_url('admin/jamaah') ?>" class="nav-item-link">
                    <span>👥</span>
                    <span class="nav-text">Jamaah</span>
                </a>
            </li>
            <li>
                <a href="<?= site_url('admin/family') ?>" class="nav-item-link">
                    <span>👨‍👩‍👧</span>
                    <span class="nav-text">Keluarga</span>
                </a>
            </li>
            <li>
                <a href="<?= site_url('admin/users') ?>" class="nav-item-link">
                    <span>👤</span>
                    <span class="nav-text">User</span>
                </a>
            </li>
            <li>
                <a href="<?= site_url('admin/master?tab=role') ?>" class="nav-item-link">
                    <span>🔑</span>
                    <span class="nav-text">Role</span>
                </a>
            </li>
            <li>
                <a href="<?= site_url('admin/master?tab=permission') ?>" class="nav-item-link">
                    <span>🛡️</span>
                    <span class="nav-text">Permission</span>
                </a>
            </li>

            <li style="padding: 12px 16px 4px; font-size: 11px; font-weight: 700; color: var(--text-tertiary); text-transform: uppercase;">CMS</li>
            <li>
                <a href="<?= site_url('admin/cms?tab=posts') ?>" class="nav-item-link">
                    <span>📰</span>
                    <span class="nav-text">Berita</span>
                </a>
            </li>
            <li>
                <a href="<?= site_url('admin/cms?tab=kajian') ?>" class="nav-item-link">
                    <span>🕌</span>
                    <span class="nav-text">Kajian</span>
                </a>
            </li>
            <li>
                <a href="<?= site_url('admin/cms?tab=program') ?>" class="nav-item-link">
                    <span>🚩</span>
                    <span class="nav-text">Program</span>
                </a>
            </li>
            <li>
                <a href="<?= site_url('admin/cms?tab=layanan') ?>" class="nav-item-link">
                    <span>🤝</span>
                    <span class="nav-text">Layanan</span>
                </a>
            </li>
            <li>
                <a href="<?= site_url('admin/cms?tab=pages') ?>" class="nav-item-link">
                    <span>📄</span>
                    <span class="nav-text">Pages</span>
                </a>
            </li>
            <li>
                <a href="<?= site_url('admin/cms?tab=gallery') ?>" class="nav-item-link">
                    <span>🖼️</span>
                    <span class="nav-text">Gallery</span>
                </a>
            </li>

            <li style="padding: 12px 16px 4px; font-size: 11px; font-weight: 700; color: var(--text-tertiary); text-transform: uppercase;">KEUANGAN</li>
            <li>
                <a href="<?= site_url('admin/financial') ?>" class="nav-item-link">
                    <span>💰</span>
                    <span class="nav-text">Keuangan & Kas</span>
                </a>
            </li>
            <li>
                <a href="<?= site_url('admin/financial/create') ?>" class="nav-item-link">
                    <span>➕</span>
                    <span class="nav-text">Input Transaksi</span>
                </a>
            </li>

            <li style="padding: 12px 16px 4px; font-size: 11px; font-weight: 700; color: var(--text-tertiary); text-transform: uppercase;">LAPORAN</li>
            <li>
                <a href="<?= site_url('admin/reporting') ?>" class="nav-item-link">
                    <span>📈</span>
                    <span class="nav-text">Laporan Keuangan</span>
                </a>
            </li>

            <li style="padding: 12px 16px 4px; font-size: 11px; font-weight: 700; color: var(--text-tertiary); text-transform: uppercase;">WEBSITE MANAGEMENT</li>
            <li>
                <a href="<?= site_url('admin/settings') ?>" class="nav-item-link">
                    <span>🌐</span>
                    <span class="nav-text">Website Settings</span>
                </a>
            </li>
            <li>
                <a href="<?= site_url('admin/homepage-manager') ?>" class="nav-item-link">
                    <span>🎨</span>
                    <span class="nav-text">Homepage Manager</span>
                </a>
            </li>
            <li>
                <a href="<?= site_url('admin/menu') ?>" class="nav-item-link">
                    <span>🧭</span>
                    <span class="nav-text">Navigation</span>
                </a>
            </li>
            <li>
                <a href="<?= site_url('admin/theme') ?>" class="nav-item-link">
                    <span>🎨</span>
                    <span class="nav-text">Theme</span>
                </a>
            </li>
            <li>
                <a href="<?= site_url('admin/media') ?>" class="nav-item-link">
                    <span>📁</span>
                    <span class="nav-text">Media Library</span>
                </a>
            </li>
            <li>
                <a href="<?= site_url('admin/settings?tab=seo') ?>" class="nav-item-link">
                    <span>🔍</span>
                    <span class="nav-text">SEO Settings</span>
                </a>
            </li>
            <li>
                <a href="<?= site_url('admin/settings?tab=advanced') ?>" class="nav-item-link">
                    <span>⚙️</span>
                    <span class="nav-text">Advanced Configuration</span>
                </a>
            </li>
            <li>
                <a href="<?= site_url('admin/notification') ?>" class="nav-item-link">
                    <span>🔔</span>
                    <span class="nav-text">Notifikasi</span>
                </a>
            </li>
            <li>
                <a href="<?= site_url('admin/prayer-time') ?>" class="nav-item-link">
                    <span>⏰</span>
                    <span class="nav-text">Jadwal Sholat</span>
                </a>
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
