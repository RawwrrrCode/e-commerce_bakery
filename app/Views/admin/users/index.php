<?= $this->extend('layout/admin') ?>
<?= $this->section('content') ?>

<div class="page-header" style="margin-bottom:20px;">
    <h1>👤 Data User</h1>
    <p>Daftar pelanggan yang terdaftar di toko</p>
</div>

<!-- FILTER -->
<div class="admin-box" style="padding:14px 18px; margin-bottom:16px;">
    <form method="get" action="<?= base_url('admin/users') ?>" style="display:flex; gap:10px; align-items:flex-end; flex-wrap:wrap;">
        <div style="flex:1; min-width:200px;">
            <label style="display:block; font-size:11px; color:#888; font-weight:600; margin-bottom:4px; text-transform:uppercase; letter-spacing:.5px;">Cari User</label>
            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>"
                   placeholder="Nama atau email..."
                   style="width:100%; padding:8px 12px; border:1px solid #E0D5CC; border-radius:7px; font-size:13px; box-sizing:border-box; outline:none;">
        </div>
        <div style="display:flex; gap:8px;">
            <button type="submit" style="background:#3C2A1E; color:white; border:none; padding:9px 18px; border-radius:7px; font-size:13px; font-weight:700; cursor:pointer;">🔍 Cari</button>
            <a href="<?= base_url('admin/users') ?>" style="background:#F0E8E0; color:#3C2A1E; padding:9px 14px; border-radius:7px; font-size:13px; font-weight:600; text-decoration:none; display:flex; align-items:center;">✕ Reset</a>
        </div>
    </form>
</div>

<div style="font-size:13px; color:#888; margin-bottom:10px;">
    Total <strong style="color:#3C2A1E;"><?= $total ?></strong> pelanggan terdaftar
</div>

<div class="admin-box" style="padding:0; overflow:hidden;">
    <table class="table" style="margin:0;">
        <thead>
            <tr>
                <th>User</th>
                <th>No. HP</th>
                <th style="text-align:center;">Total Order</th>
                <th style="text-align:right;">Total Belanja</th>
                <th>Bergabung</th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($users)): ?>
        <tr><td colspan="5" style="padding:40px; text-align:center; color:#bbb; font-size:14px;">Belum ada user</td></tr>
        <?php endif; ?>
        <?php foreach ($users as $u): ?>
        <tr>
            <td>
                <div style="display:flex; align-items:center; gap:12px;">
                    <div style="width:36px; height:36px; background:#C1121F20; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:14px; font-weight:700; color:#C1121F; flex-shrink:0;">
                        <?= strtoupper(mb_substr($u['name'] ?? '?', 0, 1)) ?>
                    </div>
                    <div>
                        <div style="font-weight:600; font-size:13px; color:#3C2A1E;"><?= htmlspecialchars($u['name']) ?></div>
                        <div style="font-size:11px; color:#aaa;"><?= htmlspecialchars($u['email']) ?></div>
                    </div>
                </div>
            </td>
            <td style="font-size:13px; color:#555;">
                <?php if (!empty($u['phone'])): ?>
                <a href="https://wa.me/<?= preg_replace('/\D/', '', $u['phone']) ?>" target="_blank"
                   style="color:#25D366; text-decoration:none; font-weight:600; font-size:12px;">
                    📱 <?= htmlspecialchars($u['phone']) ?>
                </a>
                <?php else: ?>
                <span style="color:#ddd;">–</span>
                <?php endif; ?>
            </td>
            <td style="text-align:center;">
                <span style="background:#3C2A1E10; color:#3C2A1E; padding:3px 12px; border-radius:20px; font-size:12px; font-weight:700;">
                    <?= $u['order_count'] ?> order
                </span>
            </td>
            <td style="text-align:right; font-weight:700; color:#C1121F; font-size:13px;">
                Rp <?= number_format($u['total_spent'], 0, ',', '.') ?>
            </td>
            <td style="font-size:12px; color:#aaa;">
                <?= !empty($u['created_at']) ? date('d M Y', strtotime($u['created_at'])) : '–' ?>
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
        return base_url('admin/users?' . http_build_query(array_filter(['search'=>$search,'page'=>$p])));
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
