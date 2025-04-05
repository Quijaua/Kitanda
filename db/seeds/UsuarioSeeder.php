<?php

use Phinx\Seed\AbstractSeed;

class UsuarioSeeder extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Cria dados de exemplo para a tabela de usuários
     */
    public function run(): void
    {
        $data = [];
        $faker = Faker\Factory::create('pt_BR');
        
        // Criar um usuário administrador
        $data[] = [
            'status' => 1,
            'roles' => 1, // 1 = admin
            'nome' => 'Administrador',
            'email' => 'admin@exemplo.com',
            'password' => password_hash('senha123', PASSWORD_DEFAULT),
            'recup_password' => null,
            'magic_link' => null,
            'phone' => $faker->phoneNumber(),
            'cpf' => $faker->cpf(false),
            'data_nascimento' => 1, // Parece ser um boolean conforme o tipo na tabela
            'cep' => $faker->postcode(),
            'endereco' => $faker->streetName(),
            'numero' => $faker->buildingNumber(),
            'complemento' => $faker->optional(0.3)->secondaryAddress(),
            'municipio' => $faker->city(),
            'cidade' => $faker->city(),
            'uf' => $faker->stateAbbr(),
            'pais' => 'Brasil',
            'estrangeiro' => 0,
            'asaas_id' => null,
            'newsletter' => 1,
            'private' => 0,
            'instagram' => $faker->optional(0.5)->userName(),
            'site' => $faker->optional(0.3)->url(),
            'facebook' => $faker->optional(0.4)->userName(),
            'tiktok' => $faker->optional(0.2)->userName(),
            'descricao' => $faker->optional(0.7)->paragraph(3),
            'criado_por' => 1
        ];
        
        // Criar usuários regulares
        for ($i = 0; $i < 30; $i++) {
            $isEstrangeiro = $faker->boolean(10); // 10% de chance de ser estrangeiro
            
            $data[] = [
                'status' => $faker->boolean(90) ? 1 : 0, // 90% ativos
                'roles' => 0, // 0 = usuário normal
                'nome' => $faker->name(),
                'email' => $faker->unique()->safeEmail(),
                'password' => password_hash('senha' . $faker->randomNumber(4), PASSWORD_DEFAULT),
                'recup_password' => null,
                'magic_link' => null,
                'phone' => $faker->phoneNumber(),
                'cpf' => $isEstrangeiro ? null : $faker->cpf(false),
                'data_nascimento' => $faker->boolean(70) ? 1 : 0, // 70% informaram data de nascimento
                'cep' => $isEstrangeiro ? null : $faker->postcode(),
                'endereco' => $faker->streetAddress(),
                'numero' => $faker->buildingNumber(),
                'complemento' => $faker->optional(0.4)->secondaryAddress(),
                'municipio' => $isEstrangeiro ? null : $faker->city(),
                'cidade' => $faker->city(),
                'uf' => $isEstrangeiro ? null : $faker->stateAbbr(),
                'pais' => $isEstrangeiro ? $faker->country() : 'Brasil',
                'estrangeiro' => $isEstrangeiro ? 1 : 0,
                'asaas_id' => $faker->optional(0.3)->regexify('[a-z0-9]{8}'),
                'newsletter' => $faker->boolean(60) ? 1 : 0, // 60% inscritos na newsletter
                'private' => $faker->boolean(20) ? 1 : 0, // 20% perfis privados
                'instagram' => $faker->optional(0.6)->userName(),
                'site' => $faker->optional(0.2)->url(),
                'facebook' => $faker->optional(0.4)->userName(),
                'tiktok' => $faker->optional(0.3)->userName(),
                'descricao' => $faker->optional(0.5)->paragraph(3),
                'criado_por' => 1
            ];
        }

        $table = $this->table('tb_clientes'); // Ajuste o nome da tabela conforme necessário
        $table->insert($data)->save();
    }
}
