<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin — Toko Roti</title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>

<body style="margin:0; background:#F7F2EE;">

    <div class="admin-layout">

        <!-- SIDEBAR -->
        <aside class="sidebar">

            <!-- Logo -->
            <div class="sidebar-logo">
                <div class="sidebar-logo-icon">🍞</div>
                <div>
                    <div class="sidebar-logo-name">Toko Roti</div>
                    <div class="sidebar-logo-sub">Admin Panel</div>
                </div>
            </div>

            <?php
            $segs = service('uri')->getSegments();
            $seg2 = $segs[1] ?? '';
            $seg3 = $segs[2] ?? '';

            $inKatalog  = in_array($seg2, ['products','categories','stock']);
            $inTransaksi = in_array($seg2, ['orders','report']);
            $inRec      = ($seg2 === 'recommendation');
            ?>

            <!-- Nav scrollable -->
            <nav class="sidebar-nav">

                <!-- Dashboard -->
                <a href="/admin" class="sidebar-link <?= (!$seg2) ? 'active' : '' ?>">
                    <span class="sidebar-icon">📊</span>
                    <span>Dashboard</span>
                </a>

                <!-- TRANSAKSI dropdown -->
                <div class="sidebar-dropdown-btn <?= $inTransaksi ? 'open' : '' ?>" onclick="toggleDropdown(this)">
                    <span class="sidebar-icon">🛒</span>
                    <span>Transaksi</span>
                    <span class="sidebar-dropdown-arrow">▶</span>
                </div>
                <div class="sidebar-dropdown-menu <?= $inTransaksi ? 'open' : '' ?>">
                    <a href="/admin/orders" class="sidebar-sub-link <?= ($seg2 == 'orders') ? 'active' : '' ?>">
                        <span class="sidebar-sub-dot"></span>
                        Pesanan
                    </a>
                    <a href="/admin/report" class="sidebar-sub-link <?= ($seg2 == 'report') ? 'active' : '' ?>">
                        <span class="sidebar-sub-dot"></span>
                        Laporan Penjualan
                    </a>
                </div>

                <!-- KATALOG dropdown -->
                <div class="sidebar-dropdown-btn <?= $inKatalog ? 'open' : '' ?>" onclick="toggleDropdown(this)">
                    <span class="sidebar-icon">🍞</span>
                    <span>Katalog</span>
                    <span class="sidebar-dropdown-arrow">▶</span>
                </div>
                <div class="sidebar-dropdown-menu <?= $inKatalog ? 'open' : '' ?>">
                    <a href="/admin/products" class="sidebar-sub-link <?= ($seg2 == 'products') ? 'active' : '' ?>">
                        <span class="sidebar-sub-dot"></span>
                        Produk
                    </a>
                    <a href="/admin/categories" class="sidebar-sub-link <?= ($seg2 == 'categories') ? 'active' : '' ?>">
                        <span class="sidebar-sub-dot"></span>
                        Kategori Produk
                    </a>
                    <a href="/admin/stock" class="sidebar-sub-link <?= ($seg2 == 'stock') ? 'active' : '' ?>">
                        <span class="sidebar-sub-dot"></span>
                        Kelola Stok
                    </a>
                </div>

                <!-- Moderasi Ulasan -->
                <a href="/admin/reviews" class="sidebar-link <?= ($seg2 == 'reviews') ? 'active' : '' ?>">
                    <span class="sidebar-icon">⭐</span>
                    <span>Moderasi Ulasan</span>
                </a>

                <!-- Pengaturan Toko -->
                <a href="/admin/settings" class="sidebar-link <?= ($seg2 == 'settings') ? 'active' : '' ?>">
                    <span class="sidebar-icon">⚙️</span>
                    <span>Pengaturan Toko</span>
                </a>

                <div class="sidebar-divider" style="margin:10px 0;"></div>

                <!-- SISTEM REKOMENDASI dropdown -->
                <div class="sidebar-dropdown-btn <?= $inRec ? 'open' : '' ?>" onclick="toggleDropdown(this)">
                    <span class="sidebar-icon">🤖</span>
                    <span>Sistem Rekomendasi</span>
                    <span class="sidebar-dropdown-arrow">▶</span>
                </div>
                <div class="sidebar-dropdown-menu <?= $inRec ? 'open' : '' ?>">
                    <a href="/admin/recommendation" class="sidebar-sub-link <?= ($seg2 == 'recommendation' && $seg3 == '') ? 'active' : '' ?>">
                        <span class="sidebar-sub-dot"></span>
                        Evaluasi Hybrid
                    </a>
                    <a href="/admin/recommendation/data" class="sidebar-sub-link <?= ($seg3 == 'data') ? 'active' : '' ?>">
                        <span class="sidebar-sub-dot"></span>
                        Data Input CF
                    </a>
                </div>

            </nav>

            <!-- Logout pinned at bottom -->
            <div class="sidebar-footer">
                <a href="/logout" class="sidebar-logout">
                    <span class="sidebar-icon">🚪</span>
                    <span>Keluar</span>
                </a>
            </div>

        </aside>

        <!-- CONTENT -->
        <div class="main-content">
            <?= $this->renderSection('content') ?>
        </div>

    </div>

    <script src="/js/script.js"></script>
    <script>
    function toggleDropdown(btn) {
        var menu = btn.nextElementSibling;
        var isOpen = btn.classList.contains('open');
        btn.classList.toggle('open', !isOpen);
        menu.classList.toggle('open', !isOpen);
    }
    </script>
</body>

</html>
