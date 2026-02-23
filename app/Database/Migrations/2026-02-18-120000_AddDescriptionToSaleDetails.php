<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDescriptionToSaleDetails extends Migration
{
    public function up()
    {
        $fields = [
            'description' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'subtotal'
            ],
        ];
        $this->forge->addColumn('sale_details', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('sale_details', 'description');
    }
}
