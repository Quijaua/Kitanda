<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateOrdersAndOrderItems extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        // Verifique se a tabela já existe antes de criá-la
        if (!$this->hasTable('tb_pedidos')) {
            $pedidos = $this->table('tb_pedidos');
            $pedidos
                ->addColumn('pedido_id', 'string', ['limit' => 6])
                ->addColumn('transacao_id', 'string', ['limit' => 255])
                ->addColumn('usuario_id', 'integer')
                ->addColumn('asaas_usuario_id', 'string', ['limit' => 255])

                ->addColumn('desconto', 'decimal', ['precision' => 10, 'scale' => 2])
                ->addColumn('frete', 'decimal', ['precision' => 10, 'scale' => 2])
                ->addColumn('subtotal', 'decimal', ['precision' => 10, 'scale' => 2])
                ->addColumn('total', 'decimal', ['precision' => 10, 'scale' => 2])

                ->addColumn('forma_pagamento', 'string', ['limit' => 50])
                ->addColumn('link_pagamento', 'text', ['null' => true])
                ->addColumn('link_boleto', 'text', ['null' => true])
                ->addColumn('status', 'string', ['limit' => 100])
                ->addColumn('data_vencimento', 'date', ['null' => true])
                ->addColumn('data_criacao', 'datetime', ['null' => true])
                ->addColumn('data_pagamento', 'datetime', ['null' => true])
                ->addColumn('pix_encodedImage', 'text', ['null' => true])
                ->addColumn('pix_payload', 'text', ['null' => true])
                ->addColumn('pix_expirationDate', 'datetime', ['null' => true])
                ->addColumn('boleto_barCode', 'string', ['limit' => 255, 'null' => true])
                ->addColumn('boleto_nossoNumero', 'string', ['limit' => 255, 'null' => true])
                ->addColumn('boleto_identificationField', 'string', ['limit' => 255, 'null' => true])
                ->addColumn('cartao_numero', 'integer', ['null' => true])
                ->addColumn('cartao_bandeira', 'string', ['limit' => 100, 'null' => true])

                ->addColumn('created_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
                ->addColumn('updated_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
                ->addIndex('pedido_id', ['unique' => true])
                ->create();
        }

        // Verifique se a tabela já existe antes de criá-la
        if (!$this->hasTable('tb_pedido_itens')) {
            $itensPedido = $this->table('tb_pedido_itens');
            $itensPedido
                ->addColumn('pedido_id', 'integer')
                ->addColumn('produto_id', 'integer')
                ->addColumn('nome', 'string', ['limit' => 255])
                ->addColumn('preco', 'decimal', ['precision' => 10, 'scale' => 2])
                ->addColumn('quantidade', 'integer')
                ->addColumn('preco_total', 'decimal', ['precision' => 10, 'scale' => 2])
                ->addColumn('created_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
                ->addColumn('updated_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
                ->create();
        }
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        // Exclui a tabela tb_pedidos
        if ($this->hasTable('tb_pedidos')) {
            $this->table('tb_pedidos')->drop()->save();
        }

        // Exclui a tabela tb_pedido_itens
        if ($this->hasTable('tb_pedido_itens')) {
            $this->table('tb_pedido_itens')->drop()->save();
        }
    }
}
