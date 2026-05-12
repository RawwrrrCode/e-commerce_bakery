<?= $this->extend('layout/admin') ?>
<?= $this->section('content') ?>

<div class="page-header" style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:12px;">
    <div>
        <h1>📊 Evaluasi Sistem Rekomendasi</h1>
        <p>Metrik performa Hybrid Filtering (Content-Based + Collaborative Filtering)</p>
    </div>
    <div style="display:flex; gap:10px; flex-shrink:0;">
        <a href="/admin/recommendation/data"
           style="background:#3C2A1E; color:white; border:none; padding:10px 20px; border-radius:8px; font-size:13px; font-weight:700; cursor:pointer; text-decoration:none; display:flex; align-items:center; gap:6px;">
            🗂️ Data Input CF
        </a>
        <a href="/admin/recommendation/pdf" target="_blank"
           style="background:#C1121F; color:white; border:none; padding:10px 20px; border-radius:8px; font-size:13px; font-weight:700; cursor:pointer; text-decoration:none; display:flex; align-items:center; gap:6px;">
            🖨️ Print
        </a>
    </div>
</div>

<!-- ================================================================
     METRIK UTAMA
     ================================================================ -->
<div class="dashboard-grid" style="grid-template-columns: repeat(4,1fr); margin-bottom:28px;">

    <div class="card-box" style="border-top-color:#C1121F;">
        <div class="card-icon">📐</div>
        <h3><?= $metrics['rmse'] ?: '<span style="font-size:18px;color:#999">N/A</span>' ?></h3>
        <p>RMSE (CF Rating)</p>
        <small style="color:#999; font-size:11px;">Uji pada <?= $metrics['n_test'] ?> sampel</small>
    </div>

    <div class="card-box" style="border-top-color:#C8860A;">
        <div class="card-icon">📏</div>
        <h3><?= $metrics['mae'] ?: '<span style="font-size:18px;color:#999">N/A</span>' ?></h3>
        <p>MAE (Mean Abs. Error)</p>
        <small style="color:#999; font-size:11px;">Selisih rata-rata prediksi vs aktual</small>
    </div>

    <div class="card-box" style="border-top-color:#2ecc71;">
        <div class="card-icon">🎯</div>
        <h3><?= $metrics['coverage'] ?>%</h3>
        <p>Coverage Produk</p>
        <small style="color:#999; font-size:11px;"><?= $metrics['distinct_rec'] ?> / <?= $metrics['total_products'] ?> produk direkomendasikan</small>
    </div>

    <div class="card-box" style="border-top-color:#3498db;">
        <div class="card-icon">👆</div>
        <h3><?= $metrics['ctr'] ?>%</h3>
        <p>Click-Through Rate</p>
        <small style="color:#999; font-size:11px;"><?= $metrics['total_clicked'] ?> klik dari <?= $metrics['total_logged'] ?> rekomendasi</small>
    </div>

</div>

<!-- ================================================================
     ROW 2: Precision + Distribusi Metode + User Info
     ================================================================ -->
<div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:20px; margin-bottom:28px;">

    <!-- Precision / Recall / F1 -->
    <div class="admin-box">
        <p style="font-size:13px; color:#888; text-transform:uppercase; letter-spacing:1px; margin-bottom:14px;">Evaluasi Offline (vs Pembelian)</p>

        <div style="display:flex; justify-content:space-between; gap:8px; margin-bottom:16px;">
            <div style="text-align:center; flex:1;">
                <div style="font-family:'Playfair Display',serif; font-size:26px; font-weight:700; color:#C1121F;">
                    <?= number_format($metrics['precision_buy'] * 100, 1) ?>%
                </div>
                <div style="font-size:11px; color:#888; margin-top:2px;">Precision@N</div>
            </div>
            <div style="text-align:center; flex:1; border-left:1px solid #f0e8e0; border-right:1px solid #f0e8e0;">
                <div style="font-family:'Playfair Display',serif; font-size:26px; font-weight:700; color:#3498db;">
                    <?= number_format($metrics['recall'] * 100, 1) ?>%
                </div>
                <div style="font-size:11px; color:#888; margin-top:2px;">Recall@N</div>
            </div>
            <div style="text-align:center; flex:1;">
                <div style="font-family:'Playfair Display',serif; font-size:26px; font-weight:700; color:#2ecc71;">
                    <?= number_format($metrics['f1'] * 100, 1) ?>%
                </div>
                <div style="font-size:11px; color:#888; margin-top:2px;">F1-Score</div>
            </div>
        </div>

        <div style="background:#FFF8F0; border-radius:8px; padding:12px;">
            <div style="font-size:12px; color:#888; margin-bottom:8px;">Bobot Hybrid</div>
            <div style="display:flex; justify-content:space-between;">
                <div style="text-align:center;">
                    <div style="font-weight:700; color:#C1121F; font-size:18px;">60%</div>
                    <div style="font-size:11px; color:#888;">Collaborative (CF)</div>
                </div>
                <div style="text-align:center;">
                    <div style="font-weight:700; color:#C8860A; font-size:18px;">40%</div>
                    <div style="font-size:11px; color:#888;">Content-Based (CBF)</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Distribusi Metode (Donut Chart) -->
    <div class="admin-box">
        <p style="font-size:13px; color:#888; text-transform:uppercase; letter-spacing:1px; margin-bottom:16px;">Distribusi Metode</p>

        <?php
        $methodColors = ['hybrid' => '#C1121F', 'cbf' => '#C8860A', 'popularity' => '#3498db'];
        $methodLabels = ['hybrid' => 'Hybrid (CF+CBF)', 'cbf' => 'Content-Based', 'popularity' => 'Popularity'];
        $totalMethod  = array_sum($metrics['method_dist']);
        ?>

        <?php if(empty($metrics['method_dist'])): ?>
            <p style="color:#999; font-size:13px;">Belum ada data rekomendasi</p>
        <?php else: ?>
            <canvas id="methodDonutChart" height="220"></canvas>
            <div style="margin-top:14px; display:flex; flex-direction:column; gap:6px;">
                <?php foreach($metrics['method_dist'] as $m => $cnt): ?>
                <?php $pct = $totalMethod > 0 ? round($cnt/$totalMethod*100, 1) : 0; ?>
                <div style="display:flex; justify-content:space-between; font-size:12px; align-items:center;">
                    <span style="display:flex; align-items:center; gap:6px;">
                        <span style="width:10px; height:10px; border-radius:50%; background:<?= $methodColors[$m] ?? '#999' ?>; display:inline-block;"></span>
                        <?= $methodLabels[$m] ?? $m ?>
                    </span>
                    <span style="font-weight:700; color:<?= $methodColors[$m] ?? '#333' ?>;"><?= $cnt ?> (<?= $pct ?>%)</span>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Info Matrix CF -->
    <div class="admin-box">
        <p style="font-size:13px; color:#888; text-transform:uppercase; letter-spacing:1px; margin-bottom:16px;">Info User-Item Matrix</p>

        <div style="display:flex; flex-direction:column; gap:12px;">
            <div style="display:flex; justify-content:space-between; align-items:center; padding:10px; background:#FFF8F0; border-radius:8px;">
                <span style="font-size:13px; color:#5C3D2E;">Total User</span>
                <span style="font-weight:700; color:#C1121F;"><?= $totalUsers ?></span>
            </div>
            <div style="display:flex; justify-content:space-between; align-items:center; padding:10px; background:#FFF8F0; border-radius:8px;">
                <span style="font-size:13px; color:#5C3D2E;">Explicit Ratings</span>
                <span style="font-weight:700; color:#C1121F;"><?= $totalRatings ?></span>
            </div>
            <div style="display:flex; justify-content:space-between; align-items:center; padding:10px; background:#FFF8F0; border-radius:8px;">
                <span style="font-size:13px; color:#5C3D2E;">Users Punya Rekomendasi</span>
                <span style="font-weight:700; color:#C1121F;"><?= $metrics['users_with_rec'] ?></span>
            </div>
            <div style="display:flex; justify-content:space-between; align-items:center; padding:10px; background:#FFF8F0; border-radius:8px;">
                <span style="font-size:13px; color:#5C3D2E;">K Tetangga CF</span>
                <span style="font-weight:700; color:#C1121F;">10</span>
            </div>
        </div>
    </div>

</div>

<!-- ================================================================
     PERBANDINGAN CF vs CBF vs HYBRID
     ================================================================ -->
<div class="admin-box" style="margin-bottom:28px;">
    <h3 style="font-family:'Playfair Display',serif; font-size:18px; color:#2C1810; margin-bottom:6px;">
        🔬 Perbandingan Metode: CF vs CBF vs Hybrid
    </h3>
    <p style="font-size:12px; color:#888; margin-bottom:20px;">
        Evaluasi offline — ground truth: produk yang pernah dibeli user (payment_status = paid)
    </p>

    <div style="display:grid; grid-template-columns:1fr 1fr; gap:28px; align-items:start;">

        <!-- Chart -->
        <div>
            <canvas id="comparisonChart" height="260"></canvas>
        </div>

        <!-- Tabel -->
        <div>
            <table style="width:100%; border-collapse:collapse; font-size:13px;">
                <thead>
                    <tr style="background:#FFF8F0;">
                        <th style="padding:10px 12px; text-align:left; color:#2C1810; font-weight:600; border-bottom:2px solid #E8D5C4;">Metrik</th>
                        <th style="padding:10px 12px; text-align:center; color:#C1121F; font-weight:600; border-bottom:2px solid #E8D5C4;">CF Saja</th>
                        <th style="padding:10px 12px; text-align:center; color:#C8860A; font-weight:600; border-bottom:2px solid #E8D5C4;">CBF Saja</th>
                        <th style="padding:10px 12px; text-align:center; color:#059669; font-weight:600; border-bottom:2px solid #E8D5C4;">Hybrid ✓</th>
                    </tr>
                </thead>
                <tbody>
                    <tr style="border-bottom:1px solid #f0e8e0;">
                        <td style="padding:10px 12px; color:#555;">RMSE</td>
                        <td style="padding:10px 12px; text-align:center;"><?= $metrics['rmse'] ?: '—' ?></td>
                        <td style="padding:10px 12px; text-align:center; color:#bbb;">n/a</td>
                        <td style="padding:10px 12px; text-align:center;"><?= $metrics['rmse'] ?: '—' ?></td>
                    </tr>
                    <tr style="border-bottom:1px solid #f0e8e0; background:#fafafa;">
                        <td style="padding:10px 12px; color:#555;">MAE</td>
                        <td style="padding:10px 12px; text-align:center;"><?= $metrics['mae'] ?: '—' ?></td>
                        <td style="padding:10px 12px; text-align:center; color:#bbb;">n/a</td>
                        <td style="padding:10px 12px; text-align:center;"><?= $metrics['mae'] ?: '—' ?></td>
                    </tr>
                    <tr style="border-bottom:1px solid #f0e8e0;">
                        <td style="padding:10px 12px; color:#555;">Precision@N</td>
                        <td style="padding:10px 12px; text-align:center;"><?= number_format($comparison['cf']['precision'] * 100, 1) ?>%</td>
                        <td style="padding:10px 12px; text-align:center;"><?= number_format($comparison['cbf']['precision'] * 100, 1) ?>%</td>
                        <td style="padding:10px 12px; text-align:center; font-weight:700; color:#059669;"><?= number_format($comparison['hybrid']['precision'] * 100, 1) ?>%</td>
                    </tr>
                    <tr style="border-bottom:1px solid #f0e8e0; background:#fafafa;">
                        <td style="padding:10px 12px; color:#555;">Recall@N</td>
                        <td style="padding:10px 12px; text-align:center;"><?= number_format($comparison['cf']['recall'] * 100, 1) ?>%</td>
                        <td style="padding:10px 12px; text-align:center;"><?= number_format($comparison['cbf']['recall'] * 100, 1) ?>%</td>
                        <td style="padding:10px 12px; text-align:center; font-weight:700; color:#059669;"><?= number_format($comparison['hybrid']['recall'] * 100, 1) ?>%</td>
                    </tr>
                    <tr style="border-bottom:1px solid #f0e8e0;">
                        <td style="padding:10px 12px; color:#555;">F1-Score</td>
                        <td style="padding:10px 12px; text-align:center;"><?= number_format($comparison['cf']['f1'] * 100, 1) ?>%</td>
                        <td style="padding:10px 12px; text-align:center;"><?= number_format($comparison['cbf']['f1'] * 100, 1) ?>%</td>
                        <td style="padding:10px 12px; text-align:center; font-weight:700; color:#059669;"><?= number_format($comparison['hybrid']['f1'] * 100, 1) ?>%</td>
                    </tr>
                    <tr style="border-bottom:1px solid #f0e8e0; background:#fafafa;">
                        <td style="padding:10px 12px; color:#555;">Coverage</td>
                        <td style="padding:10px 12px; text-align:center;"><?= $comparison['cf']['coverage'] ?>%</td>
                        <td style="padding:10px 12px; text-align:center;"><?= $comparison['cbf']['coverage'] ?>%</td>
                        <td style="padding:10px 12px; text-align:center; font-weight:700; color:#059669;"><?= $comparison['hybrid']['coverage'] ?>%</td>
                    </tr>
                    <tr>
                        <td style="padding:8px 12px; color:#999; font-size:11px;">Users dievaluasi</td>
                        <td style="padding:8px 12px; text-align:center; color:#999; font-size:11px;"><?= $comparison['cf']['n_users'] ?></td>
                        <td style="padding:8px 12px; text-align:center; color:#999; font-size:11px;"><?= $comparison['cbf']['n_users'] ?></td>
                        <td style="padding:8px 12px; text-align:center; color:#999; font-size:11px;"><?= $comparison['hybrid']['n_users'] ?></td>
                    </tr>
                </tbody>
            </table>
            <p style="font-size:11px; color:#bbb; margin-top:8px;">
                * RMSE/MAE hanya berlaku untuk CF (prediksi rating). Hybrid menggunakan CF sebagai komponen utamanya.
            </p>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function () {
    var ctx = document.getElementById('comparisonChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['CF Saja', 'CBF Saja', 'Hybrid'],
            datasets: [
                {
                    label: 'Precision@N',
                    data: [<?= $comparison['cf']['precision'] ?>, <?= $comparison['cbf']['precision'] ?>, <?= $comparison['hybrid']['precision'] ?>],
                    backgroundColor: 'rgba(193,18,31,0.8)',
                    borderColor: 'rgba(193,18,31,1)',
                    borderWidth: 1,
                },
                {
                    label: 'Recall@N',
                    data: [<?= $comparison['cf']['recall'] ?>, <?= $comparison['cbf']['recall'] ?>, <?= $comparison['hybrid']['recall'] ?>],
                    backgroundColor: 'rgba(52,152,219,0.8)',
                    borderColor: 'rgba(52,152,219,1)',
                    borderWidth: 1,
                },
                {
                    label: 'F1-Score',
                    data: [<?= $comparison['cf']['f1'] ?>, <?= $comparison['cbf']['f1'] ?>, <?= $comparison['hybrid']['f1'] ?>],
                    backgroundColor: 'rgba(5,150,105,0.8)',
                    borderColor: 'rgba(5,150,105,1)',
                    borderWidth: 1,
                },
                {
                    label: 'Coverage',
                    data: [
                        <?= round($comparison['cf']['coverage'] / 100, 4) ?>,
                        <?= round($comparison['cbf']['coverage'] / 100, 4) ?>,
                        <?= round($comparison['hybrid']['coverage'] / 100, 4) ?>
                    ],
                    backgroundColor: 'rgba(200,134,10,0.8)',
                    borderColor: 'rgba(200,134,10,1)',
                    borderWidth: 1,
                },
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'top' },
                title: {
                    display: true,
                    text: 'Precision, Recall, F1 — CF vs CBF vs Hybrid',
                    color: '#2C1810',
                    font: { size: 13, weight: '600' }
                },
                tooltip: {
                    callbacks: {
                        label: function(ctx) {
                            return ctx.dataset.label + ': ' + (ctx.raw * 100).toFixed(1) + '%';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 1,
                    ticks: {
                        callback: function(v) { return (v * 100).toFixed(0) + '%'; }
                    },
                    title: { display: true, text: 'Nilai' }
                }
            }
        }
    });
})();

<?php if(!empty($metrics['method_dist'])): ?>
(function () {
    var ctx2 = document.getElementById('methodDonutChart').getContext('2d');
    var labels  = <?= json_encode(array_map(fn($k) => $methodLabels[$k] ?? $k, array_keys($metrics['method_dist']))) ?>;
    var data    = <?= json_encode(array_values($metrics['method_dist'])) ?>;
    var colors  = <?= json_encode(array_map(fn($k) => $methodColors[$k] ?? '#999', array_keys($metrics['method_dist']))) ?>;
    new Chart(ctx2, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: colors.map(function(c){ return c + 'CC'; }),
                borderColor: colors,
                borderWidth: 2,
            }]
        },
        options: {
            responsive: true,
            cutout: '62%',
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(ctx) {
                            var total = ctx.dataset.data.reduce(function(a,b){return a+b;}, 0);
                            var pct   = total > 0 ? ((ctx.raw / total) * 100).toFixed(1) : 0;
                            return ctx.label + ': ' + ctx.raw + ' (' + pct + '%)';
                        }
                    }
                }
            }
        }
    });
})();
<?php endif; ?>
</script>

<!-- ================================================================
     TOP PRODUK PALING DIREKOMENDASIKAN
     ================================================================ -->
<?php if(!empty($metrics['top_recommended'])): ?>
<div class="admin-box" style="margin-bottom:28px;">
    <h3 style="font-family:'Playfair Display',serif; font-size:18px; color:#2C1810; margin-bottom:16px;">
        🏆 Top Produk Paling Direkomendasikan
    </h3>
    <div style="display:flex; gap:12px; flex-wrap:wrap;">
        <?php foreach($metrics['top_recommended'] as $i => $rec): ?>
        <div style="background:#FFF8F0; border:1px solid #E8D5C4; border-radius:10px; padding:14px 18px; min-width:160px;">
            <div style="font-size:11px; color:#C1121F; font-weight:700; margin-bottom:4px;">#<?= $i+1 ?></div>
            <div style="font-weight:600; color:#2C1810; font-size:14px;"><?= htmlspecialchars($rec['name']) ?></div>
            <div style="color:#888; font-size:12px; margin-top:4px;"><?= $rec['freq'] ?> × direkomendasikan</div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<!-- ================================================================
     LOG REKOMENDASI TERBARU
     ================================================================ -->
<div class="admin-box">
    <h3 style="font-family:'Playfair Display',serif; font-size:18px; color:#2C1810; margin-bottom:16px;">
        📋 Log Rekomendasi Terbaru
    </h3>

    <?php if(empty($recentLogs)): ?>
        <p style="color:#999; text-align:center; padding:30px 0;">
            Belum ada data. Log akan muncul setelah user mengakses halaman beranda.
        </p>
    <?php else: ?>
    <table class="table">
        <thead>
            <tr>
                <th>User</th>
                <th>Produk</th>
                <th>Metode</th>
                <th>Skor Hybrid</th>
                <th>Diklik</th>
                <th>Waktu</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($recentLogs as $log): ?>
            <tr>
                <td><?= htmlspecialchars($log['user_name'] ?? 'User #'.$log['user_id']) ?></td>
                <td><?= htmlspecialchars($log['product_name'] ?? 'Produk #'.$log['product_id']) ?></td>
                <td>
                    <?php
                    $mc = ['hybrid'=>'#C1121F','Hybrid'=>'#C1121F','cbf'=>'#C8860A','Cbf'=>'#C8860A','CBF'=>'#C8860A','popularity'=>'#3498db','Popularity'=>'#3498db'];
                    $ml = ['hybrid'=>'Hybrid','Hybrid'=>'Hybrid','cbf'=>'CBF','Cbf'=>'CBF','CBF'=>'CBF','popularity'=>'Popularity','Popularity'=>'Popularity'];
                    $m  = $log['method'];
                    ?>
                    <span style="background:<?= $mc[$m]??'#999' ?>; color:white; padding:3px 10px; border-radius:20px; font-size:11px; font-weight:600;">
                        <?= $ml[$m] ?? $m ?>
                    </span>
                </td>
                <td style="font-weight:600; color:#C1121F;"><?= number_format($log['hybrid_score'] ?? $log['score'] ?? 0, 4) ?></td>
                <td>
                    <?php if($log['is_clicked'] ?? $log['clicked'] ?? false): ?>
                        <span style="color:#065F46; font-weight:600;">✓ Ya</span>
                    <?php else: ?>
                        <span style="color:#999;">-</span>
                    <?php endif; ?>
                </td>
                <td style="color:#888; font-size:13px;"><?= date('d M Y H:i', strtotime($log['created_at'])) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>

<!-- ================================================================
     PENJELASAN METRIK (untuk laporan skripsi)
     ================================================================ -->
<div class="admin-box" style="margin-top:20px; background:#FFF8F0; border:1px solid #E8D5C4;">
    <h3 style="font-family:'Playfair Display',serif; font-size:16px; color:#2C1810; margin-bottom:14px;">
        ℹ️ Keterangan Metrik Evaluasi
    </h3>
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; font-size:13px; color:#5C3D2E;">
        <div>
            <b>RMSE (Root Mean Square Error)</b><br>
            Mengukur selisih antara prediksi rating CF dengan rating aktual user. Semakin kecil semakin baik. Formula: √(Σ(r̂ᵢ - rᵢ)² / n)
        </div>
        <div>
            <b>MAE (Mean Absolute Error)</b><br>
            Rata-rata selisih absolut prediksi vs aktual. Lebih mudah diinterpretasi daripada RMSE. Formula: Σ|r̂ᵢ - rᵢ| / n
        </div>
        <div>
            <b>Coverage</b><br>
            Persentase produk yang setidaknya sekali masuk ke daftar rekomendasi. Mengukur keberagaman sistem. Formula: |I_rec| / |I_total|
        </div>
        <div>
            <b>Precision@N</b><br>
            Proporsi rekomendasi yang relevan (diklik) dari N item yang ditampilkan. Formula: |diklik| / |direkomendasikan|
        </div>
        <div>
            <b>CTR (Click-Through Rate)</b><br>
            Persentase rekomendasi yang diklik user. Indikator relevansi dari perspektif interaksi nyata.
        </div>
        <div>
            <b>Metode Hybrid</b><br>
            Score = (0.6 × CF) + (0.4 × CBF). Cold start user baru → Popularity. User minim data CF → CBF saja.
        </div>
        <div>
            <b>Recall@N</b><br>
            Proporsi produk relevan (yang dibeli user) yang berhasil masuk ke daftar rekomendasi. Formula: |Rec ∩ Beli| / |Beli|. Ground truth = pembelian lunas.
        </div>
        <div>
            <b>F1-Score</b><br>
            Rata-rata harmonik dari Precision dan Recall — menyeimbangkan keduanya dalam satu angka. Formula: 2 × P × R / (P + R).
        </div>
    </div>
</div>

<script>
function exportData(format) {
    var btn = event.target;
    var orig = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '⏳ Mengekspor...';

    var form = new FormData();
    form.append('format', format);
    form.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

    fetch('/admin/recommendation/export', { method: 'POST', body: form })
        .then(r => r.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = orig;
            if (data.success) {
                var a = document.createElement('a');
                a.href = data.download_link;
                a.download = data.file_name;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
            } else {
                alert('Export gagal: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = orig;
            alert('Error: ' + err);
        });
}
</script>

<?= $this->endSection() ?>
