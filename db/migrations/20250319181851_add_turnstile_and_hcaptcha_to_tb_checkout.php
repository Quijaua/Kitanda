<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddTurnstileAndHcaptchaToTbCheckout extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        // Obtém a tabela tb_checkout e adiciona as colunas
        $table = $this->table('tb_checkout');
        $table
            ->addColumn('turnstile', 'boolean', ['default' => 0, 'null' => false])
            ->addColumn('hcaptcha', 'boolean', ['default' => 0, 'null' => false])
            ->update();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        // Reverte a migration removendo as colunas criadas
        $table = $this->table('tb_checkout');
        $table
            ->removeColumn('turnstile')
            ->removeColumn('hcaptcha')
            ->update();
    }
}
