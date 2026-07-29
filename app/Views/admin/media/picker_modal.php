<!-- UNIFIED MEDIA PICKER MODAL (TASK-017) -->
<div id="mediaPickerModal" class="media-modal-backdrop" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(15, 23, 42, 0.65); z-index: 99999; backdrop-filter: blur(4px); align-items: center; justify-content: center; padding: 20px;">
    <div class="media-modal-dialog" style="background: #ffffff; width: 100%; max-width: 1050px; height: 85vh; border-radius: 16px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); display: flex; flex-direction: column; overflow: hidden; animation: modalFadeIn 0.2s ease-out;">
        
        <!-- Modal Header -->
        <div style="padding: 16px 24px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <span style="font-size: 24px;">📁</span>
                <div>
                    <h3 style="font-size: 17px; font-weight: 800; color: #0f172a; margin: 0;">Unified Media Library & Picker</h3>
                    <span style="font-size: 12px; color: #64748b;">Pilih gambar dari perpustakaan media atau unggah file baru</span>
                </div>
            </div>
            <button type="button" onclick="closeMediaPickerModal()" style="background: transparent; border: none; font-size: 24px; cursor: pointer; color: #64748b; padding: 4px 8px; border-radius: 6px;" title="Tutup Modal">&times;</button>
        </div>

        <!-- Modal Toolbar & Dropzone -->
        <div style="padding: 12px 24px; background: #fff; border-bottom: 1px solid #e2e8f0; display: flex; flex-wrap: wrap; gap: 12px; align-items: center; justify-content: space-between;">
            <!-- Dropzone Area -->
            <div id="modalDropzone" style="flex: 1; min-width: 280px; border: 2px dashed #cbd5e1; border-radius: 8px; padding: 10px 16px; background: #f8fafc; text-align: center; cursor: pointer; transition: all 0.2s;" onclick="document.getElementById('modalFileInput').click()">
                <input type="file" id="modalFileInput" multiple accept="image/*,.pdf" style="display: none;" onchange="handleModalFileUpload(this.files)">
                <span style="font-size: 13px; font-weight: 700; color: #16a34a;">📤 Drag & Drop file ke sini atau Klik untuk Upload</span>
                <span style="font-size: 11px; color: #64748b; display: block;">Maks. 10MB per file (JPG, PNG, WEBP, GIF, SVG)</span>
            </div>

            <!-- Search & Filter -->
            <div style="display: flex; gap: 8px; align-items: center;">
                <input type="text" id="modalSearchInput" placeholder="Cari nama file..." style="padding: 8px 12px; font-size: 13px; border: 1px solid #cbd5e1; border-radius: 6px; width: 180px;" oninput="filterMediaPickerItems()">
                <button type="button" class="btn btn-secondary" onclick="loadMediaPickerData()" style="padding: 8px 12px; font-size: 12px;">↺ Refresh</button>
            </div>
        </div>

        <!-- Progress Bar -->
        <div id="modalUploadProgress" style="display: none; height: 4px; background: #e2e8f0; width: 100%;">
            <div id="modalProgressBar" style="height: 100%; width: 0%; background: #16a34a; transition: width 0.2s;"></div>
        </div>

        <!-- Modal Content Body (Grid + Detail Panel) -->
        <div style="flex: 1; display: flex; overflow: hidden;">
            <!-- Media Grid Area -->
            <div style="flex: 1; padding: 20px; overflow-y: auto; background: #f8fafc;">
                <div id="modalMediaGrid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)); gap: 14px;">
                    <!-- Dynamically populated -->
                </div>
                <div id="modalEmptyState" style="display: none; text-align: center; padding: 40px; color: #64748b;">
                    <span style="font-size: 40px; display: block; margin-bottom: 8px;">🖼️</span>
                    <p style="font-weight: 600; margin-bottom: 4px;">Belum ada media di perpustakaan.</p>
                    <span style="font-size: 12px;">Unggah file gambar pertama Anda di atas.</span>
                </div>
            </div>

            <!-- Media Preview Detail Panel -->
            <div id="modalDetailPanel" style="width: 280px; background: #ffffff; border-left: 1px solid #e2e8f0; padding: 20px; display: flex; flex-direction: column; justify-content: space-between; overflow-y: auto;">
                <div id="modalDetailContent">
                    <h4 style="font-size: 14px; font-weight: 800; color: #0f172a; margin-bottom: 12px;">Detail Media</h4>
                    <div style="width: 100%; height: 160px; border-radius: 8px; border: 1px solid #e2e8f0; overflow: hidden; background: #f1f5f9; display: flex; align-items: center; justify-content: center; margin-bottom: 12px;">
                        <img id="detailPreviewImg" src="" alt="Preview" style="max-width: 100%; max-height: 100%; object-fit: contain; display: none;">
                        <span id="detailNoSelect" style="font-size: 13px; color: #94a3b8;">Pilih gambar dari grid</span>
                    </div>
                    <div id="detailMetaGroup" style="display: none; font-size: 12px; color: #475569; display: flex; flex-direction: column; gap: 6px;">
                        <div style="word-break: break-all;"><strong>Nama:</strong> <span id="detailFilename">-</span></div>
                        <div><strong>Ukuran:</strong> <span id="detailFilesize">-</span></div>
                        <div><strong>Tipe:</strong> <span id="detailMime">-</span></div>
                        <div><strong>Tanggal:</strong> <span id="detailDate">-</span></div>
                        <input type="text" id="detailUrlInput" readonly style="width: 100%; padding: 6px 8px; font-size: 11px; border: 1px solid #cbd5e1; border-radius: 4px; margin-top: 6px; background: #f8fafc;" onclick="this.select()">
                    </div>
                </div>

                <div style="margin-top: 20px; display: flex; flex-direction: column; gap: 8px;">
                    <button type="button" id="btnSelectMediaInsert" class="btn btn-primary" style="width: 100%; padding: 10px; font-size: 13px; font-weight: 700;" disabled onclick="confirmMediaSelection()">
                        ✨ Gunakan Gambar Ini
                    </button>
                    <button type="button" onclick="closeMediaPickerModal()" class="btn btn-secondary" style="width: 100%; padding: 8px; font-size: 12px;">Batal</button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes modalFadeIn {
    from { opacity: 0; transform: scale(0.98); }
    to { opacity: 1; transform: scale(1); }
}
.media-picker-card {
    position: relative;
    background: #fff;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    overflow: hidden;
    cursor: pointer;
    transition: all 0.15s ease;
}
.media-picker-card:hover {
    border-color: #16a34a;
    box-shadow: 0 4px 12px rgba(22, 163, 74, 0.15);
}
.media-picker-card.selected {
    border-color: #16a34a;
    box-shadow: 0 0 0 3px rgba(22, 163, 74, 0.3);
}
.media-picker-card .check-badge {
    position: absolute;
    top: 6px;
    right: 6px;
    background: #16a34a;
    color: #fff;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    display: none;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    font-weight: 800;
}
.media-picker-card.selected .check-badge {
    display: flex;
}
</style>
