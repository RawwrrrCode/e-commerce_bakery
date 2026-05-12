<?= $this->extend('layout/admin') ?>
<?= $this->section('content') ?>

<div class="page-header" style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:12px; margin-bottom:20px;">
    <div>
        <h1>📦 Data Pesanan</h1>
        <p>Kelola dan perbarui status semua pesanan</p>
    </div>
    <a href="<?= base_url('admin/orders/export?' . http_build_query(['search'=>$search,'status'=>$status,'date_from'=>$dateFrom,'date_to'=>$dateTo])) ?>"
       style="background:#27AE60; color:white; padding:9px 18px; border-radius:8px; font-size:13px; font-weight:700; text-decoration:none; display:flex; align-items:center; gap:6px;">
        📥 Export Excel
    </a>
</div>

<!-- FILTER FORM -->
<div class="admin-box" style="padding:16px 20px; margin-bottom:16px;">
    <form method="get" action="<?= base_url('admin/orders') ?>" style="display:flex; flex-wrap:wrap; gap:10px; align-items:flex-end;">
        <div style="flex:1; min-width:180px;">
            <label style="display:block; font-size:11px; color:#888; font-weight:600; margin-bottom:4px; text-transform:uppercase; letter-spacing:.5px;">Cari Pembeli / ID</label>
            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>"
                   placeholder="Nama, email, atau ID..."
                   style="width:100%; padding:8px 12px; border:1px solid #E0D5CC; border-radius:7px; font-size:13px; box-sizing:border-box; outline:none;">
        </div>
        <div style="min-width:140px;">
            <label style="display:block; font-size:11px; color:#888; font-weight:600; margin-bottom:4px; text-transform:uppercase; letter-spacing:.5px;">Status</label>
            <select name="status" style="width:100%; padding:8px 12px; border:1px solid #E0D5CC; border-radius:7px; font-size:13px; background:white; outline:none;">
                <option value="">Semua Status</option>
                <option value="pending"    <?= $status==='pending'    ?'selected':'' ?>>Pending</option>
                <option value="processing" <?= $status==='processing' ?'selected':'' ?>>Diproses</option>
                <option value="shipped"    <?= $status==='shipped'    ?'selected':'' ?>>Dikirim</option>
                <option value="selesai"    <?= $status==='selesai'    ?'selected':'' ?>>Selesai</option>
            </select>
        </div>
        <div style="min-width:140px;">
            <label style="display:block; font-size:11px; color:#888; font-weight:600; margin-bottom:4px; text-transform:uppercase; letter-spacing:.5px;">Dari Tanggal</label>
            <input type="date" name="date_from" value="<?= htmlspecialchars($dateFrom) ?>"
                   style="width:100%; padding:8px 12px; border:1px solid #E0D5CC; border-radius:7px; font-size:13px; box-sizing:border-box; outline:none;">
        </div>
        <div style="min-width:140px;">
            <label style="display:block; font-size:11px; color:#888; font-weight:600; margin-bottom:4px; text-transform:uppercase; letter-spacing:.5px;">Sampai Tanggal</label>
            <input type="date" name="date_to" value="<?= htmlspecialchars($dateTo) ?>"
                   style="width:100%; padding:8px 12px; border:1px solid #E0D5CC; border-radius:7px; font-size:13px; box-sizing:border-box; outline:none;">
        </div>
        <div style="display:flex; gap:8px;">
            <button type="submit"
                    style="background:#3C2A1E; color:white; border:none; padding:9px 20px; border-radius:7px; font-size:13px; font-weight:700; cursor:pointer;">
                🔍 Cari
            </button>
            <a href="<?= base_url('admin/orders') ?>"
               style="background:#F0E8E0; color:#3C2A1E; border:none; padding:9px 16px; border-radius:7px; font-size:13px; font-weight:600; text-decoration:none; display:flex; align-items:center;">
                ✕ Reset
            </a>
        </div>
    </form>
</div>

<!-- RESULT INFO -->
<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px; font-size:13px; color:#888;">
    <span>
        Menampilkan <strong style="color:#3C2A1E;"><?= count($orders) ?></strong> dari <strong style="color:#3C2A1E;"><?= $total ?></strong> pesanan
        <?php if ($search !== '' || $status !== '' || $dateFrom !== '' || $dateTo !== ''): ?>
            <span style="color:#C1121F;">(difilter)</span>
        <?php endif; ?>
    </span>
    <span>Halaman <?= $currentPage ?> / <?= $totalPages ?></span>
</div>

<div class="admin-box">

    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>User</th>
                <th>Total</th>
                <th>Pembayaran</th>
                <th>Status</th>
                <th>Tanggal</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $statusLabel = ['pending' => '📋 Pending', 'processing' => '🔄 Diproses', 'shipped' => '🚚 Dikirim', 'selesai' => '✓ Selesai'];
        $statusColor = ['pending' => '#E67E22', 'processing' => '#3498db', 'shipped' => '#8B5CF6', 'selesai' => '#059669'];
        $payLabel    = ['unpaid' => '⏳ Belum', 'paid' => '✅ Lunas', 'failed' => '❌ Gagal', 'expired' => '⌛ Expired'];
        $payColor    = ['unpaid' => '#E67E22', 'paid' => '#059669', 'failed' => '#DC2626', 'expired' => '#9CA3AF'];
        ?>
        <?php if (empty($orders)): ?>
        <tr><td colspan="7" style="padding:40px; text-align:center; color:#bbb; font-size:14px;">
            Tidak ada pesanan ditemukan
        </td></tr>
        <?php endif; ?>
        <?php foreach($orders as $o): ?>
        <?php
        $st  = $o['status'];
        $pay = $o['payment_status'] ?? 'unpaid';
        ?>
        <tr id="row<?= $o['id'] ?>">
            <td style="font-weight:600;">#<?= str_pad($o['id'], 4, '0', STR_PAD_LEFT) ?></td>
            <td>
                <div style="font-weight:600; font-size:13px;"><?= htmlspecialchars($o['user_name'] ?? '–') ?></div>
                <div style="font-size:11px; color:#aaa;"><?= htmlspecialchars($o['user_email'] ?? '') ?></div>
            </td>
            <td>Rp <?= number_format($o['total'], 0, ',', '.') ?></td>
            <td>
                <span style="font-size:12px; font-weight:600; color:<?= $payColor[$pay] ?? '#888' ?>;">
                    <?= $payLabel[$pay] ?? $pay ?>
                </span>
            </td>
            <td>
                <span id="badge<?= $o['id'] ?>"
                      style="font-size:12px; font-weight:600; color:<?= $statusColor[$st] ?? '#333' ?>;">
                    <?= $statusLabel[$st] ?? $st ?>
                </span>
            </td>
            <td style="font-size:13px; color:#888;">
                <?= isset($o['created_at']) ? date('d M Y', strtotime($o['created_at'])) : '—' ?>
            </td>
            <td id="action<?= $o['id'] ?>" style="display:flex; gap:8px; align-items:center; flex-wrap:wrap;">
                <a href="/admin/orders/<?= $o['id'] ?>"
                   style="background:#3C2A1E; color:white; padding:6px 14px; border-radius:6px; font-size:12px; font-weight:600; text-decoration:none;">
                    Detail
                </a>
                <?php if ($st === 'pending'): ?>
                    <button onclick="updateOrder(<?= $o['id'] ?>, 'processing')"
                            style="background:#3498db; color:white; border:none; padding:6px 14px; border-radius:6px; font-size:12px; font-weight:600; cursor:pointer;">
                        Proses
                    </button>
                <?php elseif ($st === 'processing'): ?>
                    <button onclick="updateOrder(<?= $o['id'] ?>, 'shipped')"
                            style="background:#8B5CF6; color:white; border:none; padding:6px 14px; border-radius:6px; font-size:12px; font-weight:600; cursor:pointer;">
                        Kirim
                    </button>
                <?php elseif ($st === 'shipped'): ?>
                    <span style="font-size:11px; color:#8B5CF6; font-weight:600;">Menunggu<br>konfirmasi</span>
                <?php else: ?>
                    <span style="font-size:13px; color:#bbb;">—</span>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

</div>

<!-- PAGINATION -->
<?php if ($totalPages > 1): ?>
<div style="display:flex; justify-content:center; align-items:center; gap:6px; margin-top:20px; flex-wrap:wrap;">
    <?php
    $queryParams = array_filter(['search'=>$search,'status'=>$status,'date_from'=>$dateFrom,'date_to'=>$dateTo]);
    $buildUrl = function($p) use ($queryParams) {
        return base_url('admin/orders?' . http_build_query(array_merge($queryParams, ['page'=>$p])));
    };
    ?>
    <!-- Prev -->
    <?php if ($currentPage > 1): ?>
    <a href="<?= $buildUrl($currentPage - 1) ?>"
       style="padding:7px 14px; border:1px solid #E0D5CC; border-radius:6px; font-size:13px; color:#3C2A1E; text-decoration:none; font-weight:600;">
        ← Prev
    </a>
    <?php endif; ?>

    <!-- Page numbers -->
    <?php
    $start = max(1, $currentPage - 2);
    $end   = min($totalPages, $currentPage + 2);
    if ($start > 1): ?>
        <a href="<?= $buildUrl(1) ?>" style="padding:7px 12px; border:1px solid #E0D5CC; border-radius:6px; font-size:13px; color:#3C2A1E; text-decoration:none;">1</a>
        <?php if ($start > 2): ?><span style="padding:7px 4px; color:#bbb;">…</span><?php endif; ?>
    <?php endif; ?>
    <?php for ($p = $start; $p <= $end; $p++): ?>
    <a href="<?= $buildUrl($p) ?>"
       style="padding:7px 12px; border:1px solid <?= $p===$currentPage?'#3C2A1E':'#E0D5CC' ?>; border-radius:6px; font-size:13px;
              background:<?= $p===$currentPage?'#3C2A1E':'white' ?>; color:<?= $p===$currentPage?'white':'#3C2A1E' ?>;
              text-decoration:none; font-weight:<?= $p===$currentPage?'700':'400' ?>;">
        <?= $p ?>
    </a>
    <?php endfor; ?>
    <?php if ($end < $totalPages): ?>
        <?php if ($end < $totalPages - 1): ?><span style="padding:7px 4px; color:#bbb;">…</span><?php endif; ?>
        <a href="<?= $buildUrl($totalPages) ?>" style="padding:7px 12px; border:1px solid #E0D5CC; border-radius:6px; font-size:13px; color:#3C2A1E; text-decoration:none;"><?= $totalPages ?></a>
    <?php endif; ?>

    <!-- Next -->
    <?php if ($currentPage < $totalPages): ?>
    <a href="<?= $buildUrl($currentPage + 1) ?>"
       style="padding:7px 14px; border:1px solid #E0D5CC; border-radius:6px; font-size:13px; color:#3C2A1E; text-decoration:none; font-weight:600;">
        Next →
    </a>
    <?php endif; ?>
</div>
<?php endif; ?>

<script>
var statusLabel = { processing:'🔄 Diproses', shipped:'🚚 Dikirim', selesai:'✓ Selesai' };
var statusColor = { processing:'#3498db', shipped:'#8B5CF6', selesai:'#059669' };
var nextAction  = {
    processing: { next:'shipped', label:'Kirim', color:'#8B5CF6' },
    shipped:    null,
    selesai:    null
};

function updateOrder(id, status) {
    var csrfName  = '<?= csrf_token() ?>';
    var csrfValue = '<?= csrf_hash() ?>';
    var body = 'id=' + id + '&status=' + status + '&' + csrfName + '=' + csrfValue;

    fetch('<?= base_url('admin/orders/update') ?>', {
        method: 'POST',
        credentials: 'same-origin',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: body
    })
    .then(function(r){ return r.json(); })
    .then(function(data) {
        if (data.status !== 'ok') return;
        var ns = data.new_status;

        var badge = document.getElementById('badge' + id);
        badge.textContent = statusLabel[ns] || ns;
        badge.style.color = statusColor[ns] || '#333';

        var action = document.getElementById('action' + id);
        var next   = nextAction[ns];
        var detailBtn = '<a href="/admin/orders/' + id + '" style="background:#3C2A1E; color:white; padding:6px 14px; border-radius:6px; font-size:12px; font-weight:600; text-decoration:none;">Detail</a>';
        if (next) {
            action.innerHTML = detailBtn + ' <button onclick="updateOrder(' + id + ', \'' + next.next + '\')" '
                + 'style="background:' + next.color + '; color:white; border:none; padding:6px 14px; border-radius:6px; font-size:12px; font-weight:600; cursor:pointer;">'
                + next.label + '</button>';
        } else if (ns === 'shipped') {
            action.innerHTML = detailBtn + ' <span style="font-size:11px; color:#8B5CF6; font-weight:600;">Menunggu<br>konfirmasi</span>';
        } else {
            action.innerHTML = detailBtn + ' <span style="font-size:13px; color:#bbb;">—</span>';
        }
    });
}
</script>

<?= $this->endSection() ?>
