<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductImagesModel extends Model
{
    protected $table         = 'product_images';
    protected $allowedFields = ['product_id', 'image', 'sort_order', 'created_at'];
}
