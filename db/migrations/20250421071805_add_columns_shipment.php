<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddColumnsShipment extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $table = $this->table('tb_pedidos');
        $table
            ->addColumn('codigo_rastreamento', 'string', ['limit' => 255, 'null' => true, 'after' => 'cartao_bandeira'])
            ->addColumn('rastreamento_status', 'string', ['limit' => 255, 'null' => true, 'after' => 'codigo_rastreamento'])
            ->update();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $table = $this->table('tb_pedidos');
        $table
            ->removeColumn('codigo_rastreamento')
            ->removeColumn('rastreamento_status')
            ->update();
    }
}
