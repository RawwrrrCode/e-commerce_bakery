<?php

namespace App\Controllers;

class Cart extends BaseController
{
    public function index()
    {

        $db = \Config\Database::connect();

        // ✅ FIX DISINI
        $user_id = session()->get('id');

        $builder = $db->table('cart');
        $builder->select('cart.*, products.name, products.price, products.image');
        $builder->join('products', 'products.id = cart.product_id');
        $builder->where('cart.user_id', $user_id);

        $data['cart'] = $builder->get()->getResultArray();

        return view('cart', $data);
    }

    public function add()
    {
        $db         = \Config\Database::connect();
        $product_id = $this->request->getPost('product_id');
        $qty        = (int)$this->request->getPost('qty');

        $product = $db->table('products')->where('id', $product_id)->get()->getRow();

        if (!$product || $product->stock <= 0) {
            return redirect()->back()->with('error', 'Stok produk habis');
        }

        $model = new \App\Models\CartModel();
        $model->save([
            'user_id'    => session()->get('id'),
            'product_id' => $product_id,
            'qty'        => $qty,
        ]);

        return redirect()->to('/');
    }

    public function update()
    {
        $model = new \App\Models\CartModel();

        $model->update(
            $this->request->getPost('id'),
            ['qty' => $this->request->getPost('qty')]
        );

        return $this->response->setJSON(['status' => 'ok']);
    }

    public function delete()
    {
        $model = new \App\Models\CartModel();

        $model->delete($this->request->getPost('id'));

        return $this->response->setJSON(['status' => 'deleted']);
    }

    public function checkout()
    {
        $db = \Config\Database::connect();

        // ✅ FIX DISINI
        $user_id = session()->get('id');

        $cart = $db->table('cart')
            ->join('products', 'products.id = cart.product_id')
            ->where('cart.user_id', $user_id)
            ->get()
            ->getResultArray();

        if (!$cart) {
            return redirect()->to('/cart');
        }

        $total = 0;
        foreach ($cart as $c) {
            $total += $c['price'] * $c['qty'];
        }

        $db->table('orders')->insert([
            'user_id' => $user_id,
            'total' => $total,
            'status' => 'pending'
        ]);

        $order_id = $db->insertID();

        foreach ($cart as $c) {

            $db->table('order_detail')->insert([
                'order_id' => $order_id,
                'product_id' => $c['product_id'],
                'qty' => $c['qty'],
                'price' => $c['price']
            ]);

            $db->table('products')
                ->set('sold', 'sold+' . $c['qty'], false)
                ->set('stock', 'GREATEST(0, stock-' . $c['qty'] . ')', false)
                ->where('id', $c['product_id'])
                ->update();
        }

        $db->table('cart')->where('user_id', $user_id)->delete();

        return redirect()->to(base_url('orders'))->with('success', 'Checkout berhasil');
    }
}