<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — MasjidCMS Admin</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/app-theme.css') ?>">
    <style>
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--background, #F9FAFB);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        }
        .login-card {
            width: 100%;
            max-width: 380px;
            background: var(--surface, #FFFFFF);
            border: 1px solid var(--border, #E5E7EB);
            border-radius: var(--radius-lg, 8px);
            box-shadow: var(--shadow-lg, 0 10px 15px -3px rgba(0,0,0,0.1));
            padding: 32px;
        }
        .login-brand {
            text-align: center;
            margin-bottom: 24px;
        }
        .login-brand .logo { font-size: 36px; }
        .login-brand .name {
            display: block;
            font-weight: 700;
            font-size: 18px;
            color: var(--text-primary, #111827);
            margin-top: 4px;
        }
        .login-brand .sub {
            display: block;
            font-size: 13px;
            color: var(--text-secondary, #4B5563);
            margin-top: 2px;
        }
        .form-group { margin-bottom: 16px; }
        label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-primary, #111827);
            margin-bottom: 6px;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            box-sizing: border-box;
            padding: 10px 12px;
            border: 1px solid var(--border, #E5E7EB);
            border-radius: var(--radius-sm, 4px);
            font-size: 14px;
        }
        input:focus {
            outline: none;
            border-color: var(--color-primary, #059669);
        }
        .remember-row {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            color: var(--text-secondary, #4B5563);
            margin-bottom: 20px;
        }
        button.btn-submit {
            width: 100%;
            padding: 10px 12px;
            background: var(--color-primary, #059669);
            color: #fff;
            border: none;
            border-radius: var(--radius-sm, 4px);
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
        }
        button.btn-submit:hover { background: var(--color-primary-hover, #047857); }
        .alert {
            padding: 10px 12px;
            border-radius: var(--radius-sm, 4px);
            font-size: 13px;
            margin-bottom: 16px;
        }
        .alert-error {
            background: #FEF2F2;
            color: var(--color-danger, #991B1B);
            border: 1px solid #FCA5A5;
        }
        .alert-success {
            background: #F0FDF4;
            color: var(--color-success, #166534);
            border: 1px solid #86EFAC;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-brand">
            <div class="logo">🕌</div>
            <span class="name">MasjidCMS</span>
            <span class="sub">Masuk ke Panel Admin</span>
        </div>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-error">⚠️ <?= esc(session()->getFlashdata('error')) ?></div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success">✅ <?= esc(session()->getFlashdata('success')) ?></div>
        <?php endif; ?>

        <form method="post" action="<?= site_url('login') ?>" autocomplete="off">
            <?= csrf_field() ?>

            <div class="form-group">
                <label for="username">Username / Email</label>
                <input type="text" id="username" name="username" value="<?= esc(old('username')) ?>" required autofocus>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div class="remember-row">
                <input type="checkbox" id="remember" name="remember" value="1">
                <label for="remember" style="margin: 0; font-weight: 400;">Ingat saya</label>
            </div>

            <button type="submit" class="btn-submit">Masuk</button>
        </form>
    </div>
</body>
</html>
