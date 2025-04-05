<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class InsertInitialData extends AbstractSeed
{
    public function run(): void
    {
        // Inserindo dados na tabela tb_checkout
        $checkoutData = [
            [
                'id' => 1,
                'nome' => 'Kitanda',
                'logo' => 'kitanda-logo.png',
                'title' => 'Minha Loja',
                'descricao' => 'Loja de produtos artesanais',
                'privacidade' => 'https://seusite.com.br/politica-de-privacidade',
                'faq' => 'https://seusite.com.br/faq',
                'use_faq' => 1,
                'facebook' => 'https://facebook.com/seufacebook',
                'instagram' => 'https://instagram.com/seuinstagram',
                'linkedin' => null,
                'twitter' => null,
                'youtube' => null,
                'website' => null,
                'cep' => '12345678',
                'rua' => 'Rua',
                'numero' => '123',
                'bairro' => 'Bairro',
                'cidade' => 'Cidade',
                'estado' => 'São Paulo',
                'telefone' => '(11) 1111-1111',
                'email' => 'seuemail@seusite.com.br',
                'nav_background' => '#ffc107',
                'nav_color' => '#212529',
                'background' => '#f5f5f5',
                'color' => '#ffc107',
                'hover' => '#212529',
                'text_color' => '#212529',
                'load_btn' => '#ffc107',
                'progress' => '#212529',
                'monthly_1' => 10,
                'monthly_2' => 15,
                'monthly_3' => 30,
                'monthly_4' => 40,
                'monthly_5' => 70,
                'yearly_1' => 120,
                'yearly_2' => 360,
                'yearly_3' => 480,
                'yearly_4' => 840,
                'yearly_5' => 960,
                'once_1' => 200,
                'once_2' => 300,
                'once_3' => 400,
                'once_4' => 500,
                'once_5' => 1000,
            ]
        ];
        $checkout = $this->table('tb_checkout');
        $checkout->insert($checkoutData)->save();

        // Inserindo dados na tabela tb_clientes
//        $clientesData = [
  //          [
    //            'id' => 1,
      //          'roles' => 1,
        //        'nome' => 'Admin',
          //      'email' => 'admin@admin.com',
            //    'password' => '$2y$10$gphtP5ZDgkZNctcEhLKfs.MQ8qWc6Ebf8V6sqRf4q7QhClHSojT7.',
              //  'magic_link' => null,
                //'phone' => null,
//                'cpf' => null,
  //              'cep' => null,
    //            'endereco' => null,
      //          'numero' => null,
        //        'complemento' => null,
          //      'municipio' => null,
            //    'cidade' => null,
              //  'uf' => null,
//                'asaas_id' => null,
  //              'newsletter' => 0,
    //            'private' => 0,
      //      ]
//        ];
//
  //      $clientes = $this->table('tb_clientes');
    //    $clientes->insert($clientesData)->save();

        // Inserindo dados na tabela tb_mensagens
        $mensagensData = [
            [
                'id' => 1,
                'welcome_email' => 'Muito obrigado por colaborar com nossa instituição.',
                'privacy_policy' => '<h4>SEÇÃO 1 - O QUE FAREMOS COM ESTA INFORMAÇÃO?</h4><p>Quando você realiza alguma transação...</p>', // Adapte se precisar
                'use_privacy' => 1
            ]
        ];

        $mensagens = $this->table('tb_mensagens');
        $mensagens->insert($mensagensData)->save();

        // Inserindo dados na tabela tb_integracoes
        $integracoesData = [
            ['id' => 1, 'fb_pixel' => '', 'gtm' => '', 'g_analytics' => '']
        ];

        $integracoes = $this->table('tb_integracoes');
        $integracoes->insert($integracoesData)->save();
    }
}
