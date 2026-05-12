<?= $this->extend('layout/admin') ?>
<?= $this->section('content') ?>

<div class="page-header" style="margin-bottom:20px;">
    <h1>⚙️ Pengaturan Toko</h1>
    <p>Kelola informasi dan kontak toko</p>
</div>

<?php if (session()->getFlashdata('success')): ?>
<div style="background:#D1FAE5; border:1px solid #6EE7B7; color:#065F46; padding:12px 18px; border-radius:8px; margin-bottom:20px; font-size:13px; font-weight:600;">
    ✓ <?= session()->getFlashdata('success') ?>
</div>
<?php endif; ?>

<form method="post" action="<?= base_url('admin/settings/save') ?>">
    <?= csrf_field() ?>

    <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">

        <!-- INFO TOKO -->
        <div class="admin-box">
            <h3 style="font-size:15px; font-weight:700; color:#3C2A1E; margin-bottom:20px; padding-bottom:10px; border-bottom:1px solid #F0E8E0;">
                🏪 Informasi Toko
            </h3>

            <div style="margin-bottom:16px;">
                <label style="display:block; font-size:12px; color:#888; font-weight:600; margin-bottom:6px; text-transform:uppercase; letter-spacing:.5px;">Nama Toko *</label>
                <input type="text" name="store_name" value="<?= htmlspecialchars($config['store_name'] ?? '') ?>" placeholder="Toko Roti"
                       style="width:100%; padding:10px 14px; border:1px solid #E0D5CC; border-radius:8px; font-size:14px; box-sizing:border-box; outline:none; font-family:inherit;">
            </div>

            <div style="margin-bottom:16px;">
                <label style="display:block; font-size:12px; color:#888; font-weight:600; margin-bottom:6px; text-transform:uppercase; letter-spacing:.5px;">Tagline / Nama Perusahaan</label>
                <input type="text" name="store_tagline" value="<?= htmlspecialchars($config['store_tagline'] ?? '') ?>" placeholder="PT. Mimosa Tarte Indonesia"
                       style="width:100%; padding:10px 14px; border:1px solid #E0D5CC; border-radius:8px; font-size:14px; box-sizing:border-box; outline:none; font-family:inherit;">
            </div>

            <div style="margin-bottom:16px;">
                <label style="display:block; font-size:12px; color:#888; font-weight:600; margin-bottom:6px; text-transform:uppercase; letter-spacing:.5px;">Alamat Lengkap</label>
                <textarea name="store_address" rows="3" placeholder="Jl. Contoh No. 1, Jakarta Selatan..."
                          style="width:100%; padding:10px 14px; border:1px solid #E0D5CC; border-radius:8px; font-size:14px; box-sizing:border-box; outline:none; font-family:inherit; resize:vertical;"><?= htmlspecialchars($config['store_address'] ?? '') ?></textarea>
            </div>

            <div style="margin-bottom:0;">
                <label style="display:block; font-size:12px; color:#888; font-weight:600; margin-bottom:6px; text-transform:uppercase; letter-spacing:.5px;">Jam Operasional</label>
                <input type="text" name="store_hours" value="<?= htmlspecialchars($config['store_hours'] ?? '') ?>" placeholder="Senin–Sabtu, 08.00–20.00 WIB"
                       style="width:100%; padding:10px 14px; border:1px solid #E0D5CC; border-radius:8px; font-size:14px; box-sizing:border-box; outline:none; font-family:inherit;">
            </div>
        </div>

        <!-- KONTAK -->
        <div class="admin-box">
            <h3 style="font-size:15px; font-weight:700; color:#3C2A1E; margin-bottom:20px; padding-bottom:10px; border-bottom:1px solid #F0E8E0;">
                📞 Kontak & Sosial Media
            </h3>

            <div style="margin-bottom:16px;">
                <label style="display:block; font-size:12px; color:#888; font-weight:600; margin-bottom:6px; text-transform:uppercase; letter-spacing:.5px;">No. WhatsApp CS</label>
                <div style="display:flex; align-items:center; gap:0;">
                    <span style="background:#25D366; color:white; padding:10px 14px; border-radius:8px 0 0 8px; font-size:14px; font-weight:700; flex-shrink:0;">📱</span>
                    <input type="text" name="store_phone" value="<?= htmlspecialchars($config['store_phone'] ?? '') ?>" placeholder="628123456789 (format internasional)"
                           style="flex:1; padding:10px 14px; border:1px solid #E0D5CC; border-left:none; border-radius:0 8px 8px 0; font-size:14px; outline:none; font-family:inherit;">
                </div>
                <small style="color:#aaa; font-size:11px;">Tanpa tanda + atau strip, contoh: 6281234567890</small>
            </div>

            <div style="margin-bottom:16px;">
                <label style="display:block; font-size:12px; color:#888; font-weight:600; margin-bottom:6px; text-transform:uppercase; letter-spacing:.5px;">Email Toko</label>
                <input type="email" name="store_email" value="<?= htmlspecialchars($config['store_email'] ?? '') ?>" placeholder="info@tokuroti.com"
                       style="width:100%; padding:10px 14px; border:1px solid #E0D5CC; border-radius:8px; font-size:14px; box-sizing:border-box; outline:none; font-family:inherit;">
            </div>

            <div style="margin-bottom:16px;">
                <label style="display:block; font-size:12px; color:#888; font-weight:600; margin-bottom:6px; text-transform:uppercase; letter-spacing:.5px;">Instagram</label>
                <div style="display:flex; align-items:center; gap:0;">
                    <span style="background:#E1306C; color:white; padding:10px 14px; border-radius:8px 0 0 8px; font-size:13px; font-weight:700; flex-shrink:0;">@</span>
                    <input type="text" name="store_instagram" value="<?= htmlspecialchars($config['store_instagram'] ?? '') ?>" placeholder="namatoko"
                           style="flex:1; padding:10px 14px; border:1px solid #E0D5CC; border-left:none; border-radius:0 8px 8px 0; font-size:14px; outline:none; font-family:inherit;">
                </div>
            </div>

            <div style="margin-bottom:0;">
                <label style="display:block; font-size:12px; color:#888; font-weight:600; margin-bottom:6px; text-transform:uppercase; letter-spacing:.5px;">Google Maps Embed URL <span style="font-weight:400; color:#bbb;">(opsional)</span></label>
                <input type="text" name="store_maps_embed" value="<?= htmlspecialchars($config['store_maps_embed'] ?? '') ?>" placeholder="https://maps.google.com/maps?..."
                       style="width:100%; padding:10px 14px; border:1px solid #E0D5CC; border-radius:8px; font-size:13px; box-sizing:border-box; outline:none; font-family:inherit;">
            </div>
        </div>

    </div>

    <!-- PREVIEW -->
    <?php if (!empty($config['store_phone'])): ?>
    <div class="admin-box" style="margin-top:20px; background:linear-gradient(135deg,#F0FDF4,#DCFCE7); border:1px solid #BBF7D0;">
        <h3 style="font-size:14px; font-weight:700; color:#065F46; margin:0 0 12px;">📱 Preview Tombol WA CS</h3>
        <a href="https://wa.me/<?= preg_replace('/\D/', '', $config['store_phone']) ?>?text=Halo+<?= urlencode($config['store_name'] ?? 'Toko Roti') ?>%2C+saya+ingin+bertanya..."
           target="_blank"
           style="display:inline-flex; align-items:center; gap:8px; background:#25D366; color:white; padding:10px 20px; border-radius:8px; font-size:14px; font-weight:700; text-decoration:none;">
            📱 Chat WhatsApp CS
        </a>
        <p style="font-size:12px; color:#047857; margin:8px 0 0;">Link: https://wa.me/<?= htmlspecialchars(preg_replace('/\D/', '', $config['store_phone'])) ?></p>
    </div>
    <?php endif; ?>

    <div style="margin-top:20px; display:flex; justify-content:flex-end;">
        <button type="submit"
                style="background:#3C2A1E; color:white; border:none; padding:13px 36px; border-radius:9px; font-size:15px; font-weight:700; cursor:pointer; letter-spacing:.3px;">
            💾 Simpan Pengaturan
        </button>
    </div>

</form>

<?= $this->endSection() ?>
