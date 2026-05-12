<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateWishlistsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'user_id'    => ['type' => 'INT', 'unsigned' => true, 'null' => false],
            'product_id' => ['type' => 'INT', 'unsigned' => true, 'null' => false],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['user_id', 'product_id']);
        $this->forge->addKey('user_id');
        $this->forge->addKey('product_id');

        $this->forge->createTable('wishlists');
    }

    public function down(): void
    {
        $this->forge->dropTable('wishlists');
    }
}
