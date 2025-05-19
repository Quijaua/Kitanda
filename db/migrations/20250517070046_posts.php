<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class Posts extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $table = $this->table('tb_blog_posts');
        $table
            ->addColumn('titulo', 'string', ['limit' => 255])
            ->addColumn('resumo', 'text')
            ->addColumn('conteudo', 'text')
            ->addColumn('imagem', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('data_publicacao', 'date')
            ->addColumn('tags', 'text', ['null' => true])
            ->addColumn('seo_nome', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('seo_descricao', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('criado_por', 'integer', ['null' => false])
            ->addColumn('data_criacao', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('data_edicao', 'datetime', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
            ->create();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        // Exclui a tabela tb_blog_posts
        if ($this->hasTable('tb_blog_posts')) {
            $this->table('tb_blog_posts')->drop()->save();
        }
    }
}
