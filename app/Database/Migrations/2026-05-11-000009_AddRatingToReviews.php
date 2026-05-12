<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRatingToReviews extends Migration
{
    public function up()
    {
        $this->forge->addColumn('reviews', [
            'rating' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'unsigned'   => true,
                'null'       => true,
                'default'    => null,
                'after'      => 'review',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('reviews', 'rating');
    }
}
