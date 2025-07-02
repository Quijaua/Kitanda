<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddFreightToTbCheckout extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $table = $this->table('tb_checkout');
        $table
            ->addColumn('freight_type', 'enum', ['values' => ['default', 'fixed'], 'default' => 'default', 'null' => false, 'after' => 'load_btn'])
            ->addColumn('freight_value', 'decimal', ['precision' => 10, 'scale' => 2, 'null' => true, 'after' => 'freight_type'])
            ->update();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $table = $this->table('tb_checkout');
        $table
            ->removeColumn('freight_type')
            ->removeColumn('freight_value')
            ->update();
    }
}
