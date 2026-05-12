<?= $this->extend('layout/admin') ?>
<?= $this->section('content') ?>

<h2 style="font-family:'Playfair Display',serif; color:#3B1F0F; margin-bottom:6px;">📈 Laporan Penjualan</h2>
<p style="font-size:13px; color:#888; margin-bottom:28px;">Ringkasan pendapatan, order, dan produk terlaris.</p>

<!-- ── SUMMARY CARDS ─────────────────────────────────────────────── -->
<div class="dashboard-grid" style="margin-bottom:28px;">
    <div class="card-box" style="border-left:4px solid #27AE60;">
        <p style="color:#888; margin:0 0 4px; font-size:12px; text-transform:uppercase; letter-spacing:.5px;">Total Pendapatan</p>
        <h3 style="font-size:22px; color:#27AE60; margin:0;">Rp <?= number_format($totalRevenue, 0, ',', '.') ?></h3>
        <p style="font-size:11px; color:#aaa; margin:4px 0 0;">dari order lunas</p>
    </div>
    <div class="card-box" style="border-left:4px solid #C1121F;">
        <p style="color:#888; margin:0 0 4px; font-size:12px; text-transform:uppercase; letter-spacing:.5px;">Total Order</p>
        <h3 style="font-size:32px; color:#C1121F; margin:0;"><?= number_format($totalOrders) ?></h3>
        <p style="font-size:11px; color:#aaa; margin:4px 0 0;">semua status</p>
    </div>
    <div class="card-box" style="border-left:4px solid #3498DB;">
        <p style="color:#888; margin:0 0 4px; font-size:12px; text-transform:uppercase; letter-spacing:.5px;">Order Lunas</p>
        <h3 style="font-size:32px; color:#3498DB; margin:0;"><?= number_format($paidOrders) ?></h3>
        <p style="font-size:11px; color:#aaa; margin:4px 0 0;">payment_status = paid</p>
    </div>
    <div class="card-box" style="border-left:4px solid #C8860A;">
        <p style="color:#888; margin:0 0 4px; font-size:12px; text-transform:uppercase; letter-spacing:.5px;">Rata-rata Per Order</p>
        <h3 style="font-size:22px; color:#C8860A; margin:0;">Rp <?= number_format($avgOrder, 0, ',', '.') ?></h3>
        <p style="font-size:11px; color:#aaa; margin:4px 0 0;">dari order lunas</p>
    </div>
</div>

<!-- ── GRAFIK PENDAPATAN BULANAN ──────────────────────────────────── -->
<div class="card-box" style="margin-bottom:28px;">
    <h4 style="margin:0 0 20px; font-size:15px; color:#3B1F0F;">📊 Pendapatan Bulanan (12 Bulan Terakhir)</h4>
    <?php if (empty($monthly)): ?>
        <p style="text-align:center; color:#bbb; padding:40px 0; margin:0;">Belum ada data penjualan.</p>
    <?php else: ?>
        <canvas id="revenueChart" height="90"></canvas>
    <?php endif; ?>
</div>

<!-- ── BAWAH: Produk Terlaris + Status Distribution ──────────────── -->
<div style="display:grid; grid-template-columns:1.6fr 1fr; gap:20px; margin-bottom:28px;">

    <!-- PRODUK TERLARIS -->
    <div class="card-box" style="padding:0; overflow:hidden;">
        <div style="padding:16px 20px; border-bottom:1px solid #F0E8E0;">
            <h4 style="margin:0; font-size:15px; color:#3B1F0F;">🏆 Produk Terlaris (by Pendapatan)</h4>
        </div>
        <table style="width:100%; border-collapse:collapse; font-size:13px;">
            <thead>
                <tr style="background:#FFF8F0;">
                    <th style="padding:10px 14px; text-align:left; color:#888; font-weight:600;">#</th>
                    <th style="padding:10px 14px; text-align:left; color:#888; font-weight:600;">Produk</th>
                    <th style="padding:10px 14px; text-align:center; color:#888; font-weight:600;">Terjual</th>
                    <th style="padding:10px 14px; text-align:right; color:#888; font-weight:600;">Pendapatan</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($topProducts)): ?>
                    <tr><td colspan="4" style="padding:20px; text-align:center; color:#bbb;">Belum ada data</td></tr>
                <?php else: ?>
                    <?php foreach ($topProducts as $i => $p): ?>
                    <tr style="border-top:1px solid #F7F2EE;">
                        <td style="padding:10px 14px;">
                            <div style="width:24px; height:24px; background:<?= $i===0?'#C1121F':($i===1?'#C8860A':($i===2?'#C0A97A':'#DDD')) ?>; color:white; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:11px; font-weight:700;">
                                <?= $i + 1 ?>
                            </div>
                        </td>
                        <td style="padding:10px 14px;">
                            <div style="display:flex; align-items:center; gap:10px;">
                                <img src="/uploads/<?= htmlspecialchars($p['image']) ?>" style="width:36px; height:36px; border-radius:6px; object-fit:cover;" onerror="this.style.display='none'">
                                <div>
                                    <div style="font-weight:600; color:#3B1F0F;"><?= htmlspecialchars($p['name']) ?></div>
                                    <div style="font-size:11px; color:#aaa;"><?= htmlspecialchars($p['category'] ?? '-') ?></div>
                                </div>
                            </div>
                        </td>
                        <td style="padding:10px 14px; text-align:center; color:#555;"><?= number_format($p['total_sold']) ?></td>
                        <td style="padding:10px 14px; text-align:right; font-weight:700; color:#27AE60;">
                            Rp <?= number_format($p['revenue'], 0, ',', '.') ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- STATUS DISTRIBUTION -->
    <div class="card-box">
        <h4 style="margin:0 0 20px; font-size:15px; color:#3B1F0F;">📦 Distribusi Status Order</h4>
        <?php if (empty($statusDist)): ?>
            <p style="text-align:center; color:#bbb;">Belum ada data.</p>
        <?php else: ?>
            <canvas id="statusChart" height="200"></canvas>
            <div style="margin-top:16px; display:flex; flex-direction:column; gap:8px;">
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
                $statusTotal = array_sum(array_column($statusDist, 'total'));
                ?>
                <?php foreach ($statusDist as $s): ?>
                <div style="display:flex; justify-content:space-between; align-items:center; font-size:13px;">
                    <div style="display:flex; align-items:center; gap:8px;">
                        <span style="width:10px; height:10px; border-radius:50%; background:<?= $statusColor[$s['status']] ?? '#999' ?>; display:inline-block;"></span>
                        <span style="color:#555;"><?= $statusLabel[$s['status']] ?? $s['status'] ?></span>
                    </div>
                    <span style="font-weight:700; color:#3B1F0F;"><?= $s['total'] ?> <span style="font-weight:400; color:#aaa;">(<?= $statusTotal > 0 ? round($s['total']/$statusTotal*100) : 0 ?>%)</span></span>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
<?php if (!empty($monthly)): ?>
const monthlyLabels = <?= json_encode(array_column($monthly, 'bulan_label')) ?>;
const monthlyData   = <?= json_encode(array_map(fn($m) => (int)$m['pendapatan'], $monthly)) ?>;

new Chart(document.getElementById('revenueChart'), {
    type: 'bar',
    data: {
        labels: monthlyLabels,
        datasets: [{
            label: 'Pendapatan (Rp)',
            data: monthlyData,
            backgroundColor: '#C1121F33',
            borderColor: '#C1121F',
            borderWidth: 2,
            borderRadius: 6,
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: ctx => 'Rp ' + ctx.parsed.y.toLocaleString('id-ID')
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: v => 'Rp ' + (v/1000).toLocaleString('id-ID') + 'k'
                },
                grid: { color: '#F0E8E0' }
            },
            x: { grid: { display: false } }
        }
    }
});
<?php endif; ?>

<?php if (!empty($statusDist)): ?>
const statusLabels = <?= json_encode(array_map(fn($s) => $statusLabel[$s['status']] ?? $s['status'], $statusDist)) ?>;
const statusData   = <?= json_encode(array_column($statusDist, 'total')) ?>;
const statusColors = <?= json_encode(array_map(fn($s) => $statusColor[$s['status']] ?? '#999', $statusDist)) ?>;

new Chart(document.getElementById('statusChart'), {
    type: 'doughnut',
    data: {
        labels: statusLabels,
        datasets: [{ data: statusData, backgroundColor: statusColors, borderWidth: 2, borderColor: '#fff' }]
    },
    options: {
        responsive: true,
        cutout: '62%',
        plugins: {
            legend: { display: false },
            tooltip: { callbacks: { label: ctx => ctx.label + ': ' + ctx.parsed } }
        }
    }
});
<?php endif; ?>
</script>

<?= $this->endSection() ?>
