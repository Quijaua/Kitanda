<?php

use Phinx\Seed\AbstractSeed;

class ProdutoSeeder extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Cria dados de exemplo para a tabela de produtos
     */
    public function run(): void
    {
        $data = [];
        $faker = Faker\Factory::create('pt_BR');

        // Lista de possíveis extensões de imagem
        $imageExtensions = ['jpg', 'png', 'webp'];

        // Vamos criar 30 produtos
        for ($i = 0; $i < 30; $i++) {
            // Gerar número aleatório de 2 dígitos (entre 10 e 99)
            $numeroAleatorio = $faker->numberBetween(10, 99);

            // Nome fixo com número aleatório
            $nome = "Nome do Produto " . $numeroAleatorio;
            $titulo = "Nome do Produto " . $numeroAleatorio;
//            $titulo = $faker->sentence(4);

            // Gerar nome da imagem baseado no nome do produto
            $imagemNome = strtolower(str_replace(' ', '-', $nome)) . '.' . $faker->randomElement($imageExtensions);

            // SEO nome - versão normalizada do nome
//            $seoNome = strtolower(str_replace(' ', '-', $nome));
            $seoNome = "Teste";

            // Texto fixo para descrição
            $descricao = "Somente um teste";

            $data[] = [
                'nome' => $nome,
                'titulo' => $titulo,
                'preco' => $faker->randomFloat(2, 19.90, 999.90),
                'imagem' => 'produtos/' . $imagemNome,
                'descricao' => $descricao,
                'vitrine' => $faker->boolean(70), // 70% de chance de estar na vitrine
                'link' => 'produto/' . $seoNome,
                'seo_nome' => $seoNome,
                'seo_descricao' => $seoNome,
                'criado_por' => $faker->numberBetween(1, 5), // Assumindo que existam 5 usuários admin
                'data_criacao' => date('Y-m-d H:i:s'),
                'data_edicao' => date('Y-m-d H:i:s')
            ];
        }

        $table = $this->table('tb_produtos'); // Ajuste o nome da tabela conforme necessário
        $table->insert($data)->save();
    }
    
    /**
     * Dependências de outros seeders, se houver
     */
    public function getDependencies(): array
    {
        return [
            'UsuarioSeeder', // Caso exista um seeder para a tabela de usuários
        ];
    }
}
