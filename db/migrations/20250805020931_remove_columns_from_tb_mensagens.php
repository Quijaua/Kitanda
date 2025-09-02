<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class RemoveColumnsFromTbMensagens extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up(): void
    {
        if ($this->table('tb_mensagens')->hasColumn('welcome_email')) {
            $this->table('tb_mensagens')->removeColumn('welcome_email')->update();
        }

        if ($this->table('tb_mensagens')->hasColumn('unregister_message')) {
            $this->table('tb_mensagens')->removeColumn('unregister_message')->update();
        }
    }

    /**
     * Migrate Down.
     */
    public function down(): void
    {
        $this->table('tb_mensagens')
            ->addColumn('welcome_email', 'text', ['null' => true])
            ->addColumn('unregister_message', 'text', ['null' => true])
            ->update();
    }
}
