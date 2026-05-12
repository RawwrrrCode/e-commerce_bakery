<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="container" style="padding: 60px 0;">
    <!-- HEADER -->
    <div style="margin-bottom: 40px;">
        <h1 style="font-size: 32px; font-weight: 600; margin-bottom: 8px;">❤️ Wishlist Saya</h1>
        <p style="color: var(--gray-600); font-size: 16px;">
            <?php if ($totalItems > 0): ?>
                <?= $totalItems ?> produk disimpan dalam wishlist Anda
            <?php else: ?>
                Wishlist Anda kosong
            <?php endif; ?>
        </p>
    </div>

    <?php if ($totalItems > 0): ?>
        <!-- WISHLIST ITEMS -->
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 24px; margin-bottom: 40px;">
            <?php foreach ($items as $item): ?>
                <div class="wishlist-card" data-product-id="<?= $item['product_id'] ?>" data-wishlist-id="<?= $item['id'] ?>" style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); transition: all 0.3s ease;">
                    <div style="position: relative; width: 100%; height: 200px; overflow: hidden; background: #f5f5f5;">
                        <img src="<?= base_url('uploads/' . $item['image']) ?>" alt="<?= $item['name'] ?>" style="width: 100%; height: 100%; object-fit: cover;">
                        
                        <!-- Remove button -->
                        <button class="wishlist-remove-btn" onclick="removeFromWishlist(<?= $item['product_id'] ?>, this)" style="position: absolute; top: 8px; right: 8px; background: #ff4757; color: white; border: none; width: 32px; height: 32px; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 16px; transition: all 0.3s; font-weight: bold;">
                            ✕
                        </button>
                    </div>

                    <div style="padding: 16px;">
                        <h3 style="font-size: 14px; font-weight: 700; margin-bottom: 8px; line-height: 1.4;">
                            <a href="/product/<?= $item['product_id'] ?>" style="color: var(--dark); text-decoration: none;">
                                <?= $item['name'] ?>
                            </a>
                        </h3>

                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; font-size: 12px;">
                            <span style="color: var(--gray-600); background: #f0f0f0; padding: 4px 8px; border-radius: 4px;"><?= $item['category'] ?></span>
                            <span style="color: #f39c12;">⭐ <?= round($item['rating'] ?? 0, 1) ?></span>
                        </div>

                        <div style="font-size: 18px; font-weight: 700; color: var(--red); margin-bottom: 14px;">
                            Rp <?= number_format($item['price'], 0, ',', '.') ?>
                        </div>

                        <!-- BUTTONS -->
                        <div style="display: flex; flex-direction: column; gap: 8px;">
                            <button onclick="addToWishlist(<?= $item['id'] ?>, <?= $item['product_id'] ?>, this)" style="width: 100%; padding: 12px; background: var(--red); color: white; border: none; border-radius: 6px; font-size: 13px; font-weight: 600; cursor: pointer; transition: background 0.3s; hover: background: #d63447;">
                                🛒 Masukan Keranjang
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- ACTIONS -->
        <div style="display: flex; gap: 12px; justify-content: center;">
            <a href="/" class="btn-primary" style="padding: 12px 24px; text-decoration: none;">Lanjut Belanja</a>
            <a href="/cart" class="btn-secondary" style="padding: 12px 24px; text-decoration: none;">Lihat Keranjang</a>
        </div>

    <?php else: ?>
        <!-- EMPTY STATE -->
        <div style="text-align: center; padding: 60px 20px;">
            <div style="font-size: 72px; margin-bottom: 20px;">💔</div>
            <h2 style="font-size: 24px; font-weight: 600; margin-bottom: 12px;">Wishlist Kosong</h2>
            <p style="color: var(--gray-600); font-size: 16px; margin-bottom: 30px;">
                Tambahkan produk favorit Anda dengan klik tombol hati di halaman produk.
            </p>
            <a href="/" class="btn-primary" style="padding: 12px 24px; text-decoration: none; display: inline-block;">
                Mulai Belanja
            </a>
        </div>
    <?php endif; ?>
</div>

<script>
    // Add wishlist item to cart (AJAX - langsung masuk ke cart & hapus dari wishlist)
    function addToWishlist(wishlistId, productId, button) {
        button.disabled = true;
        button.style.opacity = '0.6';
        button.textContent = '⏳ Menambahkan...';

        fetch('<?= base_url('/wishlist/move-to-cart') ?>/' + wishlistId, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const card = button.closest('.wishlist-card');
                card.style.animation = 'fadeOut 0.3s ease-out';
                setTimeout(() => {
                    card.remove();
                    showToast('✓ Produk masuk ke keranjang', 'success');
                    // Reload jika wishlist kosong
                    if (document.querySelectorAll('.wishlist-card').length === 0) {
                        setTimeout(() => location.reload(), 1500);
                    }
                }, 300);
            } else {
                showToast(data.error || 'Gagal menambahkan ke keranjang', 'error');
                button.disabled = false;
                button.style.opacity = '1';
                button.textContent = '🛒 Masukan Keranjang';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Terjadi kesalahan', 'error');
            button.disabled = false;
            button.style.opacity = '1';
            button.textContent = '🛒 Masukan Keranjang';
        });
    }

    // Remove from wishlist
    function removeFromWishlist(productId, button) {
        if (!confirm('Hapus dari wishlist?')) return;

        fetch('<?= base_url('/wishlist/remove') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: new URLSearchParams({
                product_id: productId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const card = button.closest('.wishlist-card');
                card.style.animation = 'fadeOut 0.3s ease-out';
                setTimeout(() => {
                    card.remove();
                    showToast('✓ Produk dihapus dari wishlist', 'success');
                    // Reload page jika wishlist kosong
                    if (document.querySelectorAll('.wishlist-card').length === 0) {
                        setTimeout(() => location.reload(), 1000);
                    }
                }, 300);
            }
        })
        .catch(error => console.error('Error:', error));
    }

    // Toast notification
    function showToast(message, type = 'info') {
        const toast = document.createElement('div');
        const bgColor = type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : '#3b82f6';
        toast.style.cssText = `
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: ${bgColor};
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

    @keyframes fadeOut {
        from {
            opacity: 1;
        }
        to {
            opacity: 0;
        }
    }

    @keyframes slideUp {
        from {
            transform: translateY(20 px);opacity: 0;
        }
        to {
            transform: translateY(0);opacity: 1;
        }
    }
</script>

<?= $this->endSection() ?>