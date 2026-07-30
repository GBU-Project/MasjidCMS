<?= $this->extend('layouts/admin') ?>

<?= $this->section('title') ?>My Profile<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="workspace-header" style="margin-bottom: 24px;">
    <div style="font-size: 12px; font-weight: 500; color: var(--text-tertiary); margin-bottom: 4px;">
        <a href="<?= site_url('admin/dashboard') ?>" style="color: var(--primary-600); text-decoration: none;">Dashboard</a> / My Profile
    </div>
    <h1 class="page-title">👤 My Profile</h1>
    <p class="page-subtitle">Informasi akun Anda yang sedang login.</p>
</div>

<div class="card" style="max-width: 520px; padding: 24px; border: 1px solid var(--border-light); border-radius: 10px;">
    <?php if ($user): ?>
        <div style="display: flex; flex-direction: column; gap: 16px;">
            <div>
                <label style="font-size: 12px; color: var(--text-tertiary); font-weight: 600;">Username</label>
                <div style="font-size: 15px;"><?= esc($user->username) ?></div>
            </div>
            <div>
                <label style="font-size: 12px; color: var(--text-tertiary); font-weight: 600;">Email</label>
                <div style="font-size: 15px;"><?= esc($user->email) ?></div>
            </div>
            <div>
                <label style="font-size: 12px; color: var(--text-tertiary); font-weight: 600;">Role</label>
                <div style="font-size: 15px;"><?= esc(!empty($user->roles) ? implode(', ', $user->roles) : '-') ?></div>
            </div>
        </div>
        <div style="margin-top: 24px;">
            <a href="<?= site_url('admin/profile/password') ?>" class="btn btn-primary">🔒 Change Password</a>
        </div>
    <?php else: ?>
        <p>Sesi tidak ditemukan. Silakan <a href="<?= site_url('login') ?>">login</a> kembali.</p>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
