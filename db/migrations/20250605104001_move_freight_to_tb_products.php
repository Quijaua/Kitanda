<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class MoveFreightToTbProducts extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $produto = $this->table('tb_produtos');
        $produto
            ->addColumn('freight_type', 'enum', ['values' => ['default', 'fixed'], 'default' => 'default', 'null' => false, 'after' => 'vitrine'])
            ->addColumn('freight_value', 'decimal', ['precision' => 10, 'scale' => 2, 'null' => true, 'after' => 'freight_type'])
            ->update();

        $checkout = $this->table('tb_checkout');
        if ($checkout->hasColumn('freight_type')) {
            $checkout
                ->removeColumn('freight_type')
                ->removeColumn('freight_value')
                ->update();
        }
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $checkout = $this->table('tb_checkout');
        $checkout
            ->addColumn('freight_type', 'enum', ['values' => ['default', 'fixed'], 'default' => 'default', 'null' => false, 'after' => 'load_btn'])
            ->addColumn('freight_value', 'decimal', ['precision' => 10, 'scale' => 2, 'null' => true, 'after' => 'freight_type'])
            ->update();

        $produto = $this->table('tb_produto');
        if ($produto->hasColumn('freight_type')) {
            $produto
                ->removeColumn('freight_type')
                ->removeColumn('freight_value')
                ->update();
        }
    }
}
