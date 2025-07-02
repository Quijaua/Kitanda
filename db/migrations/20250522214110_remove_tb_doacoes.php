<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class RemoveTbDoacoes extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        if ($this->hasTable('tb_doacoes')) {
            $this->table('tb_doacoes')->drop()->save();
        }
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        if (!$this->hasTable('tb_doacoes')) {
            $table = $this->table('tb_doacoes');
            $table
                ->addColumn('customer_id', 'string', ['limit' => 100, 'null' => true])
                ->addColumn('payment_id', 'string', ['limit' => 100, 'null' => true])
                ->addColumn('cycle', 'string', ['limit' => 20, 'null' => true])
                ->addColumn('valor', 'decimal', ['precision' => 10, 'scale' => 2, 'default' => '0.00', 'null' => false])
                ->addColumn('forma_pagamento', 'string', ['limit' => 50, 'null' => true])
                ->addColumn('link_pagamento', 'text', [ 'null' => true])
                ->addColumn('link_boleto', 'text', [ 'null' => true])
                ->addColumn('status', 'string', ['limit' => 100, 'null' => true])
                ->addColumn('data_vencimento', 'date', [ 'null' => true])
                ->addColumn('data_criacao', 'date', [ 'null' => true])
                ->addColumn('data_pagamento', 'datetime', [ 'null' => true])
                ->addColumn('pix_encodedImage', 'text', [ 'null' => true])
                ->addColumn('pix_payload', 'text', [ 'null' => true])
                ->addColumn('pix_expirationDate', 'datetime', [ 'null' => true])
                ->addColumn('boleto_barCode', 'string', ['limit' => 255, 'null' => true])
                ->addColumn('boleto_nossoNumero', 'string', ['limit' => 255, 'null' => true])
                ->addColumn('boleto_identificationField', 'string', ['limit' => 255, 'null' => true])
                ->addColumn('cartao_numero', 'integer', ['signed' => false, 'null' => true])
                ->addColumn('cartao_bandeira', 'string', ['limit' => 100, 'null' => true])
                ->create();
        }
    }
}
