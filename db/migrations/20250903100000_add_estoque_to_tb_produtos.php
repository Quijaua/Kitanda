<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddEstoqueToTbProdutos extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up(): void
    {
        $table = $this->table('tb_produtos');

        // Adiciona o campo freight_dimension_id após o campo vitrine
        $table->addColumn('estoque', 'integer', [
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
        $table->removeColumn('estoque')->update();
    }
}
