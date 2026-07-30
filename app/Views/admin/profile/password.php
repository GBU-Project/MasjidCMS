<?= $this->extend('layouts/admin') ?>

<?= $this->section('title') ?>Change Password<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="workspace-header" style="margin-bottom: 24px;">
    <div style="font-size: 12px; font-weight: 500; color: var(--text-tertiary); margin-bottom: 4px;">
        <a href="<?= site_url('admin/dashboard') ?>" style="color: var(--primary-600); text-decoration: none;">Dashboard</a> /
        <a href="<?= site_url('admin/profile') ?>" style="color: var(--primary-600); text-decoration: none;">My Profile</a> / Change Password
    </div>
    <h1 class="page-title">🔒 Change Password</h1>
    <p class="page-subtitle">Perbarui password akun Anda.</p>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div style="background: #dcfce7; color: #166534; padding: 12px 16px; border-radius: 8px; margin-bottom: 16px;">
        <?= esc(session()->getFlashdata('success')) ?>
    </div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div style="background: #fee2e2; color: #991b1b; padding: 12px 16px; border-radius: 8px; margin-bottom: 16px;">
        <?= esc(session()->getFlashdata('error')) ?>
    </div>
<?php endif; ?>

<div class="card" style="max-width: 480px; padding: 24px; border: 1px solid var(--border-light); border-radius: 10px;">
    <form method="post" action="<?= site_url('admin/profile/password') ?>">
        <?= csrf_field() ?>
        <div style="margin-bottom: 16px;">
            <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px;">Password Saat Ini</label>
            <input type="password" name="current_password" required style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;">
        </div>
        <div style="margin-bottom: 16px;">
            <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px;">Password Baru</label>
            <input type="password" name="new_password" required minlength="8" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;">
        </div>
        <div style="margin-bottom: 20px;">
            <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px;">Konfirmasi Password Baru</label>
            <input type="password" name="confirm_password" required minlength="8" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px;">
        </div>
        <button type="submit" class="btn btn-primary">Simpan Password Baru</button>
    </form>
</div>
<?= $this->endSection() ?>
