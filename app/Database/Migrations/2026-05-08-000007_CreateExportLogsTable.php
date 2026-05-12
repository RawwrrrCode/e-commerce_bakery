<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateExportLogsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'            => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'export_type'   => ['type' => 'ENUM', 'constraint' => ['logs', 'metrics', 'interactions', 'all'], 'null' => false],
            'file_format'   => ['type' => 'ENUM', 'constraint' => ['CSV', 'JSON'], 'default' => 'CSV'],
            'file_name'     => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => false],
            'file_path'     => ['type' => 'VARCHAR', 'constraint' => 512],
            'file_size'     => ['type' => 'INT', 'comment' => 'Bytes'],
            'date_from'     => ['type' => 'DATE', 'null' => true],
            'date_to'       => ['type' => 'DATE', 'null' => true],
            'filter_method' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true, 'comment' => 'CF/CBF/Hybrid/All'],
            'row_count'     => ['type' => 'INT', 'default' => 0],
            'created_by'    => ['type' => 'INT', 'unsigned' => true, 'null' => false, 'comment' => 'user_id'],
            'created_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('export_type');
        $this->forge->addKey('created_by');
        $this->forge->addKey('created_at');

        $this->forge->createTable('export_logs');
    }

    public function down(): void
    {
        $this->forge->dropTable('export_logs');
    }
}
