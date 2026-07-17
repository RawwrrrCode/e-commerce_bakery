<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Mimosa</title>
    <link rel="stylesheet" href="<?= base_url('css/style.css') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>

<body class="auth-body">

<div class="auth-container">
    <div class="auth-card">

        <!-- HEADER MERAH -->
        <div class="auth-header">
            <div class="auth-logo" style="display:flex;align-items:center;justify-content:center;gap:10px;">
                <svg width="26" height="28" viewBox="0 0 60 70" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M2 2L2 24C2 35 8.5 40 16 40C23.5 40 30 35 30 24L30 2L23 2L23 24C23 31 20 34 16 34C12 34 9 31 9 24L9 2Z" fill="white"/>
                    <path d="M32 2L32 24C32 35 38.5 40 46 40C53.5 40 60 35 60 24L60 2L53 2L53 24C53 31 50 34 46 34C42 34 39 31 39 24L39 2Z" fill="white"/>
                    <circle cx="31" cy="47" r="4" fill="white"/>
                    <path d="M5 55 Q31 70 57 55" stroke="white" stroke-width="6" fill="none" stroke-linecap="round"/>
                </svg>
                Mimosa
            </div>
            <div class="auth-logo-sub">Bakery &amp; Pastry</div>
        </div>

        <!-- FORM -->
        <div class="auth-body-content">
            <h2>Selamat Datang</h2>
            <p class="auth-subtitle">Masuk ke akun Anda untuk melanjutkan</p>

            <?php if(session()->getFlashdata('success')): ?>
            <div style="background:#D1FAE5; color:#065F46; padding:12px 16px; border-radius:8px; font-size:14px; margin-bottom:16px; border-left:4px solid #10B981;">
                <?= session()->getFlashdata('success') ?>
            </div>
            <?php endif; ?>
            <?php if(session()->getFlashdata('error')): ?>
            <div style="background:var(--red-light); color:var(--red); padding:12px 16px; border-radius:8px; font-size:14px; margin-bottom:16px; border-left:4px solid var(--red);">
                <?= session()->getFlashdata('error') ?>
            </div>
            <?php endif; ?>

            <form method="post" action="<?= base_url('/login') ?>">
                <?= csrf_field() ?>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="<?= old('email') ?>" placeholder="contoh@email.com" required autofocus>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" placeholder="Masukkan password" required>
                </div>

                <button type="submit" class="auth-btn">Masuk →</button>
            </form>
        </div>

        <!-- FOOTER -->
        <div class="auth-footer">
            Belum punya akun?
            <a href="<?= base_url('/register') ?>">Daftar sekarang</a>
        </div>

    </div>
</div>

</body>
</html>
