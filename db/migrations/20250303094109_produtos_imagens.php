<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class ProdutosImagens extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $table = $this->table('tb_produto_imagens');
        $table
            ->addColumn('produto_id', 'integer')
            ->addColumn('imagem', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('data_criacao', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
            ->create();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        // Exclui a tabela tb_produto_imagens
        if ($this->hasTable('tb_produto_imagens')) {
            $this->table('tb_produto_imagens')->drop()->save();
        }
    }
}