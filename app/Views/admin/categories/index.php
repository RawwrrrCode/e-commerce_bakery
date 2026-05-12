<?= $this->extend('layout/admin') ?>
<?= $this->section('content') ?>

<div class="page-header" style="margin-bottom:20px;">
    <h1>🏷️ Manajemen Kategori</h1>
    <p>Kelola kategori produk toko roti</p>
</div>

<?php if (session()->getFlashdata('success')): ?>
<div style="background:#D1FAE5; border:1px solid #6EE7B7; color:#065F46; padding:10px 16px; border-radius:8px; margin-bottom:16px; font-size:13px; font-weight:600;">
    ✓ <?= session()->getFlashdata('success') ?>
</div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
<div style="background:#FEE2E2; border:1px solid #FECACA; color:#991B1B; padding:10px 16px; border-radius:8px; margin-bottom:16px; font-size:13px; font-weight:600;">
    ⚠ <?= session()->getFlashdata('error') ?>
</div>
<?php endif; ?>

<div style="display:grid; grid-template-columns:1fr 1.6fr; gap:20px; align-items:start;">

    <!-- TAMBAH KATEGORI -->
    <div class="admin-box">
        <h3 style="font-size:15px; font-weight:700; color:#3C2A1E; margin-bottom:18px; padding-bottom:10px; border-bottom:1px solid #F0E8E0;">
            ➕ Tambah Kategori Baru
        </h3>
        <form method="post" action="<?= base_url('admin/categories/store') ?>">
            <?= csrf_field() ?>
            <div style="margin-bottom:14px;">
                <label style="display:block; font-size:12px; color:#888; font-weight:600; margin-bottom:6px; text-transform:uppercase; letter-spacing:.5px;">
                    Nama Kategori
                </label>
                <input type="text" name="name" placeholder="Contoh: Sourdough Bread"
                       style="width:100%; padding:10px 14px; border:1px solid #E0D5CC; border-radius:8px; font-size:14px; box-sizing:border-box; outline:none; font-family:inherit;">
            </div>
            <button type="submit"
                    style="width:100%; background:#3C2A1E; color:white; border:none; padding:11px; border-radius:8px; font-size:14px; font-weight:700; cursor:pointer;">
                Simpan Kategori
            </button>
        </form>

        <div style="margin-top:20px; padding-top:16px; border-top:1px solid #F0E8E0;">
            <p style="font-size:12px; color:#aaa; line-height:1.7; margin:0;">
                💡 Kategori yang sudah digunakan produk tidak dapat dihapus. Ubah kategori produk terlebih dahulu sebelum menghapus.
            </p>
        </div>
    </div>

    <!-- DAFTAR KATEGORI -->
    <div class="admin-box" style="padding:0; overflow:hidden;">
        <div style="padding:16px 20px; border-bottom:1px solid #F0E8E0; display:flex; justify-content:space-between; align-items:center;">
            <h3 style="font-size:15px; font-weight:700; color:#3C2A1E; margin:0;">
                📋 Daftar Kategori (<?= count($categories) ?>)
            </h3>
        </div>
        <table style="width:100%; border-collapse:collapse; font-size:13px;">
            <thead>
                <tr style="background:#FFF8F0;">
                    <th style="padding:10px 18px; text-align:left; color:#888; font-weight:600;">Nama Kategori</th>
                    <th style="padding:10px 18px; text-align:center; color:#888; font-weight:600;">Jumlah Produk</th>
                    <th style="padding:10px 18px; text-align:center; color:#888; font-weight:600;">Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($categories)): ?>
            <tr><td colspan="3" style="padding:32px; text-align:center; color:#bbb;">Belum ada kategori</td></tr>
            <?php endif; ?>
            <?php foreach ($categories as $cat): ?>
            <tr style="border-top:1px solid #F7F2EE;">
                <td style="padding:12px 18px;">
                    <div style="display:flex; align-items:center; gap:10px;">
                        <div style="width:8px; height:8px; background:#C1121F; border-radius:50%; flex-shrink:0;"></div>
                        <span style="font-weight:600; color:#3C2A1E;"><?= htmlspecialchars($cat['name']) ?></span>
                    </div>
                </td>
                <td style="padding:12px 18px; text-align:center;">
                    <span style="background:#3C2A1E10; color:#3C2A1E; padding:3px 12px; border-radius:20px; font-size:12px; font-weight:700;">
                        <?= $cat['product_count'] ?> produk
                    </span>
                </td>
                <td style="padding:12px 18px; text-align:center;">
                    <?php if ($cat['product_count'] > 0): ?>
                    <span style="font-size:11px; color:#bbb;">Tidak bisa dihapus</span>
                    <?php else: ?>
                    <a href="<?= base_url('admin/categories/delete/' . $cat['id']) ?>"
                       onclick="return confirm('Hapus kategori \"<?= htmlspecialchars($cat['name']) ?>\"?')"
                       style="background:#DC262610; color:#DC2626; padding:5px 14px; border-radius:6px; font-size:12px; font-weight:700; text-decoration:none;">
                        🗑 Hapus
                    </a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>
