<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<!-- HERO -->
<section class="hero">
    <div class="hero-content">
        <div class="hero-badge">✨ Fresh Every Day</div>
        <h1>Roti & Kue<br>Berkualitas Premium</h1>
        <p>Temukan pilihan roti dan kue terbaik dengan resep autentik, dipanggang segar setiap hari untuk Anda.</p>
        <div class="hero-buttons">
            <a href="#produk" class="btn-hero-primary">Pesan Sekarang</a>
            <a href="#kategori" class="btn-hero-secondary">Lihat Kategori</a>
        </div>
    </div>
</section>

<!-- KATEGORI -->
<section class="section" id="kategori" style="background: var(--white); padding: 60px 0;">
    <div class="section-header">
        <div class="section-label">Jelajahi</div>
        <h2 class="section-title">Kategori Produk</h2>
        <div class="section-divider"></div>
        <p class="section-subtitle" style="margin-top:12px;">Temukan produk favorit Anda dari berbagai kategori pilihan kami</p>
    </div>

    <div class="category-wrapper">
        <div class="category-card">
            <img src="https://images.unsplash.com/photo-1578985545062-69928b1d9587?w=400&h=300&fit=crop" alt="Pillow Cake">
            <div class="category-overlay"></div>
            <div class="category-name">Pillow Cake</div>
        </div>
        <div class="category-card">
            <img src="https://images.unsplash.com/photo-1533134242443-d4fd215305ad?w=400&h=300&fit=crop" alt="Cheesecake">
            <div class="category-overlay"></div>
            <div class="category-name">Cheesecake</div>
        </div>
        <div class="category-card">
            <img src="https://images.unsplash.com/photo-1499636136210-6f4ee915583e?w=400&h=300&fit=crop" alt="Cookies">
            <div class="category-overlay"></div>
            <div class="category-name">Cookies</div>
        </div>
        <div class="category-card">
            <img src="https://images.unsplash.com/photo-1555507036-ab1f4038808a?w=400&h=300&fit=crop" alt="Pastry">
            <div class="category-overlay"></div>
            <div class="category-name">Pastry</div>
        </div>
    </div>
</section>


<!-- ================================================================
     SEMUA PRODUK
     ================================================================ -->
<section id="produk" style="padding: 0 0 60px;">

    <!-- FILTER BAR -->
    <div class="filter-section">
        <div class="filter-inner">
            <span class="filter-label">Filter:</span>
            <div class="category-bar">
                <a href="/" class="<?= !$category ? 'active' : '' ?>">Semua</a>
                <a href="/?category=Pillow+Cake#produk" class="<?= ($category == 'Pillow Cake') ? 'active' : '' ?>">Pillow Cake</a>
                <a href="/?category=Cheesecake#produk" class="<?= ($category == 'Cheesecake') ? 'active' : '' ?>">Cheesecake</a>
                <a href="/?category=Cookies#produk" class="<?= ($category == 'Cookies') ? 'active' : '' ?>">Cookies</a>
                <a href="/?category=Pastry#produk" class="<?= ($category == 'Pastry') ? 'active' : '' ?>">Pastry</a>
            </div>
            <form method="get" action="/" class="search-box">
                <input type="text" name="search" placeholder="Cari produk..."
                    value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                <button type="submit">🔍</button>
            </form>
        </div>
    </div>

    <!-- REKOMENDASI (hanya halaman 1, tanpa filter aktif) -->
    <?php if (!empty($recommendations) && !$category && empty($_GET['search'])): ?>
    <div style="background:#FFF8F0; border-bottom:1px solid #EDE0D8; padding: 32px 0 28px;">
        <div class="container">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:8px;">
                <div>
                    <h3 style="font-family:'Playfair Display',serif; font-size:20px; color:var(--brown-dark); margin-bottom:2px;">
                        ✨ Rekomendasi untuk Anda
                    </h3>
                    <p style="font-size:12px; color:var(--gray); font-family:'Poppins',sans-serif;">
                        Berdasarkan riwayat belanja &amp; preferensi Anda &nbsp;·&nbsp;
                        <strong style="color:var(--brown-dark);"><?= count($recommendations) ?> produk</strong>
                    </p>
                </div>
            </div>

            <div class="rec-scroll-wrap">
                <?php foreach ($recommendations as $p): ?>
                <div class="rec-card">
                    <a href="<?= base_url('product/' . $p['id']) ?>"
                       onclick="trackRecommendationClick(<?= $p['id'] ?>)"
                       class="rec-card-img-link">
                        <img src="<?= base_url('uploads/' . $p['image']) ?>"
                             alt="<?= htmlspecialchars($p['name']) ?>"
                             class="rec-card-img">
                        <?php if (($p['stock'] ?? 1) <= 0): ?>
                            <span class="rec-stock-badge" style="background:#6B7280;">Stok Habis</span>
                        <?php elseif (($p['sold'] ?? 0) > 50): ?>
                            <span class="rec-stock-badge">🔥 Laris</span>
                        <?php endif; ?>
                    </a>
                    <div class="rec-card-body">
                        <div class="rec-reason-chip"><?= htmlspecialchars($p['rec_reason'] ?? '✨ Dipilih untukmu') ?></div>
                        <div class="rec-card-name"><?= htmlspecialchars($p['name']) ?></div>
                        <?php if (($p['rating'] ?? 0) > 0): ?>
                        <div style="font-size:12px; color:#F59E0B; margin-bottom:4px;">
                            <?php for ($s = 1; $s <= 5; $s++) echo $s <= round($p['rating']) ? '★' : '☆'; ?>
                            <span style="color:var(--gray); font-family:'Poppins',sans-serif;">(<?= $p['rating_count'] ?? 0 ?>)</span>
                        </div>
                        <?php endif; ?>
                        <div class="rec-card-price">Rp <?= number_format($p['price'], 0, ',', '.') ?></div>
                        <a href="<?= base_url('product/' . $p['id']) ?>"
                           onclick="trackRecommendationClick(<?= $p['id'] ?>)"
                           class="rec-card-btn">Lihat Detail</a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <style>
        .rec-scroll-wrap {
            display: flex;
            gap: 18px;
            overflow-x: auto;
            padding-bottom: 8px;
            scroll-snap-type: x mandatory;
            -webkit-overflow-scrolling: touch;
        }
        .rec-scroll-wrap::-webkit-scrollbar { height: 4px; }
        .rec-scroll-wrap::-webkit-scrollbar-thumb { background: #E8D5C4; border-radius: 4px; }
        .rec-card {
            min-width: 200px; max-width: 200px;
            background: white; border-radius: 14px;
            box-shadow: 0 2px 10px rgba(0,0,0,.07);
            overflow: hidden; scroll-snap-align: start; flex-shrink: 0;
            transition: transform .2s, box-shadow .2s;
        }
        .rec-card:hover { transform: translateY(-4px); box-shadow: 0 8px 20px rgba(0,0,0,.12); }
        .rec-card-img-link { display:block; position:relative; aspect-ratio:1/1; overflow:hidden; }
        .rec-card-img { width:100%; height:100%; object-fit:cover; transition:transform .3s; }
        .rec-card:hover .rec-card-img { transform: scale(1.05); }
        .rec-stock-badge {
            position:absolute; top:8px; left:8px;
            background:var(--red); color:white; font-size:10px; font-weight:700;
            padding:3px 8px; border-radius:20px; font-family:'Poppins',sans-serif;
        }
        .rec-card-body { padding: 12px; }
        .rec-reason-chip {
            display:inline-block; font-size:10px; font-weight:600;
            color:#7C3AED; background:#EDE9FE; border-radius:20px;
            padding:3px 10px; margin-bottom:7px; font-family:'Poppins',sans-serif;
            max-width:100%; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;
        }
        .rec-card-name {
            font-weight:700; font-size:13px; color:var(--brown-dark);
            font-family:'Playfair Display',serif; margin-bottom:4px; line-height:1.4;
            display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden;
        }
        .rec-card-price {
            font-size:14px; font-weight:700; color:var(--red);
            font-family:'Poppins',sans-serif; margin-bottom:10px;
        }
        .rec-card-btn {
            display:block; text-align:center; background:var(--red); color:white;
            padding:8px 0; border-radius:8px; font-size:12px; font-weight:600;
            text-decoration:none; font-family:'Poppins',sans-serif; transition:opacity .2s;
        }
        .rec-card-btn:hover { opacity:.88; }
    </style>
    <?php endif; ?>

    <div class="container" style="padding-top: 36px;">

        <div class="section-header" style="text-align:left; margin-bottom:28px;">
            <h2 class="section-title" style="text-align:left; font-size:26px;">
                <?php if ($category): ?>
                    <?= htmlspecialchars($category) ?>
                <?php elseif (isset($_GET['recommend'])): ?>
                    ⭐ Produk Rekomendasi
                <?php elseif (!empty($_GET['search'])): ?>
                    Hasil: "<?= htmlspecialchars($_GET['search']) ?>"
                <?php else: ?>
                    Semua Produk
                <?php endif; ?>
            </h2>
            <p class="text-muted" style="font-size:14px; margin-top:4px;">
                <?= count($products) ?> produk ditemukan
            </p>
        </div>

        <?php if (empty($products)): ?>
            <div style="text-align:center; padding:60px 24px; background:var(--white); border-radius:var(--radius-lg); box-shadow:var(--shadow);">
                <div style="font-size:56px; margin-bottom:16px;">🍞</div>
                <h3 style="font-family:'Playfair Display',serif; font-size:22px; color:var(--brown-dark); margin-bottom:8px;">Produk tidak ditemukan</h3>
                <p class="text-muted">Coba kata kunci lain atau lihat semua produk</p>
                <a href="/" style="display:inline-block; margin-top:20px; background:var(--red); color:white; padding:10px 24px; border-radius:8px; font-weight:600;">Lihat Semua</a>
            </div>
        <?php else: ?>
            <div class="product-grid">
                <?php foreach ($products as $i => $p): ?>
                    <div class="product-card" style="animation-delay:<?= $i * 0.07 ?>s; position: relative;">
                        <div class="img-wrapper">
                            <img src="<?= base_url('uploads/' . $p['image']) ?>"
                                alt="<?= htmlspecialchars($p['name']) ?>"
                                <?php if (in_array($p['id'], $recommendedIds ?? [])): ?>onclick="trackRecommendationClick(<?= $p['id'] ?>)"<?php endif; ?>>
                            <div class="badge-group">
                                <?php if (($p['stock'] ?? 1) <= 0): ?>
                                    <span class="badge" style="background:#6B7280;">Stok Habis</span>
                                <?php elseif (($p['sold'] ?? 0) > 50): ?>
                                    <span class="badge">🔥 Laris</span>
                                <?php endif; ?>
                                <?php if (in_array($p['id'], $recommendedIds ?? [])): ?>
                                    <span class="badge-rec">⭐ Rekomendasi</span>
                                <?php endif; ?>
                            </div>
                            <?php if (session()->get('id')): ?>
                                <button class="wishlist-btn-home" onclick="toggleWishlistHome(<?= $p['id'] ?>, event)" data-product-id="<?= $p['id'] ?>" style="position: absolute; top: 8px; right: 8px; background: white; border: none; width: 36px; height: 36px; border-radius: 50%; cursor: pointer; font-size: 18px; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 8px rgba(0,0,0,0.15); transition: all 0.3s; z-index: 10;">
                                    ♡
                                </button>
                            <?php endif; ?>
                        </div>
                        <div class="product-info">
                            <?php if (($p['rating'] ?? 0) > 0): ?>
                                <div class="product-stars">
                                    <?php for ($s = 1; $s <= 5; $s++) echo $s <= round($p['rating']) ? '★' : '☆'; ?>
                                    <span>(<?= $p['rating_count'] ?>)</span>
                                </div>
                            <?php endif; ?>
                            <div class="product-name"><?= htmlspecialchars($p['name']) ?></div>
                            <div class="product-price">Rp <?= number_format($p['price'], 0, ',', '.') ?></div>
                            <a href="<?= base_url('product/' . $p['id']) ?>"
                                <?php if (in_array($p['id'], $recommendedIds ?? [])): ?>onclick="trackRecommendationClick(<?= $p['id'] ?>)"<?php endif; ?>
                                class="btn-detail">Lihat Detail</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (!$recommend && $totalPages > 1): ?>
            <?php
            $qp = array_filter(['search' => $_GET['search'] ?? '', 'category' => $_GET['category'] ?? '']);
            $qs = $qp ? '?' . http_build_query($qp) . '&' : '?';
            ?>
            <div style="display:flex; justify-content:center; align-items:center; gap:8px; margin-top:32px; flex-wrap:wrap;">
                <?php if ($currentPage > 1): ?>
                    <a href="<?= $qs ?>page=<?= $currentPage - 1 ?>#produk"
                        style="padding:8px 16px; border-radius:8px; font-size:13px; font-weight:600; text-decoration:none; background:white; color:var(--brown-dark); border:1.5px solid #E8D5C4; box-shadow:var(--shadow);">
                        ← Sebelumnya
                    </a>
                <?php endif; ?>

                <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                    <a href="<?= $qs ?>page=<?= $p ?>#produk"
                        style="padding:8px 14px; border-radius:8px; font-size:13px; font-weight:600; text-decoration:none;
                      background:<?= $p === $currentPage ? 'var(--red)' : 'white' ?>;
                      color:<?= $p === $currentPage ? 'white' : 'var(--brown-dark)' ?>;
                      border:1.5px solid <?= $p === $currentPage ? 'var(--red)' : '#E8D5C4' ?>;
                      box-shadow:var(--shadow);">
                        <?= $p ?>
                    </a>
                <?php endfor; ?>

                <?php if ($currentPage < $totalPages): ?>
                    <a href="<?= $qs ?>page=<?= $currentPage + 1 ?>#produk"
                        style="padding:8px 16px; border-radius:8px; font-size:13px; font-weight:600; text-decoration:none; background:white; color:var(--brown-dark); border:1.5px solid #E8D5C4; box-shadow:var(--shadow);">
                        Selanjutnya →
                    </a>
                <?php endif; ?>
            </div>
            <p style="text-align:center; font-size:12px; color:var(--gray); margin-top:8px; margin-bottom:0;">
                Halaman <?= $currentPage ?> dari <?= $totalPages ?>
            </p>
        <?php endif; ?>

    </div>
</section>

<script>
    function trackRecommendationClick(productId) {
        fetch('<?= base_url('rec/track') ?>', {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: 'product_id=' + productId
        });
    }

    // ── Wishlist toggle untuk home page ────────────────────────────
    function toggleWishlistHome(productId, event) {
        event.stopPropagation();
        const btn = event.target.closest('.wishlist-btn-home');
        
        fetch('<?= base_url('/wishlist/check') ?>/' + productId, {
            method: 'GET',
            headers: {'X-Requested-With': 'XMLHttpRequest'}
        })
        .then(r => r.json())
        .then(data => {
            const isInWishlist = data.inWishlist;
            const action = isInWishlist ? 'remove' : 'add';
            
            fetch('<?= base_url('/wishlist') ?>/' + action, {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest'},
                body: new URLSearchParams({product_id: productId})
            })
            .then(r => r.json())
            .then(result => {
                if (result.success) {
                    if (result.inWishlist) {
                        btn.textContent = '❤️';
                        btn.style.background = '#ffe5e5';
                        showToast('✓ Ditambahkan ke wishlist');
                    } else {
                        btn.textContent = '♡';
                        btn.style.background = 'white';
                        showToast('✓ Dihapus dari wishlist');
                    }
                }
            });
        });
    }

    // Load wishlist status on page load
    <?php if (session()->get('id')): ?>
    document.addEventListener('DOMContentLoaded', function() {
        const buttons = document.querySelectorAll('.wishlist-btn-home');
        buttons.forEach(btn => {
            const productId = btn.dataset.productId;
            fetch('<?= base_url('/wishlist/check') ?>/' + productId, {
                method: 'GET',
                headers: {'X-Requested-With': 'XMLHttpRequest'}
            })
            .then(r => r.json())
            .then(data => {
                if (data.inWishlist) {
                    btn.textContent = '❤️';
                    btn.style.background = '#ffe5e5';
                }
            });
        });
    });
    <?php endif; ?>

    function showToast(message, type = 'info') {
        const toast = document.createElement('div');
        toast.style.cssText = `
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: ${type === 'success' ? '#10b981' : '#3b82f6'};
            color: white;
            padding: 12px 20px;
            border-radius: 6px;
            font-size: 14px;
            z-index: 9999;
            animation: slideUp 0.3s ease-out;
        `;
        toast.textContent = message;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3000);
    }
</script>

<?= $this->endSection() ?>