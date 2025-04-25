<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddColumnsClientes extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $table = $this->table('tb_clientes');
        $table
            ->addColumn('data_nascimento', 'boolean', ['default' => false, 'null' => true, 'after' => 'cpf'])
            ->addColumn('pais', 'string', ['limit' => 255, 'default' => 'Brasil', 'null' => true, 'after' => 'uf'])
            ->addColumn('estrangeiro', 'boolean', ['default' => false, 'null' => true, 'after' => 'pais'])
            ->update();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $table = $this->table('tb_clientes');
        $table
            ->removeColumn('data_nascimento')
            ->removeColumn('pais')
            ->removeColumn('estrangeiro')
            ->update();
    }
}
