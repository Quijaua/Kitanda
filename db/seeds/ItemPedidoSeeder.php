<?php

use Phinx\Seed\AbstractSeed;

class ItemPedidoSeeder extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Cria dados de exemplo para a tabela de itens de pedido
     */
    public function run(): void
    {
        $data = [];
        $faker = Faker\Factory::create('pt_BR');
        
        // Vamos criar 50 itens de pedido
        for ($i = 0; $i < 50; $i++) {
            $quantidade = $faker->numberBetween(1, 10);
            $preco = $faker->randomFloat(2, 10, 500);
            $preco_total = $preco * $quantidade;
            
            $data[] = [
                'pedido_id' => $faker->numberBetween(1, 20), // Assumindo que existam 20 pedidos
                'produto_id' => $faker->numberBetween(1, 1), // Assumindo que existam 30 produtos
                'nome' => $faker->words(3, true), // Nome do produto
                'preco' => $preco,
                'quantidade' => $quantidade,
                'preco_total' => $preco_total,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
        }

        $table = $this->table('tb_pedido_itens'); // Substitua 'nome_da_tabela' pelo nome real da sua tabela
        $table->insert($data)->save();
    }
    
    /**
     * Dependências de outros seeders, se houver
     */
    public function getDependencies(): array
    {
        return [
            'PedidoSeeder',  // Caso exista um seeder para a tabela de pedidos
            'ProdutoSeeder'  // Caso exista um seeder para a tabela de produtos
        ];
    }
}
