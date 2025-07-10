<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddUrlRastreamentoToTbPedidos extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $table = $this->table('tb_pedidos');
        $table
            ->addColumn('url_rastreamento', 'text', ['null' => true, 'after' => 'codigo_rastreamento'])
            ->addColumn('data_envio', 'date', ['null' => true, 'after' => 'rastreamento_status'])
            ->addColumn('data_entrega', 'date', ['null' => true, 'after' => 'data_envio'])
            ->update();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $table = $this->table('tb_pedidos');
        $table
            ->removeColumn('url_rastreamento')
            ->removeColumn('data_envio')
            ->removeColumn('data_entrega')
            ->update();
    }
}
