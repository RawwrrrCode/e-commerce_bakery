<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="detail-wrapper container">

    <!-- BREADCRUMB -->
    <div class="breadcrumb">
        <a href="<?= base_url() ?>">Beranda</a> &rsaquo;
        <a href="<?= base_url('?category=' . urlencode($product['category'] ?? '')) ?>">
            <?= htmlspecialchars($product['category'] ?? 'Produk') ?>
        </a> &rsaquo;
        <span><?= htmlspecialchars($product['name']) ?></span>
    </div>

    <!-- DETAIL PRODUK -->
    <div class="detail-container">

        <!-- GAMBAR -->
        <div class="detail-image">
            <img src="<?= base_url('uploads/' . $product['image']) ?>"
                alt="<?= htmlspecialchars($product['name']) ?>">
        </div>

        <!-- INFO -->
        <div class="detail-info">

            <h2><?= htmlspecialchars($product['name']) ?></h2>

            <!-- RATING DISPLAY -->
            <div class="detail-rating">
                <div class="stars-display">
                    <?php $r = round($product['rating'] ?? 0);
                    for ($s = 1; $s <= 5; $s++) echo $s <= $r ? '★' : '☆'; ?>
                </div>
                <span class="rating-value"><?= number_format($product['rating'] ?? 0, 1) ?></span>
                <span class="rating-count">(<?= $product['rating_count'] ?? 0 ?> ulasan)</span>
                <span style="margin-left:8px; background:var(--beige); color:var(--brown);
                             font-size:12px; padding:3px 10px; border-radius:20px;">
                    <?= $product['sold'] ?? 0 ?> terjual
                </span>
            </div>

            <!-- HARGA -->
            <div class="price">Rp <?= number_format($product['price'], 0, ',', '.') ?></div>

            <!-- DESKRIPSI -->
            <p class="desc">
                <?= htmlspecialchars($product['description'] ?? 'Deskripsi belum tersedia.') ?>
            </p>

            <!-- QTY -->
            <div class="qty-label">Jumlah</div>
            <div class="qty-box">
                <button type="button" onclick="minusQty()">−</button>
                <input type="text" id="qty" value="1">
                <button type="button" onclick="plusQty()">+</button>
            </div>

            <!-- STOK INFO -->
            <?php $outOfStock = ($product['stock'] ?? 1) <= 0; ?>
            <div style="margin-bottom:12px; font-size:13px;">
                <?php if ($outOfStock): ?>
                    <span style="background:#FEE2E2; color:#991B1B; padding:4px 12px; border-radius:20px; font-weight:600;">❌ Stok Habis</span>
                <?php else: ?>
                    <span style="background:#D1FAE5; color:#065F46; padding:4px 12px; border-radius:20px; font-weight:600;">✓ Stok: <?= $product['stock'] ?></span>
                <?php endif; ?>
            </div>

            <!-- TAMBAH KE KERANJANG -->
            <div style="display: flex; gap: 12px; margin-bottom: 24px;">
                <?php if ($outOfStock): ?>
                    <button class="btn-add" style="width: 100%; flex: 1; opacity: 0.45; cursor: not-allowed;" disabled>🛒 Stok Habis</button>
                <?php else: ?>
                    <form method="post" action="<?= base_url('cart/add') ?>" style="flex: 1;">
                        <?= csrf_field() ?>
                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                        <input type="hidden" name="qty" id="qtyInput" value="1">
                        <button type="submit" class="btn-add" style="width: 100%;">🛒 Tambah ke Keranjang</button>
                    </form>
                <?php endif; ?>

                <?php if (session()->get('id')): ?>
                    <button id="wishlist-btn" onclick="toggleWishlist(<?= $product['id'] ?>)" class="btn-wishlist" style="flex: 0 0 50px; background: white; border: 2px solid var(--primary); color: var(--primary); padding: 0; border-radius: 8px; cursor: pointer; font-size: 20px; transition: all 0.3s; display: flex; align-items: center; justify-content: center;">
                        ❤️
                    </button>
                <?php endif; ?>
            </div>

            <!-- RATING INTERAKTIF -->
            <div class="rating-section">
                <div class="rating-section-title">Beri Rating Produk Ini</div>
                <div class="rating" id="ratingBox" data-product="<?= $product['id'] ?>">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <span class="star" data-value="<?= $i ?>">★</span>
                    <?php endfor; ?>
                </div>
                <div class="rating-note">Klik bintang untuk memberi penilaian</div>
            </div>

        </div>
    </div>

</div>

<!-- ================================================================
     ULASAN PEMBELI
     ================================================================ -->
<section style="padding: 48px 0; background: var(--white);">
    <div class="container" style="max-width: 860px;">

        <h2 style="font-family:'Playfair Display',serif; font-size:22px; color:var(--brown-dark); margin-bottom:6px;">
            💬 Ulasan Pembeli
        </h2>
        <p style="font-size:13px; color:var(--gray); margin-bottom:28px;">
            <?= count($reviews ?? []) ?> ulasan untuk produk ini
        </p>

        <!-- Form tulis ulasan -->
        <?php if ($canReview ?? false): ?>
        <div style="background:var(--beige); border-radius:var(--radius); padding:20px; margin-bottom:28px; border-left:4px solid var(--red);">
            <div style="font-size:14px; font-weight:600; color:var(--brown-dark); margin-bottom:14px;">✍️ Tulis Ulasan Anda</div>

            <!-- Star picker -->
            <div style="margin-bottom:4px; font-size:12px; color:var(--gray); font-weight:600;">Rating Produk</div>
            <div class="review-stars" id="starBox<?= $product['id'] ?>" data-selected="0" style="margin-bottom:4px;">
                <?php for ($i = 1; $i <= 5; $i++): ?>
                <span class="star" data-value="<?= $i ?>"
                      onclick="selectStar(<?= $product['id'] ?>, <?= $i ?>)"
                      onmouseover="hoverStar(<?= $product['id'] ?>, <?= $i ?>)"
                      onmouseout="resetHover(<?= $product['id'] ?>)">★</span>
                <?php endfor; ?>
            </div>
            <div id="starLabel<?= $product['id'] ?>" style="font-size:12px; color:var(--gray); margin-bottom:12px; height:16px;"></div>

            <textarea id="review<?= $product['id'] ?>" rows="3" placeholder="Bagaimana pengalaman Anda dengan produk ini?"
                style="width:100%; padding:12px 14px; border-radius:8px; border:1.5px solid var(--border); font-size:14px; font-family:'Poppins',sans-serif; color:var(--brown-dark); resize:vertical; box-sizing:border-box; background:var(--white);"></textarea>
            <button onclick="submitReview(<?= $product['id'] ?>)"
                style="margin-top:10px; background:var(--red); color:white; border:none; padding:10px 24px; border-radius:8px; font-size:14px; font-weight:600; cursor:pointer; font-family:'Poppins',sans-serif;">
                Kirim Ulasan
            </button>
            <span id="reviewMsg<?= $product['id'] ?>" style="display:block; margin-top:6px; font-size:13px; color:var(--gray);"></span>
        </div>
        <?php elseif (session()->get('id') && ($hasReviewed ?? false)): ?>
        <div style="background:#F0FDF4; border-radius:var(--radius); padding:14px 18px; margin-bottom:28px; border-left:4px solid #10B981; font-size:14px; color:#065F46;">
            ✓ Anda sudah memberikan ulasan untuk produk ini.
        </div>
        <?php elseif (session()->get('id')): ?>
        <div style="background:var(--gray-light); border-radius:var(--radius); padding:14px 18px; margin-bottom:28px; font-size:14px; color:var(--gray); border-left:4px solid var(--border);">
            💡 Beli produk ini terlebih dahulu untuk bisa memberikan ulasan.
        </div>
        <?php else: ?>
        <div style="background:var(--gray-light); border-radius:var(--radius); padding:14px 18px; margin-bottom:28px; font-size:14px; color:var(--gray); border-left:4px solid var(--border);">
            <a href="<?= base_url('login') ?>" style="color:var(--red); font-weight:600;">Login</a> dan beli produk ini untuk memberikan ulasan.
        </div>
        <?php endif; ?>

        <!-- Daftar ulasan -->
        <?php if (empty($reviews ?? [])): ?>
            <div style="text-align:center; padding:40px 0; color:var(--gray);">
                <div style="font-size:40px; margin-bottom:10px;">📝</div>
                <p>Belum ada ulasan. Jadilah yang pertama!</p>
            </div>
        <?php else: ?>
            <div style="display:flex; flex-direction:column; gap:16px;">
                <?php foreach ($reviews as $rv): ?>
                <div style="background:var(--gray-light); border-radius:var(--radius); padding:18px 20px; border:1px solid var(--border);">
                    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:10px; flex-wrap:wrap; gap:8px;">
                        <div style="display:flex; align-items:center; gap:10px;">
                            <div style="width:36px; height:36px; background:var(--red); border-radius:50%; display:flex; align-items:center; justify-content:center; color:white; font-weight:700; font-size:15px; flex-shrink:0;">
                                <?= strtoupper(substr($rv['user_name'] ?? 'U', 0, 1)) ?>
                            </div>
                            <div>
                                <div style="font-weight:600; font-size:14px; color:var(--brown-dark);"><?= htmlspecialchars($rv['user_name'] ?? 'Pengguna') ?></div>
                                <div style="font-size:11px; color:var(--gray);">Pembeli Terverifikasi ✓</div>
                            </div>
                        </div>
                        <div style="text-align:right;">
                            <?php if (!empty($rv['rating']) && $rv['rating'] > 0): ?>
                            <div style="color:var(--gold); font-size:15px; letter-spacing:1px; margin-bottom:2px;">
                                <?php for ($s = 1; $s <= 5; $s++) echo $s <= (int)$rv['rating'] ? '★' : '☆'; ?>
                            </div>
                            <?php endif; ?>
                            <div style="font-size:12px; color:var(--gray);"><?= date('d M Y', strtotime($rv['created_at'])) ?></div>
                        </div>
                    </div>
                    <p style="font-size:14px; color:var(--brown); line-height:1.7; margin:0;"><?= nl2br(htmlspecialchars($rv['review'])) ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>
</section>

<!-- ================================================================
     PRODUK SERUPA (Content-Based Filtering)
     ================================================================ -->
<?php if (!empty($similarProducts)): ?>
    <section style="background: var(--beige); padding: 50px 0; margin-top: 40px;">
        <div class="container">

            <div class="section-header" style="text-align:left; margin-bottom:28px;">
                <div class="section-label">Content-Based Filtering</div>
                <h2 class="section-title" style="text-align:left; font-size:24px;">Produk Serupa</h2>
            </div>

            <div class="product-grid" style="grid-template-columns: repeat(4, 1fr);">
                <?php foreach ($similarProducts as $i => $p): ?>
                    <div class="product-card" style="animation-delay:<?= $i * 0.08 ?>s;">

                        <div class="img-wrapper">
                            <img src="<?= base_url('uploads/' . $p['image']) ?>"
                                alt="<?= htmlspecialchars($p['name']) ?>">
                            <div class="badge-group">
                                <?php if (($p['sold'] ?? 0) > 50): ?>
                                    <span class="badge">🔥 Laris</span>
                                <?php else: ?>
                                    <span class="badge" style="background:var(--gold);">⭐ Serupa</span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="product-info">
                            <?php if (($p['rating'] ?? 0) > 0): ?>
                                <div class="product-stars">
                                    <?php for ($s = 1; $s <= 5; $s++) echo $s <= round($p['rating']) ? '★' : '☆'; ?>
                                </div>
                            <?php endif; ?>

                            <div class="product-name"><?= htmlspecialchars($p['name']) ?></div>
                            <div class="product-price">Rp <?= number_format($p['price'], 0, ',', '.') ?></div>

                            <a href="<?= base_url('product/' . $p['id']) ?>" class="btn-detail">
                                Lihat Detail
                            </a>
                        </div>

                    </div>
                <?php endforeach; ?>
            </div>

        </div>
    </section>
<?php endif; ?>

<script>
    function plusQty() {
        let qty = document.getElementById('qty');
        qty.value = parseInt(qty.value) + 1;
        document.getElementById('qtyInput').value = qty.value;
    }

    function minusQty() {
        let qty = document.getElementById('qty');
        if (parseInt(qty.value) > 1) {
            qty.value = parseInt(qty.value) - 1;
            document.getElementById('qtyInput').value = qty.value;
        }
    }

    // ── Rating interaktif ──────────────────────────────────────────────
    const stars = document.querySelectorAll('#ratingBox .star');
    let selectedRating = <?= round($product['rating'] ?? 0) ?>;

    function highlight(n) {
        stars.forEach((s, i) => s.classList.toggle('active', i < n));
    }

    highlight(selectedRating);

    stars.forEach((star, index) => {
        star.addEventListener('mouseover', () => highlight(index + 1));
        star.addEventListener('mouseout', () => highlight(selectedRating));
        star.addEventListener('click', () => {
            selectedRating = index + 1;
            fetch('<?= base_url('rate') ?>', {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `product_id=<?= $product['id'] ?>&rating=${selectedRating}`
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        highlight(selectedRating);
                        showToast('Rating berhasil disimpan!');
                    }
                });
        });
    });

    // ── Wishlist toggle ────────────────────────────────────────────────
    function toggleWishlist(productId) {
        const btn = document.getElementById('wishlist-btn');
        
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
                        btn.style.background = 'var(--primary)';
                        btn.style.color = 'white';
                        showToast('✓ Ditambahkan ke wishlist', 'success');
                    } else {
                        btn.style.background = 'white';
                        btn.style.color = 'var(--primary)';
                        showToast('✓ Dihapus dari wishlist', 'success');
                    }
                }
            });
        });
    }

    // Check wishlist status on load
    <?php if (session()->get('id')): ?>
    document.addEventListener('DOMContentLoaded', function() {
        const btn = document.getElementById('wishlist-btn');
        if (btn) {
            fetch('<?= base_url('/wishlist/check') ?>/<?= $product['id'] ?>', {
                method: 'GET',
                headers: {'X-Requested-With': 'XMLHttpRequest'}
            })
            .then(r => r.json())
            .then(data => {
                if (data.inWishlist) {
                    btn.style.background = 'var(--primary)';
                    btn.style.color = 'white';
                }
            });
        }
    });
    <?php endif; ?>

    function submitReview(productId) {
        var text = document.getElementById('reviewText').value.trim();
        var msg  = document.getElementById('reviewMsg');
        if (!text) { msg.textContent = 'Tulis ulasan terlebih dahulu.'; return; }

        msg.textContent = '⏳ Mengirim...';
        var form = new FormData();
        form.append('product_id', productId);
        form.append('review', text);

        fetch('<?= base_url('review') ?>', { method: 'POST', body: form })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    showToast('✓ Ulasan berhasil dikirim!', 'success');
                    setTimeout(() => location.reload(), 1200);
                } else {
                    msg.textContent = data.message || 'Gagal mengirim ulasan.';
                }
            })
            .catch(() => { msg.textContent = 'Terjadi kesalahan.'; });
    }

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