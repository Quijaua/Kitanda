<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class Categorias extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $table = $this->table('tb_blog_categorias');
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
        // Exclui a tabela tb_blog_categorias
        if ($this->hasTable('tb_blog_categorias')) {
            $this->table('tb_blog_categorias')->drop()->save();
        }
    }
}
