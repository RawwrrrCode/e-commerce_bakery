<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Toko Roti</title>
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
            <div class="auth-logo">🍞 Toko Roti</div>
            <div class="auth-logo-sub">Bakery &amp; Pastry</div>
        </div>

        <!-- FORM -->
        <div class="auth-body-content">
            <h2>Selamat Datang</h2>
            <p class="auth-subtitle">Masuk ke akun Anda untuk melanjutkan</p>

            <?php if(session()->getFlashdata('error')): ?>
            <div style="background:var(--red-light); color:var(--red); padding:12px 16px; border-radius:8px; font-size:14px; margin-bottom:16px; border-left:4px solid var(--red);">
                <?= session()->getFlashdata('error') ?>
            </div>
            <?php endif; ?>

            <form method="post" action="<?= base_url('/login') ?>">
                <?= csrf_field() ?>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" placeholder="contoh@email.com" required autofocus>
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
