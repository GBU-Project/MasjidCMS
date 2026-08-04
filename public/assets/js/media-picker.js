/**
 * MasjidCMS Unified Media Library & TinyMCE Community Integration JS Engine
 * TASK-017 — Unified Media Library
 */

(function(window, document) {
    'use strict';

    var currentTargetInputId = null;
    var currentTinyMCEInstance = null;
    var selectedMediaItems = [];
    var isMultiSelectMode = false;
    var cachedMediaData = [];

    // TASK-022 finding C root cause: this file used hardcoded absolute paths
    // ('/admin/media/api', '/admin/media/upload'), which 404 on any install
    // not at the domain root (e.g. XAMPP subfolder installs). Build URLs
    // from the app-base-url <meta> tag instead, mirroring PHP's base_url().
    function appUrl(path) {
        var meta = document.querySelector('meta[name="app-base-url"]');
        var base = meta ? meta.content : '';
        return base + '/' + path.replace(/^\/+/, '');
    }

    // --- 1. TinyMCE Initializer Profiles ---
    function registerMediaLibraryButton() {
        if (typeof window.tinymce === 'undefined' || !window.tinymce.PluginManager) return;

        window.tinymce.PluginManager.add('medialibrary', function(editor) {
            editor.ui.registry.addButton('medialibrary', {
                icon: 'image',
                tooltip: 'Sisipkan dari Media Library',
                onAction: function() {
                    window.openMediaPickerForTinyMCE(editor);
                }
            });
        });
    }

    function initTinyMCE() {
        if (typeof window.tinymce === 'undefined') return;

        registerMediaLibraryButton();

        // TASK-022 finding F: this used to initialize three different TinyMCE
        // profiles (.tinymce-full 420px, .tinymce-medium 300px, .tinymce-simple
        // 200px, each with a different toolbar) -- plus Agenda's description
        // field wasn't wired to TinyMCE at all (a plain 3-row textarea). That
        // produced visibly inconsistent editor heights/styling/toolbars across
        // Berita, Kajian, Agenda, Program, Layanan, Pages, etc.
        //
        // Single Standard Description Editor Profile, reused identically by
        // every module (Agenda, Layanan, Program, Berita, Pages, ...): one
        // height, one toolbar, one component.
        window.tinymce.init({
            selector: '.tinymce-standard',
            height: 320,
            plugins: 'medialibrary',
            toolbar: 'undo redo | bold italic underline | h2 h3 | numlist bullist | link medialibrary code preview',
            setup: function(editor) {
                editor.on('init', function() {
                    console.log('Standard Description Editor initialized on #' + editor.id);
                });
            }
        });
    }

    // --- 2. Global Media Picker Modal Controls ---
    window.openMediaPickerModal = function(options) {
        options = options || {};
        currentTargetInputId = options.inputId || null;
        currentTinyMCEInstance = options.tinymce || null;
        isMultiSelectMode = !!options.multiSelect;
        selectedMediaItems = [];

        var modal = document.getElementById('mediaPickerModal');
        if (modal) {
            modal.style.display = 'flex';
            loadMediaPickerData();
        }
    };

    window.openMediaPickerForTinyMCE = function(editor) {
        window.openMediaPickerModal({ tinymce: editor });
    };

    window.selectFromMediaLibrary = function(inputId) {
        window.openMediaPickerModal({ inputId: inputId });
    };

    window.closeMediaPickerModal = function() {
        var modal = document.getElementById('mediaPickerModal');
        if (modal) {
            modal.style.display = 'none';
        }
        selectedMediaItems = [];
        resetDetailPanel();
    };

    // BUGFIX (reported: Hero Slider save fails, works after refresh):
    // upload() already returned a fresh csrf_token_value, but the code only
    // patched the <meta> tag. Any surrounding <form> (e.g. the Hero Slide
    // create/edit form using "Pilih Gambar dari Media Library") keeps its own
    // hidden csrf_field() input rendered at page-load with the OLD token, so
    // it still failed on submit. This now patches every matching hidden input
    // on the page too, so the next form submit anywhere on the page succeeds.
    function syncCsrfToken(newValue) {
        if (!newValue) return;
        var csrfNameMeta = document.querySelector('meta[name="csrf-token-name"]');
        var csrfValueMeta = document.querySelector('meta[name="csrf-token-value"]');
        if (csrfValueMeta) csrfValueMeta.content = newValue;
        if (csrfNameMeta) {
            document.querySelectorAll('input[name="' + csrfNameMeta.content + '"]').forEach(function (input) {
                input.value = newValue;
            });
        }
    }

    // --- 3. AJAX Data Loader & Grid Rendering ---
    window.loadMediaPickerData = function() {
        var grid = document.getElementById('modalMediaGrid');
        var emptyState = document.getElementById('modalEmptyState');
        if (!grid) return;

        grid.innerHTML = '<div style="grid-column: 1/-1; text-align: center; padding: 40px; color: #64748b;">⏳ Memuat Media Library...</div>';

        fetch(appUrl('admin/media/api?type=image'))
            .then(function(res) { return res.json(); })
            .then(function(data) {
                // BUGFIX: this GET request also rotates the CSRF token (CI4
                // regenerates on every filtered request, not just POST), so
                // just opening the picker to browse — without uploading
                // anything — was enough to make the page's form go stale.
                if (data.csrf_token_value) {
                    syncCsrfToken(data.csrf_token_value);
                }
                if (data.status === 'success' && Array.isArray(data.data)) {
                    cachedMediaData = data.data;
                    renderMediaGrid(cachedMediaData);
                } else {
                    grid.innerHTML = '';
                    if (emptyState) emptyState.style.display = 'block';
                }
            })
            .catch(function(err) {
                console.error('Failed to load media API:', err);
                grid.innerHTML = '<div style="grid-column: 1/-1; text-align: center; padding: 40px; color: #ef4444;">Gagal memuat media. Pastikan server aktif.</div>';
            });
    };

    function renderMediaGrid(items) {
        var grid = document.getElementById('modalMediaGrid');
        var emptyState = document.getElementById('modalEmptyState');
        if (!grid) return;

        grid.innerHTML = '';
        if (items.length === 0) {
            if (emptyState) emptyState.style.display = 'block';
            return;
        }
        if (emptyState) emptyState.style.display = 'none';

        items.forEach(function(item) {
            var card = document.createElement('div');
            card.className = 'media-picker-card';
            card.dataset.id = item.id;
            card.style.cssText = 'height: 120px; position: relative; border-radius: 8px; border: 2px solid #e2e8f0; overflow: hidden; background: #fff; cursor: pointer; transition: all 0.15s;';

            card.innerHTML = '<img src="' + item.url + '" alt="' + item.filename + '" style="width: 100%; height: 100%; object-fit: cover;">' +
                             '<div class="check-badge" style="position: absolute; top: 6px; right: 6px; background: #16a34a; color: #fff; border-radius: 50%; width: 20px; height: 20px; display: none; align-items: center; justify-content: center; font-size: 11px; font-weight: 800;">✓</div>';

            card.onclick = function() {
                toggleMediaSelection(item, card);
            };

            card.ondblclick = function() {
                toggleMediaSelection(item, card);
                confirmMediaSelection();
            };

            grid.appendChild(card);
        });
    }

    window.filterMediaPickerItems = function() {
        var query = (document.getElementById('modalSearchInput').value || '').toLowerCase();
        if (!query) {
            renderMediaGrid(cachedMediaData);
            return;
        }
        var filtered = cachedMediaData.filter(function(item) {
            return item.filename.toLowerCase().indexOf(query) !== -1;
        });
        renderMediaGrid(filtered);
    };

    // --- 4. Selection & Detail Panel ---
    function toggleMediaSelection(item, cardEl) {
        if (!isMultiSelectMode) {
            document.querySelectorAll('.media-picker-card').forEach(function(c) {
                c.classList.remove('selected');
                var b = c.querySelector('.check-badge');
                if (b) b.style.display = 'none';
            });
            selectedMediaItems = [item];
            cardEl.classList.add('selected');
            var badge = cardEl.querySelector('.check-badge');
            if (badge) badge.style.display = 'flex';
        } else {
            var idx = selectedMediaItems.findIndex(function(i) { return i.id === item.id; });
            if (idx > -1) {
                selectedMediaItems.splice(idx, 1);
                cardEl.classList.remove('selected');
                var badge = cardEl.querySelector('.check-badge');
                if (badge) badge.style.display = 'none';
            } else {
                selectedMediaItems.push(item);
                cardEl.classList.add('selected');
                var badge = cardEl.querySelector('.check-badge');
                if (badge) badge.style.display = 'flex';
            }
        }

        updateDetailPanel(selectedMediaItems[0] || null);
    }

    function updateDetailPanel(item) {
        var btn = document.getElementById('btnSelectMediaInsert');
        var previewImg = document.getElementById('detailPreviewImg');
        var noSelectSpan = document.getElementById('detailNoSelect');
        var metaGroup = document.getElementById('detailMetaGroup');

        if (!item) {
            if (btn) btn.disabled = true;
            if (previewImg) previewImg.style.display = 'none';
            if (noSelectSpan) noSelectSpan.style.display = 'block';
            if (metaGroup) metaGroup.style.display = 'none';
            return;
        }

        if (btn) btn.disabled = false;
        if (previewImg) {
            previewImg.src = item.url;
            previewImg.style.display = 'block';
        }
        if (noSelectSpan) noSelectSpan.style.display = 'none';
        if (metaGroup) metaGroup.style.display = 'flex';

        document.getElementById('detailFilename').innerText = item.filename;
        document.getElementById('detailFilesize').innerText = item.formatted_size || '-';
        document.getElementById('detailMime').innerText = item.mime_type;
        document.getElementById('detailDate').innerText = (item.created_at || '').substring(0, 10);
        document.getElementById('detailUrlInput').value = item.url;
    }

    function resetDetailPanel() {
        updateDetailPanel(null);
    }

    // --- 5. Confirm Selection Insertion ---
    window.confirmMediaSelection = function() {
        if (selectedMediaItems.length === 0) return;

        if (currentTinyMCEInstance) {
            selectedMediaItems.forEach(function(item) {
                var imgHtml = '<img src="' + item.url + '" alt="' + item.filename + '" style="max-width:100%; height:auto; border-radius:8px;" loading="lazy" />';
                currentTinyMCEInstance.insertContent(imgHtml);
            });
        } else if (currentTargetInputId) {
            var targetInput = document.getElementById(currentTargetInputId);
            if (targetInput) {
                var item = selectedMediaItems[0];
                // Fields that store a Media Library reference (e.g.
                // logo_media_id, favicon_media_id) declare
                // data-picker-value="id" so we store the numeric media ID
                // instead of the public URL. Default stays "url" so
                // existing plain-path fields (e.g. Gallery) keep working.
                var pickMode = targetInput.dataset.pickerValue || 'url';
                targetInput.value = (pickMode === 'id') ? item.id : item.url;
                targetInput.dispatchEvent(new Event('change'));

                // Optional live preview: <img data-preview-for="targetInputId">
                var previewEl = document.querySelector('[data-preview-for="' + currentTargetInputId + '"]');
                if (previewEl) {
                    previewEl.src = item.url;
                    previewEl.style.display = 'inline-block';
                }
            }
        }

        closeMediaPickerModal();
    };

    // --- 6. Modal Instant File Upload ---
    window.handleModalFileUpload = function(files) {
        if (!files || files.length === 0) return;

        var formData = new FormData();
        for (var i = 0; i < files.length; i++) {
            formData.append('files[]', files[i]);
        }

        // CI4's global CSRF filter protects every non-'api/*' POST route,
        // including admin/media/upload. Without this token the request was
        // silently rejected (403 HTML body), which fetch().then(res=>res.json())
        // failed to parse, surfacing as a generic "Gagal mengunggah file."
        var csrfNameMeta = document.querySelector('meta[name="csrf-token-name"]');
        var csrfValueMeta = document.querySelector('meta[name="csrf-token-value"]');
        if (csrfNameMeta && csrfValueMeta) {
            formData.append(csrfNameMeta.content, csrfValueMeta.content);
        }

        var progressBox = document.getElementById('modalUploadProgress');
        var progressBar = document.getElementById('modalProgressBar');
        if (progressBox) progressBox.style.display = 'block';
        if (progressBar) progressBar.style.width = '30%';

        fetch(appUrl('admin/media/upload'), {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(function(res) { return res.json(); })
        .then(function(data) {
            if (progressBar) progressBar.style.width = '100%';
            setTimeout(function() {
                if (progressBox) progressBox.style.display = 'none';
                if (progressBar) progressBar.style.width = '0%';
            }, 500);

            if (data.status === 'success') {
                // CSRF token regenerates on every request (Config/Security.php
                // regenerate = true); sync it everywhere on the page — not
                // just the meta tag — so the NEXT submit anywhere (including
                // the surrounding Hero Slide / CMS form) doesn't fail with a
                // stale token.
                if (data.csrf_token_value) {
                    syncCsrfToken(data.csrf_token_value);
                }
                loadMediaPickerData();
            } else {
                alert('Upload gagal: ' + (data.errors ? data.errors.join('\n') : 'Terjadi kesalahan.'));
            }
        })
        .catch(function(err) {
            if (progressBox) progressBox.style.display = 'none';
            alert('Gagal mengunggah file.');
        });
    };

    // Initialize on DOM Ready or immediately if DOM is already ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            initTinyMCE();
        });
    } else {
        initTinyMCE();
    }

})(window, document);
