<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class RemoveCheckoutOldColsAndImagesTable extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up(): void
    {
        $table = $this->table('tb_checkout');
        $oldCols = [
            'monthly_1','monthly_2','monthly_3','monthly_4','monthly_5',
            'yearly_1','yearly_2','yearly_3','yearly_4','yearly_5',
            'once_1','once_2','once_3','once_4','once_5',
            'card_doacoes',
            'pix_tipo','pix_chave','pix_valor','pix_codigo',
            'pix_imagem_base64','pix_identificador_transacao','pix_exibir',
        ];
        foreach ($oldCols as $col) {
            if ($table->hasColumn($col)) {
                $table->removeColumn($col);
            }
        }
        $table->update();

        if ($this->hasTable('tb_imagens')) {
            $this->table('tb_imagens')->drop()->save();
        }
    }


    /**
     * Migrate Down.
     */
    public function down(): void
    {
        if ($this->hasTable('tb_checkout')) {
            $table = $this->table('tb_checkout');

            $table
                ->addColumn('monthly_1', 'decimal', ['precision'=>10,'scale'=>2,'null'=>true,'default'=>null])
                ->addColumn('monthly_2', 'decimal', ['precision'=>10,'scale'=>2,'null'=>true,'default'=>null])
                ->addColumn('monthly_3', 'decimal', ['precision'=>10,'scale'=>2,'null'=>true,'default'=>null])
                ->addColumn('monthly_4', 'decimal', ['precision'=>10,'scale'=>2,'null'=>true,'default'=>null])
                ->addColumn('monthly_5', 'decimal', ['precision'=>10,'scale'=>2,'null'=>true,'default'=>null])

                ->addColumn('yearly_1', 'decimal', ['precision'=>10,'scale'=>2,'null'=>true,'default'=>null])
                ->addColumn('yearly_2', 'decimal', ['precision'=>10,'scale'=>2,'null'=>true,'default'=>null])
                ->addColumn('yearly_3', 'decimal', ['precision'=>10,'scale'=>2,'null'=>true,'default'=>null])
                ->addColumn('yearly_4', 'decimal', ['precision'=>10,'scale'=>2,'null'=>true,'default'=>null])
                ->addColumn('yearly_5', 'decimal', ['precision'=>10,'scale'=>2,'null'=>true,'default'=>null])

                ->addColumn('once_1', 'decimal', ['precision'=>10,'scale'=>2,'null'=>true,'default'=>null])
                ->addColumn('once_2', 'decimal', ['precision'=>10,'scale'=>2,'null'=>true,'default'=>null])
                ->addColumn('once_3', 'decimal', ['precision'=>10,'scale'=>2,'null'=>true,'default'=>null])
                ->addColumn('once_4', 'decimal', ['precision'=>10,'scale'=>2,'null'=>true,'default'=>null])
                ->addColumn('once_5', 'decimal', ['precision'=>10,'scale'=>2,'null'=>true,'default'=>null])

                ->addColumn('card_doacoes', 'boolean', ['default'=>false])
                
                ->addColumn('pix_tipo', 'string', ['limit'=>100,'null'=>true,'default'=>null])
                ->addColumn('pix_chave', 'string', ['limit'=>255,'null'=>true,'default'=>null])
                ->addColumn('pix_valor', 'decimal', ['precision'=>10,'scale'=>2,'null'=>true,'default'=>null])
                ->addColumn('pix_codigo', 'string', ['limit'=>100,'null'=>true,'default'=>null])
                ->addColumn('pix_imagem_base64', 'text',    ['null'=>true,'default'=>null])
                ->addColumn('pix_identificador_transacao', 'string', ['limit'=>255,'null'=>true,'default'=>null])
                ->addColumn('pix_exibir', 'boolean', ['default'=>false])

                ->update();
        }

        if (!$this->hasTable('tb_imagens')) {
            $this->table('tb_imagens')
                ->addColumn('imagem', 'string', ['limit' => 255])
                ->create();
        }
    }
}
