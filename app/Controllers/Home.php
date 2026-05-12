<?php

namespace App\Controllers;

use App\Models\ProductModel;
use App\Libraries\HybridRecommender;

class Home extends BaseController
{
    public function index()
    {
        $model     = new ProductModel();
        $search    = $this->request->getGet('search');
        $category  = $this->request->getGet('category');
        $recommend = $this->request->getGet('recommend');
        $perPage   = 12;
        $page      = max(1, (int)($this->request->getGet('page') ?? 1));

        if ($recommend) {
            if ($search)   $model->like('name', $search);
            if ($category) $model->where('category', $category);
            $products   = $this->calculateLegacyScore($model->findAll());
            $products   = array_values(array_filter($products, fn($p) => $p['score'] > 3));
            $totalPages = 1;
            $page       = 1;
        } else {
            if ($search)   $model->like('name', $search);
            if ($category) $model->where('category', $category);
            $totalItems = $model->countAllResults();

            if ($search)   $model->like('name', $search);
            if ($category) $model->where('category', $category);
            $products   = $model->orderBy('sold', 'DESC')->findAll($perPage, ($page - 1) * $perPage);
            $totalPages = max(1, (int)ceil($totalItems / $perPage));
        }

        // --- Hybrid Recommendation (hanya halaman 1, tanpa filter aktif) ---
        $recommendations = [];
        $userId = session()->get('id');

        if ($userId && !$search && !$category && !$recommend && $page === 1) {
            $allProducts = (new ProductModel())->findAll();
            $recommender = new HybridRecommender();
            $recommendations = $recommender->recommend((int)$userId, $allProducts);
        }

        $recommendedIds = array_column($recommendations, 'id');

        return view('home', [
            'products'        => $products,
            'category'        => $category,
            'recommend'       => $recommend,
            'recommendations' => $recommendations,
            'recommendedIds'  => $recommendedIds,
            'currentPage'     => $page,
            'totalPages'      => $totalPages,
        ]);
    }

    public function detail($id)
    {
        $model   = new ProductModel();
        $product = $model->find($id);

        if (!$product) {
            return redirect()->to('/')->with('error', 'Produk tidak ditemukan.');
        }

        $userId = session()->get('id');
        $canRate = false;
        $db = \Config\Database::connect();

        if ($userId) {

            $order = $db->table('order_detail')
                ->join('orders', 'orders.id = order_detail.order_id')
                ->where('orders.user_id', $userId)
                ->where('order_detail.product_id', $id)
                ->get()->getRow();

            if ($order) {
                $rated = $db->table('ratings')
                    ->where('user_id', $userId)
                    ->where('product_id', $id)
                    ->get()->getRow();
                $canRate = !$rated;
            }
        }

        // --- Cek boleh review atau tidak ---
        $canReview = false;
        $hasReviewed = false;
        if ($userId) {
            $hasReviewed = (bool) $db->table('reviews')
                ->where('user_id', $userId)
                ->where('product_id', $id)
                ->countAllResults();
            $canReview = $order && !$hasReviewed;
        }

        // --- Ambil semua ulasan produk ini ---
        $reviews = $db->table('reviews r')
            ->select('r.review, r.rating, r.created_at, u.name as user_name', false)
            ->join('users u', 'u.id = r.user_id', 'left')
            ->where('r.product_id', $id)
            ->where('r.review !=', '')
            ->orderBy('r.created_at', 'DESC')
            ->get()->getResultArray();

        // --- Produk Serupa (CBF) ---
        $allProducts    = $model->findAll();
        $recommender    = new HybridRecommender();
        $similarProducts = $recommender->getSimilarProducts((int)$id, $allProducts, 4);

        return view('detail', [
            'product'         => $product,
            'canRate'         => $canRate,
            'canReview'       => $canReview,
            'hasReviewed'     => $hasReviewed,
            'reviews'         => $reviews,
            'similarProducts' => $similarProducts,
        ]);
    }

    // ----------------------------------------------------------------
    // Legacy scoring (masih dipakai untuk tombol "Rekomendasi" lama)
    // ----------------------------------------------------------------
    private function calculateLegacyScore(array $products): array
    {
        foreach ($products as &$p) {
            $rating      = $p['rating'] ?? 4;
            $sold        = $p['sold']   ?? 0;
            $hargaScore  = $p['price'] > 0 ? 1 / $p['price'] : 0;

            $p['score'] = ($rating * 0.4) + ($sold * 0.3) + ($hargaScore * 0.3);
        }

        usort($products, fn($a, $b) => $b['score'] <=> $a['score']);

        return $products;
    }

    // ----------------------------------------------------------------
    // Track product interaction (AJAX endpoint)
    // ----------------------------------------------------------------
    public function trackInteraction()
    {
        $userId = session()->get('id');

        if (!$userId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Not logged in']);
        }

        $productId = $this->request->getPost('product_id');
        $action = $this->request->getPost('action') ?? 'click'; // click, add_cart, wishlist

        if (!$productId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Missing product_id']);
        }

        $interactionsModel = new \App\Models\RecommendationInteractionsModel();
        $logsModel = new \App\Models\RecommendationLogsModel();

        // Log the interaction
        $interactionsModel->logInteraction($userId, $productId, $action);

        // Mark the recommendation as clicked if it exists
        if ($action === 'click') {
            $logsModel->markProductAsClicked($userId, $productId);
        }

        return $this->response->setJSON(['success' => true, 'message' => 'Interaction logged']);
    }
}
