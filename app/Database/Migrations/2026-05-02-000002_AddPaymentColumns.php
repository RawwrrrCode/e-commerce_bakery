<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Tambah kolom payment ke tabel orders.
 *
 * Jika tidak pakai spark migrate, jalankan SQL ini di phpMyAdmin:
 * -------------------------------------------------------------------
 * ALTER TABLE `orders`
 *   ADD COLUMN `payment_status`     ENUM('unpaid','paid','failed','expired')
 *                                   NOT NULL DEFAULT 'unpaid' AFTER `status`,
 *   ADD COLUMN `snap_token`         TEXT NULL AFTER `payment_status`,
 *   ADD COLUMN `midtrans_order_id`  VARCHAR(100) NULL AFTER `snap_token`;
 * -------------------------------------------------------------------
 */
class AddPaymentColumns extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('orders', [
            'payment_status' => [
                'type'       => 'ENUM',
                'constraint' => ['unpaid', 'paid', 'failed', 'expired'],
                'default'    => 'unpaid',
                'after'      => 'status',
            ],
            'snap_token' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'payment_status',
            ],
            'midtrans_order_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'after'      => 'snap_token',
            ],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('orders', ['payment_status', 'snap_token', 'midtrans_order_id']);
    }
}
