# Toko Roti — E-Commerce Bakery

Aplikasi web toko online bakery berbasis **CodeIgniter 4 (PHP)** dengan sistem rekomendasi produk hybrid. Dibangun sebagai proyek skripsi.

---

## Fitur Utama

### Pelanggan
- Halaman beranda, katalog produk, dan detail produk
- Keranjang belanja & wishlist
- Checkout & pembayaran via **Midtrans**
- Riwayat pesanan & invoice
- Ulasan produk
- Rekomendasi produk personal berbasis AI

### Admin
- Dashboard ringkasan transaksi & stok
- Manajemen produk, kategori, dan stok
- Manajemen pesanan & ulasan
- Laporan penjualan & ekspor data
- Panel sistem rekomendasi (Content-Based + Hybrid Filtering)
- Metrics & analitik performa sistem

---

## Teknologi

| Komponen | Detail |
|---|---|
| Framework | CodeIgniter 4 |
| Bahasa | PHP 8.2+ |
| Database | MySQL |
| Payment | Midtrans |
| Rekomendasi | Content-Based Filtering + Hybrid Recommender |
| Font | Playfair Display, Poppins (Google Fonts) |

---

## Instalasi

### Prasyarat
- PHP 8.2 atau lebih tinggi
- Composer
- MySQL
- Ekstensi PHP: `intl`, `mbstring`, `mysqlnd`, `libcurl`

### Langkah Setup

```bash
# 1. Clone repositori
git clone https://github.com/rawwrrrcode/e-commerce_bakery.git
cd e-commerce_bakery

# 2. Install dependensi
composer install

# 3. Salin file konfigurasi
cp .env.example .env

# 4. Edit .env — isi konfigurasi database dan Midtrans
#    CI_ENVIRONMENT = development
#    database.default.hostname = localhost
#    database.default.database = nama_database
#    database.default.username = root
#    database.default.password =

# 5. Jalankan migrasi database
php spark migrate

# 6. (Opsional) Isi data awal
php spark db:seed

# 7. Jalankan server lokal
php spark serve
