<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddCodigoProdutoToTbProdutosTable extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up(): void
    {
        $table = $this->table('tb_produtos');

        // Adiciona o campo codigo_produto
        $table->addColumn('codigo_produto', 'string', [
            'null' => true,
        ])
        ->update();
    }

    /**
     * Migrate Down.
     */
    public function down(): void
    {
        // Remove o campo caso a migration seja revertida
        $table = $this->table('tb_produtos');
        $table->removeColumn('codigo_produto')->update();
    }
}
