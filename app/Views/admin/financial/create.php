<?= $this->extend('layouts/admin') ?>

<?= $this->section('title') ?>Buat Transaksi Keuangan Baru<?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- Breadcrumb -->
<div class="breadcrumb-container">
    <span>Admin</span>
    <span>/</span>
    <span>Keuangan</span>
    <span>/</span>
    <span class="breadcrumb-active">Form Transaksi Wizard (3 Langkah)</span>
</div>

<!-- Header -->
<div class="content-header-title">
    <div>
        <h1>Wizard Transaksi Keuangan Baru</h1>
        <p style="font-size: 14px; color: var(--text-muted);">Proses 3 langkah pembuatan draft transaksi keuangan berbasis Double Entry.</p>
    </div>
</div>

<!-- Wizard Step Progress Indicator Bar -->
<div class="panel-card" style="padding: 16px; margin-bottom: 24px;">
    <div style="display: flex; justify-content: space-around; align-items: center;">
        <div style="text-align: center; color: var(--primary-600); font-weight: 600;">
            <div style="width: 32px; height: 32px; border-radius: 50%; background: var(--primary-100); margin: 0 auto 4px; display: flex; align-items: center; justify-content: center;">1</div>
            <span style="font-size: 13px;">Step 1: Informasi Transaksi</span>
        </div>
        <div style="flex: 1; height: 2px; background: var(--border-light); margin: 0 16px;"></div>
        <div style="text-align: center; color: var(--text-subtle);">
            <div style="width: 32px; height: 32px; border-radius: 50%; background: var(--bg-app); margin: 0 auto 4px; display: flex; align-items: center; justify-content: center;">2</div>
            <span style="font-size: 13px;">Step 2: Account & Fund</span>
        </div>
        <div style="flex: 1; height: 2px; background: var(--border-light); margin: 0 16px;"></div>
        <div style="text-align: center; color: var(--text-subtle);">
            <div style="width: 32px; height: 32px; border-radius: 50%; background: var(--bg-app); margin: 0 auto 4px; display: flex; align-items: center; justify-content: center;">3</div>
            <span style="font-size: 13px;">Step 3: Review & Submit</span>
        </div>
    </div>
</div>

<!-- Step 1 Form Card -->
<div class="panel-card" style="max-width: 720px; margin-left: 0;">
    <div class="panel-header">
        <span>Langkah 1: Informasi Dasar Transaksi</span>
    </div>
    <form style="display: flex; flex-direction: column; gap: 16px;">
        <div>
            <label style="display: block; font-size: 14px; font-weight: 500; margin-bottom: 6px;">Jenis Transaksi <span style="color: red;">*</span></label>
            <select style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: var(--radius-md); font-size: 14px;">
                <option value="INCOME">Pemasukan (INCOME)</option>
                <option value="EXPENSE">Pengeluaran (EXPENSE)</option>
                <option value="ADJUSTMENT">Penyesuaian (ADJUSTMENT)</option>
            </select>
        </div>

        <div>
            <label style="display: block; font-size: 14px; font-weight: 500; margin-bottom: 6px;">Nominal Transaksi (Rp) <span style="color: red;">*</span></label>
            <input type="number" placeholder="500000" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: var(--radius-md); font-size: 14px;">
        </div>

        <div>
            <label style="display: block; font-size: 14px; font-weight: 500; margin-bottom: 6px;">Tanggal Transaksi <span style="color: red;">*</span></label>
            <input type="date" value="<?= date('Y-m-d') ?>" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: var(--radius-md); font-size: 14px;">
        </div>

        <div>
            <label style="display: block; font-size: 14px; font-weight: 500; margin-bottom: 6px;">Deskripsi / Keterangan Transaksi <span style="color: red;">*</span></label>
            <textarea rows="3" placeholder="Penerimaan donasi infaq jumat jamaah..." style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: var(--radius-md); font-size: 14px;"></textarea>
        </div>

        <div style="display: flex; justify-content: space-between; margin-top: 12px;">
            <a href="/admin/financial" class="btn btn-secondary">Batal</a>
            <button type="button" class="btn btn-primary">Lanjut ke Step 2 (Account & Fund) ›</button>
        </div>
    </form>
</div>
<?= $this->endSection() ?>
