<?= $this->extend('layout/admin') ?>
<?= $this->section('content') ?>

<div class="page-header" style="margin-bottom:20px;">
    <h1>⭐ Moderasi Ulasan</h1>
    <p>Lihat dan kelola ulasan produk dari pelanggan</p>
</div>

<?php if (session()->getFlashdata('success')): ?>
<div style="background:#D1FAE5; border:1px solid #6EE7B7; color:#065F46; padding:10px 16px; border-radius:8px; margin-bottom:16px; font-size:13px; font-weight:600;">
    ✓ <?= session()->getFlashdata('success') ?>
</div>
<?php endif; ?>

<!-- FILTER -->
<div class="admin-box" style="padding:14px 18px; margin-bottom:16px;">
    <form method="get" action="<?= base_url('admin/reviews') ?>" style="display:flex; gap:10px; align-items:flex-end; flex-wrap:wrap;">
        <div style="flex:1; min-width:200px;">
            <label style="display:block; font-size:11px; color:#888; font-weight:600; margin-bottom:4px; text-transform:uppercase; letter-spacing:.5px;">Cari Ulasan</label>
            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>"
                   placeholder="Nama user, produk, atau isi ulasan..."
                   style="width:100%; padding:8px 12px; border:1px solid #E0D5CC; border-radius:7px; font-size:13px; box-sizing:border-box; outline:none;">
        </div>
        <div style="display:flex; gap:8px;">
            <button type="submit" style="background:#3C2A1E; color:white; border:none; padding:9px 18px; border-radius:7px; font-size:13px; font-weight:700; cursor:pointer;">🔍 Cari</button>
            <a href="<?= base_url('admin/reviews') ?>" style="background:#F0E8E0; color:#3C2A1E; padding:9px 14px; border-radius:7px; font-size:13px; font-weight:600; text-decoration:none; display:flex; align-items:center;">✕ Reset</a>
        </div>
    </form>
</div>

<!-- RESULT INFO -->
<div style="font-size:13px; color:#888; margin-bottom:10px;">
    Menampilkan <strong style="color:#3C2A1E;"><?= count($reviews) ?></strong> dari <strong style="color:#3C2A1E;"><?= $total ?></strong> ulasan
</div>

<div class="admin-box" style="padding:0; overflow:hidden;">
    <table class="table" style="margin:0;">
        <thead>
            <tr>
                <th style="width:40px;">No</th>
                <th>Produk</th>
                <th>Pembeli</th>
                <th>Ulasan</th>
                <th>Tanggal</th>
                <th style="width:80px; text-align:center;">Aksi</th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($reviews)): ?>
        <tr><td colspan="6" style="padding:40px; text-align:center; color:#bbb; font-size:14px;">Belum ada ulasan</td></tr>
        <?php endif; ?>
        <?php foreach ($reviews as $i => $r): ?>
        <tr>
            <td style="color:#aaa; font-size:12px;"><?= ($currentPage - 1) * $perPage + $i + 1 ?></td>
            <td>
                <div style="display:flex; align-items:center; gap:10px;">
                    <img src="<?= base_url('uploads/' . ($r['product_image'] ?? '')) ?>"
                         style="width:40px; height:40px; object-fit:cover; border-radius:6px; background:#F5EDE8; flex-shrink:0;"
                         onerror="this.style.display='none'">
                    <span style="font-weight:600; font-size:13px; color:#3C2A1E;">
                        <?= htmlspecialchars($r['product_name'] ?? '–') ?>
                    </span>
                </div>
            </td>
            <td>
                <div style="font-weight:600; font-size:13px; color:#3C2A1E;"><?= htmlspecialchars($r['user_name'] ?? '–') ?></div>
                <div style="font-size:11px; color:#aaa;"><?= htmlspecialchars($r['user_email'] ?? '') ?></div>
            </td>
            <td style="max-width:320px;">
                <p style="margin:0; font-size:13px; color:#555; line-height:1.5; word-break:break-word;">
                    "<?= htmlspecialchars($r['review'] ?? '') ?>"
                </p>
            </td>
            <td style="font-size:12px; color:#aaa; white-space:nowrap;">
                <?= !empty($r['created_at']) ? date('d M Y', strtotime($r['created_at'])) : '–' ?>
            </td>
            <td style="text-align:center;">
                <a href="<?= base_url('admin/reviews/delete/' . $r['id']) ?>"
                   onclick="return confirm('Hapus ulasan ini?')"
                   style="background:#DC262610; color:#DC2626; padding:5px 12px; border-radius:6px; font-size:12px; font-weight:700; text-decoration:none;">
                    🗑 Hapus
                </a>
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
    $buildUrl = function($p) use ($search) {
        return base_url('admin/reviews?' . http_build_query(array_filter(['search'=>$search,'page'=>$p])));
    };
    ?>
    <?php if ($currentPage > 1): ?>
    <a href="<?= $buildUrl($currentPage - 1) ?>" style="padding:7px 14px; border:1px solid #E0D5CC; border-radius:6px; font-size:13px; color:#3C2A1E; text-decoration:none; font-weight:600;">← Prev</a>
    <?php endif; ?>
    <?php for ($p = max(1,$currentPage-2); $p <= min($totalPages,$currentPage+2); $p++): ?>
    <a href="<?= $buildUrl($p) ?>"
       style="padding:7px 12px; border:1px solid <?= $p===$currentPage?'#3C2A1E':'#E0D5CC' ?>; border-radius:6px; font-size:13px;
              background:<?= $p===$currentPage?'#3C2A1E':'white' ?>; color:<?= $p===$currentPage?'white':'#3C2A1E' ?>;
              text-decoration:none; font-weight:<?= $p===$currentPage?'700':'400' ?>;">
        <?= $p ?>
    </a>
    <?php endfor; ?>
    <?php if ($currentPage < $totalPages): ?>
    <a href="<?= $buildUrl($currentPage + 1) ?>" style="padding:7px 14px; border:1px solid #E0D5CC; border-radius:6px; font-size:13px; color:#3C2A1E; text-decoration:none; font-weight:600;">Next →</a>
    <?php endif; ?>
</div>
<?php endif; ?>

<?= $this->endSection() ?>
