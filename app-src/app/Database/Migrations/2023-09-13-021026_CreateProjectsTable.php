<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProjectsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'int',
                'constraint' => 12,
                'unsigned' => true,
                'auto_increment' => true,
                'null' => false,
            ],
            'user_id' => [
                'type' => 'int',
                'constraint' => 12,
                'unsigned' => true,
                'null' => false,
            ],
            'title'   => [
                'type' => 'varchar',
                'constraint' => 150,
                'null' => false,
            ],
            'budget'   => [
                'type' => 'int',
                'constraint' => 12,
            ],
            'created_at' => [
                'type' => 'timestamp',
                'null' => false,
            ],
            'updated_at' => [
                'type' => 'timestamp',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'timestamp',
                'null' => true,
            ]
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('projects');
    }

    public function down()
    {
        $this->forge->dropTable('projects');
    }
}
