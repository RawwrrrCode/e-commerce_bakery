<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar — Toko Roti</title>
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
            <h2>Buat Akun Baru</h2>
            <p class="auth-subtitle">Bergabung dan nikmati kemudahan berbelanja</p>

            <?php if(session()->getFlashdata('error')): ?>
            <div style="background:var(--red-light); color:var(--red); padding:12px 16px; border-radius:8px; font-size:14px; margin-bottom:16px; border-left:4px solid var(--red);">
                <?= session()->getFlashdata('error') ?>
            </div>
            <?php endif; ?>

            <form method="post" action="<?= base_url('/register') ?>">
                <?= csrf_field() ?>

                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" name="name" placeholder="Masukkan nama Anda" required autofocus>
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" placeholder="contoh@email.com" required>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" placeholder="Minimal 6 karakter" required>
                </div>

                <button type="submit" class="auth-btn">Daftar Sekarang →</button>
            </form>
        </div>

        <!-- FOOTER -->
        <div class="auth-footer">
            Sudah punya akun?
            <a href="<?= base_url('/login') ?>">Masuk di sini</a>
        </div>

    </div>
</div>

</body>
</html>
