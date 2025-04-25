<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateCartTable extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $table = $this->table('tb_carrinho');
        $table
            ->addColumn('usuario_id', 'integer', ['null' => true]) // Null para usuários não logados
            ->addColumn('cookie_id', 'string', ['null' => true]) // Identificação de sessão/cookie
            ->addColumn('produto_id', 'integer')
            ->addColumn('quantidade', 'integer', ['default' => 1])
            ->addColumn('creado_em', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
            ->create();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        // Exclui a tabela tb_carrinho
        if ($this->hasTable('tb_carrinho')) {
            $this->table('tb_carrinho')->drop()->save();
        }
    }
}
