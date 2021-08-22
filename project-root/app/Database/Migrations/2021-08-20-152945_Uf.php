<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Uf extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'uf_id'          => [
					'type'           => 'INT',
					'auto_increment' => true,
			],
			'codigo'       => [
					'type'       => 'VARCHAR',
					'constraint' => '100',
			],
			'nombre'       => [
				'type'       => 'VARCHAR',
				'constraint' => '100',
			],
			'unidad_medida'       => [
				'type'       => 'VARCHAR',
				'constraint' => '100',
			],
			'codigo'       => [
				'type'       => 'VARCHAR',
				'constraint' => '100',
			],
			'fecha'       => [
				'type'       => 'DATE',
			],
			'valor'       => [
				'type'       => 'VARCHAR',
				'constraint' => '100',
			],
		]);
		$this->forge->addKey('uf_id', true);
		$this->forge->createTable('uf');
	}

	public function down()
	{
			$this->forge->dropTable('uf');
	}
}
