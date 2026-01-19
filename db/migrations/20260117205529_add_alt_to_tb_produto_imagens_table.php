<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddAltToTbProdutoImagensTable extends AbstractMigration
{
    public function up(): void
    {
        $table = $this->table('tb_produto_imagens');

        $table->addColumn('alt', 'string', [
            'null' => true,
            'limit' => 255,
            'after' => 'imagem'
        ])->update();
    }

    public function down(): void
    {
        $table = $this->table('tb_produto_imagens');
        $table->removeColumn('alt')->update();
    }
}
