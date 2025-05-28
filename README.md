# Kitanda
Loja Virtual

## Instalação

Faça download ou clone do plugin e coloque a pasta no diretório público da sua hospedagem. 

Execute o comando composer para instalar as bibliotecas

```sh
$ composer install
```

### Configurando o sistema
Antes de subir o ambiente é preciso configurá-lo. Para isso crie no servidor um arquivo `.env ` baseado no `.env_example` e preencha-o corretamente.

```sh
# criando o arquivo
$ cp .env_example .env

# editando o arquivo (utilize o seu editor preferido)
$ nano .env
```

Crie um banco de dados pelo cPanel (ou soluções alternativas a ele) e restaure o banco de dados que está no diretório ***sql***, via PHPmyAdmin ou conforme sua preferência.

### Migrar banco de dados
```sh
vendor/bin/phinx migrate
```

### Seeder

Em ambiente de produção rode
```sh
vendor/bin/phinx seed:run -s InsertInitialData -s InsertFuncoes
```

Em ambiente de desenvolvimento rode
```sh
composer require fakerphp/faker
vendor/bin/phinx seed:run
```

### Usuário administrador
O banco de dados inicial inclui um usuário de role `admin` de **id** `1` e **email** `admin@admin.com`.
Este usuário possui permissão de modificar informações da página principal.

- **email**: `admin@admin.com`
- **senha**: `admin`
