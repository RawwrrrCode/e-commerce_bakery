<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Mimosa' ?></title>
    <link rel="stylesheet" href="<?= base_url('css/style.css') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>

<body>

    <!-- NAVBAR -->
    <header class="navbar">
        <div class="nav-container">

            <!-- LEFT: Logo + Nav Links -->
            <div class="nav-left">
                <a href="<?= base_url() ?>" class="logo" style="text-decoration:none;">
                    <div class="logo-icon">
                        <svg width="24" height="26" viewBox="0 0 60 70" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M2 2L2 24C2 35 8.5 40 16 40C23.5 40 30 35 30 24L30 2L23 2L23 24C23 31 20 34 16 34C12 34 9 31 9 24L9 2Z" fill="white"/>
                            <path d="M32 2L32 24C32 35 38.5 40 46 40C53.5 40 60 35 60 24L60 2L53 2L53 24C53 31 50 34 46 34C42 34 39 31 39 24L39 2Z" fill="white"/>
                            <circle cx="31" cy="47" r="4" fill="white"/>
                            <path d="M5 55 Q31 70 57 55" stroke="white" stroke-width="6" fill="none" stroke-linecap="round"/>
                        </svg>
                    </div>
                    <div>
                        <div>Mimosa</div>
                        <span>Bakery & Pastry</span>
                    </div>
                </a>

                <nav class="nav-main">
                    <a href="<?= base_url() ?>">Beranda</a>
                    <a href="<?= base_url('about') ?>">Tentang Kami</a>
                    <a href="<?= base_url('faq') ?>">FAQ</a>
                </nav>
            </div>

            <!-- RIGHT: Actions -->
            <div class="nav-actions">
                <?php
                $userId = session()->get('id');
                if ($userId):
                    $db = \Config\Database::connect();
                    $unread = $db->table('orders')
                        ->where('user_id', $userId)
                        ->where('notification_read', 0)
                        ->countAllResults();
                ?>
                <a href="<?= base_url('orders') ?>" class="nav-action" style="position:relative;">
                    Pesanan
                    <?php if ($unread > 0): ?>
                        <span class="nav-badge"><?= $unread ?></span>
                    <?php endif; ?>
                </a>

                <a href="<?= base_url('cart') ?>" class="nav-action nav-action-icon" title="Keranjang">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/>
                    </svg>
                </a>

                <a href="<?= base_url('profile') ?>" class="nav-action nav-action-icon" title="Profil">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/>
                    </svg>
                </a>

                <span class="nav-divider"></span>

                <a href="<?= base_url('logout') ?>" class="nav-action nav-logout">Keluar</a>

                <?php else: ?>
                <a href="<?= base_url('login') ?>" class="nav-action">Masuk</a>
                <a href="<?= base_url('register') ?>" class="nav-action nav-btn-register">Daftar</a>
                <?php endif; ?>
            </div>

            <!-- HAMBURGER (mobile only) -->
            <button class="nav-hamburger" id="navHamburger" onclick="toggleMobileMenu()" aria-label="Buka menu">
                <span></span><span></span><span></span>
            </button>

        </div>

        <!-- MOBILE MENU -->
        <div class="mobile-menu" id="mobileMenu">
            <a href="<?= base_url() ?>">🏠 Beranda</a>
            <a href="<?= base_url('about') ?>">ℹ️ Tentang Kami</a>
            <a href="<?= base_url('faq') ?>">❓ FAQ</a>
            <div class="mobile-menu-divider"></div>
            <?php if (isset($userId) && $userId): ?>
            <a href="<?= base_url('orders') ?>">📋 Pesanan Saya</a>
            <a href="<?= base_url('cart') ?>">🛒 Keranjang</a>
            <a href="<?= base_url('profile') ?>">👤 Profil</a>
            <a href="<?= base_url('logout') ?>" style="color:#DC2626;">🚪 Keluar</a>
            <?php else: ?>
            <a href="<?= base_url('login') ?>">Masuk</a>
            <a href="<?= base_url('register') ?>" style="color:var(--red); font-weight:600;">Daftar Sekarang</a>
            <?php endif; ?>
        </div>

    </header>

    <!-- CONTENT -->
    <?= $this->renderSection('content') ?>

    <!-- FOOTER -->
    <footer class="footer-strip">
        &copy; <?= date('Y') ?> <span>Mimosa</span> · PT. Mimosa Tarte Indonesia — Fresh Every Day
        &nbsp;·&nbsp;
        <a href="<?= base_url('about') ?>" style="color:inherit; text-decoration:underline; opacity:0.7;">Tentang Kami</a>
        &nbsp;·&nbsp;
        <a href="<?= base_url('faq') ?>" style="color:inherit; text-decoration:underline; opacity:0.7;">FAQ</a>
        &nbsp;·&nbsp;
        <a href="<?= base_url('about') ?>#kontak" style="color:inherit; text-decoration:underline; opacity:0.7;">Kontak</a>
    </footer>

    <!-- TOAST -->
    <div id="toast" class="toast">Berhasil ditambahkan</div>

    <!-- JS -->
    <script src="<?= base_url('js/script.js') ?>"></script>

</body>

</html>