<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddFreteDimensoesTable extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up(): void
    {
        // Criação da tabela frete_dimensoes
        $table = $this->table('tb_frete_dimensoes');
        $table
            ->addColumn('nome', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('altura', 'decimal', ['precision' => 5, 'scale' => 2, 'null' => false])
            ->addColumn('largura', 'decimal', ['precision' => 5, 'scale' => 2, 'null' => false])
            ->addColumn('comprimento', 'decimal', ['precision' => 5, 'scale' => 2, 'null' => false])
            ->addColumn('peso', 'decimal', ['precision' => 5, 'scale' => 2, 'null' => false])
            ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
            ->create();
    }

    /**
     * Migrate Down.
     */
    public function down(): void
    {
        // Remove a tabela caso a migration seja revertida
        $this->table('tb_frete_dimensoes')->drop()->save();
    }
}
