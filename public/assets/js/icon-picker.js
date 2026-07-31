/**
 * Icon Picker — Reusable icon selection modal for admin forms.
 * Provides a searchable grid of commonly used icons for sections, bidang, menu items, etc.
 * 
 * Usage:
 *   <input type="text" id="myIconInput" class="icon-picker-trigger" readonly>
 *   <button onclick="openIconPicker('myIconInput')">Pick Icon</button>
 * 
 * Or via data attributes:
 *   <input type="text" class="icon-picker-input" data-icon-picker="true">
 *   <button class="icon-picker-btn" data-target="iconInputId">Pick</button>
 */

(function() {
    'use strict';

    // ---- Icon Library ----
    const ICON_SETS = {
        'Masjid & Ibadah': [
            { icon: '🕌', label: 'Masjid' },
            { icon: '🕋', label: 'Ka\'bah' },
            { icon: '🕍', label: 'Synagogue' },
            { icon: '⛪', label: 'Church' },
            { icon: '🙏', label: 'Pray' },
            { icon: '📿', label: 'Prayer Beads' },
            { icon: '☪️', label: 'Star & Crescent' },
            { icon: '🌙', label: 'Moon' },
            { icon: '⭐', label: 'Star' },
            { icon: '✨', label: 'Sparkles' },
            { icon: '🕯️', label: 'Candle' },
            { icon: '📖', label: 'Book (Quran)' },
            { icon: '🕰️', label: 'Clock' },
            { icon: '⏰', label: 'Alarm Clock' },
            { icon: '🕌', label: 'Mosque' },
        ],
        'Program & Kegiatan': [
            { icon: '🚩', label: 'Program' },
            { icon: '📋', label: 'Agenda' },
            { icon: '📅', label: 'Calendar' },
            { icon: '📆', label: 'Calendar Alt' },
            { icon: '🗓️', label: 'Spiral Calendar' },
            { icon: '🎯', label: 'Target' },
            { icon: '🏆', label: 'Achievement' },
            { icon: '🥇', label: 'Gold Medal' },
            { icon: '📈', label: 'Growth' },
            { icon: '📊', label: 'Chart' },
            { icon: '📝', label: 'Notes' },
            { icon: '✅', label: 'Check' },
            { icon: '📌', label: 'Pin' },
            { icon: '🔖', label: 'Bookmark' },
        ],
        'Organisasi & Pengurus': [
            { icon: '👔', label: 'Pengurus' },
            { icon: '👥', label: 'Jamaah' },
            { icon: '👤', label: 'User' },
            { icon: '🧑‍🤝‍🧑', label: 'Group' },
            { icon: '👨‍👩‍👧‍👦', label: 'Family' },
            { icon: '🏛️', label: 'Bidang' },
            { icon: '🏗️', label: 'Divisi' },
            { icon: '🔑', label: 'Role' },
            { icon: '🛡️', label: 'Permission' },
            { icon: '👑', label: 'Leader' },
            { icon: '🎓', label: 'Education' },
            { icon: '🧑‍🏫', label: 'Teacher' },
        ],
        'Layanan & Fasilitas': [
            { icon: '🤝', label: 'Layanan' },
            { icon: '🆘', label: 'Help' },
            { icon: '📞', label: 'Contact' },
            { icon: '📱', label: 'Mobile' },
            { icon: '💬', label: 'Chat' },
            { icon: '🗨️', label: 'Speech' },
            { icon: '📧', label: 'Email' },
            { icon: '🌐', label: 'Website' },
            { icon: '🔍', label: 'Search' },
            { icon: '⚕️', label: 'Health' },
            { icon: '🍽️', label: 'Dining' },
            { icon: '🚗', label: 'Parking' },
            { icon: '♿', label: 'Accessibility' },
            { icon: '🧹', label: 'Cleaning' },
        ],
        'Keuangan & Donasi': [
            { icon: '💰', label: 'Donasi' },
            { icon: '💳', label: 'Payment' },
            { icon: '🏦', label: 'Bank' },
            { icon: '📦', label: 'Zakat' },
            { icon: '🎁', label: 'Infaq' },
            { icon: '📊', label: 'Laporan' },
            { icon: '🧾', label: 'Receipt' },
            { icon: '📉', label: 'Expense' },
            { icon: '📈', label: 'Income' },
            { icon: '💹', label: 'Chart Up' },
            { icon: '🪙', label: 'Coin' },
            { icon: '💵', label: 'Money' },
        ],
        'Media & Dokumentasi': [
            { icon: '🖼️', label: 'Gallery' },
            { icon: '📷', label: 'Camera' },
            { icon: '🎥', label: 'Video' },
            { icon: '🎬', label: 'Film' },
            { icon: '📹', label: 'Video Cam' },
            { icon: '📸', label: 'Photo' },
            { icon: '🖌️', label: 'Design' },
            { icon: '🎨', label: 'Art' },
            { icon: '📰', label: 'News' },
            { icon: '📢', label: 'Announce' },
            { icon: '📣', label: 'Megaphone' },
            { icon: '🔔', label: 'Notification' },
            { icon: '📁', label: 'Folder' },
            { icon: '🗂️', label: 'Archive' },
        ],
        'Navigasi & UI': [
            { icon: '🏠', label: 'Home' },
            { icon: '📍', label: 'Location' },
            { icon: '🗺️', label: 'Map' },
            { icon: '🧭', label: 'Navigation' },
            { icon: '🔗', label: 'Link' },
            { icon: '⚙️', label: 'Settings' },
            { icon: '🔧', label: 'Tools' },
            { icon: '🛠️', label: 'Maintenance' },
            { icon: '📱', label: 'Mobile' },
            { icon: '💻', label: 'Desktop' },
            { icon: '🖥️', label: 'Monitor' },
            { icon: '🔄', label: 'Sync' },
            { icon: '📤', label: 'Upload' },
            { icon: '📥', label: 'Download' },
            { icon: '🗑️', label: 'Delete' },
            { icon: '✏️', label: 'Edit' },
            { icon: '📝', label: 'Write' },
        ],
        'Sosial & Komunikasi': [
            { icon: '💌', label: 'Mail' },
            { icon: '📨', label: 'Inbox' },
            { icon: '📩', label: 'Send' },
            { icon: '💬', label: 'Chat' },
            { icon: '🗣️', label: 'Speaking' },
            { icon: '👥', label: 'Community' },
            { icon: '🌍', label: 'Global' },
            { icon: '🤲', label: 'Hands' },
            { icon: '❤️', label: 'Love' },
            { icon: '💚', label: 'Green Heart' },
            { icon: '🫂', label: 'Hug' },
        ]
    };

    // ---- State ----
    let modalEl = null;
    let overlayEl = null;
    let currentCallback = null;

    // ---- Build Modal DOM ----
    function buildModal() {
        if (modalEl) return;

        // Overlay
        overlayEl = document.createElement('div');
        overlayEl.style.cssText = 'display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:10000;justify-content:center;align-items:center;padding:20px;';

        // Modal container
        modalEl = document.createElement('div');
        modalEl.style.cssText = 'background:white;width:100%;max-width:720px;max-height:80vh;border-radius:16px;display:flex;flex-direction:column;overflow:hidden;box-shadow:0 25px 50px -12px rgba(0,0,0,0.25);';

        // Header
        const header = document.createElement('div');
        header.style.cssText = 'padding:16px 20px;border-bottom:1px solid #e2e8f0;display:flex;justify-content:space-between;align-items:center;flex-shrink:0;';
        header.innerHTML = '<h3 style="margin:0;font-size:16px;font-weight:800;color:#0f172a;">🎨 Pilih Icon</h3>';

        const closeBtn = document.createElement('button');
        closeBtn.type = 'button';
        closeBtn.textContent = '✕';
        closeBtn.style.cssText = 'background:transparent;border:1px solid #e2e8f0;border-radius:8px;padding:6px 12px;cursor:pointer;font-size:14px;color:#64748b;';
        closeBtn.onclick = closeModal;
        header.appendChild(closeBtn);
        modalEl.appendChild(header);

        // Search bar
        const searchWrap = document.createElement('div');
        searchWrap.style.cssText = 'padding:12px 20px;border-bottom:1px solid #e2e8f0;flex-shrink:0;';
        const searchInput = document.createElement('input');
        searchInput.type = 'text';
        searchInput.id = 'iconPickerSearch';
        searchInput.placeholder = '🔍 Cari icon...';
        searchInput.style.cssText = 'width:100%;padding:10px 14px;border:1px solid #e2e8f0;border-radius:8px;font-size:14px;box-sizing:border-box;';
        searchInput.addEventListener('input', filterIcons);
        searchWrap.appendChild(searchInput);
        modalEl.appendChild(searchWrap);

        // Icon grid container (scrollable)
        const gridContainer = document.createElement('div');
        gridContainer.id = 'iconPickerGrid';
        gridContainer.style.cssText = 'padding:16px 20px;overflow-y:auto;flex:1;';

        // Build category sections
        for (const [category, icons] of Object.entries(ICON_SETS)) {
            const catSection = document.createElement('div');
            catSection.className = 'icon-picker-category';
            catSection.dataset.category = category;

            const catTitle = document.createElement('h4');
            catTitle.style.cssText = 'font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase;margin:0 0 8px 0;padding-top:12px;';
            catTitle.textContent = category;
            catSection.appendChild(catTitle);

            const iconRow = document.createElement('div');
            iconRow.style.cssText = 'display:flex;flex-wrap:wrap;gap:6px;margin-bottom:4px;';

            for (const item of icons) {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'icon-picker-item';
                btn.dataset.icon = item.icon;
                btn.dataset.label = item.label;
                btn.title = item.label;
                btn.style.cssText = 'width:44px;height:44px;display:flex;align-items:center;justify-content:center;font-size:24px;border:1px solid #e2e8f0;border-radius:8px;background:white;cursor:pointer;transition:all 0.15s;';
                btn.innerHTML = item.icon;
                btn.onmouseover = function() { this.style.borderColor = '#16a34a'; this.style.background = '#f0fdf4'; };
                btn.onmouseout = function() { this.style.borderColor = '#e2e8f0'; this.style.background = 'white'; };
                btn.onclick = function() { selectIcon(this.dataset.icon); };
                iconRow.appendChild(btn);
            }

            catSection.appendChild(iconRow);
            gridContainer.appendChild(catSection);
        }

        modalEl.appendChild(gridContainer);

        // Preview bar
        const previewBar = document.createElement('div');
        previewBar.id = 'iconPickerPreview';
        previewBar.style.cssText = 'padding:12px 20px;border-top:1px solid #e2e8f0;display:flex;align-items:center;gap:12px;flex-shrink:0;background:#f8fafc;';
        previewBar.innerHTML = '<span style="font-size:13px;color:#64748b;">Icon terpilih: <span id="iconPickerSelected" style="font-weight:700;color:#0f172a;">—</span></span><span style="font-size:28px;" id="iconPickerPreviewIcon"></span>';
        modalEl.appendChild(previewBar);

        overlayEl.appendChild(modalEl);
        document.body.appendChild(overlayEl);

        // Close on overlay click
        overlayEl.addEventListener('click', function(e) {
            if (e.target === overlayEl) closeModal();
        });
    }

    function filterIcons() {
        const query = document.getElementById('iconPickerSearch').value.toLowerCase().trim();
        const categories = document.querySelectorAll('.icon-picker-category');

        categories.forEach(cat => {
            const items = cat.querySelectorAll('.icon-picker-item');
            let hasVisible = false;

            items.forEach(btn => {
                const label = btn.dataset.label.toLowerCase();
                const icon = btn.dataset.icon;
                const match = label.includes(query) || icon.includes(query) || query === '';
                btn.style.display = match ? 'flex' : 'none';
                if (match) hasVisible = true;
            });

            cat.style.display = hasVisible ? 'block' : 'none';
        });
    }

    function selectIcon(icon) {
        document.getElementById('iconPickerSelected').textContent = icon;
        document.getElementById('iconPickerPreviewIcon').textContent = icon;
        // Auto-confirm after 600ms
        if (currentCallback) {
            setTimeout(function() {
                currentCallback(icon);
                closeModal();
            }, 300);
        }
    }

    function closeModal() {
        if (overlayEl) overlayEl.style.display = 'none';
        currentCallback = null;
    }

    // ---- Public API ----
    window.openIconPicker = function(callback) {
        buildModal();
        currentCallback = callback;
        overlayEl.style.display = 'flex';
        document.getElementById('iconPickerSearch').value = '';
        document.getElementById('iconPickerSelected').textContent = '—';
        document.getElementById('iconPickerPreviewIcon').textContent = '';
        filterIcons();
        // Focus search
        setTimeout(function() {
            document.getElementById('iconPickerSearch').focus();
        }, 100);
    };

    // ---- Auto-init: attach to data-attribute elements ----
    document.addEventListener('DOMContentLoaded', function() {
        // Trigger buttons with data-target
        document.querySelectorAll('.icon-picker-btn').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const targetId = this.dataset.target;
                if (targetId) {
                    const input = document.getElementById(targetId);
                    if (input) {
                        openIconPicker(function(icon) {
                            input.value = icon;
                            input.dispatchEvent(new Event('change', { bubbles: true }));
                        });
                    }
                }
            });
        });

        // Input fields with data-icon-picker attribute
        document.querySelectorAll('.icon-picker-input').forEach(function(input) {
            // Make it read-only
            input.readOnly = true;
            input.style.cursor = 'pointer';
            input.style.background = '#f8fafc';
            input.addEventListener('click', function() {
                const targetId = this.id;
                if (targetId) {
                    openIconPicker(function(icon) {
                        input.value = icon;
                        input.dispatchEvent(new Event('change', { bubbles: true }));
                    });
                }
            });
        });
    });

})();