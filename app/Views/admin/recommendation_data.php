<?= $this->extend('layout/admin') ?>
<?= $this->section('content') ?>

<div class="page-header" style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:12px; margin-bottom:28px;">
    <div>
        <h1>🗂️ Data Input Sistem Rekomendasi</h1>
        <p>Data rating dan interaksi user yang digunakan sebagai input algoritma Hybrid Filtering</p>
    </div>
    <a href="/admin/recommendation" style="background:#3C2A1E; color:white; padding:10px 18px; border-radius:8px; font-size:13px; font-weight:600; text-decoration:none;">
        ← Kembali ke Evaluasi
    </a>
</div>

<!-- ================================================================
     PARAMETER HYBRID
     ================================================================ -->
<div class="admin-box" style="margin-bottom:28px; border-left:4px solid #C1121F;">
    <h3 style="font-size:16px; font-weight:700; color:#3C2A1E; margin-bottom:6px;">⚙️ Konfigurasi Parameter Hybrid Filtering</h3>
    <p style="font-size:13px; color:#888; margin-bottom:20px;">
        Atur bobot α (Collaborative Filtering) dan β (Content-Based Filtering).<br>
        Rumus: <code style="background:#F0E8E0; padding:2px 8px; border-radius:4px; font-size:12px;">Hybrid Score = (α × CF Score) + (β × CBF Score)</code>, di mana α + β = 1
    </p>

    <div style="display:grid; grid-template-columns:1fr 1fr; gap:32px; align-items:start;">
        <div>
            <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
                <label style="font-weight:700; font-size:14px; color:#3C2A1E;">α — Collaborative Filtering</label>
                <span id="cfVal" style="font-weight:700; color:#C1121F; font-size:16px;"><?= $weights['cf'] ?></span>
            </div>
            <input type="range" id="cfSlider" min="10" max="90" step="5"
                   value="<?= $weights['cf'] * 100 ?>"
                   oninput="updateWeights(this.value)"
                   style="width:100%; accent-color:#C1121F; cursor:pointer;">
            <div style="display:flex; justify-content:space-between; font-size:11px; color:#aaa; margin-top:4px;">
                <span>0.1 (CBF dominan)</span>
                <span>0.9 (CF dominan)</span>
            </div>

            <div style="margin-top:16px; padding:14px 16px; background:#FFF8F0; border-radius:10px; border:1px solid #E8D5C4;">
                <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
                    <span style="font-size:13px; color:#555;">α (CF Weight)</span>
                    <strong id="cfDisplay" style="color:#C1121F;"><?= $weights['cf'] ?></strong>
                </div>
                <div style="display:flex; justify-content:space-between;">
                    <span style="font-size:13px; color:#555;">β (CBF Weight)</span>
                    <strong id="cbfDisplay" style="color:#3498db;"><?= $weights['cbf'] ?></strong>
                </div>
                <div style="height:1px; background:#E8D5C4; margin:10px 0;"></div>
                <div style="display:flex; justify-content:space-between;">
                    <span style="font-size:12px; color:#888;">Total</span>
                    <span style="font-size:12px; font-weight:700; color:#059669;">1.00 ✓</span>
                </div>
            </div>

            <button onclick="saveConfig()" id="btnSave"
                    style="margin-top:14px; background:#C1121F; color:white; border:none; padding:10px 24px; border-radius:8px; font-size:13px; font-weight:700; cursor:pointer; width:100%;">
                💾 Simpan Konfigurasi
            </button>
            <p id="saveMsg" style="display:none; font-size:12px; color:#059669; text-align:center; margin-top:8px; font-weight:600;">✓ Konfigurasi berhasil disimpan!</p>
        </div>

        <div>
            <p style="font-size:13px; font-weight:700; color:#3C2A1E; margin-bottom:12px;">Visualisasi Bobot</p>
            <div style="background:#F5EDE8; border-radius:10px; overflow:hidden; height:36px; display:flex;">
                <div id="cfBar" style="background:#C1121F; height:100%; display:flex; align-items:center; justify-content:center; color:white; font-size:12px; font-weight:700; transition:width .3s; width:<?= $weights['cf'] * 100 ?>%;">
                    CF <?= $weights['cf'] * 100 ?>%
                </div>
                <div id="cbfBar" style="background:#3498db; height:100%; display:flex; align-items:center; justify-content:center; color:white; font-size:12px; font-weight:700; transition:width .3s; width:<?= $weights['cbf'] * 100 ?>%;">
                    CBF <?= $weights['cbf'] * 100 ?>%
                </div>
            </div>

            <div style="margin-top:20px; font-size:13px; color:#666; line-height:1.8;">
                <p style="font-weight:700; color:#3C2A1E; margin-bottom:8px;">Panduan Pengaturan:</p>
                <p>🔴 <strong>α tinggi (0.7–0.9):</strong> CF lebih dominan — cocok jika data rating banyak</p>
                <p>🔵 <strong>β tinggi (0.7–0.9):</strong> CBF lebih dominan — cocok untuk user baru</p>
                <p>⚖️ <strong>α=0.6, β=0.4:</strong> Nilai default yang direkomendasikan</p>
            </div>
        </div>
    </div>
</div>

<!-- ================================================================
     STATISTIK DATA INPUT
     ================================================================ -->
<div style="display:grid; grid-template-columns:repeat(4,1fr); gap:16px; margin-bottom:28px;">
    <div class="card-box" style="border-top-color:#C1121F;">
        <div class="card-icon">⭐</div>
        <h3><?= number_format($totalRatings) ?></h3>
        <p>Total Data Rating</p>
        <small style="color:#999;">Input utama CF</small>
    </div>
    <div class="card-box" style="border-top-color:#C8860A;">
        <div class="card-icon">📊</div>
        <h3><?= $avgRating ?></h3>
        <p>Rata-rata Rating</p>
        <small style="color:#999;">Skala 1–5</small>
    </div>
    <div class="card-box" style="border-top-color:#2ecc71;">
        <div class="card-icon">🛒</div>
        <h3><?= number_format($totalPurchases) ?></h3>
        <p>Total Pembelian</p>
        <small style="color:#999;">Input interaksi CF</small>
    </div>
    <div class="card-box" style="border-top-color:#3498db;">
        <div class="card-icon">👥</div>
        <h3><?= number_format($totalUsers) ?></h3>
        <p>User Memberi Rating</p>
        <small style="color:#999;">Aktif di sistem</small>
    </div>
</div>

<!-- ================================================================
     TABS
     ================================================================ -->
<div style="display:flex; border-bottom:2px solid #E8D5C4; margin-bottom:24px;">
    <button onclick="switchTab('rating')" id="tab-rating"
            style="padding:12px 24px; font-size:13px; font-weight:700; border:none; background:none; cursor:pointer; border-bottom:3px solid #C1121F; color:#C1121F; margin-bottom:-2px;">
        ⭐ Data Rating (<?= $totalRatings ?>)
    </button>
    <button onclick="switchTab('purchase')" id="tab-purchase"
            style="padding:12px 24px; font-size:13px; font-weight:700; border:none; background:none; cursor:pointer; border-bottom:3px solid transparent; color:#999; margin-bottom:-2px;">
        🛒 Data Pembelian (<?= $totalPurchases ?>)
    </button>
    <button onclick="switchTab('dist')" id="tab-dist"
            style="padding:12px 24px; font-size:13px; font-weight:700; border:none; background:none; cursor:pointer; border-bottom:3px solid transparent; color:#999; margin-bottom:-2px;">
        📊 Distribusi & Top Produk
    </button>
</div>

<!-- TAB: RATING -->
<div id="panel-rating">
    <div class="admin-box" style="padding:0; overflow:hidden;">
        <table class="admin-table" style="margin:0;">
            <thead>
                <tr>
                    <th>No</th>
                    <th>User</th>
                    <th>Produk</th>
                    <th>Kategori</th>
                    <th style="text-align:center;">Rating</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($ratings)): ?>
                <tr><td colspan="6" style="text-align:center; color:#999; padding:40px;">Belum ada data rating</td></tr>
                <?php else: ?>
                <?php foreach ($ratings as $i => $r): ?>
                <tr>
                    <td style="color:#bbb;"><?= $i + 1 ?></td>
                    <td style="font-weight:600;"><?= htmlspecialchars($r['user_name'] ?? '–') ?></td>
                    <td><?= htmlspecialchars($r['product_name'] ?? '–') ?></td>
                    <td><span style="background:#FFF8F0; color:#C8860A; padding:2px 8px; border-radius:10px; font-size:11px; font-weight:600;"><?= htmlspecialchars($r['category'] ?? '–') ?></span></td>
                    <td style="text-align:center;">
                        <span style="color:#F59E0B; font-size:14px;">
                            <?php for ($s = 1; $s <= 5; $s++) echo $s <= $r['rating'] ? '★' : '☆'; ?>
                        </span>
                        <strong style="color:#333; margin-left:4px;"><?= $r['rating'] ?></strong>
                    </td>
                    <td style="color:#888; font-size:12px;"><?= date('d M Y, H:i', strtotime($r['created_at'])) ?></td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if ($totalRatings > 100): ?>
    <p style="text-align:center; font-size:12px; color:#aaa; margin-top:12px;">Menampilkan 100 data terbaru dari <?= number_format($totalRatings) ?> total data rating</p>
    <?php endif; ?>
</div>

<!-- TAB: PEMBELIAN -->
<div id="panel-purchase" style="display:none;">
    <div class="admin-box" style="padding:0; overflow:hidden;">
        <table class="admin-table" style="margin:0;">
            <thead>
                <tr>
                    <th>No</th>
                    <th>User</th>
                    <th>Produk</th>
                    <th>Kategori</th>
                    <th style="text-align:center;">Qty</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($purchases)): ?>
                <tr><td colspan="7" style="text-align:center; color:#999; padding:40px;">Belum ada data pembelian</td></tr>
                <?php else: ?>
                <?php foreach ($purchases as $i => $p): ?>
                <tr>
                    <td style="color:#bbb;"><?= $i + 1 ?></td>
                    <td style="font-weight:600;"><?= htmlspecialchars($p['user_name'] ?? '–') ?></td>
                    <td><?= htmlspecialchars($p['product_name'] ?? '–') ?></td>
                    <td><span style="background:#FFF8F0; color:#C8860A; padding:2px 8px; border-radius:10px; font-size:11px; font-weight:600;"><?= htmlspecialchars($p['category'] ?? '–') ?></span></td>
                    <td style="text-align:center; font-weight:700;"><?= $p['qty'] ?></td>
                    <td><span style="background:#D1FAE5; color:#065F46; padding:2px 8px; border-radius:10px; font-size:11px; font-weight:700;">Lunas</span></td>
                    <td style="color:#888; font-size:12px;"><?= date('d M Y, H:i', strtotime($p['created_at'])) ?></td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if ($totalPurchases > 100): ?>
    <p style="text-align:center; font-size:12px; color:#aaa; margin-top:12px;">Menampilkan 100 data terbaru dari <?= number_format($totalPurchases) ?> total pembelian</p>
    <?php endif; ?>
</div>

<!-- TAB: DISTRIBUSI -->
<div id="panel-dist" style="display:none;">
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">

        <!-- Distribusi Rating -->
        <div class="admin-box">
            <h3 style="font-size:15px; font-weight:700; color:#3C2A1E; margin-bottom:16px;">📊 Distribusi Rating (1–5 Bintang)</h3>
            <?php
            $maxDist = max(array_column($ratingDist, 'total') ?: [1]);
            $stars = ['','★','★★','★★★','★★★★','★★★★★'];
            $colors = ['','#EF4444','#F97316','#EAB308','#22C55E','#10B981'];
            for ($bintang = 5; $bintang >= 1; $bintang--):
                $found = array_filter($ratingDist, fn($r) => (int)$r['rating'] === $bintang);
                $total = $found ? reset($found)['total'] : 0;
                $pct = $maxDist > 0 ? round($total / $maxDist * 100) : 0;
            ?>
            <div style="display:flex; align-items:center; gap:10px; margin-bottom:10px;">
                <span style="color:#F59E0B; font-size:13px; width:60px;"><?= $stars[$bintang] ?></span>
                <div style="flex:1; background:#F5EDE8; border-radius:4px; height:20px; overflow:hidden;">
                    <div style="background:<?= $colors[$bintang] ?>; width:<?= $pct ?>%; height:100%; border-radius:4px; transition:width .5s;"></div>
                </div>
                <span style="font-size:13px; font-weight:700; color:#333; width:30px; text-align:right;"><?= $total ?></span>
            </div>
            <?php endfor; ?>
        </div>

        <!-- Top Produk -->
        <div class="admin-box">
            <h3 style="font-size:15px; font-weight:700; color:#3C2A1E; margin-bottom:16px;">🏆 Top 10 Produk Paling Dirating</h3>
            <?php if (empty($topRated)): ?>
                <p style="color:#999; text-align:center; padding:20px;">Belum ada data</p>
            <?php else: ?>
            <table style="width:100%; font-size:13px; border-collapse:collapse;">
                <thead>
                    <tr style="background:#FFF8F0;">
                        <th style="padding:8px 10px; text-align:left; color:#888; font-size:11px; text-transform:uppercase; letter-spacing:.5px;">Produk</th>
                        <th style="padding:8px 10px; text-align:center; color:#888; font-size:11px;">Rating</th>
                        <th style="padding:8px 10px; text-align:center; color:#888; font-size:11px;">Avg</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($topRated as $i => $t): ?>
                <tr style="border-bottom:1px solid #F5EDE8;">
                    <td style="padding:8px 10px;">
                        <span style="color:#bbb; margin-right:6px;"><?= $i+1 ?>.</span>
                        <?= htmlspecialchars($t['name']) ?>
                    </td>
                    <td style="padding:8px 10px; text-align:center; font-weight:700;"><?= $t['jml_rating'] ?></td>
                    <td style="padding:8px 10px; text-align:center;">
                        <span style="color:#F59E0B;">★</span> <?= $t['avg_rating'] ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function switchTab(tab) {
    ['rating','purchase','dist'].forEach(t => {
        document.getElementById('panel-' + t).style.display = t === tab ? 'block' : 'none';
        const btn = document.getElementById('tab-' + t);
        btn.style.borderBottomColor = t === tab ? '#C1121F' : 'transparent';
        btn.style.color = t === tab ? '#C1121F' : '#999';
    });
}

function updateWeights(val) {
    const cf  = (val / 100).toFixed(2);
    const cbf = (1 - val / 100).toFixed(2);
    document.getElementById('cfVal').textContent     = cf;
    document.getElementById('cfDisplay').textContent  = cf;
    document.getElementById('cbfDisplay').textContent = cbf;
    document.getElementById('cfBar').style.width      = val + '%';
    document.getElementById('cfBar').textContent      = 'CF ' + val + '%';
    document.getElementById('cbfBar').style.width     = (100 - val) + '%';
    document.getElementById('cbfBar').textContent     = 'CBF ' + (100 - val) + '%';
}

function saveConfig() {
    const val = document.getElementById('cfSlider').value;
    const btn = document.getElementById('btnSave');
    btn.disabled = true;
    btn.textContent = 'Menyimpan...';

    fetch('/admin/recommendation/config', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'weight_cf=' + (val / 100)
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const msg = document.getElementById('saveMsg');
            msg.style.display = 'block';
            msg.textContent = '✓ Konfigurasi disimpan! α=' + data.weight_cf + ', β=' + data.weight_cbf;
            setTimeout(() => msg.style.display = 'none', 3000);
        }
    })
    .finally(() => {
        btn.disabled = false;
        btn.textContent = '💾 Simpan Konfigurasi';
    });
}
</script>

<?= $this->endSection() ?>
