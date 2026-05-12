<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 — Halaman Tidak Ditemukan | Toko Roti</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: #FFF8F0;
            font-family: 'Poppins', sans-serif;
            color: #3C2A1E;
            text-align: center;
            padding: 24px;
        }
        .icon { font-size: 80px; margin-bottom: 16px; animation: float 3s ease-in-out infinite; }
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-12px); }
        }
        .code {
            font-family: 'Playfair Display', serif;
            font-size: 96px;
            font-weight: 700;
            color: #C1121F;
            line-height: 1;
            margin-bottom: 8px;
        }
        h1 {
            font-family: 'Playfair Display', serif;
            font-size: 26px;
            color: #3C2A1E;
            margin-bottom: 12px;
        }
        p {
            font-size: 14px;
            color: #888;
            max-width: 380px;
            line-height: 1.8;
            margin-bottom: 32px;
        }
        .btn-home {
            display: inline-block;
            background: #C1121F;
            color: white;
            padding: 13px 36px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            margin-right: 10px;
            transition: opacity .2s;
        }
        .btn-home:hover { opacity: .88; }
        .btn-back {
            display: inline-block;
            border: 2px solid #C1121F;
            color: #C1121F;
            padding: 11px 28px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            transition: all .2s;
        }
        .btn-back:hover { background: #C1121F; color: white; }
        .divider {
            width: 60px;
            height: 3px;
            background: #C1121F;
            border-radius: 2px;
            margin: 20px auto;
        }
        .footer {
            margin-top: 48px;
            font-size: 12px;
            color: #bbb;
        }
        .footer a { color: #C1121F; text-decoration: none; }
    </style>
</head>
<body>

    <div class="icon">🍞</div>
    <div class="code">404</div>
    <div class="divider"></div>
    <h1>Halaman Tidak Ditemukan</h1>
    <p>Sepertinya roti yang Anda cari sudah habis terjual atau halaman ini tidak tersedia. Yuk kembali dan temukan produk lainnya!</p>

    <div>
        <a href="/" class="btn-home">🏠 Kembali ke Beranda</a>
        <a href="javascript:history.back()" class="btn-back">← Kembali</a>
    </div>

    <div class="footer">
        &copy; <?= date('Y') ?> <a href="/">PT. Mimosa Tarte Indonesia</a> · Fresh Every Day
    </div>

</body>
</html>
