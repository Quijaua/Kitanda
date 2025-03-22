<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

final class UpdateDatabaseSchema extends AbstractMigration
{
    public function up(): void
    {
        // Alterar a coluna data_pagamento em tb_doacoes
        $this->table('tb_doacoes')
            ->changeColumn('data_pagamento', 'datetime', ['null' => true, 'default' => null])
            ->update();

        // Criar a tabela tb_bulk_emails
        $this->table('tb_bulk_emails', ['id' => false])
            ->addColumn('title', 'string', ['limit' => 100, 'null' => true])
            ->addColumn('body', 'string', ['limit' => 100, 'null' => true])
            ->addColumn('date', 'datetime', ['default' => 'CURRENT_TIMESTAMP', 'null' => true])
            ->create();

        // Adicionar coluna unregister_message em tb_mensagens
        $this->table('tb_mensagens')
            ->addColumn('unregister_message', 'text', ['null' => true])
            ->update();

        // Adicionar colunas em tb_checkout
        $this->table('tb_checkout')
            ->addColumn('tiktok', 'string', ['limit' => 255, 'null' => true, 'after' => 'website'])
            ->addColumn('linktree', 'string', ['limit' => 255, 'null' => true, 'after' => 'tiktok'])
            ->addColumn('doacoes', 'boolean', ['default' => 1])
            ->addColumn('pix_chave', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('pix_valor', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('pix_codigo', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('pix_imagem_base64', 'text', ['limit' => MysqlAdapter::TEXT_LONG, 'null' => true])
            ->addColumn('pix_identificador_transacao', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('pix_exibir', 'boolean', ['default' => 0])
            ->addColumn('pix_tipo', 'string', ['limit' => 255, 'null' => true])
            ->update();

        // Criar a tabela tb_page_captchas
        $this->table('tb_page_captchas')
            ->addColumn('page_name', 'enum', ['values' => ['doacao', 'login', 'enviar_email', 'recuperar_senha']])
            ->addColumn('captcha_type', 'enum', ['values' => ['hcaptcha', 'turnstile', 'none']])
            ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
            ->create();

        // Inserir dados na tabela tb_page_captchas
        $this->execute("INSERT INTO tb_page_captchas (page_name, captcha_type) VALUES
            ('doacao', 'none'),
            ('login', 'none'),
            ('enviar_email', 'none'),
            ('recuperar_senha', 'none')");
    }

    public function down(): void
    {
        // Remover a tabela tb_page_captchas
        $this->table('tb_page_captchas')->drop()->save();

        // Remover a tabela tb_webhook
        $this->table('tb_webhook')->drop()->save();

        // Remover as colunas adicionadas em tb_checkout
        $this->table('tb_checkout')
            ->removeColumn('tiktok')
            ->removeColumn('linktree')
            ->removeColumn('doacoes')
            ->removeColumn('pix_chave')
            ->removeColumn('pix_valor')
            ->removeColumn('pix_codigo')
            ->removeColumn('pix_imagem_base64')
            ->removeColumn('pix_identificador_transacao')
            ->removeColumn('pix_exibir')
            ->removeColumn('pix_tipo')
            ->update();

        // Remover a coluna unregister_message em tb_mensagens
        $this->table('tb_mensagens')
            ->removeColumn('unregister_message')
            ->update();

        // Remover a tabela tb_bulk_emails
        $this->table('tb_bulk_emails')->drop()->save();

        // Restaurar a coluna data_pagamento em tb_doacoes
        $this->table('tb_doacoes')
            ->changeColumn('data_pagamento', 'datetime', ['null' => false])
            ->update();
    }
}
