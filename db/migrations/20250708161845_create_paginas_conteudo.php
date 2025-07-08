<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreatePaginasConteudo extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->table('tb_paginas_conteudo')
            ->addColumn('titulo', 'string', ['limit' => 255])
            ->addColumn('slug', 'string', ['limit' => 255])
            ->addColumn('conteudo', 'text', ['null' => true])
            ->addColumn('imagem', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('criado_por', 'integer', ['default' => 1, 'null' => false])
            ->addColumn('criado_em', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('atualizado_em', 'timestamp', [
                'default' => 'CURRENT_TIMESTAMP',
                'update' => 'CURRENT_TIMESTAMP'
            ])
            ->addIndex(['slug'], ['unique' => true])
            ->create();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->table('tb_paginas_conteudo')->drop()->save();
    }
}
