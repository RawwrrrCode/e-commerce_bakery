<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div style="background:var(--cream); min-height:calc(100vh - 140px); padding:40px 0 64px;">
<div class="container" style="max-width:900px;">

    <!-- BREADCRUMB -->
    <div style="font-size:13px; color:var(--gray); margin-bottom:20px;">
        <a href="<?= base_url() ?>" style="color:var(--gray);">Beranda</a>
        <span style="margin:0 6px;">›</span>
        <span style="color:var(--brown-dark); font-weight:500;">Pesanan Saya</span>
    </div>

    <h2 style="font-family:'Playfair Display',serif; color:var(--brown-dark); font-size:28px; margin:0 0 24px;">Pesanan Saya</h2>

    <!-- FLASH MESSAGES -->
    <?php if (session()->getFlashdata('info')): ?>
    <div style="background:#EFF6FF; border:1px solid #BFDBFE; color:#1E40AF; padding:13px 18px; border-radius:10px; margin-bottom:20px; font-size:14px;">
        ℹ️ <?= session()->getFlashdata('info') ?>
    </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
    <div style="background:#FEE2E2; border:1px solid #FECACA; color:#B91C1C; padding:13px 18px; border-radius:10px; margin-bottom:20px; font-size:14px;">
        ⚠️ <?= session()->getFlashdata('error') ?>
    </div>
    <?php endif; ?>

    <!-- TABS -->
    <div style="display:flex; border-bottom:2px solid #E8D5C4; margin-bottom:28px;">
        <a href="<?= base_url('orders?tab=ongoing') ?>"
           style="padding:14px 28px; font-size:14px; font-weight:600; text-decoration:none; border-bottom:3px solid <?= ($tab==='ongoing') ? 'var(--red)' : 'transparent' ?>; color:<?= ($tab==='ongoing') ? 'var(--red)' : 'var(--gray)' ?>; margin-bottom:-2px; transition:color 0.2s;">
            Transaksi Berlangsung
            <?php if ($ongoingCount > 0): ?>
                <span style="background:var(--red); color:white; border-radius:20px; padding:2px 8px; font-size:11px; margin-left:6px;"><?= $ongoingCount ?></span>
            <?php endif; ?>
        </a>
        <a href="<?= base_url('orders?tab=history') ?>"
           style="padding:14px 28px; font-size:14px; font-weight:600; text-decoration:none; border-bottom:3px solid <?= ($tab==='history') ? 'var(--red)' : 'transparent' ?>; color:<?= ($tab==='history') ? 'var(--red)' : 'var(--gray)' ?>; margin-bottom:-2px; transition:color 0.2s;">
            Riwayat Transaksi
        </a>
    </div>

    <style>
        @keyframes pulse-badge {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: .8; transform: scale(1.05); }
        }
    </style>

    <!-- ══════════════════════════════════════════════════════════
         TAB: TRANSAKSI BERLANGSUNG
    ══════════════════════════════════════════════════════════ -->
    <?php if ($tab === 'ongoing'): ?>

    <?php
    $unreadCount = count(array_filter($ongoingOrders, fn($o) => ($o['notification_read'] ?? 1) == 0));
    if ($unreadCount > 0):
    ?>
    <div style="background:#EFF6FF; border:1px solid #BFDBFE; border-radius:var(--radius); padding:14px 18px; margin-bottom:20px; font-size:13px; color:#1E40AF; display:flex; align-items:center; gap:10px;">
        🔔 <strong><?= $unreadCount ?> pesanan</strong> memiliki pembaruan status baru. Klik untuk melihat detail.
    </div>
    <?php endif; ?>

        <?php if (empty($ongoingOrders)): ?>
        <div style="background:white; border-radius:var(--radius-lg); box-shadow:var(--shadow); padding:60px; text-align:center;">
            <div style="font-size:48px; margin-bottom:14px;">🛍️</div>
            <h3 style="font-family:'Playfair Display',serif; color:var(--brown-dark); margin-bottom:8px;">Tidak ada transaksi berlangsung</h3>
            <p style="color:var(--gray); font-size:14px; margin-bottom:24px;">Semua pesanan Anda sudah selesai atau belum ada pesanan baru.</p>
            <a href="<?= base_url() ?>" style="display:inline-block; background:var(--red); color:white; padding:11px 28px; border-radius:8px; font-weight:600; text-decoration:none; font-size:14px;">Mulai Belanja</a>
        </div>
        <?php else: ?>

        <!-- Info Box -->
        <div style="background:#FFFBEB; border:1px solid #FDE68A; border-radius:var(--radius); padding:14px 18px; margin-bottom:20px; font-size:13px; color:#92400E;">
            <strong>Catatan:</strong>
            <ol style="margin:6px 0 0 16px; padding:0; line-height:1.8;">
                <li>Pesanan yang belum dibayar akan otomatis dibatalkan setelah 24 jam.</li>
                <li>Pastikan data pesanan sudah benar sebelum melakukan pembayaran.</li>
                <li>Status pesanan akan diperbarui setelah admin mengkonfirmasi.</li>
            </ol>
        </div>

        <div style="display:flex; flex-direction:column; gap:12px;">
        <?php
        $payColor = ['unpaid'=>'#E67E22','paid'=>'#059669','failed'=>'#DC2626','expired'=>'#9CA3AF'];
        $payLabel = ['unpaid'=>'Belum Dibayar','paid'=>'Lunas','failed'=>'Gagal','expired'=>'Kadaluarsa'];
        $statusColor = ['pending'=>'#E67E22','processing'=>'#3498DB','shipped'=>'#8E44AD','selesai'=>'#27AE60'];
        $statusLabel = ['pending'=>'Pending','processing'=>'Diproses','shipped'=>'Dikirim','selesai'=>'Selesai'];
        ?>
        <?php foreach ($ongoingOrders as $o): ?>
        <?php $ps = $o['payment_status'] ?? 'unpaid'; ?>
        <div style="background:white; border-radius:var(--radius); box-shadow:var(--shadow); padding:20px 24px; display:flex; justify-content:space-between; align-items:center; gap:16px;">

            <div style="flex:1;">
                <div style="display:flex; align-items:center; gap:10px; margin-bottom:6px;">
                    <span style="font-weight:700; font-size:15px; color:var(--brown-dark);">
                        Order #<?= str_pad($o['id'], 4, '0', STR_PAD_LEFT) ?>
                    </span>
                    <span style="background:<?= ($statusColor[$o['status']]??'#999') ?>20; color:<?= $statusColor[$o['status']]??'#999' ?>; font-size:11px; font-weight:700; padding:3px 10px; border-radius:20px;">
                        <?= $statusLabel[$o['status']] ?? $o['status'] ?>
                    </span>
                    <?php if (($o['notification_read'] ?? 1) == 0): ?>
                    <span style="background:#C1121F; color:white; font-size:10px; font-weight:700; padding:2px 8px; border-radius:20px; animation:pulse-badge 1.5s infinite;">
                        🔔 Baru!
                    </span>
                    <?php endif; ?>
                </div>
                <div style="font-size:12px; color:var(--gray); margin-bottom:6px;">
                    <?= isset($o['created_at']) ? date('d M Y, H:i', strtotime($o['created_at'])) : '' ?>
                </div>
                <?php
                $etaColors = ['processing'=>'#3498DB','shipped'=>'#8E44AD'];
                $etaLabels = [
                    'processing' => '🕐 Est. tiba ' . date('d M Y', strtotime('+3 days')),
                    'shipped'    => '🚚 Est. tiba ' . date('d M', strtotime('+1 day')) . '–' . date('d M Y', strtotime('+2 days')),
                ];
                if (isset($etaLabels[$o['status']]) && ($o['payment_status'] ?? '') === 'paid'):
                ?>
                <div style="font-size:11px; font-weight:600; color:<?= $etaColors[$o['status']] ?>; background:<?= $etaColors[$o['status']] ?>15; padding:3px 10px; border-radius:20px; display:inline-block; margin-bottom:6px;">
                    <?= $etaLabels[$o['status']] ?>
                </div>
                <?php endif; ?>
                <div style="font-size:16px; font-weight:700; color:var(--red);">
                    Rp <?= number_format($o['total'], 0, ',', '.') ?>
                </div>
            </div>

            <div style="display:flex; flex-direction:column; align-items:flex-end; gap:10px; min-width:140px;">
                <span style="font-size:12px; font-weight:600; color:<?= $payColor[$ps]??'#E67E22' ?>;">
                    <?= $payLabel[$ps]??'Belum Dibayar' ?>
                </span>
                <?php if ($ps === 'unpaid'): ?>
                    <a href="<?= base_url('orders/'.$o['id'].'/pay') ?>"
                       style="background:var(--red); color:white; padding:8px 18px; border-radius:8px; font-size:13px; font-weight:600; text-decoration:none;">
                        💳 Bayar Sekarang
                    </a>
                <?php else: ?>
                    <a href="<?= base_url('orders/'.$o['id']) ?>"
                       style="border:1.5px solid var(--red); color:var(--red); padding:7px 16px; border-radius:8px; font-size:13px; font-weight:600; text-decoration:none;">
                        Lihat Detail
                    </a>
                <?php endif; ?>
            </div>

        </div>
        <?php endforeach; ?>
        </div>

        <?php endif; ?>

    <!-- ══════════════════════════════════════════════════════════
         TAB: RIWAYAT TRANSAKSI
    ══════════════════════════════════════════════════════════ -->
    <?php else: ?>

        <!-- FILTER -->
        <form method="get" action="<?= base_url('orders') ?>">
            <input type="hidden" name="tab" value="history">
            <div style="background:white; border-radius:var(--radius); box-shadow:var(--shadow); padding:20px 24px; margin-bottom:20px;">
                <div style="font-size:12px; font-weight:700; color:var(--gray); text-transform:uppercase; letter-spacing:.6px; margin-bottom:14px;">Filter</div>
                <div style="display:grid; grid-template-columns:1fr 1fr 1fr auto; gap:12px; align-items:end;">
                    <div>
                        <label style="font-size:12px; color:var(--gray); display:block; margin-bottom:5px;">No. Invoice</label>
                        <input type="text" name="invoice" value="<?= htmlspecialchars($invoice) ?>" placeholder="Contoh: 42"
                               style="width:100%; padding:9px 12px; border:1.5px solid #E8D5C4; border-radius:8px; font-size:13px; box-sizing:border-box; font-family:'Poppins',sans-serif;">
                    </div>
                    <div>
                        <label style="font-size:12px; color:var(--gray); display:block; margin-bottom:5px;">Tanggal Awal</label>
                        <input type="date" name="date_from" value="<?= htmlspecialchars($dateFrom) ?>"
                               style="width:100%; padding:9px 12px; border:1.5px solid #E8D5C4; border-radius:8px; font-size:13px; box-sizing:border-box; font-family:'Poppins',sans-serif;">
                    </div>
                    <div>
                        <label style="font-size:12px; color:var(--gray); display:block; margin-bottom:5px;">Tanggal Akhir</label>
                        <input type="date" name="date_to" value="<?= htmlspecialchars($dateTo) ?>"
                               style="width:100%; padding:9px 12px; border:1.5px solid #E8D5C4; border-radius:8px; font-size:13px; box-sizing:border-box; font-family:'Poppins',sans-serif;">
                    </div>
                    <div>
                        <button type="submit" style="background:var(--red); color:white; border:none; padding:10px 20px; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer; font-family:'Poppins',sans-serif; white-space:nowrap;">
                            Cari
                        </button>
                    </div>
                </div>
            </div>
        </form>

        <!-- HISTORY TABLE -->
        <div style="background:white; border-radius:var(--radius); box-shadow:var(--shadow); overflow:hidden;">
            <table style="width:100%; border-collapse:collapse; font-size:13px;">
                <thead>
                    <tr style="background:var(--red); color:white;">
                        <th style="padding:14px 16px; text-align:left; font-weight:600;">Tanggal</th>
                        <th style="padding:14px 16px; text-align:left; font-weight:600;">No. Invoice</th>
                        <th style="padding:14px 16px; text-align:right; font-weight:600;">Total</th>
                        <th style="padding:14px 16px; text-align:center; font-weight:600;">Status Order</th>
                        <th style="padding:14px 16px; text-align:center; font-weight:600;">Pembayaran</th>
                        <th style="padding:14px 16px; text-align:center; font-weight:600;">Detail</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($historyOrders)): ?>
                    <tr>
                        <td colspan="6" style="padding:48px; text-align:center; color:var(--gray);">
                            <div style="font-size:40px; margin-bottom:10px;">📋</div>
                            <div>Tidak ada riwayat transaksi.</div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php
                    $statusColor = ['pending'=>'#E67E22','processing'=>'#3498DB','shipped'=>'#8E44AD','selesai'=>'#27AE60'];
                    $statusLabel = ['pending'=>'Pending','processing'=>'Diproses','shipped'=>'Dikirim','selesai'=>'Selesai'];
                    $payColor = ['unpaid'=>'#E67E22','paid'=>'#059669','failed'=>'#DC2626','expired'=>'#9CA3AF'];
                    $payLabel = ['unpaid'=>'Belum Dibayar','paid'=>'Lunas','failed'=>'Gagal','expired'=>'Kadaluarsa'];
                    ?>
                    <?php foreach ($historyOrders as $o): ?>
                    <?php $ps = $o['payment_status'] ?? 'unpaid'; ?>
                    <tr style="border-top:1px solid #F5EDE8;">
                        <td style="padding:13px 16px; color:var(--gray);">
                            <?= isset($o['created_at']) ? date('d M Y', strtotime($o['created_at'])) : '-' ?>
                        </td>
                        <td style="padding:13px 16px; font-weight:600; color:var(--brown-dark);">
                            #<?= str_pad($o['id'], 4, '0', STR_PAD_LEFT) ?>
                        </td>
                        <td style="padding:13px 16px; text-align:right; font-weight:700; color:var(--red);">
                            Rp <?= number_format($o['total'], 0, ',', '.') ?>
                        </td>
                        <td style="padding:13px 16px; text-align:center;">
                            <span style="background:<?= ($statusColor[$o['status']]??'#999') ?>20; color:<?= $statusColor[$o['status']]??'#999' ?>; font-size:11px; font-weight:700; padding:3px 10px; border-radius:20px;">
                                <?= $statusLabel[$o['status']] ?? $o['status'] ?>
                            </span>
                        </td>
                        <td style="padding:13px 16px; text-align:center;">
                            <span style="font-size:12px; font-weight:600; color:<?= $payColor[$ps]??'#999' ?>;">
                                <?= $payLabel[$ps]??'-' ?>
                            </span>
                        </td>
                        <td style="padding:13px 16px; text-align:center;">
                            <a href="<?= base_url('orders/'.$o['id']) ?>"
                               style="color:var(--red); font-size:12px; font-weight:600; text-decoration:none;">
                                Lihat →
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- PAGINATION (History) -->
        <?php if ($totalPages > 1): ?>
        <div style="display:flex; justify-content:center; align-items:center; gap:8px; margin-top:20px; flex-wrap:wrap;">
            <?php $qs = '?tab=history' . ($invoice ? '&invoice='.urlencode($invoice) : '') . ($dateFrom ? '&date_from='.$dateFrom : '') . ($dateTo ? '&date_to='.$dateTo : ''); ?>
            <?php if ($currentPage > 1): ?>
            <a href="<?= $qs ?>&page=<?= $currentPage-1 ?>" style="padding:8px 16px; border-radius:8px; font-size:13px; font-weight:600; text-decoration:none; background:white; color:var(--brown-dark); border:1.5px solid #E8D5C4;">← Sebelumnya</a>
            <?php endif; ?>
            <?php for ($p = 1; $p <= $totalPages; $p++): ?>
            <a href="<?= $qs ?>&page=<?= $p ?>" style="padding:8px 14px; border-radius:8px; font-size:13px; font-weight:600; text-decoration:none; background:<?= $p===$currentPage?'var(--red)':'white' ?>; color:<?= $p===$currentPage?'white':'var(--brown-dark)' ?>; border:1.5px solid <?= $p===$currentPage?'var(--red)':'#E8D5C4' ?>;"><?= $p ?></a>
            <?php endfor; ?>
            <?php if ($currentPage < $totalPages): ?>
            <a href="<?= $qs ?>&page=<?= $currentPage+1 ?>" style="padding:8px 16px; border-radius:8px; font-size:13px; font-weight:600; text-decoration:none; background:white; color:var(--brown-dark); border:1.5px solid #E8D5C4;">Selanjutnya →</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>

    <?php endif; ?>

</div>
</div>

<?= $this->endSection() ?>
