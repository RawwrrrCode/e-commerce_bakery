<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRecommendationLogsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'                   => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'user_id'              => ['type' => 'INT', 'unsigned' => true, 'null' => false],
            'product_id'           => ['type' => 'INT', 'unsigned' => true, 'null' => false],
            'method'               => ['type' => 'ENUM', 'constraint' => ['CF', 'CBF', 'Hybrid', 'Popularity'], 'default' => 'Hybrid'],
            'cf_score'             => ['type' => 'FLOAT', 'default' => 0],
            'cbf_score'            => ['type' => 'FLOAT', 'default' => 0],
            'hybrid_score'         => ['type' => 'FLOAT', 'default' => 0],
            'recommendation_rank'  => ['type' => 'INT', 'default' => 0],
            'position_in_list'     => ['type' => 'INT', 'default' => 0],
            'is_clicked'           => ['type' => 'BOOLEAN', 'default' => false],
            'created_at'           => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('user_id');
        $this->forge->addKey('product_id');
        $this->forge->addKey('method');
        $this->forge->addKey('created_at');
        $this->forge->addKey(['user_id', 'created_at']);

        $this->forge->createTable('recommendation_logs');
    }

    public function down(): void
    {
        $this->forge->dropTable('recommendation_logs');
    }
}
