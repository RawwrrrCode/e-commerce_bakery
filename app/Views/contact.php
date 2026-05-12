<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<?php
$storeName  = $config['store_name']    ?? 'Toko Roti';
$tagline    = $config['store_tagline'] ?? 'PT. Mimosa Tarte Indonesia';
$address    = $config['store_address'] ?? '';
$phone      = $config['store_phone']   ?? '';
$email      = $config['store_email']   ?? '';
$hours      = $config['store_hours']   ?? '';
$instagram  = $config['store_instagram'] ?? '';
$mapsEmbed  = $config['store_maps_embed'] ?? '';
$waClean    = preg_replace('/\D/', '', $phone);
?>

<div class="container" style="padding-top:48px; padding-bottom:64px; max-width:920px; margin:0 auto;">

    <div style="text-align:center; margin-bottom:40px;">
        <h1 style="font-family:'Playfair Display',serif; font-size:32px; color:var(--brown-dark); margin-bottom:8px;">
            Hubungi Kami
        </h1>
        <p style="color:var(--gray); font-size:15px; max-width:480px; margin:0 auto; line-height:1.7;">
            Ada pertanyaan atau butuh bantuan? Tim kami siap membantu Anda.
        </p>
    </div>

    <div style="display:grid; grid-template-columns:1fr 1fr; gap:24px; align-items:start;">

        <!-- INFO KONTAK -->
        <div>

            <!-- WA CTA -->
            <?php if ($phone): ?>
            <div style="background:linear-gradient(135deg,#F0FDF4,#DCFCE7); border:1.5px solid #86EFAC; border-radius:16px; padding:28px; margin-bottom:20px; text-align:center;">
                <div style="font-size:48px; margin-bottom:12px;">💬</div>
                <h3 style="font-size:18px; font-weight:700; color:#065F46; margin:0 0 6px;">Chat WhatsApp CS</h3>
                <p style="font-size:13px; color:#047857; margin:0 0 18px; line-height:1.6;">
                    Respon cepat! Kami siap melayani Anda<br>
                    <?php if ($hours): ?><strong><?= htmlspecialchars($hours) ?></strong><?php endif; ?>
                </p>
                <a href="https://wa.me/<?= $waClean ?>?text=Halo+<?= urlencode($storeName) ?>%2C+saya+ingin+bertanya..."
                   target="_blank"
                   style="display:inline-flex; align-items:center; gap:10px; background:#25D366; color:white; padding:13px 28px; border-radius:12px; font-size:15px; font-weight:700; text-decoration:none;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="white">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                    </svg>
                    Chat Sekarang
                </a>
                <div style="margin-top:10px; font-size:12px; color:#6B7280;">
                    +<?= htmlspecialchars($waClean) ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Detail kontak -->
            <div style="background:white; border-radius:16px; border:1px solid var(--border); padding:24px;">
                <h3 style="font-size:15px; font-weight:700; color:var(--brown-dark); margin:0 0 18px; padding-bottom:12px; border-bottom:1px solid var(--border);">
                    📋 Informasi Toko
                </h3>

                <div style="display:flex; flex-direction:column; gap:14px; font-size:14px;">

                    <div style="font-weight:700; font-size:16px; color:var(--brown-dark);">
                        <?= htmlspecialchars($storeName) ?>
                        <?php if ($tagline): ?>
                        <div style="font-size:12px; font-weight:400; color:var(--gray); margin-top:2px;"><?= htmlspecialchars($tagline) ?></div>
                        <?php endif; ?>
                    </div>

                    <?php if ($address): ?>
                    <div style="display:flex; gap:10px; align-items:flex-start;">
                        <span style="font-size:18px; flex-shrink:0; margin-top:1px;">📍</span>
                        <span style="color:var(--brown); line-height:1.6;"><?= nl2br(htmlspecialchars($address)) ?></span>
                    </div>
                    <?php endif; ?>

                    <?php if ($hours): ?>
                    <div style="display:flex; gap:10px; align-items:center;">
                        <span style="font-size:18px; flex-shrink:0;">🕐</span>
                        <span style="color:var(--brown);"><?= htmlspecialchars($hours) ?></span>
                    </div>
                    <?php endif; ?>

                    <?php if ($email): ?>
                    <div style="display:flex; gap:10px; align-items:center;">
                        <span style="font-size:18px; flex-shrink:0;">✉️</span>
                        <a href="mailto:<?= htmlspecialchars($email) ?>" style="color:var(--red); font-weight:600; text-decoration:none;">
                            <?= htmlspecialchars($email) ?>
                        </a>
                    </div>
                    <?php endif; ?>

                    <?php if ($instagram): ?>
                    <div style="display:flex; gap:10px; align-items:center;">
                        <span style="font-size:18px; flex-shrink:0;">📸</span>
                        <a href="https://instagram.com/<?= htmlspecialchars($instagram) ?>" target="_blank"
                           style="color:#E1306C; font-weight:600; text-decoration:none;">
                            @<?= htmlspecialchars($instagram) ?>
                        </a>
                    </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>

        <!-- MAPS / PESAN SEKARANG -->
        <div style="display:flex; flex-direction:column; gap:20px;">

            <?php if ($mapsEmbed): ?>
            <div style="border-radius:16px; overflow:hidden; border:1px solid var(--border); height:280px;">
                <iframe src="<?= htmlspecialchars($mapsEmbed) ?>"
                        width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
            <?php else: ?>
            <div style="background:var(--gray-light); border-radius:16px; border:1px solid var(--border); height:220px; display:flex; align-items:center; justify-content:center; text-align:center; color:var(--gray); padding:20px;">
                <div>
                    <div style="font-size:40px; margin-bottom:10px;">🗺️</div>
                    <p style="margin:0; font-size:13px;">Peta belum dikonfigurasi.<br>Admin dapat menambahkan di Pengaturan Toko.</p>
                </div>
            </div>
            <?php endif; ?>

            <!-- CTA Belanja -->
            <div style="background:linear-gradient(135deg,#FFF8F0,#FEF3E8); border:1.5px solid #E8D5C4; border-radius:16px; padding:24px; text-align:center;">
                <div style="font-size:36px; margin-bottom:10px;">🍞</div>
                <h3 style="font-size:17px; font-weight:700; color:var(--brown-dark); margin:0 0 8px;">
                    Siap untuk memesan?
                </h3>
                <p style="font-size:13px; color:var(--gray); margin:0 0 18px; line-height:1.6;">
                    Jelajahi produk roti kami yang segar dan lezat setiap hari.
                </p>
                <a href="<?= base_url() ?>"
                   style="display:inline-flex; align-items:center; gap:8px; background:var(--red); color:white; padding:12px 28px; border-radius:10px; font-size:14px; font-weight:700; text-decoration:none;">
                    🛒 Belanja Sekarang
                </a>
            </div>

        </div>
    </div>

</div>

<?= $this->endSection() ?>
