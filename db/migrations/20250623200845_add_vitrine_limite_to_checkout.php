<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddVitrineLimiteToCheckout extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up(): void
    {
        $table = $this->table('tb_checkout');
        if (!$table->hasColumn('vitrine_limite')) {
            $table->addColumn('vitrine_limite', 'integer', [
                'null'   => true,
                'default'=> 6,
                'after'  => 'descricao',
            ])->update();
        }
    }


    /**
     * Migrate Down.
     */
    public function down(): void
    {
        $table = $this->table('tb_checkout');
        if ($table->hasColumn('vitrine_limite')) {
            $table->removeColumn('vitrine_limite')->update();
        }
    }
}
