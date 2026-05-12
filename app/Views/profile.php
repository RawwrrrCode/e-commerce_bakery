<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div style="background:var(--cream); min-height:calc(100vh - 140px); padding:40px 0 64px;">
<div class="container" style="max-width:960px;">

    <!-- FLASH MESSAGES -->
    <?php if (session()->getFlashdata('info')): ?>
    <div style="background:#ECFDF5; border:1px solid #A7F3D0; color:#065F46; padding:13px 18px; border-radius:10px; margin-bottom:24px; font-size:14px;">
        ✅ <?= session()->getFlashdata('info') ?>
    </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
    <div style="background:#FEE2E2; border:1px solid #FECACA; color:#B91C1C; padding:13px 18px; border-radius:10px; margin-bottom:24px; font-size:14px;">
        ⚠️ <?= session()->getFlashdata('error') ?>
    </div>
    <?php endif; ?>

    <div style="display:grid; grid-template-columns:280px 1fr; gap:24px; align-items:start;">

        <!-- ── LEFT: PROFILE CARD ────────────────────────────────── -->
        <div>
            <!-- Avatar + Name -->
            <div style="background:white; border-radius:var(--radius-lg); box-shadow:var(--shadow); padding:28px 20px; text-align:center; margin-bottom:16px;">
                <div style="width:80px; height:80px; background:var(--red); border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:32px; font-weight:700; color:white; margin:0 auto 16px; letter-spacing:-1px;">
                    <?= strtoupper(substr($user->name ?? 'U', 0, 1)) ?>
                </div>
                <div style="font-family:'Playfair Display',serif; font-size:18px; font-weight:700; color:var(--brown-dark); margin-bottom:4px;">
                    <?= htmlspecialchars($user->name ?? '') ?>
                </div>
                <div style="font-size:12px; color:var(--gray); margin-bottom:16px;">
                    <?= htmlspecialchars($user->email ?? '') ?>
                </div>
                <div style="font-size:11px; color:var(--gray); background:var(--cream); padding:6px 12px; border-radius:20px; display:inline-block;">
                    🗓 Member sejak <?= date('M Y', strtotime($user->created_at ?? 'now')) ?>
                </div>
            </div>

            <!-- Stats -->
            <div style="background:white; border-radius:var(--radius-lg); box-shadow:var(--shadow); overflow:hidden;">
                <div style="padding:14px 18px; border-bottom:1px solid #F0E8E0;">
                    <div style="font-size:11px; font-weight:700; color:var(--gray); text-transform:uppercase; letter-spacing:.8px;">Statistik Akun</div>
                </div>
                <div style="padding:0;">
                    <div style="display:flex; justify-content:space-between; align-items:center; padding:14px 18px; border-bottom:1px solid #F7F2EE;">
                        <span style="font-size:13px; color:var(--gray);">📦 Total Pesanan</span>
                        <span style="font-weight:700; color:var(--brown-dark); font-size:15px;"><?= $totalOrders ?></span>
                    </div>
                    <div style="display:flex; justify-content:space-between; align-items:center; padding:14px 18px; border-bottom:1px solid #F7F2EE;">
                        <span style="font-size:13px; color:var(--gray);">💰 Total Belanja</span>
                        <span style="font-weight:700; color:var(--brown-dark); font-size:13px;">Rp <?= number_format($totalSpent, 0, ',', '.') ?></span>
                    </div>
                    <div style="display:flex; justify-content:space-between; align-items:center; padding:14px 18px;">
                        <span style="font-size:13px; color:var(--gray);">❤️ Wishlist</span>
                        <span style="font-weight:700; color:var(--brown-dark); font-size:15px;"><?= $totalWishlist ?></span>
                    </div>
                </div>
            </div>

            <!-- Quick Links -->
            <div style="background:white; border-radius:var(--radius-lg); box-shadow:var(--shadow); overflow:hidden; margin-top:16px;">
                <a href="<?= base_url('orders') ?>" style="display:flex; align-items:center; gap:10px; padding:14px 18px; font-size:13px; color:var(--brown); border-bottom:1px solid #F7F2EE; transition:background 0.15s;">
                    <span>📋</span> Riwayat Pesanan
                </a>
                <a href="<?= base_url('logout') ?>" style="display:flex; align-items:center; gap:10px; padding:14px 18px; font-size:13px; color:#DC2626;">
                    <span>🚪</span> Keluar
                </a>
            </div>
        </div>

        <!-- ── RIGHT: EDIT FORM ───────────────────────────────────── -->
        <div style="background:white; border-radius:var(--radius-lg); box-shadow:var(--shadow);">

            <!-- Tab Header -->
            <div style="display:flex; border-bottom:1px solid #F0E8E0;">
                <button onclick="showTab('info')" id="tab-info"
                    style="flex:1; padding:16px; font-size:14px; font-weight:600; border:none; background:none; cursor:pointer; color:var(--red); border-bottom:2px solid var(--red); font-family:'Poppins',sans-serif;">
                    Informasi Akun
                </button>
                <button onclick="showTab('pass')" id="tab-pass"
                    style="flex:1; padding:16px; font-size:14px; font-weight:600; border:none; background:none; cursor:pointer; color:var(--gray); border-bottom:2px solid transparent; font-family:'Poppins',sans-serif;">
                    Ganti Password
                </button>
            </div>

            <!-- TAB: Informasi Akun -->
            <div id="panel-info" style="padding:28px;">
                <form method="post" action="<?= base_url('profile') ?>">
                    <?= csrf_field() ?>
                    <input type="hidden" name="_tab" value="info">

                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px;">
                        <div>
                            <label style="display:block; font-size:12px; font-weight:600; color:var(--gray); margin-bottom:6px; text-transform:uppercase; letter-spacing:.5px;">Nama Lengkap</label>
                            <input type="text" name="name" value="<?= htmlspecialchars($user->name ?? '') ?>" required
                                   style="width:100%; padding:11px 14px; border:1.5px solid #E8D5C4; border-radius:8px; font-size:14px; box-sizing:border-box; font-family:'Poppins',sans-serif; color:var(--brown-dark);">
                        </div>
                        <div>
                            <label style="display:block; font-size:12px; font-weight:600; color:var(--gray); margin-bottom:6px; text-transform:uppercase; letter-spacing:.5px;">Nomor Telepon</label>
                            <input type="text" name="phone" value="<?= htmlspecialchars($user->phone ?? '') ?>" placeholder="08xx-xxxx-xxxx"
                                   style="width:100%; padding:11px 14px; border:1.5px solid #E8D5C4; border-radius:8px; font-size:14px; box-sizing:border-box; font-family:'Poppins',sans-serif; color:var(--brown-dark);">
                        </div>
                    </div>

                    <div style="margin-bottom:16px;">
                        <label style="display:block; font-size:12px; font-weight:600; color:var(--gray); margin-bottom:6px; text-transform:uppercase; letter-spacing:.5px;">Email</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($user->email ?? '') ?>" required
                               style="width:100%; padding:11px 14px; border:1.5px solid #E8D5C4; border-radius:8px; font-size:14px; box-sizing:border-box; font-family:'Poppins',sans-serif; color:var(--brown-dark);">
                    </div>

                    <div style="margin-bottom:24px;">
                        <label style="display:block; font-size:12px; font-weight:600; color:var(--gray); margin-bottom:6px; text-transform:uppercase; letter-spacing:.5px;">Alamat</label>
                        <textarea name="address" rows="3" placeholder="Masukkan alamat lengkap Anda..."
                               style="width:100%; padding:11px 14px; border:1.5px solid #E8D5C4; border-radius:8px; font-size:14px; box-sizing:border-box; font-family:'Poppins',sans-serif; color:var(--brown-dark); resize:vertical;"><?= htmlspecialchars($user->address ?? '') ?></textarea>
                    </div>

                    <button type="submit"
                        style="background:var(--red); color:white; border:none; padding:12px 32px; border-radius:8px; font-size:14px; font-weight:600; cursor:pointer; font-family:'Poppins',sans-serif;">
                        Simpan Perubahan
                    </button>
                </form>
            </div>

            <!-- TAB: Ganti Password -->
            <div id="panel-pass" style="padding:28px; display:none;">
                <form method="post" action="<?= base_url('profile') ?>">
                    <?= csrf_field() ?>
                    <input type="hidden" name="_tab" value="pass">
                    <!-- dummy fields so updateProfile doesn't fail on name/email -->
                    <input type="hidden" name="name" value="<?= htmlspecialchars($user->name ?? '') ?>">
                    <input type="hidden" name="email" value="<?= htmlspecialchars($user->email ?? '') ?>">
                    <input type="hidden" name="phone" value="<?= htmlspecialchars($user->phone ?? '') ?>">
                    <input type="hidden" name="address" value="<?= htmlspecialchars($user->address ?? '') ?>">

                    <div style="margin-bottom:16px;">
                        <label style="display:block; font-size:12px; font-weight:600; color:var(--gray); margin-bottom:6px; text-transform:uppercase; letter-spacing:.5px;">Password Saat Ini</label>
                        <input type="password" name="current_password" placeholder="Masukkan password lama"
                               style="width:100%; padding:11px 14px; border:1.5px solid #E8D5C4; border-radius:8px; font-size:14px; box-sizing:border-box; font-family:'Poppins',sans-serif;">
                    </div>
                    <div style="margin-bottom:16px;">
                        <label style="display:block; font-size:12px; font-weight:600; color:var(--gray); margin-bottom:6px; text-transform:uppercase; letter-spacing:.5px;">Password Baru</label>
                        <input type="password" name="new_password" placeholder="Minimal 6 karakter"
                               style="width:100%; padding:11px 14px; border:1.5px solid #E8D5C4; border-radius:8px; font-size:14px; box-sizing:border-box; font-family:'Poppins',sans-serif;">
                    </div>
                    <div style="margin-bottom:24px;">
                        <label style="display:block; font-size:12px; font-weight:600; color:var(--gray); margin-bottom:6px; text-transform:uppercase; letter-spacing:.5px;">Konfirmasi Password Baru</label>
                        <input type="password" name="confirm_password" placeholder="Ulangi password baru"
                               style="width:100%; padding:11px 14px; border:1.5px solid #E8D5C4; border-radius:8px; font-size:14px; box-sizing:border-box; font-family:'Poppins',sans-serif;">
                    </div>

                    <button type="submit"
                        style="background:var(--red); color:white; border:none; padding:12px 32px; border-radius:8px; font-size:14px; font-weight:600; cursor:pointer; font-family:'Poppins',sans-serif;">
                        Ubah Password
                    </button>
                </form>
            </div>

        </div>
    </div>

</div>
</div>

<script>
function showTab(tab) {
    document.getElementById('panel-info').style.display = tab === 'info' ? 'block' : 'none';
    document.getElementById('panel-pass').style.display = tab === 'pass' ? 'block' : 'none';
    document.getElementById('tab-info').style.color        = tab === 'info' ? 'var(--red)' : 'var(--gray)';
    document.getElementById('tab-pass').style.color        = tab === 'pass' ? 'var(--red)' : 'var(--gray)';
    document.getElementById('tab-info').style.borderBottom = tab === 'info' ? '2px solid var(--red)' : '2px solid transparent';
    document.getElementById('tab-pass').style.borderBottom = tab === 'pass' ? '2px solid var(--red)' : '2px solid transparent';
}
</script>

<?= $this->endSection() ?>
