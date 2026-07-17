<?= $this->extend('layout/admin') ?>
<?= $this->section('content') ?>

<div class="admin-box">
    <h2>Edit Produk</h2>

    <?php if(session()->getFlashdata('success')): ?>
        <div class="alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>

    <form method="post" action="/admin/products/update/<?= $product['id'] ?>" enctype="multipart/form-data">

        <div class="form-group">
            <label>Nama Produk</label>
            <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>">
        </div>

        <div class="form-group">
            <label>Harga</label>
            <input type="number" name="price" value="<?= $product['price'] ?>">
        </div>

        <div class="form-group">
            <label>Kategori</label>
            <select name="category">
                <option value="">-- Pilih Kategori --</option>
                <?php foreach ($categories as $cat): ?>
                <option value="<?= htmlspecialchars($cat['name']) ?>"
                        <?= ($product['category'] ?? '') === $cat['name'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['name']) ?>
                </option>
                <?php endforeach; ?>
            </select>
            <small style="color:#aaa; font-size:11px;">Tambah kategori baru di <a href="/admin/categories" style="color:#C1121F;">Manajemen Kategori</a></small>
        </div>

        <div class="form-group">
            <label>Deskripsi</label>
            <textarea name="description" rows="4"><?= htmlspecialchars($product['description'] ?? '') ?></textarea>
        </div>

        <!-- Cover / Gambar Utama -->
        <div class="form-group">
            <label>Foto Utama (Cover)</label>
            <?php if (!empty($product['image'])): ?>
            <div style="margin-bottom:8px;">
                <img src="/uploads/<?= $product['image'] ?>"
                     style="width:120px; height:120px; object-fit:cover; border-radius:8px; border:2px solid #E8D5C4;">
                <div style="font-size:12px; color:#888; margin-top:4px;">Foto cover saat ini</div>
            </div>
            <?php endif; ?>
            <input type="file" name="image" id="imageInput" accept="image/*">
            <img id="previewImg" style="display:none; margin-top:8px; max-width:180px; border-radius:8px; border:2px solid #E8D5C4;">
        </div>

        <!-- Gallery yang sudah ada -->
        <?php if (!empty($gallery)): ?>
        <div class="form-group">
            <label>Foto Gallery Saat Ini</label>
            <div style="display:flex; gap:10px; flex-wrap:wrap; margin-top:8px;" id="galleryExisting">
                <?php foreach ($gallery as $g): ?>
                <div style="position:relative; display:inline-block;" id="gitem<?= $g['id'] ?>">
                    <img src="/uploads/<?= $g['image'] ?>"
                         style="width:90px; height:90px; object-fit:cover; border-radius:8px; border:2px solid #E8D5C4;">
                    <button type="button"
                            onclick="deleteGalleryImage(<?= $g['id'] ?>)"
                            style="position:absolute; top:-6px; right:-6px; width:22px; height:22px;
                                   background:#C1121F; color:white; border:none; border-radius:50%;
                                   font-size:13px; line-height:1; cursor:pointer; display:flex;
                                   align-items:center; justify-content:center;">
                        &times;
                    </button>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Tambah foto gallery baru -->
        <div class="form-group">
            <label>Tambah Foto Gallery <span style="font-weight:400; font-size:12px; color:#888;">(opsional, bisa beberapa)</span></label>
            <input type="file" name="gallery[]" id="galleryInput" accept="image/*" multiple>
            <div id="galleryPreview" style="display:flex; gap:8px; flex-wrap:wrap; margin-top:10px;"></div>
        </div>

        <button class="btn">Update Produk</button>
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

function deleteGalleryImage(id) {
    if (!confirm('Hapus foto ini?')) return;
    fetch('/admin/products/image/delete/' + id, { method: 'POST' })
        .then(r => r.json())
        .then(data => {
            if (data.status === 'ok') {
                var el = document.getElementById('gitem' + id);
                if (el) el.remove();
            }
        });
}
</script>

<?= $this->endSection() ?>
