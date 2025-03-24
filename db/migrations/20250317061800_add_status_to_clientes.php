<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddStatusToClientes extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $table = $this->table('tb_clientes');
        $table
            ->addColumn('status', 'boolean', ['default' => true, 'null' => false, 'after' => 'id'])
            ->addColumn('instagram', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('site', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('facebook', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('tiktok', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('descricao', 'text', ['null' => true])
            ->update();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        // Remove a coluna "status" caso seja necessário reverter a migração
        $table = $this->table('tb_clientes');
        $table
            ->removeColumn('status')
            ->removeColumn('instagram')
            ->removeColumn('site')
            ->removeColumn('facebook')
            ->removeColumn('tiktok')
            ->removeColumn('descricao')
            ->update();
    }
}
