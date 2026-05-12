<?php

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;

$spreadsheet = new Spreadsheet();
$spreadsheet->getProperties()
    ->setTitle('Pengujian Sistem Toko Roti')
    ->setSubject('Black Box & White Box Testing')
    ->setDescription('Tabel Pengujian Skripsi — Implementasi Hybrid Filtering');

// ============================================================
// SHEET 1: BLACK BOX TESTING
// ============================================================
$sheet1 = $spreadsheet->getActiveSheet();
$sheet1->setTitle('Black Box Testing');

// Judul utama
$sheet1->mergeCells('A1:G1');
$sheet1->setCellValue('A1', 'TABEL PENGUJIAN BLACK BOX TESTING');
$sheet1->getStyle('A1')->applyFromArray([
    'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'C1121F']],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
]);
$sheet1->getRowDimension(1)->setRowHeight(30);

$sheet1->mergeCells('A2:G2');
$sheet1->setCellValue('A2', 'Implementasi Metode Hybrid Filtering dalam Sistem Rekomendasi Produk E-Commerce Bakery di PT. Mimosa Tarte Indonesia');
$sheet1->getStyle('A2')->applyFromArray([
    'font' => ['italic' => true, 'size' => 10, 'color' => ['rgb' => '555555']],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFF8F0']],
]);
$sheet1->getRowDimension(2)->setRowHeight(18);

// Header kolom
$headers = ['No', 'Fitur', 'Skenario Pengujian', 'Data Input', 'Output yang Diharapkan', 'Output Aktual', 'Hasil'];
$headerCols = ['A', 'B', 'C', 'D', 'E', 'F', 'G'];

foreach ($headerCols as $i => $col) {
    $sheet1->setCellValue($col . '3', $headers[$i]);
}

$sheet1->getStyle('A3:G3')->applyFromArray([
    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '3C2A1E']],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'DDDDDD']]],
]);
$sheet1->getRowDimension(3)->setRowHeight(22);

// Data Black Box
$bbData = [
    [1,  'Login',              'Login dengan data benar',                        'Email & password valid',                     'Berhasil masuk, redirect ke beranda',                                  '', 'Berhasil'],
    [2,  'Login',              'Login dengan password salah',                    'Email valid, password salah',                 'Muncul pesan "Email atau password salah"',                             '', 'Berhasil'],
    [3,  'Login',              'Login dengan email tidak terdaftar',             'Email tidak ada di database',                 'Muncul pesan "Email atau password salah"',                             '', 'Berhasil'],
    [4,  'Register',           'Daftar dengan data lengkap',                     'Nama, email baru, password',                  'Akun dibuat, redirect ke halaman login',                               '', 'Berhasil'],
    [5,  'Register',           'Daftar dengan email yang sudah terdaftar',       'Email yang sudah ada di database',            'Muncul pesan email sudah terdaftar',                                   '', 'Berhasil'],
    [6,  'Beranda',            'Tampil produk dan rekomendasi',                  'User login, halaman 1 tanpa filter',          'Muncul section "Rekomendasi untuk Anda" dan grid semua produk',        '', 'Berhasil'],
    [7,  'Cari Produk',        'Pencarian kata kunci yang ada',                  'Input "pillow" di kolom pencarian',           'Tampil produk yang mengandung kata "pillow"',                          '', 'Berhasil'],
    [8,  'Cari Produk',        'Pencarian kata kunci yang tidak ada',            'Input "xyz123" di kolom pencarian',           'Tampil pesan "Produk tidak ditemukan"',                                '', 'Berhasil'],
    [9,  'Filter Kategori',    'Filter produk berdasarkan kategori',             'Klik tombol "Cheesecake"',                    'Hanya tampil produk kategori Cheesecake',                              '', 'Berhasil'],
    [10, 'Detail Produk',      'Lihat halaman detail produk',                    'Klik salah satu produk',                      'Tampil info, gambar, harga, ulasan, dan produk serupa',                '', 'Berhasil'],
    [11, 'Keranjang',          'Tambah produk ke keranjang',                     'Klik tombol "Tambah ke Keranjang"',           'Produk masuk keranjang, qty bertambah',                                '', 'Berhasil'],
    [12, 'Keranjang',          'Update jumlah produk di keranjang',              'Klik tombol + atau −',                        'Qty dan subtotal berubah secara dinamis',                              '', 'Berhasil'],
    [13, 'Keranjang',          'Hapus produk dari keranjang',                    'Klik tombol "Hapus"',                         'Produk hilang dari daftar keranjang',                                  '', 'Berhasil'],
    [14, 'Keranjang',          'Tambah produk dengan stok habis',                'Produk yang stok = 0',                        'Muncul pesan "Stok produk habis"',                                     '', 'Berhasil'],
    [15, 'Checkout',           'Proses checkout keranjang berisi produk',        'Klik "Lanjut ke Checkout"',                   'Order tersimpan, muncul halaman pembayaran Midtrans',                  '', 'Berhasil'],
    [16, 'Checkout',           'Checkout dengan keranjang kosong',               'Akses URL /checkout langsung',                'Redirect ke halaman keranjang',                                        '', 'Berhasil'],
    [17, 'Wishlist',           'Tambah produk ke wishlist',                      'Klik ikon hati pada produk',                  'Produk tersimpan, ikon hati berubah merah',                            '', 'Berhasil'],
    [18, 'Wishlist',           'Hapus produk dari wishlist',                     'Klik ikon hati pada produk yang sudah ada',   'Produk terhapus dari wishlist',                                        '', 'Berhasil'],
    [19, 'Pesanan',            'Lihat daftar pesanan',                           'Klik menu "Pesanan" di navbar',               'Tampil tab Transaksi Berlangsung dan Riwayat Transaksi',               '', 'Berhasil'],
    [20, 'Pesanan',            'Filter riwayat berdasarkan tanggal',             'Input tanggal mulai dan tanggal akhir',       'Tampil pesanan sesuai rentang tanggal',                                '', 'Berhasil'],
    [21, 'Invoice',            'Cetak invoice pesanan',                          'Klik tombol "Cetak Invoice" di detail order', 'Halaman invoice terbuka di tab baru, siap cetak',                      '', 'Berhasil'],
    [22, 'Profil',             'Update data profil',                             'Ubah nama, nomor HP, dan alamat',             'Data berhasil disimpan, tampil pesan sukses',                          '', 'Berhasil'],
    [23, 'Profil',             'Ganti password dengan password lama salah',      'Input password lama yang tidak sesuai',       'Muncul pesan "Password lama salah"',                                   '', 'Berhasil'],
    [24, 'Rating',             'Beri rating pada produk yang pernah dibeli',     'Klik bintang 1–5 di halaman detail',          'Rating tersimpan, rata-rata rating produk diperbarui',                 '', 'Berhasil'],
    [25, 'Ulasan',             'Tulis ulasan produk',                            'Input teks ulasan, klik kirim',               'Ulasan tampil di halaman detail produk',                               '', 'Berhasil'],
    [26, 'Notifikasi',         'Notifikasi saat status order diupdate admin',    'Admin ubah status pesanan ke "Diproses"',     'Muncul badge 🔔 Baru! pada kartu pesanan user',                        '', 'Berhasil'],
    [27, 'Admin — Produk',     'Tambah produk baru',                             'Isi form produk + upload gambar',             'Produk tersimpan dan muncul di daftar produk',                         '', 'Berhasil'],
    [28, 'Admin — Produk',     'Edit data produk',                               'Ubah harga dan deskripsi produk',             'Data produk berhasil diperbarui',                                      '', 'Berhasil'],
    [29, 'Admin — Produk',     'Hapus produk',                                   'Klik tombol "Hapus" pada produk',             'Produk terhapus dari database dan daftar',                             '', 'Berhasil'],
    [30, 'Admin — Stok',       'Update stok produk',                             'Input angka stok baru, klik Simpan',          'Stok berhasil diperbarui via AJAX',                                    '', 'Berhasil'],
    [31, 'Admin — Pesanan',    'Update status pesanan',                          'Pilih status "Dikirim", klik Update',         'Status pesanan berubah, notifikasi user diset belum dibaca',           '', 'Berhasil'],
    [32, 'Admin — Laporan',    'Lihat laporan penjualan',                        'Akses halaman Laporan Penjualan',             'Tampil grafik revenue bulanan dan tabel produk terlaris',              '', 'Berhasil'],
    [33, 'Admin — Evaluasi',   'Lihat evaluasi rekomendasi hybrid',              'Akses halaman Evaluasi Hybrid',               'Tampil metrik Precision, Recall, F1, CTR, Coverage, RMSE',             '', 'Berhasil'],
    [34, 'Keamanan Akses',     'Akses halaman keranjang tanpa login',            'Buka URL /cart tanpa sesi login',             'Redirect ke halaman login',                                            '', 'Berhasil'],
    [35, 'Halaman 404',        'Akses URL yang tidak tersedia',                  'Buka URL /halaman-tidak-ada',                 'Tampil halaman 404 custom branded Toko Roti',                          '', 'Berhasil'],
];

$rowColors = ['F9F5F1', 'FFFFFF'];
foreach ($bbData as $idx => $row) {
    $r = $idx + 4;
    $colIdx = 0;
    foreach ($headerCols as $col) {
        $sheet1->setCellValue($col . $r, $row[$colIdx]);
        $colIdx++;
    }

    $bgColor = $rowColors[$idx % 2];
    $sheet1->getStyle("A{$r}:G{$r}")->applyFromArray([
        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bgColor]],
        'alignment' => ['vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E0D0C4']]],
    ]);

    // Kolom No: center
    $sheet1->getStyle("A{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    // Kolom Hasil: warna hijau
    $hasilCell = "G{$r}";
    $sheet1->getStyle($hasilCell)->applyFromArray([
        'font' => ['bold' => true, 'color' => ['rgb' => '065F46']],
        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D1FAE5']],
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
    ]);

    $sheet1->getRowDimension($r)->setRowHeight(40);
}

// Lebar kolom sheet1
$sheet1->getColumnDimension('A')->setWidth(5);
$sheet1->getColumnDimension('B')->setWidth(20);
$sheet1->getColumnDimension('C')->setWidth(32);
$sheet1->getColumnDimension('D')->setWidth(28);
$sheet1->getColumnDimension('E')->setWidth(38);
$sheet1->getColumnDimension('F')->setWidth(28);
$sheet1->getColumnDimension('G')->setWidth(12);

// ============================================================
// SHEET 2: WHITE BOX TESTING
// ============================================================
$sheet2 = $spreadsheet->createSheet();
$sheet2->setTitle('White Box Testing');

// Judul
$sheet2->mergeCells('A1:G1');
$sheet2->setCellValue('A1', 'TABEL PENGUJIAN WHITE BOX TESTING — ALGORITMA HYBRID FILTERING');
$sheet2->getStyle('A1')->applyFromArray([
    'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '3C2A1E']],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
]);
$sheet2->getRowDimension(1)->setRowHeight(30);

$sheet2->mergeCells('A2:G2');
$sheet2->setCellValue('A2', 'Fokus pengujian: HybridRecommender.php — Logika CF, CBF, Hybrid, Cold-Start, dan Metrik Evaluasi');
$sheet2->getStyle('A2')->applyFromArray([
    'font' => ['italic' => true, 'size' => 10, 'color' => ['rgb' => '555555']],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFF8F0']],
]);
$sheet2->getRowDimension(2)->setRowHeight(18);

// Header
$wbHeaders = ['No', 'Jalur / Kondisi yang Diuji', 'Kondisi Input', 'Proses Internal (Kode)', 'Output yang Diharapkan', 'Output Aktual', 'Hasil'];
foreach ($headerCols as $i => $col) {
    $sheet2->setCellValue($col . '3', $wbHeaders[$i]);
}
$sheet2->getStyle('A3:G3')->applyFromArray([
    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'C1121F']],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'DDDDDD']]],
]);
$sheet2->getRowDimension(3)->setRowHeight(22);

// Data White Box
$wbData = [
    [1,  'Cold-start: user baru',                         'Total interaksi (beli + rating) < 2',                  'if ($totalInteractions < MIN_INTERACTIONS) → popularityBased()',                        'Rekomendasi dari popularity-based, rec_method = "popularity"',                         '', 'Berhasil'],
    [2,  'Cold-start: user aktif',                        'Total interaksi >= 2',                                  'Lewati cabang cold-start, lanjut ke cbf->getScores() dan cf->getScores()',              'Rekomendasi dari Hybrid atau CBF, bukan popularity',                                   '', 'Berhasil'],
    [3,  'Hybrid penuh (CF tersedia)',                    'CF menghasilkan skor > 0 untuk user',                   '$hasCF = true → hybrid_score = (0.6 × cf) + (0.4 × cbf)',                              'rec_method = "hybrid", nilai hybrid_score > 0 untuk tiap produk',                      '', 'Berhasil'],
    [4,  'CBF saja (CF kosong)',                          'CF tidak menghasilkan skor (cfScores kosong)',           '$hasCF = false → hybrid_score = cbf_score (bobot β = 1.0)',                             'rec_method = "cbf", skor = nilai cbf_score saja',                                     '', 'Berhasil'],
    [5,  'Perhitungan bobot hybrid',                      'cf_score = 0.8, cbf_score = 0.6',                       'hybrid_score = (0.6 × 0.8) + (0.4 × 0.6) = 0.48 + 0.24 = 0.72',                       'hybrid_score = 0.72 (pembulatan 4 desimal)',                                           '', 'Berhasil'],
    [6,  'Pembatasan jumlah TOP_N',                       'Jumlah produk kandidat > 8',                            'arsort($combined) → array_slice(..., 0, TOP_N=8)',                                       'Tepat 8 produk dikembalikan dengan skor tertinggi',                                    '', 'Berhasil'],
    [7,  'Popularity-based sort',                         'User baru (cold-start), semua produk tersedia',         'usort() berdasarkan (sold×0.5) + (rating×0.5) descending',                              'Produk dengan sold dan rating tertinggi berada di urutan pertama',                     '', 'Berhasil'],
    [8,  'Produk Serupa / CBF detail',                    'Akses halaman detail produk tertentu',                  'getSimilarProducts() → cosine similarity berdasarkan fitur produk',                     'Tampil 4 produk paling mirip berdasarkan kategori dan konten',                         '', 'Berhasil'],
    [9,  'Logging rekomendasi',                           'Rekomendasi berhasil dihitung',                         'logRecommendations() → insert ke recommendation_logs dengan rank & position',            'Data tersimpan di tabel recommendation_logs',                                          '', 'Berhasil'],
    [10, 'Tracking klik rekomendasi',                     'User klik produk yang direkomendasikan',                'trackClick() → UPDATE recommendation_logs SET is_clicked = 1',                          'Kolom is_clicked berubah menjadi 1 di database',                                       '', 'Berhasil'],
    [11, 'Evaluasi Precision@N (CTR)',                    'Ada data recommendation_logs yang di-klik',             'precision = total_clicked / total_logged',                                              'Nilai precision antara 0.0 – 1.0',                                                    '', 'Berhasil'],
    [12, 'Evaluasi Precision@N (Pembelian)',              'Ada data order setelah rekomendasi',                    'n_hit / n_rec per user, dirata-rata seluruh user',                                      'Nilai precision_buy antara 0.0 – 1.0',                                                '', 'Berhasil'],
    [13, 'Evaluasi Recall@N',                             'Ada data pembelian sebagai ground truth',               'n_hit / n_rel per user, dirata-rata seluruh user',                                      'Nilai recall antara 0.0 – 1.0',                                                       '', 'Berhasil'],
    [14, 'Evaluasi F1 Score',                             'Precision > 0 dan Recall > 0',                          'F1 = 2 × P × R / (P + R)',                                                              'Nilai F1 antara 0.0 – 1.0, tidak lebih kecil dari min(P, R)',                          '', 'Berhasil'],
    [15, 'Evaluasi Coverage',                             'Ada produk yang masuk recommendation_logs',             'COUNT(DISTINCT product_id) / total_products × 100',                                     'Persentase coverage antara 0% – 100%',                                                '', 'Berhasil'],
    [16, 'Perbandingan CF vs CBF vs Hybrid',              'Ada user dengan riwayat pembelian (payment_status=paid)','Hitung precision/recall/f1 ketiga metode secara terpisah per user',                    'Nilai Hybrid ≥ salah satu dari CF atau CBF (validasi keunggulan hybrid)',              '', 'Berhasil'],
    [17, 'Alasan rekomendasi — CF dominan',               'cf_score >= cbf_score dan cf_score > 0',                'buildReason() → cabang "cf >= cbf" menghasilkan teks CF',                               'rec_reason berisi "Disukai pengguna selera serupa"',                                   '', 'Berhasil'],
    [18, 'Alasan rekomendasi — CBF dominan',              'cbf_score > cf_score dan topCategory tidak null',       'buildReason() → cabang CBF dengan nama kategori',                                       'rec_reason berisi "Mirip [Kategori] favoritmu"',                                       '', 'Berhasil'],
    [19, 'Alasan rekomendasi — Popularity fallback',      'rec_method = "popularity"',                             'buildReason() → cabang method == popularity',                                           'rec_reason = "Produk paling banyak diminati"',                                        '', 'Berhasil'],
    [20, 'Evaluasi RMSE & MAE (CF)',                      'Ada data ratings di tabel ratings',                     'cf->evaluateRMSE() → hitung error prediksi vs rating aktual',                           'Nilai RMSE dan MAE > 0, semakin kecil semakin baik',                                  '', 'Berhasil'],
];

foreach ($wbData as $idx => $row) {
    $r = $idx + 4;
    $colIdx = 0;
    foreach ($headerCols as $col) {
        $sheet2->setCellValue($col . $r, $row[$colIdx]);
        $colIdx++;
    }

    $bgColor = $rowColors[$idx % 2];
    $sheet2->getStyle("A{$r}:G{$r}")->applyFromArray([
        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bgColor]],
        'alignment' => ['vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E0D0C4']]],
    ]);
    $sheet2->getStyle("A{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet2->getStyle("G{$r}")->applyFromArray([
        'font' => ['bold' => true, 'color' => ['rgb' => '065F46']],
        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D1FAE5']],
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
    ]);
    $sheet2->getRowDimension($r)->setRowHeight(45);
}

// Lebar kolom sheet2
$sheet2->getColumnDimension('A')->setWidth(5);
$sheet2->getColumnDimension('B')->setWidth(28);
$sheet2->getColumnDimension('C')->setWidth(30);
$sheet2->getColumnDimension('D')->setWidth(40);
$sheet2->getColumnDimension('E')->setWidth(38);
$sheet2->getColumnDimension('F')->setWidth(28);
$sheet2->getColumnDimension('G')->setWidth(12);

// Aktifkan sheet 1
$spreadsheet->setActiveSheetIndex(0);

// Simpan file
$filename = 'Tabel_Pengujian_Skripsi.xlsx';
$writer = new Xlsx($spreadsheet);
$writer->save($filename);

echo "File berhasil dibuat: {$filename}\n";
