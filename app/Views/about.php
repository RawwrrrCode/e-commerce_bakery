<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<?php
$storeName  = $config['store_name']      ?? 'Mimosa';
$tagline    = $config['store_tagline']   ?? 'PT. Mimosa Tarte Indonesia';
$address    = $config['store_address']   ?? '';
$phone      = $config['store_phone']     ?? '';
$storeEmail = $config['store_email']     ?? '';
$hours      = $config['store_hours']     ?? '';
$instagram  = $config['store_instagram'] ?? '';
$mapsEmbed  = $config['store_maps_embed'] ?? '';
$waClean    = preg_replace('/\D/', '', $phone);
?>

<style>
@media (max-width: 768px) {
    .about-two-col   { grid-template-columns: 1fr !important; gap: 28px !important; }
    .about-three-col { grid-template-columns: 1fr 1fr !important; }
    .about-contact   { grid-template-columns: 1fr !important; }
}
@media (max-width: 480px) {
    .about-three-col { grid-template-columns: 1fr !important; }
}
</style>

<!-- HERO TENTANG KAMI -->
<section style="background:linear-gradient(135deg, #3B1F0F 0%, #6B3A25 100%); padding:80px 0 60px; text-align:center; color:white;">
    <div class="container">
        <div style="font-size:56px; margin-bottom:16px;">🍞</div>
        <h1 style="font-family:'Playfair Display',serif; font-size:38px; margin:0 0 12px; color:white;"><?= htmlspecialchars($tagline ?: 'PT. Mimosa Tarte Indonesia') ?></h1>
        <p style="font-size:16px; color:rgba(255,255,255,0.75); max-width:560px; margin:0 auto; line-height:1.7;">
            Menghadirkan roti dan kue berkualitas premium dengan cita rasa autentik, dipanggang segar setiap hari untuk kepuasan Anda.
        </p>
    </div>
</section>

<!-- TENTANG PERUSAHAAN -->
<section style="padding:64px 0; background:var(--white);">
    <div class="container" style="max-width:860px;">

        <div class="about-two-col" style="display:grid; grid-template-columns:1fr 1fr; gap:48px; align-items:center;">
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

        <div class="about-two-col" style="display:grid; grid-template-columns:1fr 1fr; gap:24px;">

            <div style="background:white; border-radius:var(--radius-lg); padding:32px; border-top:4px solid var(--red); box-shadow:var(--shadow);">
                <div style="font-size:36px; margin-bottom:16px;">🎯</div>
                <h3 style="font-family:'Playfair Display',serif; font-size:20px; color:var(--brown-dark); margin:0 0 14px;">Visi</h3>
                <p style="color:var(--gray); font-size:14px; line-height:1.8; margin:0;">
                    Menjadi brand bakery terpercaya dan terdepan di Indonesia yang menghadirkan produk berkualitas premium dengan pengalaman berbelanja yang mudah, cepat, dan menyenangkan.
                </p>
            </div>

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

        <div class="about-three-col" style="display:grid; grid-template-columns:repeat(3,1fr); gap:20px;">
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

<!-- HUBUNGI KAMI -->
<section id="kontak" style="padding:64px 0; background:var(--cream);">
    <div class="container" style="max-width:900px;">

        <div class="section-header" style="margin-bottom:40px;">
            <div class="section-label">Hubungi Kami</div>
            <h2 class="section-title">Kontak & Lokasi</h2>
            <div class="section-divider"></div>
            <p class="section-subtitle" style="margin-top:12px;">Ada pertanyaan atau butuh bantuan? Tim kami siap membantu Anda.</p>
        </div>

        <div class="about-contact" style="display:grid; grid-template-columns:1fr 1fr; gap:24px; align-items:start;">

            <!-- INFO + WA -->
            <div style="display:flex; flex-direction:column; gap:20px;">

                <?php if ($phone): ?>
                <div style="background:linear-gradient(135deg,#F0FDF4,#DCFCE7); border:1.5px solid #86EFAC; border-radius:16px; padding:28px; text-align:center;">
                    <div style="font-size:44px; margin-bottom:10px;">💬</div>
                    <h3 style="font-size:17px; font-weight:700; color:#065F46; margin:0 0 6px;">Chat WhatsApp CS</h3>
                    <?php if ($hours): ?>
                    <p style="font-size:13px; color:#047857; margin:0 0 16px;">Respon cepat! Kami siap <strong><?= htmlspecialchars($hours) ?></strong></p>
                    <?php endif; ?>
                    <a href="https://wa.me/<?= $waClean ?>?text=Halo+<?= urlencode($storeName) ?>%2C+saya+ingin+bertanya..."
                       target="_blank"
                       style="display:inline-flex; align-items:center; gap:10px; background:#25D366; color:white; padding:12px 26px; border-radius:12px; font-size:15px; font-weight:700; text-decoration:none;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="white">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                        </svg>
                        Chat Sekarang
                    </a>
                    <div style="margin-top:8px; font-size:12px; color:#6B7280;">+<?= htmlspecialchars($waClean) ?></div>
                </div>
                <?php endif; ?>

                <div style="background:white; border-radius:16px; border:1px solid var(--border); padding:24px;">
                    <h3 style="font-size:14px; font-weight:700; color:var(--brown-dark); margin:0 0 16px; padding-bottom:12px; border-bottom:1px solid var(--border);">
                        📋 Informasi Toko
                    </h3>
                    <div style="display:flex; flex-direction:column; gap:13px; font-size:14px;">

                        <div style="font-weight:700; font-size:15px; color:var(--brown-dark);">
                            <?= htmlspecialchars($storeName) ?>
                            <?php if ($tagline): ?>
                            <div style="font-size:12px; font-weight:400; color:var(--gray); margin-top:2px;"><?= htmlspecialchars($tagline) ?></div>
                            <?php endif; ?>
                        </div>

                        <?php if ($address): ?>
                        <div style="display:flex; gap:10px; align-items:flex-start;">
                            <span style="font-size:16px; flex-shrink:0;">📍</span>
                            <span style="color:var(--brown); line-height:1.6;"><?= nl2br(htmlspecialchars($address)) ?></span>
                        </div>
                        <?php else: ?>
                        <div style="color:var(--gray); font-size:13px; font-style:italic;">Alamat belum dikonfigurasi.</div>
                        <?php endif; ?>

                        <?php if ($hours): ?>
                        <div style="display:flex; gap:10px; align-items:center;">
                            <span style="font-size:16px;">🕐</span>
                            <span style="color:var(--brown);"><?= htmlspecialchars($hours) ?></span>
                        </div>
                        <?php endif; ?>

                        <?php if ($storeEmail): ?>
                        <div style="display:flex; gap:10px; align-items:center;">
                            <span style="font-size:16px;">✉️</span>
                            <a href="mailto:<?= htmlspecialchars($storeEmail) ?>" style="color:var(--red); font-weight:600; text-decoration:none;">
                                <?= htmlspecialchars($storeEmail) ?>
                            </a>
                        </div>
                        <?php endif; ?>

                        <?php if ($instagram): ?>
                        <div style="display:flex; gap:10px; align-items:center;">
                            <span style="font-size:16px;">📸</span>
                            <a href="https://instagram.com/<?= htmlspecialchars($instagram) ?>" target="_blank"
                               style="color:#E1306C; font-weight:600; text-decoration:none;">
                                @<?= htmlspecialchars($instagram) ?>
                            </a>
                        </div>
                        <?php endif; ?>

                    </div>
                </div>

            </div>

            <!-- MAPS + CTA -->
            <div style="display:flex; flex-direction:column; gap:20px;">

                <?php if ($mapsEmbed): ?>
                <div style="border-radius:16px; overflow:hidden; border:1px solid var(--border); height:280px;">
                    <iframe src="<?= htmlspecialchars($mapsEmbed) ?>"
                            width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
                <?php else: ?>
                <div style="background:var(--gray-light); border-radius:16px; border:1px solid var(--border); height:200px; display:flex; align-items:center; justify-content:center; text-align:center; color:var(--gray); padding:20px;">
                    <div>
                        <div style="font-size:36px; margin-bottom:8px;">🗺️</div>
                        <p style="margin:0; font-size:13px;">Peta belum dikonfigurasi.<br>Tambahkan di <strong>Admin → Pengaturan Toko</strong>.</p>
                    </div>
                </div>
                <?php endif; ?>

                <div style="background:linear-gradient(135deg,#FFF8F0,#FEF3E8); border:1.5px solid #E8D5C4; border-radius:16px; padding:24px; text-align:center;">
                    <div style="font-size:32px; margin-bottom:10px;">🍞</div>
                    <h3 style="font-size:16px; font-weight:700; color:var(--brown-dark); margin:0 0 8px;">Siap untuk memesan?</h3>
                    <p style="font-size:13px; color:var(--gray); margin:0 0 16px; line-height:1.6;">Jelajahi produk roti kami yang segar dan lezat setiap hari.</p>
                    <a href="<?= base_url() ?>"
                       style="display:inline-flex; align-items:center; gap:8px; background:var(--red); color:white; padding:11px 26px; border-radius:10px; font-size:14px; font-weight:700; text-decoration:none;">
                        🛒 Belanja Sekarang
                    </a>
                </div>

            </div>

        </div>

    </div>
</section>

<?= $this->endSection() ?>
