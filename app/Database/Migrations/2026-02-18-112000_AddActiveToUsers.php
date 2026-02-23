<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddActiveToUsers extends Migration
{
    public function up()
    {
        $fields = [
            'active' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
                'after' => 'role_id' // Assuming role_id exists
            ],
        ];
        $this->forge->addColumn('users', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('users', 'active');
    }
}
