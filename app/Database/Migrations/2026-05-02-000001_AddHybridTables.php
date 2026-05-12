<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migrasi: Tambah tabel pendukung Hybrid Filtering
 *
 * Jalankan dengan: php spark migrate
 * Atau import file SQL di bawah ini langsung ke phpMyAdmin.
 *
 * SQL MANUAL (tempel ke phpMyAdmin jika tidak pakai spark):
 * -----------------------------------------------------------
 * CREATE TABLE IF NOT EXISTS `recommendation_log` (
 *   `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 *   `user_id`    INT UNSIGNED NOT NULL,
 *   `product_id` INT UNSIGNED NOT NULL,
 *   `method`     ENUM('hybrid','cbf','popularity') NOT NULL DEFAULT 'popularity',
 *   `score`      FLOAT NOT NULL DEFAULT 0,
 *   `clicked`    TINYINT(1) NOT NULL DEFAULT 0,
 *   `created_at` DATETIME NOT NULL,
 *   INDEX idx_user   (`user_id`),
 *   INDEX idx_product(`product_id`)
 * ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
 * -----------------------------------------------------------
 */
class AddHybridTables extends Migration
{
    public function up(): void
    {
        // Tabel log rekomendasi (untuk evaluasi CTR, Coverage, Precision)
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 10,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
            ],
            'product_id' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
            ],
            'method' => [
                'type'       => 'ENUM',
                'constraint' => ['hybrid', 'cbf', 'popularity'],
                'default'    => 'popularity',
            ],
            'score' => [
                'type'    => 'FLOAT',
                'default' => 0,
            ],
            'clicked' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'created_at' => [
                'type' => 'DATETIME',
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('user_id');
        $this->forge->addKey('product_id');
        $this->forge->createTable('recommendation_log', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('recommendation_log', true);
    }
}
