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

        <!-- Cover / Gambar Utama -->
        <div class="form-group">
            <label>Foto Utama (Cover)</label>
            <input type="file" name="image" id="imageInput" accept="image/*">
            <img id="previewImg" style="display:none; margin-top:10px; max-width:180px; border-radius:8px; border:2px solid #E8D5C4;">
        </div>

        <!-- Gallery tambahan -->
        <div class="form-group">
            <label>Foto Gallery <span style="font-weight:400; font-size:12px; color:#888;">(opsional, bisa pilih beberapa)</span></label>
            <input type="file" name="gallery[]" id="galleryInput" accept="image/*" multiple>
            <div id="galleryPreview" style="display:flex; gap:8px; flex-wrap:wrap; margin-top:10px;"></div>
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

document.getElementById('galleryInput').addEventListener('change', function () {
    var wrap = document.getElementById('galleryPreview');
    wrap.innerHTML = '';
    Array.from(this.files).forEach(function(file) {
        var img = document.createElement('img');
        img.src = URL.createObjectURL(file);
        img.style.cssText = 'width:80px; height:80px; object-fit:cover; border-radius:6px; border:2px solid #E8D5C4;';
        wrap.appendChild(img);
    });
});
</script>

<?= $this->endSection() ?>
