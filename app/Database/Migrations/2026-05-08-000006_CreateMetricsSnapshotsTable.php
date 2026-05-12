<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMetricsSnapshotsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'                => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'snapshot_date'     => ['type' => 'DATE', 'null' => false],
            'method'            => ['type' => 'ENUM', 'constraint' => ['CF', 'CBF', 'Hybrid', 'Overall'], 'default' => 'Hybrid'],
            'precision'         => ['type' => 'FLOAT', 'default' => 0],
            'recall'            => ['type' => 'FLOAT', 'default' => 0],
            'rmse'              => ['type' => 'FLOAT', 'default' => 0],
            'mae'               => ['type' => 'FLOAT', 'default' => 0],
            'coverage'          => ['type' => 'FLOAT', 'default' => 0],
            'ctr'               => ['type' => 'FLOAT', 'comment' => 'Click-Through Rate %'],
            'conversion_rate'   => ['type' => 'FLOAT', 'comment' => 'Purchase conversion %'],
            'total_recommendations' => ['type' => 'INT', 'default' => 0],
            'total_interactions'    => ['type' => 'INT', 'default' => 0],
            'total_clicks'          => ['type' => 'INT', 'default' => 0],
            'total_purchases'       => ['type' => 'INT', 'default' => 0],
            'created_at'        => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('snapshot_date');
        $this->forge->addKey(['snapshot_date', 'method']);

        $this->forge->createTable('metrics_snapshots');
    }

    public function down(): void
    {
        $this->forge->dropTable('metrics_snapshots');
    }
}
