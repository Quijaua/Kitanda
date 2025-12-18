<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddSomenteEncomendaToTbProdutosTable extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up(): void
    {
        $table = $this->table('tb_produtos');

        // Adiciona o campo somente_encomenda
        $table->addColumn('somente_encomenda', 'integer', [
            'null' => true,
        ])
        ->update();

        // Adiciona o campo prazo_criacao
        $table->addColumn('prazo_criacao', 'string', [
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
        $table->removeColumn('somente_encomenda')->update();
        $table->removeColumn('prazo_criacao')->update();
    }
}
