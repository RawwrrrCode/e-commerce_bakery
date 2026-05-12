<?= $this->extend('layout/admin') ?>

<?= $this->section('content') ?>

<div class="admin-box">

    <h2>Tambah Produk</h2>

    <form method="post" action="/admin/products/store" enctype="multipart/form-data">

        <div class="form-group">
            <label>Nama Produk</label>
            <input type="text" name="name" placeholder="Contoh: Bika Ambon">
        </div>

        <div class="form-group">
            <label>Harga</label>
            <input type="number" name="price" placeholder="Contoh: 15000">
        </div>

        <div class="form-group">
            <label>Kategori</label>
            <select name="category">
                <option value="">-- Pilih Kategori --</option>
                <?php foreach ($categories as $cat): ?>
                <option value="<?= htmlspecialchars($cat['name']) ?>"><?= htmlspecialchars($cat['name']) ?></option>
                <?php endforeach; ?>
            </select>
            <small style="color:#aaa; font-size:11px;">Tambah kategori baru di <a href="/admin/categories" style="color:#C1121F;">Manajemen Kategori</a></small>
        </div>

        <div class="form-group">
            <label>Deskripsi</label>
            <textarea name="description" rows="4" placeholder="Deskripsikan produk ini..."></textarea>
        </div>

        <div class="form-group">
            <label>Gambar Produk</label>
            <input type="file" name="image" id="imageInput" accept="image/*">
            <img id="previewImg" class="preview-img" style="display:none; margin-top:10px; max-width:200px; border-radius:8px; border:2px solid #E8D5C4;">
        </div>
        

        <button class="btn">Simpan Produk</button>

    </form>

</div>

<script>
document.getElementById('imageInput').addEventListener('change', function () {
    var file = this.files[0];
    if (!file) return;
    var img = document.getElementById('previewImg');
    img.src = URL.createObjectURL(file);
    img.style.display = 'block';
});
</script>

<?= $this->endSection() ?>