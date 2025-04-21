<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class MoveFieldsFromClientesToLojas extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        // Remove columns from tb_clientes
        $clientes = $this->table('tb_clientes');
        if ($clientes->hasColumn('descricao')) {
            $clientes->removeColumn('descricao');
        }
        foreach (['phone', 'facebook', 'instagram', 'tiktok', 'site'] as $col) {
            if ($clientes->hasColumn($col)) {
                $clientes->removeColumn($col);
            }
        }
        $clientes->save();

        // Add columns to tb_lojas after 'nome'
        $lojas = $this->table('tb_lojas');
        $lojas
            ->addColumn('telefone', 'string', ['limit' => 255, 'null' => true, 'after' => 'nome'])
            ->addColumn('site', 'string', ['limit' => 255, 'null' => true, 'after' => 'telefone'])
            ->addColumn('facebook', 'string', ['limit' => 255, 'null' => true, 'after' => 'site'])
            ->addColumn('instagram', 'string', ['limit' => 255, 'null' => true, 'after' => 'facebook'])
            ->addColumn('tiktok', 'string', ['limit' => 255, 'null' => true, 'after' => 'instagram'])
            ->save();

    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        // Remove added columns from tb_lojas
        $lojas = $this->table('tb_lojas');
        foreach (array_reverse(['telefone', 'facebook', 'instagram', 'tiktok', 'site']) as $col) {
            if ($lojas->hasColumn($col)) {
                $lojas->removeColumn($col);
            }
        }
        $lojas->save();

        // Re-add columns to tb_clientes
        $clientes = $this->table('tb_clientes');
        $clientes
            ->addColumn('descricao', 'text', ['null' => true, 'after' => 'nome'])
            ->addColumn('phone', 'string', ['limit' => 255, 'null' => true, 'after' => 'descricao'])
            ->addColumn('facebook', 'string', ['limit' => 255, 'null' => true, 'after' => 'phone'])
            ->addColumn('instagram', 'string', ['limit' => 255, 'null' => true, 'after' => 'facebook'])
            ->addColumn('tiktok', 'string', ['limit' => 255, 'null' => true, 'after' => 'instagram'])
            ->addColumn('site', 'string', ['limit' => 255, 'null' => true, 'after' => 'tiktok'])
            ->save();
    }
}
