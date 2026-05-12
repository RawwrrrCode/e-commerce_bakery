<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="container" style="padding-top:40px; padding-bottom:60px;">

    <h2 class="page-title">🧾 Konfirmasi Pesanan</h2>

    <div class="cart-layout">

        <!-- DAFTAR ITEM -->
        <div class="cart-items-list">
            <?php foreach ($cart as $c):
                $subtotal = $c['price'] * $c['qty'];
            ?>
            <div class="cart-item">
                <img src="<?= base_url('uploads/' . ($c['image'] ?? '')) ?>"
                     class="cart-img"
                     onerror="this.src='https://images.unsplash.com/photo-1509440159596-0249088772ff?w=100&h=100&fit=crop'"
                     alt="<?= htmlspecialchars($c['name']) ?>">
                <div class="cart-info">
                    <div class="name"><?= htmlspecialchars($c['name']) ?></div>
                    <div class="price">Rp <?= number_format($c['price'], 0, ',', '.') ?> / pcs</div>
                    <div class="subtotal">
                        Qty: <?= $c['qty'] ?> pcs &nbsp;·&nbsp;
                        Subtotal: <strong>Rp <?= number_format($subtotal, 0, ',', '.') ?></strong>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- RINGKASAN -->
        <div class="cart-summary">
            <h3>Ringkasan Pembayaran</h3>

            <div class="summary-row">
                <span>Subtotal (<?= count($cart) ?> item)</span>
                <span>Rp <?= number_format($total, 0, ',', '.') ?></span>
            </div>
            <div class="summary-row">
                <span>Ongkos Kirim</span>
                <span style="color:var(--gold); font-weight:600;">Gratis</span>
            </div>
            <div class="summary-row total">
                <span>Total Pembayaran</span>
                <span>Rp <?= number_format($total, 0, ',', '.') ?></span>
            </div>

            <form method="post" action="<?= base_url('checkout') ?>">
                <?= csrf_field() ?>
                <button type="submit" class="btn-checkout"
                        style="border:none; cursor:pointer; width:100%; font-size:15px;">
                    💳 Lanjut ke Pembayaran →
                </button>
            </form>

            <a href="<?= base_url('cart') ?>"
               style="display:block; text-align:center; margin-top:14px; font-size:14px; color:var(--gray);">
                ← Kembali ke Keranjang
            </a>
        </div>

    </div>

</div>

<?= $this->endSection() ?>
