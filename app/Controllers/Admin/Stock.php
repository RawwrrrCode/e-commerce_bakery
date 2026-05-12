<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ProductModel;

class Stock extends BaseController
{
    public function index()
    {
        $model    = new ProductModel();
        $products = $model->orderBy('name', 'ASC')->findAll();

        return view('admin/stock/index', [
            'products' => $products,
            'success'  => session()->getFlashdata('success'),
        ]);
    }

    public function update()
    {
        $db         = \Config\Database::connect();
        $product_id = (int)$this->request->getPost('product_id');
        $stock      = max(0, (int)$this->request->getPost('stock'));

        $db->table('products')->where('id', $product_id)->update(['stock' => $stock]);

        return $this->response->setJSON(['success' => true, 'stock' => $stock]);
    }
}
