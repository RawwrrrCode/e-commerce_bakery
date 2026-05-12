<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #<?= str_pad($order->id, 4, '0', STR_PAD_LEFT) ?> — Toko Roti</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            background: #f4f4f4;
            font-family: 'Poppins', sans-serif;
            font-size: 13px;
            color: #333;
        }

        /* ── Toolbar (hanya di layar) ───────────── */
        .toolbar {
            background: #3C2A1E;
            color: white;
            padding: 14px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .toolbar a { color: rgba(255,255,255,.7); font-size: 13px; text-decoration: none; }
        .toolbar a:hover { color: white; }
        .btn-print {
            background: #C1121F;
            color: white;
            border: none;
            padding: 10px 28px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            font-family: 'Poppins', sans-serif;
        }

        /* ── Invoice paper ──────────────────────── */
        .invoice-wrap {
            max-width: 720px;
            margin: 32px auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(0,0,0,.10);
            overflow: hidden;
        }

        /* Header merah */
        .inv-header {
            background: #C1121F;
            color: white;
            padding: 32px 40px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        .inv-logo-name {
            font-family: 'Playfair Display', serif;
            font-size: 24px;
            font-weight: 700;
        }
        .inv-logo-sub { font-size: 11px; opacity: .75; margin-top: 2px; letter-spacing: 1px; text-transform: uppercase; }
        .inv-number { text-align: right; }
        .inv-number .label { font-size: 11px; opacity: .75; text-transform: uppercase; letter-spacing: 1px; }
        .inv-number .value { font-family: 'Playfair Display', serif; font-size: 22px; font-weight: 700; margin-top: 2px; }

        /* Meta info */
        .inv-meta {
            padding: 28px 40px;
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 24px;
            border-bottom: 1px solid #F0E8E0;
        }
        .meta-label { font-size: 10px; font-weight: 700; color: #999; text-transform: uppercase; letter-spacing: .8px; margin-bottom: 5px; }
        .meta-value { font-size: 13px; color: #333; font-weight: 500; line-height: 1.6; }

        /* Items table */
        .inv-body { padding: 0 40px 32px; }
        .inv-body h3 { font-family: 'Playfair Display', serif; font-size: 16px; color: #3C2A1E; margin-bottom: 16px; padding-top: 24px; }

        table { width: 100%; border-collapse: collapse; }
        thead tr { background: #FFF8F0; }
        th { padding: 11px 14px; text-align: left; font-size: 11px; font-weight: 700; color: #888; text-transform: uppercase; letter-spacing: .6px; border-bottom: 2px solid #E8D5C4; }
        th:last-child, td:last-child { text-align: right; }
        td { padding: 13px 14px; border-bottom: 1px solid #F5EDE8; color: #444; vertical-align: middle; }
        td .prod-name { font-weight: 600; color: #3C2A1E; margin-bottom: 2px; }
        td .prod-cat  { font-size: 11px; color: #aaa; }

        /* Total box */
        .inv-total {
            margin: 0 40px 32px;
            background: #FFF8F0;
            border-radius: 10px;
            padding: 20px 24px;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 8px;
            border: 1px solid #E8D5C4;
        }
        .total-row { display: flex; gap: 80px; font-size: 13px; color: #666; }
        .total-row span:last-child { font-weight: 600; color: #333; min-width: 120px; text-align: right; }
        .total-final { display: flex; gap: 80px; font-size: 16px; font-weight: 700; color: #C1121F; border-top: 1px solid #E8D5C4; padding-top: 12px; margin-top: 4px; }
        .total-final span:last-child { min-width: 120px; text-align: right; }

        /* Status badges */
        .badge-paid    { background:#D1FAE5; color:#065F46; padding:4px 12px; border-radius:20px; font-size:11px; font-weight:700; }
        .badge-unpaid  { background:#FEF3C7; color:#92400E; padding:4px 12px; border-radius:20px; font-size:11px; font-weight:700; }
        .badge-failed  { background:#FEE2E2; color:#991B1B; padding:4px 12px; border-radius:20px; font-size:11px; font-weight:700; }

        /* Footer strip */
        .inv-footer {
            background: #3C2A1E;
            color: rgba(255,255,255,.6);
            text-align: center;
            padding: 16px;
            font-size: 11px;
        }
        .inv-footer span { color: white; }

        /* ── Print styles ────────────────────────── */
        @media print {
            body { background: white; }
            .toolbar { display: none; }
            .invoice-wrap { margin: 0; box-shadow: none; border-radius: 0; }
            @page { margin: 0; size: A4; }
        }
    </style>
</head>
<body>

    <!-- TOOLBAR (screen only) -->
    <div class="toolbar">
        <a href="<?= base_url('orders/' . $order->id) ?>">← Kembali ke Detail Order</a>
        <button class="btn-print" onclick="window.print()">🖨️ Cetak Invoice</button>
    </div>

    <!-- INVOICE PAPER -->
    <div class="invoice-wrap">

        <!-- HEADER -->
        <div class="inv-header">
            <div>
                <div class="inv-logo-name">🍞 Toko Roti</div>
                <div class="inv-logo-sub">PT. Mimosa Tarte Indonesia</div>
                <div style="font-size:11px; opacity:.7; margin-top:8px; line-height:1.6;">
                    Jl. Raya Bakery No. 1, Jakarta Selatan<br>
                    hello@mimosatarte.id
                </div>
            </div>
            <div class="inv-number">
                <div class="label">Invoice</div>
                <div class="value">#<?= str_pad($order->id, 4, '0', STR_PAD_LEFT) ?></div>
                <div style="font-size:11px; opacity:.75; margin-top:6px;">
                    <?= isset($order->created_at) ? date('d M Y', strtotime($order->created_at)) : date('d M Y') ?>
                </div>
            </div>
        </div>

        <!-- META -->
        <div class="inv-meta">
            <div>
                <div class="meta-label">Ditagih kepada</div>
                <div class="meta-value">
                    <?= htmlspecialchars($user->name ?? 'Pelanggan') ?><br>
                    <span style="color:#888; font-size:12px;"><?= htmlspecialchars($user->email ?? '') ?></span>
                    <?php if (!empty($user->phone)): ?>
                        <br><span style="color:#888; font-size:12px;"><?= htmlspecialchars($user->phone) ?></span>
                    <?php endif; ?>
                    <?php if (!empty($user->address)): ?>
                        <br><span style="color:#888; font-size:12px;"><?= htmlspecialchars($user->address) ?></span>
                    <?php endif; ?>
                </div>
            </div>
            <div>
                <div class="meta-label">Status Pembayaran</div>
                <div class="meta-value" style="margin-top:4px;">
                    <?php $ps = $order->payment_status ?? 'unpaid'; ?>
                    <?php if ($ps === 'paid'): ?>
                        <span class="badge-paid">✓ Lunas</span>
                    <?php elseif ($ps === 'failed' || $ps === 'expired'): ?>
                        <span class="badge-failed">✗ <?= ucfirst($ps) ?></span>
                    <?php else: ?>
                        <span class="badge-unpaid">⏳ Belum Dibayar</span>
                    <?php endif; ?>
                </div>
            </div>
            <div>
                <div class="meta-label">Status Pengiriman</div>
                <div class="meta-value" style="margin-top:4px;">
                    <?php
                    $sLabel = ['pending'=>'Pending','processing'=>'Diproses','shipped'=>'Dikirim','selesai'=>'Selesai'];
                    echo $sLabel[$order->status] ?? $order->status;
                    ?>
                </div>
            </div>
        </div>

        <!-- ITEMS -->
        <div class="inv-body">
            <h3>Daftar Produk</h3>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Produk</th>
                        <th style="text-align:center;">Qty</th>
                        <th>Harga Satuan</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $i => $item): ?>
                    <tr>
                        <td style="color:#bbb;"><?= $i + 1 ?></td>
                        <td>
                            <div class="prod-name"><?= htmlspecialchars($item['name']) ?></div>
                            <div class="prod-cat"><?= htmlspecialchars($item['category'] ?? '') ?></div>
                        </td>
                        <td style="text-align:center;"><?= $item['qty'] ?></td>
                        <td>Rp <?= number_format($item['price'], 0, ',', '.') ?></td>
                        <td style="font-weight:600;">Rp <?= number_format($item['price'] * $item['qty'], 0, ',', '.') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- TOTAL -->
        <div class="inv-total">
            <?php
            $subtotal = array_sum(array_map(fn($i) => $i['price'] * $i['qty'], $items));
            ?>
            <div class="total-row">
                <span>Subtotal</span>
                <span>Rp <?= number_format($subtotal, 0, ',', '.') ?></span>
            </div>
            <div class="total-row">
                <span>Biaya Pengiriman</span>
                <span>–</span>
            </div>
            <div class="total-final">
                <span>Total</span>
                <span>Rp <?= number_format($order->total, 0, ',', '.') ?></span>
            </div>
        </div>

        <!-- FOOTER -->
        <div class="inv-footer">
            Terima kasih telah berbelanja di <span>Toko Roti</span> · PT. Mimosa Tarte Indonesia · Fresh Every Day 🍞
        </div>

    </div>

</body>
</html>
