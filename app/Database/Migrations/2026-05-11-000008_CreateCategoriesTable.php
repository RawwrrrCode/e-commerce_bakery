<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCategoriesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'name'       => ['type' => 'VARCHAR', 'constraint' => 100],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('categories', true);

        // Seed initial categories from existing product data
        $db = \Config\Database::connect();
        $existing = $db->table('products')->select('category')->distinct()->where('category !=', '')->get()->getResultArray();
        foreach ($existing as $row) {
            if (!empty($row['category'])) {
                $db->table('categories')->ignore(true)->insert([
                    'name'       => $row['category'],
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }
    }

    public function down()
    {
        $this->forge->dropTable('categories', true);
    }
}
