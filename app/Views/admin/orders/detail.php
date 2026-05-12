<?= $this->extend('layout/admin') ?>
<?= $this->section('content') ?>

<?php
$statusLabel = ['pending'=>'📋 Pending','processing'=>'🔄 Diproses','shipped'=>'🚚 Dikirim','selesai'=>'✓ Selesai'];
$statusColor = ['pending'=>'#E67E22','processing'=>'#3498db','shipped'=>'#8B5CF6','selesai'=>'#059669'];
$payLabel    = ['unpaid'=>'⏳ Belum Dibayar','paid'=>'✅ Lunas','failed'=>'❌ Gagal','expired'=>'⌛ Expired'];
$payColor    = ['unpaid'=>'#E67E22','paid'=>'#059669','failed'=>'#DC2626','expired'=>'#9CA3AF'];
$st  = $order->status;
$pay = $order->payment_status ?? 'unpaid';
$subtotal = array_sum(array_map(fn($i) => $i['price'] * $i['qty'], $items));
?>

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; flex-wrap:wrap; gap:12px;">
    <div>
        <h1 style="font-family:'Playfair Display',serif; font-size:24px; color:#3C2A1E; margin:0 0 4px;">
            Detail Pesanan #<?= str_pad($order->id, 4, '0', STR_PAD_LEFT) ?>
        </h1>
        <p style="color:#888; font-size:13px; margin:0;">
            <?= isset($order->created_at) ? date('d M Y, H:i', strtotime($order->created_at)) : '' ?>
        </p>
    </div>
    <div style="display:flex; gap:10px; align-items:center;">
        <span style="font-size:13px; font-weight:700; color:<?= $statusColor[$st] ?? '#333' ?>; background:<?= $statusColor[$st] ?? '#333' ?>15; padding:6px 16px; border-radius:20px;">
            <?= $statusLabel[$st] ?? $st ?>
        </span>
        <span style="font-size:13px; font-weight:700; color:<?= $payColor[$pay] ?? '#888' ?>; background:<?= $payColor[$pay] ?? '#888' ?>15; padding:6px 16px; border-radius:20px;">
            <?= $payLabel[$pay] ?? $pay ?>
        </span>
        <a href="/admin/orders" style="background:#3C2A1E; color:white; padding:8px 18px; border-radius:8px; font-size:13px; font-weight:600; text-decoration:none;">
            ← Kembali
        </a>
    </div>
</div>

<div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:24px;">

    <!-- INFO PEMBELI -->
    <div class="admin-box">
        <h3 style="font-size:15px; font-weight:700; color:#3C2A1E; margin-bottom:16px; padding-bottom:10px; border-bottom:1px solid #F0E8E0;">
            👤 Informasi Pembeli
        </h3>
        <table style="width:100%; font-size:13px; border-collapse:collapse;">
            <tr>
                <td style="padding:7px 0; color:#888; width:110px;">Nama</td>
                <td style="padding:7px 0; font-weight:600; color:#3C2A1E;"><?= htmlspecialchars($user['name'] ?? '–') ?></td>
            </tr>
            <tr>
                <td style="padding:7px 0; color:#888;">Email</td>
                <td style="padding:7px 0; color:#555;"><?= htmlspecialchars($user['email'] ?? '–') ?></td>
            </tr>
            <tr>
                <td style="padding:7px 0; color:#888;">No. HP</td>
                <td style="padding:7px 0; color:#555;">
                    <?php if (!empty($user['phone'])): ?>
                        <a href="https://wa.me/<?= preg_replace('/\D/', '', $user['phone']) ?>" target="_blank"
                           style="color:#25D366; font-weight:600; text-decoration:none;">
                            📱 <?= htmlspecialchars($user['phone']) ?>
                        </a>
                    <?php else: ?>
                        <span style="color:#bbb;">–</span>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <td style="padding:7px 0; color:#888; vertical-align:top;">Alamat</td>
                <td style="padding:7px 0; color:#555; line-height:1.6;">
                    <?= !empty($user['address']) ? nl2br(htmlspecialchars($user['address'])) : '<span style="color:#bbb;">–</span>' ?>
                </td>
            </tr>
            <tr>
                <td style="padding:7px 0; color:#888;">Member Sejak</td>
                <td style="padding:7px 0; color:#555;">
                    <?= !empty($user['created_at']) ? date('d M Y', strtotime($user['created_at'])) : '–' ?>
                </td>
            </tr>
        </table>
    </div>

    <!-- RINGKASAN PESANAN -->
    <div class="admin-box">
        <h3 style="font-size:15px; font-weight:700; color:#3C2A1E; margin-bottom:16px; padding-bottom:10px; border-bottom:1px solid #F0E8E0;">
            📋 Ringkasan Pesanan
        </h3>
        <table style="width:100%; font-size:13px; border-collapse:collapse;">
            <tr>
                <td style="padding:7px 0; color:#888; width:130px;">No. Order</td>
                <td style="padding:7px 0; font-weight:700; color:#3C2A1E;">#<?= str_pad($order->id, 4, '0', STR_PAD_LEFT) ?></td>
            </tr>
            <tr>
                <td style="padding:7px 0; color:#888;">Tanggal Order</td>
                <td style="padding:7px 0; color:#555;"><?= date('d M Y, H:i', strtotime($order->created_at)) ?></td>
            </tr>
            <tr>
                <td style="padding:7px 0; color:#888;">Status Pesanan</td>
                <td style="padding:7px 0;">
                    <span style="font-weight:700; color:<?= $statusColor[$st] ?? '#333' ?>;"><?= $statusLabel[$st] ?? $st ?></span>
                </td>
            </tr>
            <tr>
                <td style="padding:7px 0; color:#888;">Status Bayar</td>
                <td style="padding:7px 0;">
                    <span style="font-weight:700; color:<?= $payColor[$pay] ?? '#888' ?>;"><?= $payLabel[$pay] ?? $pay ?></span>
                </td>
            </tr>
            <tr>
                <td style="padding:7px 0; color:#888;">Subtotal</td>
                <td style="padding:7px 0; color:#555;">Rp <?= number_format($subtotal, 0, ',', '.') ?></td>
            </tr>
            <tr style="border-top:1px solid #F0E8E0;">
                <td style="padding:10px 0 4px; color:#3C2A1E; font-weight:700; font-size:14px;">Total</td>
                <td style="padding:10px 0 4px; font-weight:700; font-size:16px; color:#C1121F;">
                    Rp <?= number_format($order->total, 0, ',', '.') ?>
                </td>
            </tr>
        </table>

        <!-- Update status -->
        <?php if (in_array($st, ['pending', 'processing'])): ?>
        <div style="margin-top:16px; padding-top:14px; border-top:1px solid #F0E8E0;">
            <p style="font-size:12px; color:#888; margin-bottom:8px; font-weight:600; text-transform:uppercase; letter-spacing:.5px;">Update Status</p>
            <div style="display:flex; gap:8px; flex-wrap:wrap;">
                <?php if ($st === 'pending'): ?>
                <button onclick="updateStatus(<?= $order->id ?>, 'processing')"
                        style="background:#3498db; color:white; border:none; padding:9px 20px; border-radius:8px; font-size:13px; font-weight:700; cursor:pointer;">
                    🔄 Proses Pesanan
                </button>
                <?php elseif ($st === 'processing'): ?>
                <button onclick="updateStatus(<?= $order->id ?>, 'shipped')"
                        style="background:#8B5CF6; color:white; border:none; padding:9px 20px; border-radius:8px; font-size:13px; font-weight:700; cursor:pointer;">
                    🚚 Tandai Dikirim
                </button>
                <?php endif; ?>
            </div>
            <p id="statusMsg" style="display:none; font-size:12px; color:#059669; margin-top:8px; font-weight:600;"></p>
        </div>
        <?php elseif ($st === 'shipped'): ?>
        <div style="margin-top:16px; padding:10px 14px; background:#EDE9FE; border-radius:8px; font-size:12px; color:#7C3AED; font-weight:600;">
            ⏳ Menunggu konfirmasi penerimaan dari pembeli
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- DAFTAR PRODUK -->
<div class="admin-box" style="padding:0; overflow:hidden;">
    <div style="padding:18px 24px; border-bottom:1px solid #F0E8E0;">
        <h3 style="font-size:15px; font-weight:700; color:#3C2A1E; margin:0;">🛍️ Daftar Produk (<?= count($items) ?> item)</h3>
    </div>
    <table class="admin-table" style="margin:0;">
        <thead>
            <tr>
                <th style="width:60px;">Gambar</th>
                <th>Produk</th>
                <th style="text-align:center;">Qty</th>
                <th style="text-align:right;">Harga Satuan</th>
                <th style="text-align:right;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
            <tr>
                <td>
                    <img src="<?= base_url('uploads/' . ($item['image'] ?? '')) ?>"
                         style="width:52px; height:52px; object-fit:cover; border-radius:8px; background:#F5EDE8;"
                         onerror="this.style.display='none'">
                </td>
                <td>
                    <div style="font-weight:600; color:#3C2A1E; font-size:14px;"><?= htmlspecialchars($item['name']) ?></div>
                    <div style="font-size:11px; color:#aaa; margin-top:2px;"><?= htmlspecialchars($item['category'] ?? '') ?></div>
                </td>
                <td style="text-align:center; font-weight:700; font-size:15px;"><?= $item['qty'] ?></td>
                <td style="text-align:right; color:#555;">Rp <?= number_format($item['price'], 0, ',', '.') ?></td>
                <td style="text-align:right; font-weight:700; color:#C1121F;">Rp <?= number_format($item['price'] * $item['qty'], 0, ',', '.') ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr style="background:#FFF8F0;">
                <td colspan="4" style="text-align:right; padding:14px 16px; font-weight:700; color:#3C2A1E;">Total Pembayaran</td>
                <td style="text-align:right; padding:14px 16px; font-weight:700; font-size:16px; color:#C1121F;">
                    Rp <?= number_format($order->total, 0, ',', '.') ?>
                </td>
            </tr>
        </tfoot>
    </table>
</div>

<script>
function updateStatus(id, status) {
    if (!confirm('Update status pesanan ini?')) return;
    fetch('<?= base_url('admin/orders/update') ?>', {
        method: 'POST',
        credentials: 'same-origin',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'id=' + id + '&status=' + status + '&<?= csrf_token() ?>=<?= csrf_hash() ?>'
    })
    .then(r => r.json())
    .then(data => {
        if (data.status === 'ok') {
            document.getElementById('statusMsg').style.display = 'block';
            document.getElementById('statusMsg').textContent = '✓ Status berhasil diperbarui. Halaman akan dimuat ulang...';
            setTimeout(() => location.reload(), 1200);
        }
    });
}
</script>

<?= $this->endSection() ?>
