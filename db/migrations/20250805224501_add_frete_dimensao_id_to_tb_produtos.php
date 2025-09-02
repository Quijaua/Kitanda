<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddFreteDimensaoIdToTbProdutos extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up(): void
    {
        $table = $this->table('tb_produtos');

        // Adiciona o campo freight_dimension_id após o campo vitrine
        $table->addColumn('freight_dimension_id', 'integer', [
            'default' => 0,
            'null' => false,
            'after' => 'vitrine',
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
        $table->removeColumn('freight_dimension_id')->update();
    }
}
