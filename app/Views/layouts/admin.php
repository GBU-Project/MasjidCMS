<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token-name" content="<?= csrf_token() ?>">
    <meta name="csrf-token-value" content="<?= csrf_hash() ?>">
    <title><?= $this->renderSection('title') ?> — MasjidCMS Admin</title>
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

        <div class="user-nav-profile" style="position: relative;">
            <button class="btn btn-secondary" style="padding: 6px 12px; font-size: 12px;">🔔 Notifikasi</button>
            <div class="avatar-circle" id="userProfileTrigger" style="cursor: pointer; position: relative;" title="<?= esc(session()->get('auth_user')['displayName'] ?? 'Administrator') ?>">
                <?php
                    $displayName = session()->get('auth_user')['displayName'] ?? 'Administrator';
                    $initials = '';
                    $words = explode(' ', $displayName);
                    foreach ($words as $w) { if (!empty(trim($w))) $initials .= strtoupper(substr(trim($w), 0, 1)); }
                    echo esc(substr($initials, 0, 2));
                ?>
            </div>
            <!-- Profile Dropdown -->
            <div id="userProfileDropdown" style="display: none; position: absolute; top: 100%; right: 0; margin-top: 8px; background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); min-width: 220px; z-index: 9999; padding: 8px 0;">
                <div style="padding: 12px 16px; border-bottom: 1px solid #f1f5f9;">
                    <div style="font-weight: 700; font-size: 14px; color: #0f172a;"><?= esc($displayName) ?></div>
                    <div style="font-size: 12px; color: #64748b;"><?= esc(session()->get('auth_user')['username'] ?? '') ?></div>
                    <?php
                        $roles = session()->get('auth_user')['roles'] ?? [];
                        if (!empty($roles)) {
                            echo '<div style="font-size: 11px; color: #16a34a; margin-top: 2px;">' . esc(implode(', ', $roles)) . '</div>';
                        }
                    ?>
                </div>
                <a href="<?= site_url('admin/settings?tab=profile') ?>" style="display: block; padding: 10px 16px; font-size: 13px; color: #334155; text-decoration: none; transition: background 0.1s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">👤 My Profile</a>
                <a href="<?= site_url('admin/settings?tab=password') ?>" style="display: block; padding: 10px 16px; font-size: 13px; color: #334155; text-decoration: none; transition: background 0.1s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">🔑 Change Password</a>
                <div style="border-top: 1px solid #f1f5f9; margin: 4px 0;"></div>
                <a href="<?= site_url('logout') ?>" style="display: block; padding: 10px 16px; font-size: 13px; color: #ef4444; text-decoration: none; transition: background 0.1s;" onmouseover="this.style.background='#fef2f2'" onmouseout="this.style.background='transparent'">🚪 Logout</a>
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
