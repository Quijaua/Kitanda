<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateUserTokensTable extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up(): void
    {
        // Cria a tabela de tokens para “remember-me”
        $table = $this->table('tb_user_tokens');
        if (!$table->exists()) {
            $table
                ->addColumn('user_id', 'integer', ['null' => false])
                ->addColumn('token', 'string', ['limit' => 255, 'null' => false])
                ->addColumn('token_hash', 'string', ['limit' => 255, 'null' => false])
                ->addColumn('expires_at', 'datetime', ['null' => false])
                ->addColumn('created_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
                ->addIndex(['token_hash'], ['unique' => true, 'name' => 'idx_token_hash'])
                ->create();
        }
    }

    /**
     * Migrate Down.
     */
    public function down(): void
    {
        if ($this->hasTable('tb_user_tokens')) {
            $table = $this->table('tb_user_tokens');
            if ($table->hasForeignKey('user_id')) {
                $table->dropForeignKey('user_id');
            }
            if ($table->hasIndexByName('idx_token_hash')) {
                $table->removeIndexByName('idx_token_hash');
            }
            $table->drop()->save();
        }
    }
}
