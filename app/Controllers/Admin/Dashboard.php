<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ProductModel;
use App\Models\OrderModel;

class Dashboard extends BaseController
{
    public function index()
    {
        $productModel = new ProductModel();
        $orderModel   = new OrderModel();
        $db           = \Config\Database::connect();

        $totalProduk = $productModel->countAll();
        $totalOrder  = $orderModel->countAll();
        $pending     = $orderModel->where('status', 'pending')->countAllResults();
        $totalUang   = $db->table('orders')->selectSum('total', 'total')->get()->getRow()->total ?? 0;

        // Pesanan terbaru (5)
        $recentOrders = $db->table('orders')
            ->select('orders.*, users.name as user_name')
            ->join('users', 'users.id = orders.user_id', 'left')
            ->orderBy('orders.id', 'DESC')
            ->limit(5)
            ->get()->getResultArray();

        // Produk terlaris (5)
        $topProducts = $db->table('products')
            ->orderBy('sold', 'DESC')
            ->limit(5)
            ->get()->getResultArray();

        // Statistik rekomendasi
        $totalRec     = $db->table('recommendation_logs')->countAllResults();
        $totalClicked = $db->table('recommendation_logs')->where('is_clicked', 1)->countAllResults();
        $ctr          = $totalRec > 0 ? round(($totalClicked / $totalRec) * 100, 1) : 0;
        $totalRatings = $db->table('ratings')->countAllResults();

        // User terbaru (5)
        $recentUsers = $db->table('users')
            ->where('role', 'user')
            ->orderBy('id', 'DESC')
            ->limit(5)
            ->get()->getResultArray();

        // Statistik hari ini
        $today       = date('Y-m-d');
        $todayOrders = $db->table('orders')->where('DATE(created_at)', $today)->countAllResults();
        $todayRev    = $db->table('orders')
            ->selectSum('total', 'total')
            ->where('DATE(created_at)', $today)
            ->where('payment_status', 'paid')
            ->get()->getRow()->total ?? 0;

        // Stok menipis (stok <= 5)
        $lowStock = $db->table('products')->where('stock <=', 5)->where('stock >', 0)->countAllResults();
        $outStock = $db->table('products')->where('stock', 0)->countAllResults();

        // Ulasan pending moderasi
        $totalReviews = $db->table('reviews')->countAllResults();

        return view('admin/dashboard', [
            'produk'       => $totalProduk,
            'order'        => $totalOrder,
            'pending'      => $pending,
            'uang'         => $totalUang,
            'recentOrders' => $recentOrders,
            'topProducts'  => $topProducts,
            'totalRec'     => $totalRec,
            'totalClicked' => $totalClicked,
            'ctr'          => $ctr,
            'totalRatings' => $totalRatings,
            'recentUsers'  => $recentUsers,
            'todayOrders'  => $todayOrders,
            'todayRev'     => $todayRev,
            'lowStock'     => $lowStock,
            'outStock'     => $outStock,
            'totalReviews' => $totalReviews,
        ]);
    }
}
