<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDescriptionToPurchaseDetails extends Migration
{
    public function up()
    {
        if (!$this->db->fieldExists('description', 'purchase_details')) {
            $fields = [
                'description' => [
                    'type' => 'TEXT',
                    'null' => true,
                    'after' => 'subtotal'
                ],
            ];
            $this->forge->addColumn('purchase_details', $fields);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('description', 'purchase_details')) {
            $this->forge->dropColumn('purchase_details', 'description');
        }
    }
}
