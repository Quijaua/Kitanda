<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class Kitanda extends AbstractMigration
{
    public function change(): void
    {
        // Tabela tb_checkout
        $table = $this->table('tb_checkout');
        $table->addColumn('nome', 'string', ['limit' => 255])
              ->addColumn('logo', 'string', ['limit' => 255])
              ->addColumn('title', 'string', ['limit' => 255])
              ->addColumn('descricao', 'text')
              ->addColumn('privacidade', 'string', ['limit' => 255])
              ->addColumn('faq', 'string', ['limit' => 255])
              ->addColumn('use_faq', 'boolean', ['null' => true])
              ->addColumn('facebook', 'string', ['limit' => 255, 'null' => true])
              ->addColumn('instagram', 'string', ['limit' => 255, 'null' => true])
              ->addColumn('linkedin', 'string', ['limit' => 255, 'null' => true])
              ->addColumn('twitter', 'string', ['limit' => 255, 'null' => true])
              ->addColumn('youtube', 'string', ['limit' => 255, 'null' => true])
              ->addColumn('website', 'string', ['limit' => 255, 'null' => true])
              ->addColumn('cep', 'string', ['limit' => 255])
              ->addColumn('rua', 'string', ['limit' => 255])
              ->addColumn('numero', 'string', ['limit' => 255, 'null' => true])
              ->addColumn('bairro', 'string', ['limit' => 255])
              ->addColumn('cidade', 'string', ['limit' => 255])
              ->addColumn('estado', 'string', ['limit' => 255])
              ->addColumn('telefone', 'string', ['limit' => 255])
              ->addColumn('email', 'string', ['limit' => 255])
              ->addColumn('nav_background', 'string', ['limit' => 255])
              ->addColumn('nav_color', 'string', ['limit' => 255])
              ->addColumn('background', 'string', ['limit' => 255])
              ->addColumn('color', 'string', ['limit' => 255])
              ->addColumn('hover', 'string', ['limit' => 255])
              ->addColumn('text_color', 'string', ['limit' => 255])
              ->addColumn('load_btn', 'string', ['limit' => 255])
              ->addColumn('progress', 'string', ['limit' => 255])
              ->addColumn('monthly_1', 'string', ['limit' => 255])
              ->addColumn('monthly_2', 'string', ['limit' => 255])
              ->addColumn('monthly_3', 'string', ['limit' => 255])
              ->addColumn('monthly_4', 'string', ['limit' => 255])
              ->addColumn('monthly_5', 'string', ['limit' => 255])
              ->addColumn('yearly_1', 'string', ['limit' => 255])
              ->addColumn('yearly_2', 'string', ['limit' => 255])
              ->addColumn('yearly_3', 'string', ['limit' => 255])
              ->addColumn('yearly_4', 'string', ['limit' => 255])
              ->addColumn('yearly_5', 'string', ['limit' => 255])
              ->addColumn('once_1', 'string', ['limit' => 255])
              ->addColumn('once_2', 'string', ['limit' => 255])
              ->addColumn('once_3', 'string', ['limit' => 255])
              ->addColumn('once_4', 'string', ['limit' => 255])
              ->addColumn('once_5', 'string', ['limit' => 255])
              ->create();
        
        // Tabela tb_clientes
        $table = $this->table('tb_clientes');
        $table->addColumn('roles', 'boolean')
              ->addColumn('nome', 'string', ['limit' => 100, 'null' => true])
              ->addColumn('email', 'string', ['limit' => 100, 'null' => true])
              ->addColumn('password', 'string', ['limit' => 255, 'null' => true])
              ->addColumn('recup_password', 'string', ['limit' => 255, 'null' => true])
              ->addColumn('magic_link', 'string', ['limit' => 255, 'null' => true])
              ->addColumn('phone', 'string', ['limit' => 50, 'null' => true])
              ->addColumn('cpf', 'string', ['limit' => 25, 'null' => true])
              ->addColumn('cep', 'char', ['limit' => 10, 'null' => true])
              ->addColumn('endereco', 'string', ['limit' => 255, 'null' => true])
              ->addColumn('numero', 'integer', ['null' => true])
              ->addColumn('complemento', 'string', ['limit' => 255, 'null' => true])
              ->addColumn('municipio', 'string', ['limit' => 255, 'null' => true])
              ->addColumn('cidade', 'string', ['limit' => 255, 'null' => true])
              ->addColumn('uf', 'string', ['limit' => 255, 'null' => true])
              ->addColumn('asaas_id', 'string', ['limit' => 255, 'null' => true])
              ->addColumn('newsletter', 'boolean')
              ->addColumn('private', 'boolean')
              ->create();

        // Tabela tb_mensagens
        $table = $this->table('tb_mensagens');
        $table->addColumn('welcome_email', 'text', ['null' => true])
              ->addColumn('privacy_policy', 'text', ['null' => true])
              ->addColumn('use_privacy', 'boolean')
              ->create();

        // Tabela tb_doacoes
        $table = $this->table('tb_doacoes', ['id' => false, 'primary_key' => 'id']);
        $table->addColumn('id', 'integer', ['identity' => true])
            ->addColumn('customer_id', 'string', ['limit' => 100, 'null' => true])
            ->addColumn('payment_id', 'string', ['limit' => 100])
            ->addColumn('cycle', 'string', ['limit' => 20, 'null' => true])
            ->addColumn('valor', 'decimal', ['precision' => 10, 'scale' => 2, 'default' => 0.00])
            ->addColumn('forma_pagamento', 'string', ['limit' => 50])
            ->addColumn('link_pagamento', 'text', ['null' => true])
            ->addColumn('link_boleto', 'text', ['null' => true])
            ->addColumn('status', 'string', ['limit' => 100])
            ->addColumn('data_vencimento', 'date', ['null' => true])
            ->addColumn('data_criacao', 'date', ['null' => true])
            ->addColumn('data_pagamento', 'datetime', ['null' => true])
            ->addColumn('pix_encodedImage', 'text', ['null' => true])
            ->addColumn('pix_payload', 'text', ['null' => true])
            ->addColumn('pix_expirationDate', 'datetime', ['null' => true])
            ->addColumn('boleto_barCode', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('boleto_nossoNumero', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('boleto_identificationField', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('cartao_numero', 'integer', ['null' => true])
            ->addColumn('cartao_bandeira', 'string', ['limit' => 100, 'null' => true])
            ->create();

        // Tabela tb_imagens
        $table = $this->table('tb_imagens');
        $table->addColumn('imagem', 'string', ['limit' => 255])
            ->create();

        // Tabela tb_integracoes
        $table = $this->table('tb_integracoes');
        $table->addColumn('fb_pixel', 'text', ['null' => true])
            ->addColumn('gtm', 'text', ['null' => true])
            ->addColumn('g_analytics', 'text', ['null' => true])
            ->create();

        // Tabela tb_transacoes
        $table = $this->table('tb_transacoes', ['id' => false, 'primary_key' => 'id']);
        $table->addColumn('id', 'integer', ['identity' => true])
            ->addColumn('event', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('payment_id', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('payment_date_created', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('customer_id', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('subscription_id', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('value', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('net_value', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('description', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('billing_type', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('confirmed_date', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('credit_card_number', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('credit_card_brand', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('credit_card_token', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('status', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('credit_date', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('estimated_credit_date', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('webhook_date_created', 'string', ['limit' => 255, 'null' => true])
            ->create();

        // Tabela tb_webhook
        $table = $this->table('tb_webhook');
        $table->addColumn('webhook_id', 'string', ['limit' => 255])
            ->addColumn('enabled', 'boolean')
            ->addColumn('name', 'string', ['limit' => 50])
            ->addColumn('email', 'string', ['limit' => 255])
            ->addColumn('interrupted', 'boolean')
            ->addColumn('send_type', 'string', ['limit' => 100])
            ->addColumn('date_create', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
            ->create();

    }
}