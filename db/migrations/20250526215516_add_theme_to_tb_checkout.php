<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddThemeToTbCheckout extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $table = $this->table('tb_checkout');
        $table
            ->addColumn('theme', 'string', ['limit' => 50, 'null'  => true, 'after' => 'load_btn'])
            ->update();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $table = $this->table('tb_checkout');
        $table
            ->removeColumn('theme')
            ->update();
    }
}