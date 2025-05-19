<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CategoriaPosts extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $table = $this->table('tb_blog_categoria_posts');
        $table
            ->addColumn('categoria_id', 'integer', ['null' => false])
            ->addColumn('post_id', 'integer', ['null' => false])
            ->create();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        // Exclui a tabela tb_blog_categoria_posts
        if ($this->hasTable('tb_blog_categoria_posts')) {
            $this->table('tb_blog_categoria_posts')->drop()->save();
        }
    }
}
