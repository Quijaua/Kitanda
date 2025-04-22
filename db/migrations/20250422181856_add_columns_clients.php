<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddColumnsClients extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $table = $this->table('tb_clientes');
        $table
            ->addColumn('phone', 'string', ['limit' => 255, 'null' => true, 'after' => 'email'])
            ->update();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $table = $this->table('tb_clientes');
        $table
            ->removeColumn('phone')
            ->update();
    }
}
