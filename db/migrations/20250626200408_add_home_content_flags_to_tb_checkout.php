<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddHomeContentFlagsToTbCheckout extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $table = $this->table('tb_checkout');

        // Campos para o tema Ankara
        $table
            ->addColumn('ankara_hero',        'boolean', ['default' => true, 'after' => 'theme'])
            ->addColumn('ankara_colorful',    'boolean', ['default' => true])
            ->addColumn('ankara_yellow',      'boolean', ['default' => true])
            ->addColumn('ankara_footer_top',  'boolean', ['default' => true])
            ->addColumn('ankara_footer_blog', 'boolean', ['default' => true])

        // Campos para o tema TerraDourada
            ->addColumn('td_hero',            'boolean', ['default' => true])
            ->addColumn('td_entrepreneurs',   'boolean', ['default' => true])
            ->addColumn('td_news',            'boolean', ['default' => true])
            ->addColumn('td_footer_info',     'boolean', ['default' => true])
            ->addColumn('td_footer_socials',  'boolean', ['default' => true])

            ->update();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $table = $this->table('tb_checkout');

        $table
            ->removeColumn('td_footer_socials')
            ->removeColumn('td_footer_info')
            ->removeColumn('td_news')
            ->removeColumn('td_entrepreneurs')
            ->removeColumn('td_hero')

            ->removeColumn('ankara_footer_blog')
            ->removeColumn('ankara_footer_top')
            ->removeColumn('ankara_yellow')
            ->removeColumn('ankara_colorful')
            ->removeColumn('ankara_hero')
            ->update();
    }
}
