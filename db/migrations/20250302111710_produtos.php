<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class Produtos extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $table = $this->table('tb_produtos');
        $table
            ->addColumn('nome', 'string', ['limit' => 255])
            ->addColumn('titulo', 'string', ['limit' => 255])
            ->addColumn('preco', 'decimal', ['precision' => 10, 'scale' => 2])
            ->addColumn('imagem', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('descricao', 'text', ['null' => true])
            ->addColumn('vitrine', 'boolean', ['default' => false])
            ->addColumn('link', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('seo_nome', 'string', ['limit' => 255])
            ->addColumn('seo_descricao', 'string', ['limit' => 255])
            ->addColumn('data_criacao', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('data_edicao', 'datetime', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
            ->create();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        // Exclui a tabela tb_produtos
        if ($this->hasTable('tb_produtos')) {
            $this->table('tb_produtos')->drop()->save();
        }
    }
}