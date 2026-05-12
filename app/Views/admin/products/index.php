<?= $this->extend('layout/admin') ?>

<?= $this->section('content') ?>

<h2>Data Produk</h2>
<?php if(session()->getFlashdata('success')): ?>
    <div class="alert-success">
        <?= session()->getFlashdata('success') ?>
    </div>
<?php endif; ?>

<a href="/admin/products/create" class="btn">+ Tambah</a>

<table class="table">
    <tr>
        <th>Gambar</th>
        <th>Nama</th>
        <th>Harga</th>
        <th>Aksi</th>
    </tr>

    <?php foreach($products as $p): ?>
    <tr>
        <td><img src="/uploads/<?= $p['image'] ?>" width="60"></td>
        <td><?= $p['name'] ?></td>
        <td>Rp <?= number_format($p['price']) ?></td>
        <td>
    <div class="action-buttons">

        <a href="/admin/products/edit/<?= $p['id'] ?>" class="btn-edit">
            Edit
        </a>

        <a href="/admin/products/delete/<?= $p['id'] ?>" 
           class="btn-delete"
           onclick="return confirm('Yakin hapus?')">
           Hapus
        </a>

    </div>
</td>
        
    </tr>
    <?php endforeach; ?>

</table>

<?= $this->endSection() ?>