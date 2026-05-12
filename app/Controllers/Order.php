<?php

namespace App\Controllers;

class Order extends BaseController
{
    // GET /checkout — tampilkan halaman konfirmasi sebelum bayar
    public function checkout()
    {
        $db      = \Config\Database::connect();
        $user_id = session()->get('id');

        $cart = $db->table('cart')
            ->select('cart.*, products.name, products.price, products.image')
            ->join('products', 'products.id = cart.product_id')
            ->where('cart.user_id', $user_id)
            ->get()->getResultArray();

        if (empty($cart)) {
            return redirect()->to('/cart');
        }

        $total = 0;
        foreach ($cart as $c) {
            $total += $c['price'] * $c['qty'];
        }

        return view('checkout', ['cart' => $cart, 'total' => $total]);
    }

    // POST /checkout — buat order lalu ambil Midtrans Snap token
    public function processCheckout()
    {
        $db      = \Config\Database::connect();
        $user_id = session()->get('id');

        $cart = $db->table('cart')
            ->select('cart.*, products.name, products.price, products.image')
            ->join('products', 'products.id = cart.product_id')
            ->where('cart.user_id', $user_id)
            ->get()->getResultArray();

        if (empty($cart)) {
            return redirect()->to('/cart');
        }

        $total = 0;
        foreach ($cart as $c) {
            $total += $c['price'] * $c['qty'];
        }

        // Simpan order
        $db->table('orders')->insert([
            'user_id'        => $user_id,
            'total'          => $total,
            'status'         => 'pending',
            'payment_status' => 'unpaid',
        ]);
        $order_id = $db->insertID();

        foreach ($cart as $c) {
            $db->table('order_detail')->insert([
                'order_id'   => $order_id,
                'product_id' => $c['product_id'],
                'qty'        => $c['qty'],
                'price'      => $c['price'],
            ]);
        }

        // Kosongkan keranjang
        $db->table('cart')->where('user_id', $user_id)->delete();

        // Data user untuk Midtrans
        $user = $db->table('users')->where('id', $user_id)->get()->getRow();

        // Setup Midtrans
        $cfg = config('Midtrans');
        \Midtrans\Config::$serverKey    = $cfg->serverKey;
        \Midtrans\Config::$isProduction = $cfg->isProduction;
        \Midtrans\Config::$isSanitized  = true;
        \Midtrans\Config::$is3ds        = true;

        $mtOrderId   = 'ORDER-' . $order_id . '-' . time();
        $itemDetails = [];

        foreach ($cart as $c) {
            $itemDetails[] = [
                'id'       => (string)$c['product_id'],
                'price'    => (int)$c['price'],
                'quantity' => (int)$c['qty'],
                'name'     => substr($c['name'], 0, 50),
            ];
        }

        $params = [
            'transaction_details' => [
                'order_id'     => $mtOrderId,
                'gross_amount' => (int)$total,
            ],
            'customer_details' => [
                'first_name' => $user->name ?? 'Customer',
                'email'      => $user->email ?? '',
            ],
            'item_details' => $itemDetails,
            'callbacks'    => ['finish' => base_url('orders')],
        ];

        try {
            $snapToken = \Midtrans\Snap::getSnapToken($params);
            $db->table('orders')->where('id', $order_id)->update([
                'snap_token'        => $snapToken,
                'midtrans_order_id' => $mtOrderId,
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Midtrans Snap error: ' . $e->getMessage());
            return redirect()->to('/orders/' . $order_id . '/pay')
                ->with('error', 'Gagal terhubung ke Midtrans. Coba bayar ulang dari halaman pesanan.');
        }

        return redirect()->to('/orders/' . $order_id . '/pay');
    }

    // GET /orders/{id}/pay — halaman pembayaran (bisa diakses ulang)
    public function pay(int $id)
    {
        $db      = \Config\Database::connect();
        $user_id = session()->get('id');

        $order = $db->table('orders')
            ->where('id', $id)
            ->where('user_id', $user_id)
            ->get()->getRow();

        if (!$order) {
            return redirect()->to('/orders');
        }

        if ($order->payment_status === 'paid') {
            return redirect()->to('/orders')->with('info', 'Pesanan ini sudah lunas.');
        }

        // Jika belum ada snap_token, generate baru
        if (empty($order->snap_token)) {
            $items = $db->table('order_detail')
                ->select('order_detail.*, products.name')
                ->join('products', 'products.id = order_detail.product_id')
                ->where('order_id', $id)
                ->get()->getResultArray();

            $user = $db->table('users')->where('id', $user_id)->get()->getRow();

            $cfg = config('Midtrans');
            \Midtrans\Config::$serverKey    = $cfg->serverKey;
            \Midtrans\Config::$isProduction = $cfg->isProduction;
            \Midtrans\Config::$isSanitized  = true;
            \Midtrans\Config::$is3ds        = true;

            $mtOrderId   = 'ORDER-' . $id . '-' . time();
            $itemDetails = [];

            foreach ($items as $item) {
                $itemDetails[] = [
                    'id'       => (string)$item['product_id'],
                    'price'    => (int)$item['price'],
                    'quantity' => (int)$item['qty'],
                    'name'     => substr($item['name'], 0, 50),
                ];
            }

            $params = [
                'transaction_details' => [
                    'order_id'     => $mtOrderId,
                    'gross_amount' => (int)$order->total,
                ],
                'customer_details' => [
                    'first_name' => $user->name ?? 'Customer',
                    'email'      => $user->email ?? '',
                ],
                'item_details' => $itemDetails,
                'callbacks'    => ['finish' => base_url('orders')],
            ];

            try {
                $snapToken = \Midtrans\Snap::getSnapToken($params);
                $db->table('orders')->where('id', $id)->update([
                    'snap_token'        => $snapToken,
                    'midtrans_order_id' => $mtOrderId,
                ]);
                $order->snap_token = $snapToken;
            } catch (\Exception $e) {
                log_message('error', 'Midtrans Snap error: ' . $e->getMessage());
                return redirect()->to('/orders')
                    ->with('error', 'Gagal terhubung ke Midtrans: ' . $e->getMessage());
            }
        }

        $cfg = config('Midtrans');

        return view('payment', [
            'order'        => $order,
            'clientKey'    => $cfg->clientKey,
            'isProduction' => $cfg->isProduction,
        ]);
    }

    // GET /orders/{id}/check-status — cek status ke Midtrans API (tanpa webhook)
    public function checkStatus(int $id)
    {
        $db      = \Config\Database::connect();
        $user_id = session()->get('id');

        $order = $db->table('orders')
            ->where('id', $id)
            ->where('user_id', $user_id)
            ->get()->getRow();

        if (!$order) {
            return redirect()->to('/orders');
        }

        if (empty($order->midtrans_order_id)) {
            return redirect()->to('/orders/' . $id . '/pay')
                ->with('error', 'Belum ada transaksi yang dibuat. Klik "Bayar Sekarang" terlebih dahulu.');
        }

        // Tanya langsung ke Midtrans API
        $cfg = config('Midtrans');
        \Midtrans\Config::$serverKey    = $cfg->serverKey;
        \Midtrans\Config::$isProduction = $cfg->isProduction;

        try {
            $status = \Midtrans\Transaction::status($order->midtrans_order_id);
        } catch (\Exception $e) {
            return redirect()->to('/orders/' . $id . '/pay')
                ->with('error', 'Gagal cek status: ' . $e->getMessage());
        }

        $txStatus    = $status->transaction_status;
        $fraudStatus = $status->fraud_status ?? null;

        if ($txStatus === 'capture') {
            $payStatus = ($fraudStatus === 'accept') ? 'paid' : 'failed';
        } elseif ($txStatus === 'settlement') {
            $payStatus = 'paid';
        } elseif (in_array($txStatus, ['cancel', 'deny', 'failure'])) {
            $payStatus = 'failed';
        } elseif ($txStatus === 'expire') {
            $payStatus = 'expired';
        } else {
            $payStatus = 'unpaid';
        }

        // Hanya update jika belum pernah paid (hindari double update sold)
        if ($order->payment_status !== 'paid') {
            $update = ['payment_status' => $payStatus];

            if ($payStatus === 'paid') {
                $update['status'] = 'processing';

                $items = $db->table('order_detail')->where('order_id', $id)->get()->getResultArray();
                foreach ($items as $item) {
                    $db->table('products')
                        ->set('sold', 'sold+' . $item['qty'], false)
                        ->set('stock', 'GREATEST(0, stock-' . $item['qty'] . ')', false)
                        ->where('id', $item['product_id'])
                        ->update();

                    // Log purchase interaction
                    $interactionsModel = new \App\Models\RecommendationInteractionsModel();
                    $interactionsModel->logInteraction($user_id, $item['product_id'], 'purchase');
                }
            }

            $db->table('orders')->where('id', $id)->update($update);
        }

        if ($payStatus === 'paid') {
            return redirect()->to('/orders')
                ->with('info', '✅ Pembayaran berhasil! Pesanan sedang diproses.');
        } elseif ($payStatus === 'unpaid') {
            return redirect()->to('/orders/' . $id . '/pay')
                ->with('info', 'Pembayaran belum selesai. Silakan selesaikan pembayaran di popup.');
        } else {
            return redirect()->to('/orders')
                ->with('error', 'Status pembayaran: ' . $payStatus . '. Silakan hubungi admin.');
        }
    }

    // POST /payment/notification — webhook dari Midtrans
    public function notification()
    {
        $cfg = config('Midtrans');
        \Midtrans\Config::$serverKey    = $cfg->serverKey;
        \Midtrans\Config::$isProduction = $cfg->isProduction;

        try {
            $notif = new \Midtrans\Notification();
        } catch (\Exception $e) {
            return $this->response->setStatusCode(400)->setJSON(['error' => $e->getMessage()]);
        }

        $txStatus    = $notif->transaction_status;
        $mtOrderId   = $notif->order_id;
        $fraudStatus = $notif->fraud_status ?? null;

        $db    = \Config\Database::connect();
        $order = $db->table('orders')->where('midtrans_order_id', $mtOrderId)->get()->getRow();

        if (!$order) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Order not found']);
        }

        if ($txStatus === 'capture') {
            $payStatus = ($fraudStatus === 'accept') ? 'paid' : 'failed';
        } elseif ($txStatus === 'settlement') {
            $payStatus = 'paid';
        } elseif (in_array($txStatus, ['cancel', 'deny', 'failure'])) {
            $payStatus = 'failed';
        } elseif ($txStatus === 'expire') {
            $payStatus = 'expired';
        } else {
            $payStatus = 'unpaid';
        }

        $update = ['payment_status' => $payStatus];

        if ($payStatus === 'paid') {
            $update['status'] = 'processing';

            // Update sold count per produk setelah pembayaran lunas
            $items = $db->table('order_detail')->where('order_id', $order->id)->get()->getResultArray();
            foreach ($items as $item) {
                $db->table('products')
                    ->set('sold', 'sold+' . $item['qty'], false)
                    ->set('stock', 'GREATEST(0, stock-' . $item['qty'] . ')', false)
                    ->where('id', $item['product_id'])
                    ->update();

                // Log purchase interaction
                $interactionsModel = new \App\Models\RecommendationInteractionsModel();
                $interactionsModel->logInteraction($order->user_id, $item['product_id'], 'purchase');
            }
        }

        $db->table('orders')->where('id', $order->id)->update($update);

        return $this->response->setJSON(['status' => 'ok']);
    }

    // GET /orders
    public function index()
    {
        $db      = \Config\Database::connect();
        $user_id = session()->get('id');

        // Mark all unread notifications as read
        $db->table('orders')
            ->where('user_id', $user_id)
            ->where('notification_read', 0)
            ->update(['notification_read' => 1]);

        $tab      = $this->request->getGet('tab') ?? 'ongoing';
        $invoice  = trim($this->request->getGet('invoice') ?? '');
        $dateFrom = $this->request->getGet('date_from') ?? '';
        $dateTo   = $this->request->getGet('date_to') ?? '';
        $perPage  = 10;
        $page     = max(1, (int)($this->request->getGet('page') ?? 1));
        $offset   = ($page - 1) * $perPage;

        // Ongoing: belum selesai / belum gagal
        $ongoingBuilder = $db->table('orders')
            ->where('user_id', $user_id)
            ->groupStart()
                ->where('payment_status', 'unpaid')
                ->orGroupStart()
                    ->where('payment_status', 'paid')
                    ->whereNotIn('status', ['selesai'])
                ->groupEnd()
            ->groupEnd();
        $ongoingCount  = (clone $ongoingBuilder)->countAllResults();
        $ongoingOrders = (clone $ongoingBuilder)->orderBy('id', 'DESC')->get()->getResultArray();

        // History: selesai atau gagal/expired
        $historyBuilder = $db->table('orders')
            ->where('user_id', $user_id)
            ->groupStart()
                ->where('status', 'selesai')
                ->orWhereIn('payment_status', ['failed', 'expired'])
            ->groupEnd();
        if ($invoice !== '') {
            $historyBuilder->like('id', $invoice);
        }
        if ($dateFrom !== '') {
            $historyBuilder->where('created_at >=', $dateFrom . ' 00:00:00');
        }
        if ($dateTo !== '') {
            $historyBuilder->where('created_at <=', $dateTo . ' 23:59:59');
        }
        $historyTotal  = (clone $historyBuilder)->countAllResults();
        $historyOrders = (clone $historyBuilder)->orderBy('id', 'DESC')
            ->limit($perPage, $offset)->get()->getResultArray();

        return view('orders', [
            'tab'           => $tab,
            'ongoingOrders' => $ongoingOrders,
            'ongoingCount'  => $ongoingCount,
            'historyOrders' => $historyOrders,
            'historyTotal'  => $historyTotal,
            'invoice'       => $invoice,
            'dateFrom'      => $dateFrom,
            'dateTo'        => $dateTo,
            'currentPage'   => $page,
            'totalPages'    => max(1, (int)ceil($historyTotal / $perPage)),
        ]);
    }

    // GET /orders/{id}
    public function detail($id)
    {
        $db    = \Config\Database::connect();
        $order = $db->table('orders')->where('id', $id)->get()->getRow();

        $items = $db->table('order_detail')
            ->join('products', 'products.id = order_detail.product_id')
            ->where('order_id', $id)
            ->get()->getResult();

        return view('order_detail', ['order' => $order, 'items' => $items]);
    }

    // GET /orders/{id}/invoice
    public function invoice($id)
    {
        $db      = \Config\Database::connect();
        $user_id = session()->get('id');

        $order = $db->table('orders')
            ->where('id', $id)
            ->where('user_id', $user_id)
            ->get()->getRow();

        if (!$order) return redirect()->to('/orders');

        $items = $db->table('order_detail')
            ->select('order_detail.*, products.name, products.image, products.category')
            ->join('products', 'products.id = order_detail.product_id')
            ->where('order_id', $id)
            ->get()->getResultArray();

        $user = $db->table('users')->where('id', $user_id)->get()->getRow();

        return view('invoice', [
            'order' => $order,
            'items' => $items,
            'user'  => $user,
        ]);
    }

    // POST /orders/{id}/confirm — user konfirmasi pesanan diterima
    public function confirm($id)
    {
        $db      = \Config\Database::connect();
        $user_id = session()->get('id');

        $order = $db->table('orders')
            ->where('id', $id)
            ->where('user_id', $user_id)
            ->get()->getRow();

        if (!$order) {
            return redirect()->to('/orders')->with('error', 'Pesanan tidak ditemukan.');
        }

        if ($order->status !== 'shipped' || ($order->payment_status ?? 'unpaid') !== 'paid') {
            return redirect()->to('/orders/' . $id)->with('error', 'Pesanan belum dapat dikonfirmasi.');
        }

        $db->table('orders')->where('id', $id)->update([
            'status'            => 'selesai',
            'notification_read' => 1,
        ]);

        return redirect()->to('/orders/' . $id)->with('success', '✅ Pesanan berhasil dikonfirmasi sebagai diterima!');
    }

    // POST /review
    public function review()
    {
        $db      = \Config\Database::connect();
        $user_id = session()->get('id');

        if (!$user_id) {
            return $this->response->setJSON(['success' => false, 'message' => 'Harus login']);
        }

        $product_id = $this->request->getPost('product_id');
        $review     = trim($this->request->getPost('review') ?? '');
        $rating     = (int)($this->request->getPost('rating') ?? 0);

        $cek = $db->table('orders')
            ->join('order_detail', 'order_detail.order_id = orders.id')
            ->where('orders.user_id', $user_id)
            ->where('order_detail.product_id', $product_id)
            ->countAllResults();

        if ($cek == 0) {
            return $this->response->setJSON(['success' => false, 'message' => 'Anda belum membeli produk ini']);
        }

        $insertData = [
            'user_id'    => $user_id,
            'product_id' => $product_id,
            'review'     => $review,
        ];
        if ($rating >= 1 && $rating <= 5) {
            $insertData['rating'] = $rating;

            // Update product average rating
            $product = $db->table('products')->where('id', $product_id)->get()->getRow();
            if ($product) {
                $newCount  = $product->rating_count + 1;
                $newRating = (($product->rating * $product->rating_count) + $rating) / $newCount;
                $db->table('products')->where('id', $product_id)->update([
                    'rating'       => $newRating,
                    'rating_count' => $newCount,
                ]);
            }
        }

        $db->table('reviews')->insert($insertData);

        return $this->response->setJSON(['success' => true, 'message' => 'Ulasan berhasil disimpan']);
    }

    // POST /orders/{id}/reorder
    public function reorder($id)
    {
        $db      = \Config\Database::connect();
        $user_id = session()->get('id');

        $order = $db->table('orders')
            ->where('id', $id)
            ->where('user_id', $user_id)
            ->get()->getRow();

        if (!$order) return redirect()->to('/orders');

        $items = $db->table('order_detail')
            ->select('order_detail.product_id, order_detail.qty, products.stock, products.name')
            ->join('products', 'products.id = order_detail.product_id')
            ->where('order_id', $id)
            ->get()->getResultArray();

        $added   = 0;
        $skipped = 0;
        foreach ($items as $item) {
            if ((int)$item['stock'] <= 0) { $skipped++; continue; }

            $qty      = min((int)$item['qty'], (int)$item['stock']);
            $existing = $db->table('cart')
                ->where('user_id', $user_id)
                ->where('product_id', $item['product_id'])
                ->get()->getRow();

            if ($existing) {
                $newQty = min($existing->qty + $qty, (int)$item['stock']);
                $db->table('cart')->where('id', $existing->id)->update(['qty' => $newQty]);
            } else {
                $db->table('cart')->insert([
                    'user_id'    => $user_id,
                    'product_id' => $item['product_id'],
                    'qty'        => $qty,
                ]);
            }
            $added++;
        }

        if ($added === 0) {
            return redirect()->to('/orders/' . $id)->with('error', '⚠️ Semua produk pada pesanan ini habis stok.');
        }

        $msg = '✅ ' . $added . ' produk berhasil ditambahkan ke keranjang!';
        if ($skipped > 0) $msg .= ' (' . $skipped . ' produk dilewati karena habis stok)';

        return redirect()->to('/cart')->with('success', $msg);
    }
}
