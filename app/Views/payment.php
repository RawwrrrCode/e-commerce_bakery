<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="container" style="padding-top:60px; padding-bottom:80px; max-width:520px;">

    <?php if (session()->getFlashdata('error')): ?>
    <div style="background:#FEE2E2; border:1px solid #FECACA; color:#B91C1C; padding:14px 18px; border-radius:10px; margin-bottom:24px; font-size:14px;">
        ⚠️ <?= session()->getFlashdata('error') ?>
    </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('info')): ?>
    <div style="background:#EFF6FF; border:1px solid #BFDBFE; color:#1E40AF; padding:14px 18px; border-radius:10px; margin-bottom:24px; font-size:14px;">
        ℹ️ <?= session()->getFlashdata('info') ?>
    </div>
    <?php endif; ?>

    <div style="text-align:center; margin-bottom:40px;">
        <div style="font-size:56px; margin-bottom:12px;">💳</div>
        <h2 style="font-family:'Playfair Display',serif; color:var(--brown-dark); font-size:26px; margin-bottom:6px;">
            Selesaikan Pembayaran
        </h2>
        <p style="color:var(--gray); font-size:14px;">
            Order #<?= str_pad($order->id, 4, '0', STR_PAD_LEFT) ?>
        </p>
    </div>

    <!-- RINGKASAN ORDER -->
    <div style="background:white; border-radius:16px; box-shadow:var(--shadow); padding:28px; margin-bottom:24px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
            <span style="color:var(--gray); font-size:14px;">Total Pembayaran</span>
            <span style="font-family:'Playfair Display',serif; font-size:26px; font-weight:700; color:var(--red);">
                Rp <?= number_format($order->total, 0, ',', '.') ?>
            </span>
        </div>
        <div style="border-top:1px dashed #E8D5C4; padding-top:14px; display:flex; justify-content:space-between; font-size:13px; color:var(--gray);">
            <span>Status</span>
            <span style="color:#E67E22; font-weight:600;">⏳ Menunggu Pembayaran</span>
        </div>
    </div>

    <!-- TOMBOL BAYAR -->
    <button id="pay-button" onclick="openPayment()"
            style="width:100%; padding:16px; background:var(--red); color:white; border:none; border-radius:12px;
                   font-size:16px; font-weight:700; cursor:pointer; letter-spacing:0.5px; transition:0.2s;"
            onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
        💳 Bayar Sekarang
    </button>

    <!-- TOMBOL CEK STATUS (muncul setelah popup ditutup) -->
    <div id="check-status-box" style="display:none; margin-top:16px;">
        <div style="background:#FFF8F0; border:1px solid #E8D5C4; border-radius:12px; padding:18px; text-align:center;">
            <p style="font-size:14px; color:var(--brown-dark); margin-bottom:12px;">
                Sudah selesai bayar di popup? Klik tombol di bawah untuk konfirmasi.
            </p>
            <a href="<?= base_url('orders/' . $order->id . '/check-status') ?>"
               style="display:inline-block; background:#059669; color:white; padding:12px 28px;
                      border-radius:10px; font-weight:700; font-size:14px; text-decoration:none;">
                ✅ Saya Sudah Bayar — Cek Status
            </a>
        </div>
    </div>

    <p style="text-align:center; font-size:12px; color:var(--gray); margin-top:16px;">
        Pembayaran diproses oleh <strong>Midtrans</strong> — aman &amp; terenkripsi 🔒
    </p>

    <a href="<?= base_url('orders') ?>"
       style="display:block; text-align:center; margin-top:16px; font-size:13px; color:var(--gray);">
        ← Bayar nanti (pesanan tersimpan di Pesanan Saya)
    </a>

</div>

<!-- Midtrans Snap JS -->
<script src="<?= $isProduction
    ? 'https://app.midtrans.com/snap/snap.js'
    : 'https://app.sandbox.midtrans.com/snap/snap.js' ?>"
    data-client-key="<?= htmlspecialchars($clientKey) ?>"></script>

<script>
var snapToken = '<?= $order->snap_token ?>';

function openPayment() {
    var btn = document.getElementById('pay-button');
    btn.disabled = true;
    btn.textContent = '⏳ Membuka pembayaran...';

    window.snap.pay(snapToken, {
        onSuccess: function(result) {
            window.location.href = '<?= base_url('orders/' . $order->id . '/check-status') ?>';
        },
        onPending: function(result) {
            btn.disabled = false;
            btn.textContent = '💳 Bayar Sekarang';
            document.getElementById('check-status-box').style.display = 'block';
        },
        onError: function(result) {
            btn.disabled = false;
            btn.textContent = '💳 Bayar Sekarang';
            alert('Pembayaran gagal. Silakan coba lagi.');
        },
        onClose: function() {
            btn.disabled = false;
            btn.textContent = '💳 Bayar Sekarang';
            // Tampilkan tombol cek status setelah popup ditutup
            document.getElementById('check-status-box').style.display = 'block';
        }
    });
}

// Auto-buka popup hanya jika tidak ada flash message (bukan redirect dari check-status)
var hasFlashMessage = <?= (session()->getFlashdata('info') || session()->getFlashdata('error')) ? 'true' : 'false' ?>;
window.addEventListener('load', function() {
    if (!hasFlashMessage) {
        setTimeout(openPayment, 500);
    }
});
</script>

<?= $this->endSection() ?>
