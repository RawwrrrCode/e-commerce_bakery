<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<!-- HERO -->
<section style="background:linear-gradient(135deg, #3B1F0F 0%, #6B3A25 100%); padding:64px 0 48px; text-align:center; color:white;">
    <div class="container">
        <div style="font-size:48px; margin-bottom:12px;">❓</div>
        <h1 style="font-family:'Playfair Display',serif; font-size:34px; margin:0 0 10px; color:white;">Pertanyaan yang Sering Diajukan</h1>
        <p style="font-size:14px; color:rgba(255,255,255,0.7); margin:0;">Temukan jawaban atas pertanyaan umum seputar produk, pembayaran, dan pengiriman.</p>
    </div>
</section>

<!-- FAQ CONTENT -->
<section style="padding:64px 0; background:var(--cream);">
    <div class="container" style="max-width:760px;">

        <?php
        $faqGroups = [
            [
                'icon'  => '🛍️',
                'title' => 'Pemesanan & Produk',
                'items' => [
                    [
                        'q' => 'Apakah semua produk dipanggang segar setiap hari?',
                        'a' => 'Ya! Semua produk kami dipanggang segar setiap pagi sebelum toko dibuka. Kami tidak menggunakan pengawet buatan sehingga kesegaran produk sangat kami jaga.',
                    ],
                    [
                        'q' => 'Bagaimana cara memesan produk?',
                        'a' => 'Pilih produk yang Anda inginkan, tambahkan ke keranjang, lalu lanjutkan ke halaman checkout. Anda perlu login terlebih dahulu untuk menyelesaikan pemesanan.',
                    ],
                    [
                        'q' => 'Apakah saya bisa memesan produk dalam jumlah banyak (bulk order)?',
                        'a' => 'Tentu! Untuk pemesanan dalam jumlah besar (misalnya untuk acara atau hampers), silakan hubungi kami langsung melalui email di hello@mimosatarte.id agar kami bisa menyiapkan pesanan Anda.',
                    ],
                    [
                        'q' => 'Apakah ada batas minimum pembelian?',
                        'a' => 'Tidak ada batas minimum pembelian. Anda bisa memesan bahkan hanya satu produk.',
                    ],
                ],
            ],
            [
                'icon'  => '💳',
                'title' => 'Pembayaran',
                'items' => [
                    [
                        'q' => 'Metode pembayaran apa saja yang tersedia?',
                        'a' => 'Kami mendukung berbagai metode pembayaran melalui Midtrans, termasuk transfer bank (BCA, BRI, Mandiri, BNI), kartu kredit/debit, GoPay, OVO, DANA, dan gerai minimarket (Indomaret, Alfamart).',
                    ],
                    [
                        'q' => 'Apakah transaksi saya aman?',
                        'a' => 'Ya. Semua transaksi diproses melalui Midtrans yang telah bersertifikat PCI-DSS. Data kartu dan akun pembayaran Anda tidak disimpan di server kami.',
                    ],
                    [
                        'q' => 'Berapa lama batas waktu pembayaran?',
                        'a' => 'Setelah checkout, Anda memiliki waktu 24 jam untuk menyelesaikan pembayaran. Jika melewati batas waktu, pesanan akan otomatis dibatalkan.',
                    ],
                    [
                        'q' => 'Bagaimana jika pembayaran saya gagal?',
                        'a' => 'Anda bisa mencoba lagi melalui halaman "Pesanan Saya" dengan menekan tombol "Bayar Sekarang". Jika masih bermasalah, hubungi tim kami di hello@mimosatarte.id.',
                    ],
                ],
            ],
            [
                'icon'  => '🚚',
                'title' => 'Pengiriman',
                'items' => [
                    [
                        'q' => 'Berapa lama pesanan saya tiba?',
                        'a' => 'Pesanan yang masuk sebelum pukul 12.00 WIB akan diproses dan dikirim pada hari yang sama. Estimasi pengiriman 1–3 hari kerja tergantung lokasi tujuan.',
                    ],
                    [
                        'q' => 'Apakah ada biaya pengiriman?',
                        'a' => 'Biaya pengiriman dihitung berdasarkan lokasi Anda dan akan ditampilkan saat checkout sebelum pembayaran dilakukan.',
                    ],
                    [
                        'q' => 'Apakah produk aman selama pengiriman?',
                        'a' => 'Kami menggunakan kemasan khusus food-grade yang dirancang untuk menjaga produk tetap utuh dan segar selama pengiriman.',
                    ],
                    [
                        'q' => 'Bagaimana cara melacak pesanan saya?',
                        'a' => 'Anda bisa memantau status pesanan di halaman "Pesanan Saya". Status akan diperbarui oleh admin dari Pending → Diproses → Dikirim → Selesai. Anda akan mendapat notifikasi setiap ada perubahan status.',
                    ],
                ],
            ],
            [
                'icon'  => '🔄',
                'title' => 'Pengembalian & Keluhan',
                'items' => [
                    [
                        'q' => 'Apakah saya bisa mengembalikan produk?',
                        'a' => 'Karena sifat produk makanan segar, kami tidak menerima pengembalian. Namun jika produk yang Anda terima rusak atau tidak sesuai pesanan, segera hubungi kami dalam 24 jam setelah penerimaan.',
                    ],
                    [
                        'q' => 'Bagaimana cara mengajukan keluhan?',
                        'a' => 'Hubungi kami melalui email hello@mimosatarte.id dengan menyertakan nomor pesanan dan foto produk yang bermasalah. Tim kami akan merespons dalam 1×24 jam.',
                    ],
                ],
            ],
            [
                'icon'  => '⭐',
                'title' => 'Akun & Rekomendasi',
                'items' => [
                    [
                        'q' => 'Apakah saya harus membuat akun untuk berbelanja?',
                        'a' => 'Ya, akun diperlukan untuk melakukan pemesanan. Pendaftaran gratis dan hanya membutuhkan nama, email, dan kata sandi.',
                    ],
                    [
                        'q' => 'Bagaimana sistem rekomendasi produk bekerja?',
                        'a' => 'Sistem kami menggunakan Hybrid Recommendation — kombinasi Collaborative Filtering (preferensi pengguna serupa) dan Content-Based Filtering (kemiripan produk). Semakin sering Anda berbelanja dan memberi rating, semakin akurat rekomendasinya.',
                    ],
                    [
                        'q' => 'Apakah ulasan produk saya bisa dilihat orang lain?',
                        'a' => 'Ya, ulasan yang Anda tulis akan ditampilkan di halaman detail produk dan dapat dibaca oleh pengguna lain sebagai referensi.',
                    ],
                ],
            ],
        ];
        ?>

        <?php foreach ($faqGroups as $gi => $group): ?>
        <div style="margin-bottom:36px;">

            <!-- Group Header -->
            <div style="display:flex; align-items:center; gap:10px; margin-bottom:16px;">
                <span style="font-size:24px;"><?= $group['icon'] ?></span>
                <h2 style="font-family:'Playfair Display',serif; font-size:20px; color:var(--brown-dark); margin:0;"><?= $group['title'] ?></h2>
            </div>

            <!-- Accordion Items -->
            <?php foreach ($group['items'] as $ii => $item): ?>
            <?php $id = 'faq-' . $gi . '-' . $ii; ?>
            <div class="faq-item" style="background:white; border-radius:var(--radius); margin-bottom:8px; border:1px solid #EDE0D8; overflow:hidden;">
                <button class="faq-btn" onclick="toggleFaq('<?= $id ?>')"
                    style="width:100%; text-align:left; padding:16px 20px; background:none; border:none; cursor:pointer; display:flex; justify-content:space-between; align-items:center; font-family:'Poppins',sans-serif; font-size:14px; font-weight:500; color:var(--brown-dark); gap:12px;">
                    <span><?= htmlspecialchars($item['q']) ?></span>
                    <span id="<?= $id ?>-icon" style="font-size:18px; color:var(--red); flex-shrink:0; transition:transform 0.2s;">＋</span>
                </button>
                <div id="<?= $id ?>" style="display:none; padding:0 20px 16px; font-size:14px; color:var(--gray); line-height:1.8; border-top:1px solid #F5EDE8;">
                    <div style="padding-top:12px;"><?= htmlspecialchars($item['a']) ?></div>
                </div>
            </div>
            <?php endforeach; ?>

        </div>
        <?php endforeach; ?>

        <!-- STILL HAVE QUESTIONS -->
        <?php
        $phone   = $config['store_phone'] ?? '';
        $email   = $config['store_email'] ?? '';
        $storeName = $config['store_name'] ?? 'Mimosa';
        $waClean = preg_replace('/\D/', '', $phone);
        ?>
        <div style="background:var(--brown-dark); border-radius:var(--radius-lg); padding:32px; text-align:center; color:white; margin-top:12px;">
            <div style="font-size:32px; margin-bottom:12px;">💬</div>
            <h3 style="font-family:'Playfair Display',serif; font-size:20px; margin:0 0 8px; color:white;">Masih punya pertanyaan?</h3>
            <p style="font-size:13px; color:rgba(255,255,255,0.7); margin:0 0 20px;">Kami siap membantu Anda. Hubungi tim kami langsung.</p>

            <div style="display:flex; gap:12px; justify-content:center; flex-wrap:wrap;">
                <?php if ($waClean): ?>
                <a href="https://wa.me/<?= $waClean ?>?text=Halo+<?= urlencode($storeName) ?>%2C+saya+punya+pertanyaan..."
                   target="_blank"
                   style="display:inline-flex; align-items:center; gap:8px; background:#25D366; color:white; padding:10px 22px; border-radius:8px; font-size:14px; font-weight:600; text-decoration:none;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="white"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    Chat WhatsApp
                </a>
                <?php endif; ?>

                <?php if ($email): ?>
                <a href="mailto:<?= htmlspecialchars($email) ?>"
                   style="display:inline-flex; align-items:center; gap:8px; background:var(--red); color:white; padding:10px 22px; border-radius:8px; font-size:14px; font-weight:600; text-decoration:none;">
                    ✉️ Kirim Email
                </a>
                <?php else: ?>
                <a href="<?= base_url('about') ?>#kontak"
                   style="display:inline-flex; align-items:center; gap:8px; background:var(--red); color:white; padding:10px 22px; border-radius:8px; font-size:14px; font-weight:600; text-decoration:none;">
                    📋 Lihat Kontak
                </a>
                <?php endif; ?>
            </div>
        </div>

    </div>
</section>

<script>
function toggleFaq(id) {
    const panel = document.getElementById(id);
    const icon  = document.getElementById(id + '-icon');
    const open  = panel.style.display === 'block';
    panel.style.display = open ? 'none' : 'block';
    icon.textContent    = open ? '＋' : '－';
}
</script>

<?= $this->endSection() ?>
