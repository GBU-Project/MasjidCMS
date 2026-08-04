<?= $this->extend('layouts/admin') ?>

<?= $this->section('title') ?>Buat Transaksi Keuangan Baru<?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- Breadcrumb -->
<div class="breadcrumb-container">
    <span>Admin</span>
    <span>/</span>
    <span>Keuangan</span>
    <span>/</span>
    <span class="breadcrumb-active">Catat Transaksi Baru</span>
</div>

<!-- Header -->
<div class="content-header-title">
    <div>
        <h1>Catat Transaksi Keuangan Baru</h1>
        <p style="font-size: 14px; color: var(--text-muted);">Isi 3 langkah singkat berikut. Anda bisa memeriksa ulang semua isian sebelum benar-benar disimpan.</p>
    </div>
</div>

<!--
    TASK-AUDIT: Sebelumnya indikator "Step 1/2/3" ini hanya dekorasi — seluruh
    field tampil sekaligus di satu form panjang, sehingga Step 2 & 3 tidak
    pernah benar-benar berfungsi sebagai langkah terpisah dan pengguna tidak
    pernah melihat ringkasan sebelum submit. Sekarang tiap langkah adalah
    section terpisah yang ditampilkan/disembunyikan lewat JS (lihat script di
    bawah), diakhiri langkah Review sungguhan sebelum data disimpan.
-->
<!-- Wizard Step Progress Indicator Bar -->
<div class="panel-card" style="padding: 16px; margin-bottom: 24px;">
    <div style="display: flex; justify-content: space-around; align-items: center;">
        <div class="wizard-indicator" data-indicator="1" style="text-align: center; font-weight: 600;">
            <div class="wizard-indicator-dot" style="width: 32px; height: 32px; border-radius: 50%; margin: 0 auto 4px; display: flex; align-items: center; justify-content: center;">1</div>
            <span style="font-size: 13px;">Rincian Transaksi</span>
        </div>
        <div style="flex: 1; height: 2px; background: var(--border-light); margin: 0 16px;"></div>
        <div class="wizard-indicator" data-indicator="2" style="text-align: center;">
            <div class="wizard-indicator-dot" style="width: 32px; height: 32px; border-radius: 50%; margin: 0 auto 4px; display: flex; align-items: center; justify-content: center;">2</div>
            <span style="font-size: 13px;">Dana &amp; Akun</span>
        </div>
        <div style="flex: 1; height: 2px; background: var(--border-light); margin: 0 16px;"></div>
        <div class="wizard-indicator" data-indicator="3" style="text-align: center;">
            <div class="wizard-indicator-dot" style="width: 32px; height: 32px; border-radius: 50%; margin: 0 auto 4px; display: flex; align-items: center; justify-content: center;">3</div>
            <span style="font-size: 13px;">Periksa &amp; Simpan</span>
        </div>
    </div>
</div>

<div class="panel-card" style="max-width: 720px; margin-left: 0;">
    <div class="panel-header">
        <span>Form Transaksi Keuangan</span>
    </div>

    <form id="transactionWizardForm" action="<?= site_url('admin/financial/store') ?>" method="POST" style="padding: 20px;">
        <?= csrf_field() ?>

        <!-- ============ STEP 1: Rincian Transaksi ============ -->
        <div class="wizard-step" data-step="1" style="display: flex; flex-direction: column; gap: 16px;">
            <div>
                <label for="field_transaction_type" style="display: block; font-size: 14px; font-weight: 500; margin-bottom: 6px;">Jenis Transaksi <span style="color: red;">*</span></label>
                <select name="transaction_type" required style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: var(--radius-md); font-size: 14px;" id="field_transaction_type">
                    <option value="INCOME">Uang Masuk (donasi, infaq, dsb.)</option>
                    <option value="EXPENSE">Uang Keluar (pengeluaran/belanja)</option>
                    <option value="ADJUSTMENT">Penyesuaian Saldo</option>
                </select>
                <p class="field-help">Pilih apakah ini uang yang diterima masjid, dikeluarkan, atau sekadar koreksi saldo.</p>
            </div>

            <div>
                <label for="field_amount" style="display: block; font-size: 14px; font-weight: 500; margin-bottom: 6px;">Nominal (Rp) <span style="color: red;">*</span></label>
                <input type="number" name="amount" step="0.01" required placeholder="500000" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: var(--radius-md); font-size: 14px;" id="field_amount">
            </div>

            <div>
                <label for="field_transaction_date" style="display: block; font-size: 14px; font-weight: 500; margin-bottom: 6px;">Tanggal Transaksi <span style="color: red;">*</span></label>
                <input type="date" name="transaction_date" id="field_transaction_date" required value="<?= date('Y-m-d') ?>" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: var(--radius-md); font-size: 14px;">
            </div>

            <div>
                <label for="field_description" style="display: block; font-size: 14px; font-weight: 500; margin-bottom: 6px;">Keterangan <span style="color: red;">*</span></label>
                <textarea name="description" rows="3" required placeholder="Contoh: Donasi infaq Jumat dari jamaah..." style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: var(--radius-md); font-size: 14px;" id="field_description"></textarea>
                <p class="field-help">Tulis singkat asal/tujuan dana ini — akan muncul di daftar transaksi dan laporan.</p>
            </div>

            <div style="display: flex; justify-content: flex-end; margin-top: 8px;">
                <button type="button" class="btn btn-primary wizard-next-btn">Lanjut →</button>
            </div>
        </div>

        <!-- ============ STEP 2: Dana & Akun ============ -->
        <div class="wizard-step" data-step="2" style="display: none; flex-direction: column; gap: 16px;">
            <div>
                <label for="field_fund_id" style="display: block; font-size: 14px; font-weight: 500; margin-bottom: 6px;">Untuk Kebutuhan Apa Dana Ini? <span style="color: red;">*</span></label>
                <select name="fund_id" required style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: var(--radius-md); font-size: 14px;" id="field_fund_id">
                    <?php if (!empty($funds)): ?>
                        <?php foreach ($funds as $f): ?>
                            <option value="<?= $f['id'] ?>"><?= esc($f['name']) ?> (<?= esc($f['fund_code'] ?? 'FUND-' . $f['id']) ?> - <?= esc($f['fund_type'] ?? 'RESTRICTED') ?>)</option>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <option value="1">Kas Umum (Default Fund)</option>
                    <?php endif; ?>
                </select>
                <p class="field-help">"Kantong dana" — misalnya Kas Umum untuk kebutuhan harian, atau Zakat/Qurban/Wakaf jika dananya harus dipakai khusus untuk itu.</p>
            </div>

            <div>
                <label for="field_financial_account_id" style="display: block; font-size: 14px; font-weight: 500; margin-bottom: 6px;">Uangnya Disimpan / Diambil dari Mana? <span style="color: red;">*</span></label>
                <select name="financial_account_id" required style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: var(--radius-md); font-size: 14px;" id="field_financial_account_id">
                    <?php if (!empty($financialAccounts)): ?>
                        <?php foreach ($financialAccounts as $fa): ?>
                            <option value="<?= $fa['id'] ?>"><?= esc($fa['name']) ?> (<?= esc($fa['account_number'] ?? 'Kas Utama') ?>) — Saldo: Rp <?= number_format((float)($fa['balance'] ?? 0), 0, ',', '.') ?></option>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <option value="1">Kas Tunai Utama (Default Account)</option>
                    <?php endif; ?>
                </select>
                <p class="field-help">Rekening bank atau kas tunai fisik tempat uang ini benar-benar berada.</p>
            </div>

            <div>
                <label for="field_account_id" style="display: block; font-size: 14px; font-weight: 500; margin-bottom: 6px;">Kategori Pembukuan <span style="color: red;">*</span></label>
                <select name="account_id" required style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: var(--radius-md); font-size: 14px;" id="field_account_id">
                    <?php if (!empty($coaAccounts)): ?>
                        <?php foreach ($coaAccounts as $ca): ?>
                            <option value="<?= $ca['id'] ?>"><?= esc($ca['name']) ?> <span style="color: var(--text-subtle);">(<?= esc($ca['account_code']) ?>)</span></option>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <option value="1">Infaq Jamaah (Pemasukan) — 4001</option>
                    <?php endif; ?>
                </select>
                <p class="field-help">Kategori akuntansi untuk transaksi ini (mis. "Infaq Jamaah", "Listrik & Air"). Kode di sebelahnya untuk referensi bendahara/akuntan.</p>
            </div>

            <div style="display: flex; justify-content: space-between; margin-top: 8px;">
                <button type="button" class="btn btn-secondary wizard-back-btn">← Kembali</button>
                <button type="button" class="btn btn-primary wizard-next-btn">Lanjut ke Review →</button>
            </div>
        </div>

        <!-- ============ STEP 3: Review sungguhan sebelum submit ============ -->
        <div class="wizard-step" data-step="3" style="display: none; flex-direction: column; gap: 16px;">
            <p style="font-size: 13px; color: var(--text-muted);">Periksa kembali data di bawah ini. Setelah disimpan, transaksi akan membuat jurnal akuntansi dan tidak bisa diedit bebas — hanya bisa disesuaikan lewat proses koreksi resmi.</p>
            <div id="wizardReviewSummary" class="panel-card" style="padding: 16px; background: var(--bg-app);"></div>

            <div style="display: flex; justify-content: space-between; margin-top: 8px;">
                <button type="button" class="btn btn-secondary wizard-back-btn">← Kembali &amp; Edit</button>
                <button type="submit" class="btn btn-primary">💾 Simpan Transaksi &amp; Buat Jurnal</button>
            </div>
        </div>
    </form>
</div>

<style>
.field-help {
    font-size: 12px;
    color: var(--text-subtle);
    margin-top: 4px;
    line-height: 1.4;
}
.wizard-indicator .wizard-indicator-dot {
    background: var(--bg-app);
    color: var(--text-subtle);
}
.wizard-indicator.is-active { color: var(--primary-600); }
.wizard-indicator.is-active .wizard-indicator-dot { background: var(--primary-100); color: var(--primary-600); }
.wizard-indicator.is-done { color: var(--primary-700); }
.wizard-indicator.is-done .wizard-indicator-dot { background: var(--primary-600); color: #fff; }
#wizardReviewSummary dl { display: grid; grid-template-columns: 180px 1fr; gap: 8px 12px; margin: 0; font-size: 14px; }
#wizardReviewSummary dt { color: var(--text-muted); }
#wizardReviewSummary dd { font-weight: 500; color: var(--text-main); }
</style>

<script>
(function () {
    var form = document.getElementById('transactionWizardForm');
    if (!form) return;

    var steps = Array.prototype.slice.call(form.querySelectorAll('.wizard-step'));
    var indicators = Array.prototype.slice.call(document.querySelectorAll('.wizard-indicator'));
    var currentStep = 1;

    function labelFor(select) {
        var opt = select.options[select.selectedIndex];
        return opt ? opt.textContent.trim() : '';
    }

    function formatRupiah(value) {
        var n = parseFloat(value || '0');
        if (isNaN(n)) return 'Rp 0';
        return 'Rp ' + n.toLocaleString('id-ID', { maximumFractionDigits: 0 });
    }

    function renderReview() {
        var typeSelect = form.querySelector('[name="transaction_type"]');
        var amount = form.querySelector('[name="amount"]').value;
        var date = form.querySelector('[name="transaction_date"]').value;
        var description = form.querySelector('[name="description"]').value;
        var fundSelect = form.querySelector('[name="fund_id"]');
        var accountSelect = form.querySelector('[name="financial_account_id"]');
        var coaSelect = form.querySelector('[name="account_id"]');

        var html = '<dl>'
            + '<dt>Jenis Transaksi</dt><dd>' + labelFor(typeSelect) + '</dd>'
            + '<dt>Nominal</dt><dd>' + formatRupiah(amount) + '</dd>'
            + '<dt>Tanggal</dt><dd>' + (date || '-') + '</dd>'
            + '<dt>Keterangan</dt><dd>' + (description ? description.replace(/</g, '&lt;') : '-') + '</dd>'
            + '<dt>Kantong Dana</dt><dd>' + labelFor(fundSelect) + '</dd>'
            + '<dt>Rekening/Kas</dt><dd>' + labelFor(accountSelect) + '</dd>'
            + '<dt>Kategori Pembukuan</dt><dd>' + labelFor(coaSelect) + '</dd>'
            + '</dl>';

        document.getElementById('wizardReviewSummary').innerHTML = html;
    }

    function showStep(step) {
        currentStep = step;
        steps.forEach(function (el) {
            var isCurrent = parseInt(el.getAttribute('data-step'), 10) === step;
            el.style.display = isCurrent ? 'flex' : 'none';
        });
        indicators.forEach(function (el) {
            var n = parseInt(el.getAttribute('data-indicator'), 10);
            el.classList.remove('is-active', 'is-done');
            if (n === step) el.classList.add('is-active');
            else if (n < step) el.classList.add('is-done');
        });
        if (step === 3) renderReview();
        var card = form.closest('.panel-card');
        if (card) card.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    form.querySelectorAll('.wizard-next-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var currentStepEl = form.querySelector('.wizard-step[data-step="' + currentStep + '"]');
            // Rely on native required-field validation before advancing.
            var invalid = currentStepEl.querySelector(':invalid');
            if (invalid) {
                invalid.reportValidity();
                return;
            }
            showStep(currentStep + 1);
        });
    });
    form.querySelectorAll('.wizard-back-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            showStep(currentStep - 1);
        });
    });

    showStep(1);
})();
</script>
<?= $this->endSection() ?>
