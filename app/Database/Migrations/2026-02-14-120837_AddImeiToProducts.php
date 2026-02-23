<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddImeiToProducts extends Migration
{
    public function up()
    {
        $fields = [
            'imei1' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'unique' => true,
                'null' => true,
            ],
            'imei2' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'unique' => true,
                'null' => true,
            ],
        ];
        // $this->forge->addColumn('products', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('products', ['imei1', 'imei2']);
    }
}
