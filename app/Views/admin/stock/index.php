<?= $this->extend('layout/admin') ?>
<?= $this->section('content') ?>

<div class="admin-box" style="max-width:860px;">

    <h2 style="margin-bottom:6px;">Kelola Stok Produk</h2>
    <p style="font-size:13px; color:#888; margin-bottom:28px;">Perbarui jumlah stok tersedia untuk setiap produk.</p>

    <div id="toast-stock" style="display:none; background:#10B981; color:white; padding:10px 18px; border-radius:8px; margin-bottom:16px; font-size:14px;"></div>

    <table style="width:100%; border-collapse:collapse; font-size:14px;">
        <thead>
            <tr style="background:#F3EDE8; text-align:left;">
                <th style="padding:12px 14px; border-bottom:2px solid #E8D5C4;">Gambar</th>
                <th style="padding:12px 14px; border-bottom:2px solid #E8D5C4;">Nama Produk</th>
                <th style="padding:12px 14px; border-bottom:2px solid #E8D5C4;">Kategori</th>
                <th style="padding:12px 14px; border-bottom:2px solid #E8D5C4; text-align:center;">Stok Sekarang</th>
                <th style="padding:12px 14px; border-bottom:2px solid #E8D5C4; text-align:center;">Ubah Stok</th>
                <th style="padding:12px 14px; border-bottom:2px solid #E8D5C4;"></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $p): ?>
            <tr style="border-bottom:1px solid #F0E8E0;" id="row-<?= $p['id'] ?>">
                <td style="padding:10px 14px;">
                    <img src="/uploads/<?= htmlspecialchars($p['image']) ?>"
                         style="width:48px; height:48px; object-fit:cover; border-radius:8px; border:1px solid #E8D5C4;">
                </td>
                <td style="padding:10px 14px; font-weight:500; color:#3C2A1E;">
                    <?= htmlspecialchars($p['name']) ?>
                </td>
                <td style="padding:10px 14px; color:#888;">
                    <?= htmlspecialchars($p['category'] ?? '-') ?>
                </td>
                <td style="padding:10px 14px; text-align:center;">
                    <?php if (($p['stock'] ?? 1) <= 0): ?>
                        <span style="background:#FEE2E2; color:#991B1B; padding:3px 10px; border-radius:20px; font-size:12px; font-weight:600;">Habis</span>
                    <?php else: ?>
                        <span id="stock-display-<?= $p['id'] ?>" style="background:#D1FAE5; color:#065F46; padding:3px 10px; border-radius:20px; font-size:12px; font-weight:600;"><?= $p['stock'] ?></span>
                    <?php endif; ?>
                </td>
                <td style="padding:10px 14px; text-align:center;">
                    <input type="number" id="stock-input-<?= $p['id'] ?>"
                           value="<?= $p['stock'] ?? 0 ?>" min="0"
                           style="width:80px; padding:6px 10px; border:1.5px solid #E8D5C4; border-radius:8px; font-size:14px; text-align:center; font-family:'Poppins',sans-serif;">
                </td>
                <td style="padding:10px 14px;">
                    <button onclick="saveStock(<?= $p['id'] ?>)"
                            style="background:#C1121F; color:white; border:none; padding:7px 18px; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer; font-family:'Poppins',sans-serif;">
                        Simpan
                    </button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</div>

<script>
function saveStock(productId) {
    const input = document.getElementById('stock-input-' + productId);
    const stock = parseInt(input.value);

    if (isNaN(stock) || stock < 0) {
        alert('Masukkan jumlah stok yang valid (minimal 0).');
        return;
    }

    fetch('/admin/stock/update', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'product_id=' + productId + '&stock=' + stock
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const display = document.getElementById('stock-display-' + productId);
            if (display) {
                display.textContent = data.stock;
                display.style.background = data.stock <= 0 ? '#FEE2E2' : '#D1FAE5';
                display.style.color      = data.stock <= 0 ? '#991B1B' : '#065F46';
            }
            showToastStock('✓ Stok produk berhasil diperbarui');
        }
    });
}

function showToastStock(msg) {
    const t = document.getElementById('toast-stock');
    t.textContent = msg;
    t.style.display = 'block';
    setTimeout(() => { t.style.display = 'none'; }, 3000);
}
</script>

<?= $this->endSection() ?>
