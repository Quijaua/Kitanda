<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateArquivosTable extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->table('tb_arquivos')
            ->addColumn('nome', 'string', ['limit' => 255])
            ->addColumn('criado_por', 'integer', ['default' => 1, 'null' => false])
            ->addColumn('criado_em', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('atualizado_em', 'timestamp', [
                'default' => 'CURRENT_TIMESTAMP',
                'update' => 'CURRENT_TIMESTAMP'
            ])
            ->create();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->table('tb_arquivos')->drop()->save();
    }
}
