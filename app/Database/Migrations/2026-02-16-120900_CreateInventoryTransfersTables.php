<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateInventoryTransfersTables extends Migration
{
    public function up()
    {
        // Table: inventory_transfers
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'transfer_code' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'unique' => true,
            ],
            // Warehouses and Users use signed INT in this DB
            'source_warehouse_id' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'destination_warehouse_id' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['completed', 'pending', 'cancelled'],
                'default' => 'completed',
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('source_warehouse_id', 'warehouses', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('destination_warehouse_id', 'warehouses', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('inventory_transfers');

        // Table: inventory_transfer_items
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'transfer_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true, // Matches inventory_transfers.id
            ],
            'product_id' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'quantity' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('transfer_id', 'inventory_transfers', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('product_id', 'products', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('inventory_transfer_items');
    }

    public function down()
    {
        $this->forge->dropTable('inventory_transfer_items');
        $this->forge->dropTable('inventory_transfers');
    }
}
