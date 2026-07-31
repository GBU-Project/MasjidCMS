/**
 * TASK-022 finding D — Icon Picker.
 *
 * Before: every "icon" field in the CMS (Bidang, Layanan) was a plain text
 * input where the user had to manually find and paste an emoji or symbol
 * themselves. This widget replaces that with a lightweight modal offering
 * three tabs -- Emoji, FontAwesome, Bootstrap Icons -- and writes the
 * chosen identifier into the target input:
 *   - Emoji tab writes the raw emoji character (there's no separate
 *     "identifier" for emoji; the character itself already fully
 *     specifies what to render, exactly like the old free-text field did).
 *   - FontAwesome tab writes a class string like "fa-solid fa-mosque".
 *   - Bootstrap Icons tab writes a class string like "bi-building".
 * app/Helpers/icon_helper.php's render_icon() is the single place that
 * turns any of those three encodings back into HTML wherever an icon is
 * displayed, so this widget is reusable throughout the CMS without every
 * view needing its own icon-type detection logic.
 */
(function () {
    var EMOJI_ICONS = [
        { char: '🕌', kw: 'masjid mosque ibadah' },
        { char: '🏛️', kw: 'gedung bidang departemen lembaga' },
        { char: '📖', kw: 'quran kajian pengajian buku' },
        { char: '🤝', kw: 'kerjasama program sosial gotong royong' },
        { char: '💰', kw: 'donasi infaq zakat uang' },
        { char: '🎓', kw: 'pendidikan tpq sekolah' },
        { char: '🚑', kw: 'ambulans kesehatan darurat' },
        { char: '👔', kw: 'pengurus dkm jabatan' },
        { char: '🧕', kw: 'muslimah wanita' },
        { char: '🕋', kw: 'haji umroh kabah' },
        { char: '🌙', kw: 'ramadhan bulan malam' },
        { char: '⭐', kw: 'bintang unggulan favorit' },
        { char: '📅', kw: 'agenda jadwal kalender' },
        { char: '🎤', kw: 'ceramah khutbah dakwah' },
        { char: '🍽️', kw: 'buka puasa makan sedekah' },
        { char: '🧹', kw: 'kebersihan kerja bakti' },
        { char: '🛡️', kw: 'keamanan satpam jaga' },
        { char: '🏥', kw: 'kesehatan klinik' },
        { char: '📢', kw: 'pengumuman informasi' },
        { char: '🤲', kw: 'doa berdoa' },
        { char: '👶', kw: 'anak yatim keluarga' },
        { char: '🌳', kw: 'lingkungan taman kebun' },
        { char: '💼', kw: 'usaha koperasi ekonomi' },
        { char: '🚻', kw: 'fasilitas toilet umum' },
        { char: '🚗', kw: 'parkir transportasi' },
        { char: '📚', kw: 'perpustakaan buku ilmu' },
        { char: '🕯️', kw: 'sosial santunan' },
        { char: '🏆', kw: 'lomba prestasi' },
        { char: '❤️', kw: 'kasih sayang peduli' },
        { char: '🔔', kw: 'notifikasi pengingat adzan' }
    ];

    var FA_ICONS = [
        'fa-solid fa-mosque', 'fa-solid fa-kaaba', 'fa-solid fa-book-quran', 'fa-solid fa-book-open',
        'fa-solid fa-hands-praying', 'fa-solid fa-hand-holding-heart', 'fa-solid fa-hands-holding-child',
        'fa-solid fa-people-group', 'fa-solid fa-user-tie', 'fa-solid fa-users', 'fa-solid fa-graduation-cap',
        'fa-solid fa-school', 'fa-solid fa-truck-medical', 'fa-solid fa-briefcase-medical',
        'fa-solid fa-money-bill-wave', 'fa-solid fa-hand-holding-dollar', 'fa-solid fa-sack-dollar',
        'fa-solid fa-calendar-days', 'fa-solid fa-microphone', 'fa-solid fa-bullhorn', 'fa-solid fa-broom',
        'fa-solid fa-shield-halved', 'fa-solid fa-house-medical', 'fa-solid fa-tree', 'fa-solid fa-car',
        'fa-solid fa-restroom', 'fa-solid fa-book', 'fa-solid fa-star', 'fa-solid fa-trophy', 'fa-solid fa-heart',
        'fa-solid fa-building', 'fa-solid fa-bell', 'fa-solid fa-utensils', 'fa-solid fa-moon',
        'fa-solid fa-child-reaching', 'fa-solid fa-handshake', 'fa-solid fa-clipboard-list'
    ];

    var BI_ICONS = [
        'bi-building', 'bi-people', 'bi-people-fill', 'bi-person-badge', 'bi-mortarboard', 'bi-book',
        'bi-journal-bookmark', 'bi-cash-coin', 'bi-piggy-bank', 'bi-heart', 'bi-hand-thumbs-up',
        'bi-calendar-event', 'bi-mic', 'bi-megaphone', 'bi-shield-check', 'bi-hospital', 'bi-tree',
        'bi-car-front', 'bi-star', 'bi-trophy', 'bi-bell', 'bi-cup-hot', 'bi-moon-stars', 'bi-house-door',
        'bi-briefcase', 'bi-clipboard-check', 'bi-basket', 'bi-flag', 'bi-gift'
    ];

    var currentTargetInputId = null;
    var currentPreviewSelector = null;
    var currentCallback = null;

    function ensureModal() {
        if (document.getElementById('iconPickerModal')) return;

        var modal = document.createElement('div');
        modal.id = 'iconPickerModal';
        modal.style.cssText = 'display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:2000; align-items:center; justify-content:center;';
        modal.innerHTML =
            '<div style="background:#fff; border-radius:12px; width:90%; max-width:520px; max-height:80vh; display:flex; flex-direction:column; overflow:hidden;">' +
                '<div style="display:flex; justify-content:space-between; align-items:center; padding:14px 18px; border-bottom:1px solid #e2e8f0;">' +
                    '<strong>Pilih Icon</strong>' +
                    '<button type="button" onclick="closeIconPicker()" style="background:none; border:none; font-size:18px; cursor:pointer;">&times;</button>' +
                '</div>' +
                '<div style="display:flex; border-bottom:1px solid #e2e8f0;">' +
                    '<button type="button" class="icon-picker-tab active" data-tab="emoji" onclick="switchIconPickerTab(\'emoji\')" style="flex:1; padding:10px; border:none; background:none; cursor:pointer; font-weight:600;">😀 Emoji</button>' +
                    '<button type="button" class="icon-picker-tab" data-tab="fa" onclick="switchIconPickerTab(\'fa\')" style="flex:1; padding:10px; border:none; background:none; cursor:pointer; font-weight:600;">FontAwesome</button>' +
                    '<button type="button" class="icon-picker-tab" data-tab="bi" onclick="switchIconPickerTab(\'bi\')" style="flex:1; padding:10px; border:none; background:none; cursor:pointer; font-weight:600;">Bootstrap Icons</button>' +
                '</div>' +
                '<div style="padding:12px 18px;">' +
                    '<input type="text" id="iconPickerSearch" placeholder="Cari icon..." oninput="renderIconPickerGrid()" style="width:100%; padding:8px 12px; border:1px solid #e2e8f0; border-radius:6px;">' +
                '</div>' +
                '<div id="iconPickerGrid" style="flex:1; overflow-y:auto; padding:0 18px 18px; display:grid; grid-template-columns:repeat(auto-fill, minmax(48px, 1fr)); gap:8px;"></div>' +
            '</div>';
        document.body.appendChild(modal);
    }

    window.selectIconFor = function (inputId, previewSelector) {
        ensureModal();
        currentTargetInputId = inputId;
        currentPreviewSelector = previewSelector || null;
        currentCallback = null;
        document.getElementById('iconPickerSearch').value = '';
        switchIconPickerTab('emoji');
        document.getElementById('iconPickerModal').style.display = 'flex';
    };

    window.openIconPicker = function (callback) {
        ensureModal();
        currentCallback = typeof callback === 'function' ? callback : null;
        currentTargetInputId = typeof callback === 'string' ? callback : null;
        currentPreviewSelector = null;
        document.getElementById('iconPickerSearch').value = '';
        switchIconPickerTab('emoji');
        document.getElementById('iconPickerModal').style.display = 'flex';
    };

    window.closeIconPicker = function () {
        var modal = document.getElementById('iconPickerModal');
        if (modal) modal.style.display = 'none';
    };

    window.switchIconPickerTab = function (tab) {
        document.querySelectorAll('.icon-picker-tab').forEach(function (btn) {
            var active = btn.getAttribute('data-tab') === tab;
            btn.classList.toggle('active');
            btn.style.borderBottom = active ? '2px solid #16a34a' : 'none';
            btn.style.color = active ? '#16a34a' : 'inherit';
        });
        document.getElementById('iconPickerGrid').setAttribute('data-active-tab', tab);
        renderIconPickerGrid();
    };

    window.renderIconPickerGrid = function () {
        var grid = document.getElementById('iconPickerGrid');
        var tab = grid.getAttribute('data-active-tab') || 'emoji';
        var query = (document.getElementById('iconPickerSearch').value || '').toLowerCase().trim();
        grid.innerHTML = '';

        if (tab === 'emoji') {
            EMOJI_ICONS
                .filter(function (e) { return !query || e.kw.indexOf(query) !== -1; })
                .forEach(function (e) {
                    grid.appendChild(makeCell(e.char, function () { pickIcon(e.char); }, e.char));
                });
        } else if (tab === 'fa') {
            FA_ICONS
                .filter(function (c) { return !query || c.indexOf(query) !== -1; })
                .forEach(function (c) {
                    grid.appendChild(makeCell('<i class="' + c + '"></i>', function () { pickIcon(c); }, c));
                });
        } else {
            BI_ICONS
                .filter(function (c) { return !query || c.indexOf(query) !== -1; })
                .forEach(function (c) {
                    grid.appendChild(makeCell('<i class="bi ' + c + '"></i>', function () { pickIcon(c); }, c));
                });
        }
    };

    function makeCell(innerHtml, onClick, title) {
        var cell = document.createElement('button');
        cell.type = 'button';
        cell.title = title;
        cell.innerHTML = innerHtml;
        cell.style.cssText = 'font-size:20px; padding:10px; border:1px solid #e2e8f0; border-radius:8px; background:#fff; cursor:pointer;';
        cell.addEventListener('click', onClick);
        return cell;
    }

    function pickIcon(value) {
        if (currentCallback) {
            currentCallback(value);
        }
        if (currentTargetInputId) {
            var input = document.getElementById(currentTargetInputId);
            if (input) {
                input.value = value;
                input.dispatchEvent(new Event('change', { bubbles: true }));
            }
        }
        if (currentPreviewSelector) {
            var preview = document.querySelector(currentPreviewSelector);
            if (preview) {
                if (value.indexOf('fa-') === 0) {
                    preview.innerHTML = '<i class="' + value + '"></i>';
                } else if (value.indexOf('bi-') === 0) {
                    preview.innerHTML = '<i class="bi ' + value + '"></i>';
                } else {
                    preview.textContent = value;
                }
            }
        }
        closeIconPicker();
    }
})();
