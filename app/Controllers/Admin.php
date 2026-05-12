<?php
namespace App\Controllers;

use App\Models\ProductModel;
use App\Models\OrderModel;

class Admin extends BaseController
{
    public function index()
    {
        $product = new ProductModel();

        return view('admin/dashboard', [
            'totalProduct' => $product->countAll()
        ]);
    }
}