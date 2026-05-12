<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Evaluasi Sistem Rekomendasi</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Times New Roman', serif; font-size: 12pt; color: #000; background: #fff; }

        .cover { text-align: center; padding: 60px 40px; border-bottom: 3px double #000; margin-bottom: 30px; }
        .cover h1 { font-size: 16pt; font-weight: bold; margin-bottom: 8px; text-transform: uppercase; }
        .cover h2 { font-size: 13pt; font-weight: normal; margin-bottom: 20px; }
        .cover .logo { font-size: 48pt; margin-bottom: 16px; }
        .cover .meta { font-size: 10pt; color: #555; margin-top: 20px; }

        .section { margin-bottom: 28px; page-break-inside: avoid; }
        .section-title {
            font-size: 13pt; font-weight: bold; color: #fff;
            background: #8B1A1A; padding: 8px 14px;
            margin-bottom: 12px; border-radius: 4px;
        }

        table { width: 100%; border-collapse: collapse; font-size: 11pt; margin-bottom: 12px; }
        th { background: #f0e8e0; padding: 8px 12px; text-align: center; border: 1px solid #ccc; font-weight: bold; }
        td { padding: 7px 12px; border: 1px solid #ccc; }
        tr:nth-child(even) td { background: #faf7f4; }

        .metric-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; margin-bottom: 16px; }
        .metric-card {
            border: 1px solid #ddd; border-radius: 6px; padding: 14px 16px;
            text-align: center; background: #fffaf7;
        }
        .metric-card .val { font-size: 22pt; font-weight: bold; color: #8B1A1A; }
        .metric-card .lbl { font-size: 9pt; color: #666; margin-top: 4px; }

        .formula-box {
            background: #F9F5F1; border-left: 4px solid #8B1A1A;
            padding: 10px 14px; font-size: 10pt; margin: 10px 0;
            font-family: 'Courier New', monospace;
        }

        .badge { display: inline-block; padding: 2px 10px; border-radius: 20px; font-size: 9pt; font-weight: bold; }
        .badge-hybrid { background: #fde8e8; color: #8B1A1A; }
        .badge-cf     { background: #e8f0fe; color: #1a4fd4; }
        .badge-cbf    { background: #fef3e8; color: #a05a00; }

        .note { font-size: 9pt; color: #666; font-style: italic; margin-top: 6px; }
        .highlight { font-weight: bold; color: #8B1A1A; }

        .footer { margin-top: 40px; padding-top: 14px; border-top: 1px solid #ccc; font-size: 9pt; color: #888; text-align: center; }

        @media print {
            body { margin: 0; }
            .no-print { display: none !important; }
            .section { page-break-inside: avoid; }
        }
    </style>
</head>
<body>

<!-- TOMBOL PRINT (hilang saat print) -->
<div class="no-print" style="position:fixed; top:16px; right:20px; z-index:999; display:flex; gap:8px; align-items:center;">
    <button onclick="window.print()"
            style="background:#8B1A1A; color:white; border:none; padding:10px 22px; border-radius:8px; font-size:13px; font-weight:700; cursor:pointer; box-shadow:0 2px 8px rgba(0,0,0,.2);">
        🖨️ Print / Save PDF
    </button>
    <button onclick="exportFile('CSV')"
            style="background:#27AE60; color:white; border:none; padding:10px 18px; border-radius:8px; font-size:13px; font-weight:700; cursor:pointer; box-shadow:0 2px 8px rgba(0,0,0,.2);">
        📥 CSV
    </button>
    <button onclick="exportFile('JSON')"
            style="background:#2980B9; color:white; border:none; padding:10px 18px; border-radius:8px; font-size:13px; font-weight:700; cursor:pointer; box-shadow:0 2px 8px rgba(0,0,0,.2);">
        📋 JSON
    </button>
    <button onclick="window.close()"
            style="background:#666; color:white; border:none; padding:10px 14px; border-radius:8px; font-size:13px; cursor:pointer;">
        ✕ Tutup
    </button>
</div>

<script>
function exportFile(format) {
    var btn = event.target;
    var orig = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '⏳';

    var form = new FormData();
    form.append('format', format);

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
        .catch(err => { btn.disabled = false; btn.innerHTML = orig; alert('Error: ' + err); });
}
</script>

<!-- COVER -->
<div class="cover">
    <div class="logo">🍞</div>
    <h1>Laporan Evaluasi Sistem Rekomendasi</h1>
    <h2>Hybrid Filtering — E-Commerce Bakery<br>PT. Mimosa Tarte Indonesia</h2>
    <div style="margin:20px auto; width:60%; border-top:1px solid #ccc;"></div>
    <div class="meta">
        Tanggal Laporan &nbsp;:&nbsp; <?= $generatedAt ?><br>
        Metode &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp; Collaborative Filtering + Content-Based Filtering (Hybrid)<br>
        Total Pengguna &nbsp;:&nbsp; <?= $totalUsers ?> user &nbsp;|&nbsp;
        Total Rating &nbsp;&nbsp;&nbsp;:&nbsp; <?= $totalRatings ?> rating
    </div>
</div>

<!-- ── 1. RINGKASAN METRIK ── -->
<div class="section">
    <div class="section-title">1. Ringkasan Metrik Evaluasi</div>

    <div class="metric-grid">
        <div class="metric-card">
            <div class="val"><?= $metrics['rmse'] ?: 'N/A' ?></div>
            <div class="lbl">RMSE<br>(Root Mean Square Error)</div>
        </div>
        <div class="metric-card">
            <div class="val"><?= $metrics['mae'] ?: 'N/A' ?></div>
            <div class="lbl">MAE<br>(Mean Absolute Error)</div>
        </div>
        <div class="metric-card">
            <div class="val"><?= $metrics['coverage'] ?>%</div>
            <div class="lbl">Coverage<br>(Cakupan Produk)</div>
        </div>
        <div class="metric-card">
            <div class="val"><?= round($metrics['precision_buy'] * 100, 2) ?>%</div>
            <div class="lbl">Precision@N<br>(Ketepatan Rekomendasi)</div>
        </div>
        <div class="metric-card">
            <div class="val"><?= round($metrics['recall'] * 100, 2) ?>%</div>
            <div class="lbl">Recall@N<br>(Cakupan Relevan)</div>
        </div>
        <div class="metric-card">
            <div class="val"><?= round($metrics['f1'] * 100, 2) ?>%</div>
            <div class="lbl">F1-Score<br>(Harmonic Mean P&R)</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Metrik</th><th>Nilai</th><th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <tr><td>RMSE</td><td class="highlight" style="text-align:center;"><?= $metrics['rmse'] ?: 'N/A' ?></td><td>Semakin kecil semakin baik (akurasi prediksi rating CF)</td></tr>
            <tr><td>MAE</td><td class="highlight" style="text-align:center;"><?= $metrics['mae'] ?: 'N/A' ?></td><td>Rata-rata selisih absolut prediksi vs aktual</td></tr>
            <tr><td>Coverage</td><td class="highlight" style="text-align:center;"><?= $metrics['coverage'] ?>%</td><td>Persentase produk yang pernah direkomendasikan</td></tr>
            <tr><td>CTR</td><td class="highlight" style="text-align:center;"><?= $metrics['ctr'] ?>%</td><td>Click-Through Rate rekomendasi</td></tr>
            <tr><td>Precision@N</td><td class="highlight" style="text-align:center;"><?= round($metrics['precision_buy'] * 100, 2) ?>%</td><td>Proporsi produk yang relevan dari yang direkomendasikan</td></tr>
            <tr><td>Recall@N</td><td class="highlight" style="text-align:center;"><?= round($metrics['recall'] * 100, 2) ?>%</td><td>Proporsi produk relevan yang berhasil ditangkap</td></tr>
            <tr><td>F1-Score</td><td class="highlight" style="text-align:center;"><?= round($metrics['f1'] * 100, 2) ?>%</td><td>Keseimbangan antara Precision dan Recall</td></tr>
            <tr><td>Total Rekomendasi</td><td style="text-align:center;"><?= number_format($metrics['total_logged']) ?></td><td>Total log rekomendasi yang tersimpan</td></tr>
            <tr><td>Total Diklik</td><td style="text-align:center;"><?= number_format($metrics['total_clicked']) ?></td><td>Jumlah rekomendasi yang diklik pengguna</td></tr>
            <tr><td>Data Uji CF</td><td style="text-align:center;"><?= $metrics['n_test'] ?> sampel</td><td>Jumlah sampel yang digunakan untuk RMSE/MAE</td></tr>
        </tbody>
    </table>
</div>

<!-- ── 2. PERBANDINGAN CF vs CBF vs HYBRID ── -->
<div class="section">
    <div class="section-title">2. Perbandingan Metode CF vs CBF vs Hybrid</div>

    <table>
        <thead>
            <tr>
                <th>Metode</th>
                <th>Precision@N</th>
                <th>Recall@N</th>
                <th>F1-Score</th>
                <th>Coverage</th>
                <th>N User</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><span class="badge badge-cf">Collaborative Filtering (CF)</span></td>
                <td style="text-align:center;"><?= round(($comparison['cf']['precision'] ?? 0) * 100, 2) ?>%</td>
                <td style="text-align:center;"><?= round(($comparison['cf']['recall']    ?? 0) * 100, 2) ?>%</td>
                <td style="text-align:center;"><?= round(($comparison['cf']['f1']        ?? 0) * 100, 2) ?>%</td>
                <td style="text-align:center;"><?= $comparison['cf']['coverage'] ?? 0 ?>%</td>
                <td style="text-align:center;"><?= $comparison['cf']['n_users']  ?? 0 ?></td>
            </tr>
            <tr>
                <td><span class="badge badge-cbf">Content-Based Filtering (CBF)</span></td>
                <td style="text-align:center;"><?= round(($comparison['cbf']['precision'] ?? 0) * 100, 2) ?>%</td>
                <td style="text-align:center;"><?= round(($comparison['cbf']['recall']    ?? 0) * 100, 2) ?>%</td>
                <td style="text-align:center;"><?= round(($comparison['cbf']['f1']        ?? 0) * 100, 2) ?>%</td>
                <td style="text-align:center;"><?= $comparison['cbf']['coverage'] ?? 0 ?>%</td>
                <td style="text-align:center;"><?= $comparison['cbf']['n_users']  ?? 0 ?></td>
            </tr>
            <tr style="background:#fef5f5;">
                <td><span class="badge badge-hybrid">⭐ Hybrid (CF + CBF)</span></td>
                <td style="text-align:center; font-weight:bold; color:#8B1A1A;"><?= round(($comparison['hybrid']['precision'] ?? 0) * 100, 2) ?>%</td>
                <td style="text-align:center; font-weight:bold; color:#8B1A1A;"><?= round(($comparison['hybrid']['recall']    ?? 0) * 100, 2) ?>%</td>
                <td style="text-align:center; font-weight:bold; color:#8B1A1A;"><?= round(($comparison['hybrid']['f1']        ?? 0) * 100, 2) ?>%</td>
                <td style="text-align:center; font-weight:bold; color:#8B1A1A;"><?= $comparison['hybrid']['coverage'] ?? 0 ?>%</td>
                <td style="text-align:center;"><?= $comparison['hybrid']['n_users'] ?? 0 ?></td>
            </tr>
        </tbody>
    </table>
    <p class="note">* Ground truth: produk yang benar-benar dibeli user (payment_status = paid). Evaluasi offline menggunakan top-<?= \App\Libraries\HybridRecommender::TOP_N ?> rekomendasi per user.</p>
</div>

<!-- ── 3. DISTRIBUSI METODE ── -->
<div class="section">
    <div class="section-title">3. Distribusi Metode yang Digunakan</div>
    <table>
        <thead>
            <tr><th>Metode</th><th>Jumlah Rekomendasi</th><th>Persentase</th></tr>
        </thead>
        <tbody>
            <?php
            $total = array_sum($metrics['method_dist'] ?? []);
            foreach ($metrics['method_dist'] ?? [] as $m => $cnt):
                $pct = $total > 0 ? round($cnt / $total * 100, 1) : 0;
            ?>
            <tr>
                <td><?= htmlspecialchars($m) ?></td>
                <td style="text-align:center;"><?= number_format($cnt) ?></td>
                <td style="text-align:center;"><?= $pct ?>%</td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($metrics['method_dist'])): ?>
            <tr><td colspan="3" style="text-align:center; color:#aaa;">Belum ada data</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- ── 4. TOP PRODUK DIREKOMENDASIKAN ── -->
<?php if (!empty($metrics['top_recommended'])): ?>
<div class="section">
    <div class="section-title">4. Top 5 Produk Paling Sering Direkomendasikan</div>
    <table>
        <thead>
            <tr><th>Rank</th><th>Nama Produk</th><th>Frekuensi Rekomendasi</th></tr>
        </thead>
        <tbody>
            <?php foreach ($metrics['top_recommended'] as $i => $p): ?>
            <tr>
                <td style="text-align:center;"><?= $i + 1 ?></td>
                <td><?= htmlspecialchars($p['name']) ?></td>
                <td style="text-align:center;"><?= number_format($p['freq']) ?> kali</td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<!-- ── 5. KETERANGAN METRIK ── -->
<div class="section">
    <div class="section-title">5. Keterangan Formula & Metrik</div>

    <table>
        <thead><tr><th>Metrik</th><th>Formula</th><th>Interpretasi</th></tr></thead>
        <tbody>
            <tr>
                <td><b>RMSE</b></td>
                <td><code>√( Σ(ŷ − y)² / n )</code></td>
                <td>Mengukur akurasi prediksi rating. Nilai kecil = lebih akurat.</td>
            </tr>
            <tr>
                <td><b>MAE</b></td>
                <td><code>Σ|ŷ − y| / n</code></td>
                <td>Rata-rata selisih absolut. Lebih robust terhadap outlier.</td>
            </tr>
            <tr>
                <td><b>Precision@N</b></td>
                <td><code>|Rec ∩ Beli| / |Rec|</code></td>
                <td>Dari N item yang direkomendasikan, berapa yang relevan (dibeli).</td>
            </tr>
            <tr>
                <td><b>Recall@N</b></td>
                <td><code>|Rec ∩ Beli| / |Beli|</code></td>
                <td>Dari semua item yang dibeli, berapa yang berhasil direkomendasikan.</td>
            </tr>
            <tr>
                <td><b>F1-Score</b></td>
                <td><code>2 × P × R / (P + R)</code></td>
                <td>Rata-rata harmonik Precision dan Recall.</td>
            </tr>
            <tr>
                <td><b>Coverage</b></td>
                <td><code>|Produk Direk.| / |Total Produk|</code></td>
                <td>Persentase katalog yang pernah muncul sebagai rekomendasi.</td>
            </tr>
            <tr>
                <td><b>Hybrid Score</b></td>
                <td><code>0.6 × CF + 0.4 × CBF</code></td>
                <td>Skor akhir gabungan dengan bobot CF 60% dan CBF 40%.</td>
            </tr>
        </tbody>
    </table>
</div>

<div class="footer">
    Laporan ini di-generate otomatis oleh Sistem Rekomendasi E-Commerce Bakery —
    PT. Mimosa Tarte Indonesia &nbsp;|&nbsp; <?= $generatedAt ?>
</div>

</body>
</html>
