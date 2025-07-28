<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddWhatsappToTbCheckout extends AbstractMigration
{
    public function up(): void
    {
        $table = $this->table('tb_checkout');

        $table->addColumn('whatsapp', 'string', ['limit' => 255, 'null' => true, 'after' => 'instagram'])->update();
    }

    public function down(): void
    {
        $table = $this->table('tb_checkout');

        $table->removeColumn('whatsapp')->update();
    }
}
