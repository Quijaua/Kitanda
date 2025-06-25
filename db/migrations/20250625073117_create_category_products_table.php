<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateCategoryProductsTable extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $table = $this->table('tb_categoria_produtos');
        $table
            ->addColumn('categoria_id', 'integer', ['null' => false, 'signed' => false])
            ->addColumn('produto_id', 'integer', ['null' => false, 'signed' => false])
            ->create();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        // Exclui a tabela tb_categoria_produtos
        if ($this->hasTable('tb_categoria_produtos')) {
            $this->table('tb_categoria_produtos')->drop()->save();
        }
    }
}
