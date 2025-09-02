<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

final class FreteDimensoesSeeder extends AbstractSeed
{
    public function run(): void
    {
        $data = [
            [
                'nome' => 'Mini',
                'altura' => 2,
                'largura' => 11,
                'comprimento' => 16,
                'peso' => 1.0,
            ],
            [
                'nome' => 'Pequeno',
                'altura' => 8,
                'largura' => 15,
                'comprimento' => 20,
                'peso' => 3.0,
            ],
            [
                'nome' => 'Médio',
                'altura' => 16,
                'largura' => 25,
                'comprimento' => 36,
                'peso' => 10.0,
            ],
            [
                'nome' => 'Grande',
                'altura' => 30,
                'largura' => 35,
                'comprimento' => 60,
                'peso' => 20.0,
            ],
        ];

        // Insere os dados na tabela 'tb_frete_dimensoes'
        $pages = $this->table('tb_frete_dimensoes');
        $pages
            ->insert($data)
            ->saveData();
    }
}