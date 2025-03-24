<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddCriadoPorColumn extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $table = $this->table('tb_produtos');
        $table
            ->addColumn('criado_por', 'integer', ['default' => 1, 'null' => false, 'after' => 'seo_descricao'])
            ->update();

        $table = $this->table('tb_bulk_emails');
        $table
            ->addColumn('criado_por', 'integer', ['default' => 1, 'null' => false])
            ->update();

        $table = $this->table('tb_funcoes');
        $table
            ->addColumn('criado_por', 'integer', ['default' => 1, 'null' => false])
            ->update();

        $table = $this->table('tb_clientes');
        $table
            ->addColumn('criado_por', 'integer', ['default' => 1, 'null' => false])
            ->update();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        // Remove a coluna "criado_por" para reverter a migration
        $table = $this->table('tb_produtos');
        $table
            ->removeColumn('criado_por')
            ->update();

        // Remove a coluna "criado_por" para reverter a migration
        $table = $this->table('tb_bulk_emails');
        $table
            ->removeColumn('criado_por')
            ->update();

        // Remove a coluna "criado_por" para reverter a migration
        $table = $this->table('tb_funcoes');
        $table
            ->removeColumn('criado_por')
            ->update();

        // Remove a coluna "criado_por" para reverter a migration
        $table = $this->table('tb_clientes');
        $table
            ->removeColumn('criado_por')
            ->update();
    }
}
