<?= $this->extend('layout/admin') ?>
<?= $this->section('content') ?>

<h2 style="font-family:'Playfair Display',serif; color:#3B1F0F; margin-bottom:24px;">Dashboard</h2>

<!-- STAT CARDS -->
<div class="dashboard-grid" style="margin-bottom:28px;">
    <div class="card-box" style="border-left:4px solid #C1121F;">
        <h3 style="font-size:32px; color:#C1121F; margin:0 0 4px;"><?= $produk ?></h3>
        <p style="color:#888; margin:0; font-size:13px;">Total Produk</p>
    </div>
    <div class="card-box" style="border-left:4px solid #C8860A;">
        <h3 style="font-size:32px; color:#C8860A; margin:0 0 4px;"><?= $order ?></h3>
        <p style="color:#888; margin:0; font-size:13px;">Total Order</p>
    </div>
    <div class="card-box" style="border-left:4px solid #E67E22;">
        <h3 style="font-size:32px; color:#E67E22; margin:0 0 4px;"><?= $pending ?></h3>
        <p style="color:#888; margin:0; font-size:13px;">Order Pending</p>
    </div>
    <div class="card-box" style="border-left:4px solid #27AE60;">
        <h3 style="font-size:22px; color:#27AE60; margin:0 0 4px;">Rp <?= number_format($uang, 0, ',', '.') ?></h3>
        <p style="color:#888; margin:0; font-size:13px;">Total Pendapatan</p>
    </div>
</div>

<!-- TODAY STATS -->
<div style="display:grid; grid-template-columns:repeat(4,1fr); gap:16px; margin-bottom:20px;">
    <div class="card-box" style="border-left:4px solid #3498DB; padding:14px 18px;">
        <p style="color:#888; font-size:11px; text-transform:uppercase; letter-spacing:1px; margin:0 0 4px;">Pesanan Hari Ini</p>
        <h3 style="font-size:28px; color:#3498DB; margin:0;"><?= $todayOrders ?></h3>
    </div>
    <div class="card-box" style="border-left:4px solid #27AE60; padding:14px 18px;">
        <p style="color:#888; font-size:11px; text-transform:uppercase; letter-spacing:1px; margin:0 0 4px;">Pendapatan Hari Ini</p>
        <h3 style="font-size:20px; color:#27AE60; margin:0;">Rp <?= number_format($todayRev, 0, ',', '.') ?></h3>
    </div>
    <div class="card-box" style="border-left:4px solid #E67E22; padding:14px 18px;">
        <p style="color:#888; font-size:11px; text-transform:uppercase; letter-spacing:1px; margin:0 0 4px;">Stok Menipis</p>
        <h3 style="font-size:28px; color:#E67E22; margin:0;"><?= $lowStock ?> <span style="font-size:13px; font-weight:400;">produk</span></h3>
        <?php if ($outStock > 0): ?>
        <p style="font-size:11px; color:#DC2626; margin:4px 0 0; font-weight:600;">⚠️ <?= $outStock ?> habis</p>
        <?php endif; ?>
    </div>
    <div class="card-box" style="border-left:4px solid #8B5CF6; padding:14px 18px;">
        <p style="color:#888; font-size:11px; text-transform:uppercase; letter-spacing:1px; margin:0 0 4px;">Total Ulasan</p>
        <h3 style="font-size:28px; color:#8B5CF6; margin:0;"><?= number_format($totalReviews) ?></h3>
        <a href="<?= base_url('admin/reviews') ?>" style="font-size:11px; color:#8B5CF6; font-weight:600;">Moderasi →</a>
    </div>
</div>

<!-- REKOMENDASI STATS -->
<div style="display:grid; grid-template-columns:repeat(3,1fr); gap:16px; margin-bottom:28px;">
    <div class="card-box" style="text-align:center; background:linear-gradient(135deg,#FFF8F0,#FEF3E8);">
        <div style="font-size:28px; margin-bottom:4px;">🤖</div>
        <h3 style="font-size:26px; color:#C1121F; margin:0 0 4px;"><?= number_format($totalRec) ?></h3>
        <p style="color:#888; margin:0; font-size:12px;">Total Rekomendasi</p>
    </div>
    <div class="card-box" style="text-align:center; background:linear-gradient(135deg,#FFF8F0,#FEF3E8);">
        <div style="font-size:28px; margin-bottom:4px;">👆</div>
        <h3 style="font-size:26px; color:#C1121F; margin:0 0 4px;"><?= $ctr ?>%</h3>
        <p style="color:#888; margin:0; font-size:12px;">CTR (Click-Through Rate)</p>
    </div>
    <div class="card-box" style="text-align:center; background:linear-gradient(135deg,#FFF8F0,#FEF3E8);">
        <div style="font-size:28px; margin-bottom:4px;">⭐</div>
        <h3 style="font-size:26px; color:#C1121F; margin:0 0 4px;"><?= number_format($totalRatings) ?></h3>
        <p style="color:#888; margin:0; font-size:12px;">Total Rating</p>
    </div>
</div>

<!-- TABEL BAWAH: Pesanan Terbaru + User Terbaru + Produk Terlaris -->
<div style="display:grid; grid-template-columns:1.4fr 1fr 1fr; gap:20px;">

    <!-- PESANAN TERBARU -->
    <div class="card-box" style="padding:0; overflow:hidden;">
        <div style="padding:16px 20px; border-bottom:1px solid #F0E8E0; display:flex; justify-content:space-between; align-items:center;">
            <h4 style="margin:0; font-size:15px; color:#3B1F0F;">📦 Pesanan Terbaru</h4>
            <a href="/admin/orders" style="font-size:12px; color:#C1121F; text-decoration:none; font-weight:600;">Lihat Semua →</a>
        </div>
        <table style="width:100%; border-collapse:collapse; font-size:13px;">
            <thead>
                <tr style="background:#FFF8F0;">
                    <th style="padding:10px 16px; text-align:left; color:#888; font-weight:600;">#</th>
                    <th style="padding:10px 16px; text-align:left; color:#888; font-weight:600;">Customer</th>
                    <th style="padding:10px 16px; text-align:right; color:#888; font-weight:600;">Total</th>
                    <th style="padding:10px 16px; text-align:center; color:#888; font-weight:600;">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($recentOrders)): ?>
                <tr><td colspan="4" style="padding:20px; text-align:center; color:#bbb;">Belum ada pesanan</td></tr>
                <?php else: ?>
                <?php
                $statusColor = [
                    'pending'    => '#E67E22',
                    'processing' => '#3498DB',
                    'shipped'    => '#8E44AD',
                    'selesai'    => '#27AE60',
                ];
                $statusLabel = [
                    'pending'    => 'Pending',
                    'processing' => 'Diproses',
                    'shipped'    => 'Dikirim',
                    'selesai'    => 'Selesai',
                ];
                ?>
                <?php foreach ($recentOrders as $o): ?>
                <tr style="border-top:1px solid #F7F2EE;">
                    <td style="padding:10px 16px; color:#aaa;">#<?= str_pad($o['id'], 4, '0', STR_PAD_LEFT) ?></td>
                    <td style="padding:10px 16px; font-weight:500;"><?= htmlspecialchars($o['user_name'] ?? '-') ?></td>
                    <td style="padding:10px 16px; text-align:right; font-weight:600; color:#C1121F;">
                        Rp <?= number_format($o['total'], 0, ',', '.') ?>
                    </td>
                    <td style="padding:10px 16px; text-align:center;">
                        <span style="background:<?= $statusColor[$o['status']] ?? '#999' ?>20; color:<?= $statusColor[$o['status']] ?? '#999' ?>; padding:3px 10px; border-radius:20px; font-size:11px; font-weight:700;">
                            <?= $statusLabel[$o['status']] ?? $o['status'] ?>
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- USER TERBARU -->
    <div class="card-box" style="padding:0; overflow:hidden;">
        <div style="padding:16px 20px; border-bottom:1px solid #F0E8E0; display:flex; justify-content:space-between; align-items:center;">
            <h4 style="margin:0; font-size:15px; color:#3B1F0F;">👤 User Terbaru</h4>
            <a href="/admin/users" style="font-size:12px; color:#C1121F; text-decoration:none; font-weight:600;">Lihat Semua →</a>
        </div>
        <div style="padding:8px 0;">
            <?php if (empty($recentUsers)): ?>
            <p style="padding:20px; text-align:center; color:#bbb; margin:0;">Belum ada user</p>
            <?php else: ?>
            <?php foreach ($recentUsers as $u): ?>
            <div style="display:flex; align-items:center; gap:12px; padding:10px 16px; border-bottom:1px solid #F7F2EE;">
                <div style="width:34px; height:34px; background:#C1121F20; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:13px; font-weight:700; color:#C1121F; flex-shrink:0;">
                    <?= strtoupper(mb_substr($u['name'] ?? '?', 0, 1)) ?>
                </div>
                <div style="flex:1; min-width:0;">
                    <div style="font-weight:600; font-size:13px; color:#3B1F0F;"><?= htmlspecialchars($u['name']) ?></div>
                    <div style="font-size:11px; color:#aaa; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;"><?= htmlspecialchars($u['email']) ?></div>
                </div>
                <div style="font-size:11px; color:#bbb; flex-shrink:0;"><?= date('d M', strtotime($u['created_at'])) ?></div>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- PRODUK TERLARIS -->
    <div class="card-box" style="padding:0; overflow:hidden;">
        <div style="padding:16px 20px; border-bottom:1px solid #F0E8E0; display:flex; justify-content:space-between; align-items:center;">
            <h4 style="margin:0; font-size:15px; color:#3B1F0F;">🏆 Produk Terlaris</h4>
            <a href="/admin/products" style="font-size:12px; color:#C1121F; text-decoration:none; font-weight:600;">Lihat Semua →</a>
        </div>
        <div style="padding:8px 0;">
            <?php if (empty($topProducts)): ?>
            <p style="padding:20px; text-align:center; color:#bbb; margin:0;">Belum ada produk</p>
            <?php else: ?>
            <?php foreach ($topProducts as $i => $p): ?>
            <div style="display:flex; align-items:center; gap:12px; padding:10px 16px; border-bottom:1px solid #F7F2EE;">
                <div style="width:28px; height:28px; background:<?= $i===0?'#C1121F':($i===1?'#C8860A':($i===2?'#C0A97A':'#DDD')) ?>; color:white; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:700; flex-shrink:0;">
                    <?= $i + 1 ?>
                </div>
                <img src="/uploads/<?= htmlspecialchars($p['image']) ?>" alt="" style="width:36px; height:36px; border-radius:8px; object-fit:cover; flex-shrink:0;" onerror="this.style.display='none'">
                <div style="flex:1; min-width:0;">
                    <div style="font-weight:600; font-size:13px; color:#3B1F0F; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                        <?= htmlspecialchars($p['name']) ?>
                    </div>
                    <div style="font-size:11px; color:#aaa;"><?= number_format($p['sold']) ?> terjual</div>
                </div>
                <div style="font-size:13px; font-weight:700; color:#C1121F; white-space:nowrap; flex-shrink:0;">
                    Rp <?= number_format($p['price'], 0, ',', '.') ?>
                </div>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

</div>

<?= $this->endSection() ?>
