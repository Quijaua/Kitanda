<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddPesoToTbProdutosTable extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up(): void
    {
        $table = $this->table('tb_produtos');

        // Adiciona o campo peso
        $table->addColumn('peso', 'float', [
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
        $table->removeColumn('peso')->update();
    }
}
