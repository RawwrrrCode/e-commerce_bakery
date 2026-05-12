<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<?php
$payStatus   = $order->payment_status ?? 'unpaid';
$status      = $order->status;
$createdAt   = strtotime($order->created_at ?? 'now');

// Hitung estimasi tiba
function estimasiTiba(string $status, int $createdAt): array {
    return match($status) {
        'pending'    => ['label' => '–',       'note' => 'Selesaikan pembayaran terlebih dahulu',          'color' => '#9CA3AF'],
        'processing' => ['label' => date('d M Y', strtotime('+3 days')),  'note' => 'Estimasi 3 hari kerja setelah diproses',  'color' => '#3498DB'],
        'shipped'    => ['label' => date('d M Y', strtotime('+1 day')) . ' – ' . date('d M Y', strtotime('+2 days')), 'note' => 'Paket sedang dalam perjalanan', 'color' => '#8E44AD'],
        'selesai'    => ['label' => 'Sudah Diterima ✓', 'note' => 'Pesanan telah selesai', 'color' => '#059669'],
        default      => ['label' => '–', 'note' => '', 'color' => '#999'],
    };
}
$eta = estimasiTiba($status, $createdAt);

// Step tracking
$steps = [
    ['key' => 'created',    'label' => 'Pesanan Dibuat',  'icon' => '📋', 'done' => true],
    ['key' => 'paid',       'label' => 'Pembayaran',      'icon' => '💳', 'done' => $payStatus === 'paid'],
    ['key' => 'processing', 'label' => 'Diproses',        'icon' => '🔄', 'done' => in_array($status, ['processing','shipped','selesai'])],
    ['key' => 'shipped',    'label' => 'Dikirim',         'icon' => '🚚', 'done' => in_array($status, ['shipped','selesai'])],
    ['key' => 'selesai',    'label' => 'Diterima',        'icon' => '✅', 'done' => $status === 'selesai'],
];
$currentStep = match(true) {
    $status === 'selesai'                       => 4,
    $status === 'shipped'                       => 3,
    $status === 'processing'                    => 2,
    $payStatus === 'paid'                       => 1,
    default                                     => 0,
};
?>

<div class="container" style="padding-top:40px; padding-bottom:60px;">

    <!-- FLASH -->
    <?php if (session()->getFlashdata('success')): ?>
    <div style="background:#D1FAE5; border:1px solid #6EE7B7; color:#065F46; padding:13px 18px; border-radius:10px; margin-bottom:20px; font-size:14px; font-weight:600;">
        <?= session()->getFlashdata('success') ?>
    </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
    <div style="background:#FEE2E2; border:1px solid #FECACA; color:#B91C1C; padding:13px 18px; border-radius:10px; margin-bottom:20px; font-size:14px;">
        <?= session()->getFlashdata('error') ?>
    </div>
    <?php endif; ?>

    <!-- HEADER -->
    <div class="order-detail-header">
        <div>
            <h2 class="page-title" style="margin-bottom:4px;">
                Detail Order #<?= str_pad($order->id, 4, '0', STR_PAD_LEFT) ?>
            </h2>
            <p style="color:var(--gray); font-size:14px;">
                <?= isset($order->created_at) ? date('d M Y, H:i', strtotime($order->created_at)) : '' ?>
            </p>
        </div>
        <div style="display:flex; flex-direction:column; align-items:flex-end; gap:8px;">
            <span class="status <?= $status ?>">
                <?php
                $statusLabel = ['pending'=>'📋 Pending','processing'=>'🔄 Diproses','shipped'=>'🚚 Dikirim','selesai'=>'✓ Selesai'];
                echo $statusLabel[$status] ?? $status;
                ?>
            </span>
            <?php
            $payLabel = ['unpaid'=>'⏳ Belum Dibayar','paid'=>'✅ Lunas','failed'=>'❌ Gagal','expired'=>'⌛ Kadaluarsa'];
            $payColor = ['unpaid'=>'#E67E22','paid'=>'#059669','failed'=>'#DC2626','expired'=>'#9CA3AF'];
            ?>
            <span style="font-size:13px; font-weight:600; color:<?= $payColor[$payStatus] ?? '#E67E22' ?>;">
                <?= $payLabel[$payStatus] ?? '⏳ Belum Dibayar' ?>
            </span>
        </div>
    </div>

    <!-- TRACKING TIMELINE -->
    <div style="background:white; border-radius:14px; box-shadow:0 2px 12px rgba(0,0,0,.07); padding:24px 28px; margin-bottom:24px;">
        <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:20px; flex-wrap:wrap; gap:12px;">
            <h3 style="font-family:'Playfair Display',serif; font-size:16px; color:var(--brown-dark); margin:0;">🗺️ Tracking Pesanan</h3>
            <?php if ($status !== 'pending' && $payStatus !== 'failed' && $payStatus !== 'expired'): ?>
            <div style="text-align:right;">
                <div style="font-size:11px; color:#999; text-transform:uppercase; letter-spacing:.8px; margin-bottom:3px;">Estimasi Tiba</div>
                <div style="font-size:15px; font-weight:700; color:<?= $eta['color'] ?>;"><?= $eta['label'] ?></div>
                <div style="font-size:11px; color:#aaa; margin-top:2px;"><?= $eta['note'] ?></div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Steps -->
        <div style="display:flex; align-items:flex-start; gap:0; position:relative;">
            <?php foreach ($steps as $si => $step): ?>
            <div style="flex:1; display:flex; flex-direction:column; align-items:center; position:relative; z-index:1;">
                <!-- Connector line (except first) -->
                <?php if ($si > 0): ?>
                <div style="position:absolute; top:18px; right:50%; width:100%; height:3px; background:<?= $steps[$si]['done'] ? '#C1121F' : '#E8D5C4' ?>; z-index:0;"></div>
                <?php endif; ?>

                <!-- Circle -->
                <div style="width:38px; height:38px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:16px; position:relative; z-index:1;
                    background:<?= $step['done'] ? ($si === $currentStep ? '#C1121F' : '#FEE2E2') : '#F5EDE8' ?>;
                    border:2px solid <?= $step['done'] ? '#C1121F' : '#E8D5C4' ?>;
                    box-shadow:<?= $si === $currentStep ? '0 0 0 4px rgba(193,18,31,0.15)' : 'none' ?>;">
                    <?= $step['icon'] ?>
                </div>

                <div style="font-size:11px; font-weight:<?= $step['done'] ? '700' : '400' ?>; color:<?= $step['done'] ? 'var(--brown-dark)' : '#bbb' ?>; text-align:center; margin-top:8px; line-height:1.3;">
                    <?= $step['label'] ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- TOMBOL BAYAR -->
    <?php if ($payStatus === 'unpaid' && $status !== 'selesai'): ?>
    <div style="background:#FFF8F0; border:1px solid #E8D5C4; border-radius:12px; padding:20px 24px; margin-bottom:24px; display:flex; justify-content:space-between; align-items:center;">
        <div>
            <div style="font-weight:600; color:var(--brown-dark); margin-bottom:4px;">Pesanan belum dibayar</div>
            <div style="font-size:13px; color:var(--gray);">Total: <strong>Rp <?= number_format($order->total, 0, ',', '.') ?></strong></div>
        </div>
        <a href="<?= base_url('orders/' . $order->id . '/pay') ?>"
           style="background:var(--red); color:white; padding:12px 24px; border-radius:10px; font-weight:700; text-decoration:none; font-size:14px;">
            💳 Bayar Sekarang
        </a>
    </div>
    <?php endif; ?>

    <!-- TOMBOL KONFIRMASI DITERIMA -->
    <?php if ($status === 'shipped' && $payStatus === 'paid'): ?>
    <div style="background:linear-gradient(135deg,#EDE9FE,#F5F3FF); border:1.5px solid #A78BFA; border-radius:14px; padding:20px 24px; margin-bottom:24px; display:flex; justify-content:space-between; align-items:center; gap:16px; flex-wrap:wrap;">
        <div>
            <div style="font-weight:700; font-size:15px; color:#5B21B6; margin-bottom:4px;">📦 Paket sudah tiba?</div>
            <div style="font-size:13px; color:#7C3AED;">Konfirmasi bahwa Anda telah menerima pesanan ini.</div>
        </div>
        <form action="<?= base_url('orders/' . $order->id . '/confirm') ?>" method="post"
              onsubmit="return confirm('Konfirmasi bahwa pesanan ini sudah Anda terima?');">
            <?= csrf_field() ?>
            <button type="submit"
                    style="background:#7C3AED; color:white; border:none; padding:12px 28px; border-radius:10px; font-size:14px; font-weight:700; cursor:pointer; white-space:nowrap;">
                ✓ Pesanan Diterima
            </button>
        </form>
    </div>
    <?php endif; ?>

    <!-- DAFTAR ITEM -->
    <h3 style="font-family:'Playfair Display',serif; font-size:20px; color:var(--brown-dark); margin-bottom:20px;">
        Item Pesanan
    </h3>

    <?php foreach ($items as $item): ?>
    <div class="order-item-card">
        <h3><?= htmlspecialchars($item->name) ?></h3>
        <p class="qty-info">Qty: <?= $item->qty ?> pcs &nbsp;·&nbsp; Rp <?= number_format($item->price ?? 0, 0, ',', '.') ?></p>

        <?php if ($payStatus === 'paid'): ?>
        <div class="review-title">Beri Rating &amp; Ulasan</div>

        <!-- Star picker (no immediate POST — hanya visual selector) -->
        <div class="review-stars" id="starBox<?= $item->product_id ?>" data-selected="0">
            <?php for ($i = 1; $i <= 5; $i++): ?>
            <span class="star"
                  data-value="<?= $i ?>"
                  onclick="selectStar(<?= $item->product_id ?>, <?= $i ?>)"
                  onmouseover="hoverStar(<?= $item->product_id ?>, <?= $i ?>)"
                  onmouseout="resetHover(<?= $item->product_id ?>)">★</span>
            <?php endfor; ?>
        </div>
        <div id="starLabel<?= $item->product_id ?>" style="font-size:12px; color:var(--gray); margin:4px 0 10px; height:16px;"></div>

        <textarea id="review<?= $item->product_id ?>" class="review-textarea"
                  placeholder="Bagikan pengalaman Anda dengan produk ini..."></textarea>
        <button onclick="submitReview(<?= $item->product_id ?>)" class="btn-review">Kirim Ulasan</button>
        <span id="reviewMsg<?= $item->product_id ?>" style="display:block; margin-top:6px; font-size:13px; color:var(--gray);"></span>
        <?php endif; ?>
    </div>
    <?php endforeach; ?>

    <div style="display:flex; align-items:center; justify-content:space-between; margin-top:10px; flex-wrap:wrap; gap:12px;">
        <a href="<?= base_url('orders') ?>" style="display:inline-flex; align-items:center; gap:6px; color:var(--red); font-weight:600; font-size:14px;">
            ← Kembali ke Pesanan
        </a>
        <div style="display:flex; gap:10px; flex-wrap:wrap;">
            <?php if ($status === 'selesai'): ?>
            <form action="<?= base_url('orders/' . $order->id . '/reorder') ?>" method="post"
                  onsubmit="return confirm('Tambahkan semua item ke keranjang?');">
                <?= csrf_field() ?>
                <button type="submit"
                        style="display:inline-flex; align-items:center; gap:8px; background:#059669; color:white; border:none; padding:10px 22px; border-radius:10px; font-weight:700; font-size:13px; cursor:pointer;">
                    🔁 Beli Lagi
                </button>
            </form>
            <?php endif; ?>
            <a href="<?= base_url('orders/' . $order->id . '/invoice') ?>"
               target="_blank"
               style="display:inline-flex; align-items:center; gap:8px; background:#3C2A1E; color:white; padding:10px 22px; border-radius:10px; font-weight:600; font-size:13px; text-decoration:none;">
                🖨️ Cetak Invoice
            </a>
        </div>
    </div>

</div>

<?= $this->endSection() ?>
