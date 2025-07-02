<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateCategoriesTable extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $table = $this->table('tb_categorias');
        $table
            ->addColumn('nome', 'string', ['limit' => 255])
            ->addColumn('criado_por', 'integer', ['null' => false])
            ->addColumn('data_criacao', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('data_edicao', 'datetime', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
            ->create();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        // Exclui a tabela tb_categorias
        if ($this->hasTable('tb_categorias')) {
            $this->table('tb_categorias')->drop()->save();
        }
    }
}
