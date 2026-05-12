<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<!-- HERO TENTANG KAMI -->
<section style="background:linear-gradient(135deg, #3B1F0F 0%, #6B3A25 100%); padding:80px 0 60px; text-align:center; color:white;">
    <div class="container">
        <div style="font-size:56px; margin-bottom:16px;">🍞</div>
        <h1 style="font-family:'Playfair Display',serif; font-size:38px; margin:0 0 12px; color:white;">PT. Mimosa Tarte Indonesia</h1>
        <p style="font-size:16px; color:rgba(255,255,255,0.75); max-width:560px; margin:0 auto; line-height:1.7;">
            Menghadirkan roti dan kue berkualitas premium dengan cita rasa autentik, dipanggang segar setiap hari untuk kepuasan Anda.
        </p>
    </div>
</section>

<!-- TENTANG PERUSAHAAN -->
<section style="padding:64px 0; background:var(--white);">
    <div class="container" style="max-width:860px;">

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:48px; align-items:center;">
            <div>
                <div style="font-size:11px; font-weight:700; color:var(--red); letter-spacing:2px; text-transform:uppercase; margin-bottom:10px;">Tentang Kami</div>
                <h2 style="font-family:'Playfair Display',serif; font-size:30px; color:var(--brown-dark); margin:0 0 16px; line-height:1.3;">
                    Bakery dengan Sentuhan Keahlian & Cinta
                </h2>
                <p style="color:var(--gray); line-height:1.8; font-size:14px; margin:0 0 16px;">
                    PT. Mimosa Tarte Indonesia adalah perusahaan kuliner yang berfokus pada produksi roti artisan, kue premium, dan pastry berkualitas tinggi. Berdiri dengan semangat menghadirkan pengalaman makan yang tak terlupakan, kami memadukan resep tradisional dengan teknik modern.
                </p>
                <p style="color:var(--gray); line-height:1.8; font-size:14px; margin:0;">
                    Setiap produk dibuat dari bahan-bahan pilihan tanpa pengawet buatan, dipanggang segar setiap hari agar sampai ke tangan Anda dalam kondisi terbaik.
                </p>
            </div>
            <div style="background:var(--cream); border-radius:var(--radius-lg); padding:32px; text-align:center;">
                <div style="font-size:64px; margin-bottom:16px;">🏭</div>
                <div style="font-family:'Playfair Display',serif; font-size:18px; color:var(--brown-dark); font-weight:600; margin-bottom:8px;">Berdiri Sejak 2020</div>
                <p style="font-size:13px; color:var(--gray); margin:0; line-height:1.6;">
                    Melayani ribuan pelanggan setia di seluruh Indonesia dengan standar kualitas tertinggi.
                </p>
            </div>
        </div>

    </div>
</section>

<!-- VISI & MISI -->
<section style="padding:64px 0; background:var(--cream);">
    <div class="container" style="max-width:860px;">

        <div class="section-header" style="margin-bottom:40px;">
            <div class="section-label">Arah Perusahaan</div>
            <h2 class="section-title">Visi & Misi</h2>
            <div class="section-divider"></div>
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:24px;">

            <!-- VISI -->
            <div style="background:white; border-radius:var(--radius-lg); padding:32px; border-top:4px solid var(--red); box-shadow:var(--shadow);">
                <div style="font-size:36px; margin-bottom:16px;">🎯</div>
                <h3 style="font-family:'Playfair Display',serif; font-size:20px; color:var(--brown-dark); margin:0 0 14px;">Visi</h3>
                <p style="color:var(--gray); font-size:14px; line-height:1.8; margin:0;">
                    Menjadi brand bakery terpercaya dan terdepan di Indonesia yang menghadirkan produk berkualitas premium dengan pengalaman berbelanja yang mudah, cepat, dan menyenangkan.
                </p>
            </div>

            <!-- MISI -->
            <div style="background:white; border-radius:var(--radius-lg); padding:32px; border-top:4px solid var(--brown); box-shadow:var(--shadow);">
                <div style="font-size:36px; margin-bottom:16px;">🚀</div>
                <h3 style="font-family:'Playfair Display',serif; font-size:20px; color:var(--brown-dark); margin:0 0 14px;">Misi</h3>
                <ul style="color:var(--gray); font-size:14px; line-height:1.9; margin:0; padding-left:18px;">
                    <li>Menggunakan bahan baku berkualitas tinggi tanpa pengawet buatan</li>
                    <li>Menghadirkan produk segar yang dipanggang setiap hari</li>
                    <li>Memberikan pelayanan terbaik dan pengiriman tepat waktu</li>
                    <li>Memanfaatkan teknologi digital untuk pengalaman belanja yang lebih baik</li>
                </ul>
            </div>

        </div>

    </div>
</section>

<!-- KEUNGGULAN -->
<section style="padding:64px 0; background:var(--white);">
    <div class="container" style="max-width:860px;">

        <div class="section-header" style="margin-bottom:40px;">
            <div class="section-label">Mengapa Kami</div>
            <h2 class="section-title">Keunggulan Kami</h2>
            <div class="section-divider"></div>
        </div>

        <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:20px;">
            <?php
            $features = [
                ['🌿', 'Bahan Alami', 'Dipilih dengan cermat, bebas pengawet dan pewarna buatan.'],
                ['🔥', 'Segar Setiap Hari', 'Dipanggang pagi hari agar sampai dalam kondisi terbaik.'],
                ['🚀', 'Pengiriman Cepat', 'Proses order cepat dengan konfirmasi realtime via aplikasi.'],
                ['⭐', 'Kualitas Premium', 'Standar produksi ketat untuk menjaga konsistensi rasa.'],
                ['🤖', 'Rekomendasi Cerdas', 'Sistem AI menyarankan produk sesuai selera Anda.'],
                ['💬', 'Layanan Pelanggan', 'Tim kami siap membantu selama jam operasional.'],
            ];
            foreach ($features as $f): ?>
            <div style="background:var(--cream); border-radius:var(--radius); padding:24px; text-align:center;">
                <div style="font-size:36px; margin-bottom:12px;"><?= $f[0] ?></div>
                <h4 style="font-family:'Playfair Display',serif; color:var(--brown-dark); margin:0 0 8px; font-size:15px;"><?= $f[1] ?></h4>
                <p style="font-size:13px; color:var(--gray); margin:0; line-height:1.7;"><?= $f[2] ?></p>
            </div>
            <?php endforeach; ?>
        </div>

    </div>
</section>

<!-- KONTAK -->
<section style="padding:64px 0; background:var(--brown-dark); color:white; text-align:center;">
    <div class="container" style="max-width:600px;">
        <div style="font-size:40px; margin-bottom:16px;">📍</div>
        <h2 style="font-family:'Playfair Display',serif; font-size:28px; margin:0 0 24px; color:white;">Hubungi Kami</h2>

        <div style="display:flex; flex-direction:column; gap:14px; font-size:14px; color:rgba(255,255,255,0.8);">
            <div>
                <strong style="color:white;">Perusahaan</strong><br>
                PT. Mimosa Tarte Indonesia
            </div>
            <div>
                <strong style="color:white;">Alamat</strong><br>
                Jl. Raya Bakery No. 1, Jakarta Selatan, DKI Jakarta
            </div>
            <div>
                <strong style="color:white;">Email</strong><br>
                hello@mimosatarte.id
            </div>
            <div>
                <strong style="color:white;">Jam Operasional</strong><br>
                Senin – Sabtu, 07.00 – 20.00 WIB
            </div>
        </div>

        <a href="<?= base_url() ?>" style="display:inline-block; margin-top:32px; background:var(--red); color:white; padding:12px 32px; border-radius:8px; font-weight:600; text-decoration:none; font-size:14px;">
            Pesan Sekarang →
        </a>
    </div>
</section>

<?= $this->endSection() ?>
