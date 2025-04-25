<?php

use Phinx\Seed\AbstractSeed;

class PedidosSeeder extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Cria dados de exemplo para a tabela de pedidos
     */
    public function run(): void
    {
        $data = [];
        $faker = Faker\Factory::create('pt_BR');
        
        // Status possíveis para os pedidos
        $statusPedido = ['aguardando_pagamento', 'pago', 'em_processamento', 'enviado', 'entregue', 'cancelado'];
        
        // Vamos criar 20 pedidos
        for ($i = 0; $i < 20; $i++) {
            // Gerar um ID de pedido apenas com números (8 dígitos)
            $pedidoId = $faker->numerify('######');
            
            // Data de criação aleatória (entre 30 dias atrás e hoje)
            $dataCriacao = $faker->dateTimeBetween('-30 days', 'now')->format('Y-m-d H:i:s');
            
            // Data de atualização igual ou posterior à data de criação
            $dataAtualizacao = $faker->dateTimeBetween($dataCriacao, 'now')->format('Y-m-d H:i:s');
            
            $data[] = [
                'id' => $i + 1, // Chave primária autoincrement
//                'pedido_numero' => $pedidoId, // ID do pedido apenas com números
                'pedido_id' => $pedidoId, // ID do pedido apenas com números
//                'cliente_id' => 1, // Assumindo que existam 50 clientes
                'usuario_id' => $faker->numberBetween(1, 2), // Assumindo que existam 50 clientes
//                'valor_total' => $faker->randomFloat(2, 50, 2000),
                'total' => $faker->randomFloat(2, 50, 2000),
                'status' => $faker->randomElement($statusPedido),
                'forma_pagamento' => $faker->randomElement(['credito', 'debito', 'boleto', 'pix']),
//                'observacoes' => $faker->optional(0.3)->sentence(),
//                'observacoes' => "teste",
                'data_criacao' => $dataCriacao,
//                'data_edicao' => $dataAtualizacao
            ];
        }

        $table = $this->table('tb_pedidos'); // Ajuste o nome da tabela conforme necessário
        $table->insert($data)->save();
    }
    
    /**
     * Dependências de outros seeders, se houver
     */
    public function getDependencies(): array
    {
        return [
            'ClienteSeeder', // Caso exista um seeder para a tabela de clientes
        ];
    }
}
