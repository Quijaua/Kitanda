<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class Funcoes extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $table = $this->table('tb_funcoes');
        $table
            ->addColumn('nome', 'string', ['limit' => 255])
            ->create();

        $table = $this->table('tb_paginas');
        $table
            ->addColumn('nome', 'string', ['limit' => 255])
            ->addColumn('link', 'string', ['limit' => 255])
            ->addColumn('descricao', 'string', ['limit' => 255])
            ->addColumn('status', 'boolean', ['default' => true])
            ->create();

        $table = $this->table('tb_acoes');
        $table
            ->addColumn('nome', 'string', ['limit' => 255])
            ->addColumn('tipo', 'string', ['limit' => 255])
            ->addColumn('descricao', 'string', ['limit' => 255])
            ->create();

        $table = $this->table('tb_pagina_acoes');
        $table
            ->addColumn('pagina_id', 'integer')
            ->addColumn('acao_id', 'integer')
            ->create();

        $table = $this->table('tb_permissao_funcao');
        $table
            ->addColumn('pagina_id', 'integer')
            ->addColumn('funcao_id', 'integer')
            ->addColumn('acao_id', 'integer')
            ->create();

        $table = $this->table('tb_permissao_usuario');
        $table
            ->addColumn('usuario_id', 'integer')
            ->addColumn('permissao_id', 'integer')
            ->create();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        // Exclui a tabela tb_funcoes
        if ($this->hasTable('tb_funcoes')) {
            $this->table('tb_funcoes')->drop()->save();
        }

        // Exclui a tabela tb_paginas
        if ($this->hasTable('tb_paginas')) {
            $this->table('tb_paginas')->drop()->save();
        }

        // Exclui a tabela tb_acoes
        if ($this->hasTable('tb_acoes')) {
            $this->table('tb_acoes')->drop()->save();
        }

        // Exclui a tabela tb_pagina_acoes
        if ($this->hasTable('tb_pagina_acoes')) {
            $this->table('tb_pagina_acoes')->drop()->save();
        }

        // Exclui a tabela tb_permissao_funcao
        if ($this->hasTable('tb_permissao_funcao')) {
            $this->table('tb_permissao_funcao')->drop()->save();
        }

        // Exclui a tabela tb_permissao_usuario
        if ($this->hasTable('tb_permissao_usuario')) {
            $this->table('tb_permissao_usuario')->drop()->save();
        }
    }
}
