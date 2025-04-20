<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateStoreTable extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        // Verifique se a tabela já existe antes de criá-la
        if (!$this->hasTable('tb_lojas')) {
            $table = $this->table('tb_lojas');
            $table
                ->addColumn('vendedora_id', 'integer', ['null' => false])
                ->addColumn('nome', 'string', ['limit' => 255, 'null' => false])
                ->addColumn('imagem', 'string', ['limit' => 255, 'null' => true, 'default' => null])
                ->addColumn('mini_bio', 'text', ['null' => true, 'default' => null])
                ->addColumn('asaas_email', 'string', ['limit' => 100, 'null' => true, 'default' => null])
                ->addColumn('cep', 'string', ['limit' => 20, 'null' => true, 'default' => null])
                ->addColumn('logradouro', 'string', ['limit' => 255, 'null' => true, 'default' => null])
                ->addColumn('numero', 'string', ['limit' => 20, 'null' => true, 'default' => null])
                ->addColumn('complemento', 'string', ['limit' => 255, 'null' => true, 'default' => null])
                ->addColumn('bairro', 'string', ['limit' => 100, 'null' => true, 'default' => null])
                ->addColumn('cidade', 'string', ['limit' => 100, 'null' => true, 'default' => null])
                ->addColumn('estado', 'string', ['limit' => 50, 'null' => true, 'default' => null])
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
        // Exclui a tabela tb_lojas
        if ($this->hasTable('tb_lojas')) {
            $this->table('tb_lojas')->drop()->save();
        }
    }
}
