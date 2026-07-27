<?= $this->extend('layouts/public') ?>

<?= $this->section('title') ?>Hubungi Kami<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="portal-container" style="max-width: 800px;">
    <h1 style="font-size: 28px; font-weight: 800; margin-bottom: 24px;">Hubungi Pengurus Masjid</h1>
    <div style="background: white; border: 1px solid var(--border-light); border-radius: var(--radius-lg); padding: 32px;">
        <form style="display: flex; flex-direction: column; gap: 16px;">
            <div>
                <label style="display: block; font-size: 14px; font-weight: 500; margin-bottom: 6px;">Nama Lengkap <span style="color: red;">*</span></label>
                <input type="text" placeholder="Masukkan nama Anda" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;">
            </div>
            <div>
                <label style="display: block; font-size: 14px; font-weight: 500; margin-bottom: 6px;">Email / No. WhatsApp <span style="color: red;">*</span></label>
                <input type="text" placeholder="08123456789" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;">
            </div>
            <div>
                <label style="display: block; font-size: 14px; font-weight: 500; margin-bottom: 6px;">Pesan / Pertanyaan / Saran <span style="color: red;">*</span></label>
                <textarea rows="4" placeholder="Tuliskan pesan Anda untuk sekretariat DKM..." style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;"></textarea>
            </div>
            <div>
                <button type="button" class="btn-portal btn-portal-primary">Kirim Pesan Ke Sekretariat</button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
