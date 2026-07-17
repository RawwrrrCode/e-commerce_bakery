<?= $this->extend('layout/admin') ?>

<?= $this->section('content') ?>

<h2>Data Produk</h2>

<?php if(session()->getFlashdata('success')): ?>
    <div class="alert-success"><?= session()->getFlashdata('success') ?></div>
<?php endif; ?>

<a href="/admin/products/create" class="btn">+ Tambah</a>

<table class="table" style="margin-top: 18px;">
    <thead>
        <tr>
            <th style="width:70px;">Gambar</th>
            <th>Nama Produk</th>
            <th style="width:130px;">Harga</th>
            <th style="width:140px;">Aksi</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($grouped as $category => $products): ?>

        <!-- Category separator row -->
        <tr>
            <td colspan="4" style="
                background: #2c1a0e;
                color: #f5e6d3;
                font-size: 12px;
                font-weight: 700;
                letter-spacing: 1px;
                text-transform: uppercase;
                padding: 8px 16px;
                border: none;
            ">
                <?= htmlspecialchars($category) ?>
                <span style="font-weight:400; opacity:0.6; margin-left:8px;">(<?= count($products) ?> produk)</span>
            </td>
        </tr>

        <?php foreach ($products as $p): ?>
        <tr>
            <td>
                <?php if (!empty($p['image'])): ?>
                    <img src="/uploads/<?= $p['image'] ?>" width="52" height="52"
                         style="object-fit:cover; border-radius:6px;">
                <?php else: ?>
                    <div style="width:52px; height:52px; background:#f0e8e0; border-radius:6px;
                                display:flex; align-items:center; justify-content:center;
                                font-size:18px; color:#bbb;">🖼</div>
                <?php endif; ?>
            </td>
            <td><?= htmlspecialchars($p['name']) ?></td>
            <td>Rp <?= number_format($p['price'], 0, ',', '.') ?></td>
            <td>
                <div class="action-buttons">
                    <a href="/admin/products/edit/<?= $p['id'] ?>" class="btn-edit">Edit</a>
                    <a href="/admin/products/delete/<?= $p['id'] ?>"
                       class="btn-delete"
                       onclick="return confirm('Yakin hapus produk ini?')">Hapus</a>
                </div>
            </td>
        </tr>
        <?php endforeach; ?>

    <?php endforeach; ?>
    </tbody>
</table>

<?= $this->endSection() ?>
