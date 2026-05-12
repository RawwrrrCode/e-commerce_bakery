<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="container" style="padding-top: 40px; padding-bottom: 60px;">

    <h2 class="page-title">🛒 Keranjang Belanja</h2>

    <?php if(empty($cart)): ?>

    <div class="cart-empty">
        <div class="cart-empty-icon">🛒</div>
        <h3>Keranjang Masih Kosong</h3>
        <p>Yuk, mulai pilih produk roti & kue favorit Anda!</p>
        <a href="<?= base_url() ?>" style="display:inline-block; background:var(--red); color:white; padding:12px 28px; border-radius:10px; font-weight:600; transition:0.2s;">
            Mulai Belanja
        </a>
    </div>

    <?php else: ?>

    <?php $total = 0; ?>

    <div class="cart-layout">

        <!-- DAFTAR ITEM -->
        <div class="cart-items-list">

            <?php foreach($cart as $c):
                $subtotal = $c['price'] * $c['qty'];
                $total += $subtotal;
            ?>

            <div class="cart-item" id="item<?= $c['id'] ?>">

                <img src="<?= base_url('uploads/' . ($c['image'] ?? '')) ?>"
                     class="cart-img"
                     onerror="this.src='https://images.unsplash.com/photo-1509440159596-0249088772ff?w=100&h=100&fit=crop'"
                     alt="<?= htmlspecialchars($c['name']) ?>">

                <div class="cart-info">
                    <div class="name"><?= htmlspecialchars($c['name']) ?></div>
                    <div class="price">Rp <?= number_format($c['price'], 0, ',', '.') ?> / pcs</div>
                    <div class="subtotal">
                        Subtotal: <span id="subtotal<?= $c['id'] ?>">Rp <?= number_format($subtotal, 0, ',', '.') ?></span>
                    </div>
                </div>

                <div class="cart-action">
                    <div class="qty-box-small">
                        <button onclick="minus(<?= $c['id'] ?>, <?= $c['price'] ?>)">−</button>
                        <input type="text" id="qty<?= $c['id'] ?>" value="<?= $c['qty'] ?>"
                               onkeyup="validateQty(this)">
                        <button onclick="plus(<?= $c['id'] ?>, <?= $c['price'] ?>)">+</button>
                    </div>

                    <div class="delete" onclick="deleteItem(<?= $c['id'] ?>)">
                        🗑 Hapus
                    </div>
                </div>

            </div>

            <?php endforeach; ?>

        </div>

        <!-- RINGKASAN -->
        <div class="cart-summary">
            <h3>Ringkasan Pesanan</h3>

            <div class="summary-row">
                <span>Subtotal (<?= count($cart) ?> item)</span>
                <span id="totalHarga">Rp <?= number_format($total, 0, ',', '.') ?></span>
            </div>
            <div class="summary-row">
                <span>Ongkos Kirim</span>
                <span style="color:var(--gold); font-weight:600;">Gratis</span>
            </div>

            <div class="summary-row total">
                <span>Total Pembayaran</span>
                <span id="totalHargaBottom">Rp <?= number_format($total, 0, ',', '.') ?></span>
            </div>

            <a href="<?= base_url('checkout') ?>" class="btn-checkout">
                Lanjut ke Checkout →
            </a>

            <a href="<?= base_url() ?>" style="display:block; text-align:center; margin-top:14px; font-size:14px; color:var(--gray);">
                ← Lanjut Belanja
            </a>
        </div>

    </div>

    <?php endif; ?>

</div>

<?= $this->endSection() ?>
