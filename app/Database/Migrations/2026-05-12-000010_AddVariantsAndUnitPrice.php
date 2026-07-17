<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddVariantsAndUnitPrice extends Migration
{
    public function up()
    {
        // variants JSON ke products
        $this->forge->addColumn('products', [
            'variants' => ['type' => 'TEXT', 'null' => true, 'after' => 'description'],
        ]);
        // unit_price ke cart (harga varian yang dipilih user)
        $this->forge->addColumn('cart', [
            'unit_price' => ['type' => 'INT', 'unsigned' => true, 'null' => true, 'after' => 'qty'],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('products', 'variants');
        $this->forge->dropColumn('cart', 'unit_price');
    }
}
