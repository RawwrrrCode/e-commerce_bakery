<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRecommendationInteractionsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'                    => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'recommendation_id'     => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'user_id'               => ['type' => 'INT', 'unsigned' => true, 'null' => false],
            'product_id'            => ['type' => 'INT', 'unsigned' => true, 'null' => false],
            'action'                => ['type' => 'ENUM', 'constraint' => ['view', 'click', 'add_cart', 'purchase', 'wishlist'], 'null' => false],
            'method'                => ['type' => 'ENUM', 'constraint' => ['CF', 'CBF', 'Hybrid', 'Popularity'], 'default' => 'Hybrid'],
            'action_timestamp'      => ['type' => 'DATETIME', 'null' => false],
            'created_at'            => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('recommendation_id');
        $this->forge->addKey('user_id');
        $this->forge->addKey('product_id');
        $this->forge->addKey('action');
        $this->forge->addKey(['user_id', 'action_timestamp']);
        $this->forge->addKey(['product_id', 'action']);

        $this->forge->createTable('recommendation_interactions');
    }

    public function down(): void
    {
        $this->forge->dropTable('recommendation_interactions');
    }
}
