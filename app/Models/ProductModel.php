<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductModel extends Model
{
    protected $table = 'products';
protected $allowedFields = [
    'name',
    'price',
    'description',
    'image',
    'category',
    'is_best_seller',
    'rating',
    'rating_count',
    'sold'
];
}
