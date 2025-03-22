<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class InsertFuncoes extends AbstractSeed
{
    public function run(): void
    {
        // Dados das páginas para inserir na tabela 'tb_paginas'
        $dataPages = [
            [
                'nome' => 'Sobre',
                'link' => 'sobre'
            ],
            [
                'nome' => 'Produtos',
                'link' => 'produtos'
            ],
            [
                'nome' => 'Doadores',
                'link' => 'doadores'
            ],
            [
                'nome' => 'Financeiro',
                'link' => 'financeiro'
            ],
            [
                'nome' => 'CAPTCHA',
                'link' => 'captcha'
            ],
            [
                'nome' => 'Integracoes',
                'link' => 'integracoes'
            ],
            [
                'nome' => 'Mensagens',
                'link' => 'mensagens'
            ],
            [
                'nome' => 'Novidades',
                'link' => 'novidades'
            ],
            [
                'nome' => 'Privacidade e Termo',
                'link' => 'politica-de-privacidade'
            ],
            [
                'nome' => 'Aparência',
                'link' => 'aparencia'
            ],
            [
                'nome' => 'Cabeçalho',
                'link' => 'cabecalho'
            ],
            [
                'nome' => 'Rodapé',
                'link' => 'rodape'
            ],
            [
                'nome' => 'Webhook',
                'link' => 'webhook'
            ],
            [
                'nome' => 'Funções',
                'link' => 'funcoes'
            ],
            [
                'nome' => 'Usuários',
                'link' => 'usuarios'
            ],
        ];

        // Insere os dados na tabela 'tb_paginas'
        $pages = $this->table('tb_paginas');
        $pages
            ->insert($dataPages)
            ->saveData();

        // Dados das páginas para inserir na tabela 'tb_paginas'
        $dataActions = [
            [
                'nome' => 'Visualizar (criados somente por ele)',
                'tipo' => 'only_own'
            ],
            [
                'nome' => 'Visualizar (criados por todos)',
                'tipo' => 'read'
            ],
            [
                'nome' => 'Criar',
                'tipo' => 'create'
            ],
            [
                'nome' => 'Editar',
                'tipo' => 'update'
            ],
            [
                'nome' => 'Deletar',
                'tipo' => 'delete'
            ],
        ];

        // Insere os dados na tabela 'tb_acoes'
        $pages = $this->table('tb_acoes');
        $pages
            ->insert($dataActions)
            ->saveData();

        // Dados das páginas para inserir na tabela 'tb_paginas'
        $dataPageActions = [
            [
                'pagina_id' => 1,
                'acao_id' => 2
            ],
            [
                'pagina_id' => 1,
                'acao_id' => 4
            ],

            [
                'pagina_id' => 2,
                'acao_id' => 1
            ],
            [
                'pagina_id' => 2,
                'acao_id' => 2
            ],
            [
                'pagina_id' => 2,
                'acao_id' => 3
            ],
            [
                'pagina_id' => 2,
                'acao_id' => 4
            ],
            [
                'pagina_id' => 2,
                'acao_id' => 5
            ],

            [
                'pagina_id' => 3,
                'acao_id' => 2
            ],

            [
                'pagina_id' => 4,
                'acao_id' => 2
            ],

            [
                'pagina_id' => 5,
                'acao_id' => 2
            ],
            [
                'pagina_id' => 5,
                'acao_id' => 3
            ],
            [
                'pagina_id' => 5,
                'acao_id' => 4
            ],
            [
                'pagina_id' => 5,
                'acao_id' => 5
            ],

            [
                'pagina_id' => 6,
                'acao_id' => 2
            ],
            [
                'pagina_id' => 6,
                'acao_id' => 4
            ],

            [
                'pagina_id' => 7,
                'acao_id' => 2
            ],
            [
                'pagina_id' => 7,
                'acao_id' => 4
            ],

            [
                'pagina_id' => 8,
                'acao_id' => 1
            ],
            [
                'pagina_id' => 8,
                'acao_id' => 2
            ],
            [
                'pagina_id' => 8,
                'acao_id' => 3
            ],

            [
                'pagina_id' => 9,
                'acao_id' => 2
            ],
            [
                'pagina_id' => 9,
                'acao_id' => 4
            ],

            [
                'pagina_id' => 10,
                'acao_id' => 2
            ],
            [
                'pagina_id' => 10,
                'acao_id' => 4
            ],

            [
                'pagina_id' => 11,
                'acao_id' => 2
            ],
            [
                'pagina_id' => 11,
                'acao_id' => 4
            ],

            [
                'pagina_id' => 12,
                'acao_id' => 2
            ],
            [
                'pagina_id' => 12,
                'acao_id' => 4
            ],

            [
                'pagina_id' => 13,
                'acao_id' => 2
            ],
            [
                'pagina_id' => 13,
                'acao_id' => 4
            ],
            [
                'pagina_id' => 13,
                'acao_id' => 5
            ],

            [
                'pagina_id' => 14,
                'acao_id' => 1
            ],
            [
                'pagina_id' => 14,
                'acao_id' => 2
            ],
            [
                'pagina_id' => 14,
                'acao_id' => 3
            ],
            [
                'pagina_id' => 14,
                'acao_id' => 4
            ],
            [
                'pagina_id' => 14,
                'acao_id' => 5
            ],

            [
                'pagina_id' => 15,
                'acao_id' => 1
            ],
            [
                'pagina_id' => 15,
                'acao_id' => 2
            ],
            [
                'pagina_id' => 15,
                'acao_id' => 3
            ],
            [
                'pagina_id' => 15,
                'acao_id' => 4
            ],
            [
                'pagina_id' => 15,
                'acao_id' => 5
            ],

        ];

        // Insere os dados na tabela 'tb_pagina_acoes'
        $pages = $this->table('tb_pagina_acoes');
        $pages
            ->insert($dataPageActions)
            ->saveData();

        // Dados para a tabela tb_funcoes
        $funcoesData = [
            [
                'id' => 1,
                'nome' => 'Administrador'
            ],
            [
                'id' => 2,
                'nome' => 'Vendedor'
            ],
        ];

        // Insere os dados na tabela 'tb_funcoes'
        $pages = $this->table('tb_funcoes');
        $pages
            ->insert($funcoesData)
            ->saveData();

        // Dados para a tabela tb_permissao_funcao
        $permissaoFuncaoData = [
            ['pagina_id' => 1,  'funcao_id' => 1, 'acao_id' => 2],
            ['pagina_id' => 1,  'funcao_id' => 1, 'acao_id' => 4],
            ['pagina_id' => 2,  'funcao_id' => 1, 'acao_id' => 2],
            ['pagina_id' => 2,  'funcao_id' => 1, 'acao_id' => 3],
            ['pagina_id' => 2,  'funcao_id' => 1, 'acao_id' => 4],
            ['pagina_id' => 2,  'funcao_id' => 1, 'acao_id' => 5],
            ['pagina_id' => 3,  'funcao_id' => 1, 'acao_id' => 2],
            ['pagina_id' => 4,  'funcao_id' => 1, 'acao_id' => 2],
            ['pagina_id' => 5,  'funcao_id' => 1, 'acao_id' => 2],
            ['pagina_id' => 5,  'funcao_id' => 1, 'acao_id' => 3],
            ['pagina_id' => 5,  'funcao_id' => 1, 'acao_id' => 4],
            ['pagina_id' => 5,  'funcao_id' => 1, 'acao_id' => 5],
            ['pagina_id' => 6,  'funcao_id' => 1, 'acao_id' => 2],
            ['pagina_id' => 6,  'funcao_id' => 1, 'acao_id' => 4],
            ['pagina_id' => 7,  'funcao_id' => 1, 'acao_id' => 2],
            ['pagina_id' => 7,  'funcao_id' => 1, 'acao_id' => 4],
            ['pagina_id' => 8,  'funcao_id' => 1, 'acao_id' => 2],
            ['pagina_id' => 8,  'funcao_id' => 1, 'acao_id' => 3],
            ['pagina_id' => 9,  'funcao_id' => 1, 'acao_id' => 2],
            ['pagina_id' => 9,  'funcao_id' => 1, 'acao_id' => 3],
            ['pagina_id' => 10, 'funcao_id' => 1, 'acao_id' => 2],
            ['pagina_id' => 10, 'funcao_id' => 1, 'acao_id' => 4],
            ['pagina_id' => 11, 'funcao_id' => 1, 'acao_id' => 2],
            ['pagina_id' => 11, 'funcao_id' => 1, 'acao_id' => 4],
            ['pagina_id' => 12, 'funcao_id' => 1, 'acao_id' => 2],
            ['pagina_id' => 12, 'funcao_id' => 1, 'acao_id' => 4],
            ['pagina_id' => 13, 'funcao_id' => 1, 'acao_id' => 2],
            ['pagina_id' => 13, 'funcao_id' => 1, 'acao_id' => 4],
            ['pagina_id' => 13, 'funcao_id' => 1, 'acao_id' => 5],
            ['pagina_id' => 14, 'funcao_id' => 1, 'acao_id' => 2],
            ['pagina_id' => 14, 'funcao_id' => 1, 'acao_id' => 3],
            ['pagina_id' => 14, 'funcao_id' => 1, 'acao_id' => 4],
            ['pagina_id' => 14, 'funcao_id' => 1, 'acao_id' => 5],
            ['pagina_id' => 15, 'funcao_id' => 1, 'acao_id' => 2],
            ['pagina_id' => 15, 'funcao_id' => 1, 'acao_id' => 3],
            ['pagina_id' => 15, 'funcao_id' => 1, 'acao_id' => 4],
            ['pagina_id' => 15, 'funcao_id' => 1, 'acao_id' => 5],
            ['pagina_id' => 2,  'funcao_id' => 2, 'acao_id' => 1],
            ['pagina_id' => 2,  'funcao_id' => 2, 'acao_id' => 3],
            ['pagina_id' => 2,  'funcao_id' => 2, 'acao_id' => 4],
            ['pagina_id' => 2,  'funcao_id' => 2, 'acao_id' => 5],
            ['pagina_id' => 3,  'funcao_id' => 2, 'acao_id' => 2],
            ['pagina_id' => 4,  'funcao_id' => 2, 'acao_id' => 2],
        ];

        // Insere os dados na tabela 'tb_permissao_funcao'
        $pages = $this->table('tb_permissao_funcao');
        $pages
            ->insert($permissaoFuncaoData)
            ->saveData();
    }
}