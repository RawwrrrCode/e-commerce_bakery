<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Report extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();

        // ── Summary cards ──────────────────────────────────────────
        $totalRevenue = $db->query(
            "SELECT COALESCE(SUM(total),0) AS val FROM orders WHERE payment_status='paid'"
        )->getRow()->val;

        $totalOrders = $db->table('orders')->countAllResults();

        $paidOrders = $db->query(
            "SELECT COUNT(*) AS val FROM orders WHERE payment_status='paid'"
        )->getRow()->val;

        $avgOrder = $paidOrders > 0 ? round($totalRevenue / $paidOrders) : 0;

        // ── Monthly revenue — last 12 months ───────────────────────
        $monthly = $db->query("
            SELECT
                DATE_FORMAT(created_at, '%Y-%m')   AS bulan_key,
                DATE_FORMAT(created_at, '%b %Y')   AS bulan_label,
                SUM(total)                          AS pendapatan,
                COUNT(*)                            AS jumlah
            FROM orders
            WHERE payment_status = 'paid'
              AND created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
            GROUP BY DATE_FORMAT(created_at, '%Y-%m'), DATE_FORMAT(created_at, '%b %Y')
            ORDER BY bulan_key ASC
        ")->getResultArray();

        // ── Top 10 products by revenue ─────────────────────────────
        $topProducts = $db->query("
            SELECT
                p.id, p.name, p.image, p.category,
                SUM(od.qty)           AS total_sold,
                SUM(od.qty * od.price) AS revenue
            FROM order_detail od
            JOIN products p  ON p.id  = od.product_id
            JOIN orders   o  ON o.id  = od.order_id
            WHERE o.payment_status = 'paid'
            GROUP BY p.id, p.name, p.image, p.category
            ORDER BY revenue DESC
            LIMIT 10
        ")->getResultArray();

        // ── Order status distribution ──────────────────────────────
        $statusDist = $db->query("
            SELECT status, COUNT(*) AS total
            FROM orders
            GROUP BY status
        ")->getResultArray();

        return view('admin/report/index', [
            'totalRevenue' => $totalRevenue,
            'totalOrders'  => $totalOrders,
            'paidOrders'   => $paidOrders,
            'avgOrder'     => $avgOrder,
            'monthly'      => $monthly,
            'topProducts'  => $topProducts,
            'statusDist'   => $statusDist,
        ]);
    }
}
