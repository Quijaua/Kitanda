# Floema Doar
Solução para recebimentos de doações

## Instalação

Faça download ou clone do plugin e coloque a pasta no diretório público da sua hospedagem. 

Execute o comando composer para instalar as bibliotecas

```sh
$ composer install
```

Criei um banco de dados pelo cPanel (ou soluções alternativas a ele) e restaure o banco de dados que está no diretório ***sql***, via PHPmyAdmin ou conforme sua preferência.


### Migrar banco de dados


### Configurando o sistema
Antes de subir o ambiente é preciso configurá-lo. Para isso crie no servidor um arquivo `.env ` baseado no `.env_example` e preencha-o corretamente.

```sh
# criando o arquivo
$ cp .env_example .env

# editando o arquivo (utilize o seu editor preferido)
$ nano .env
```

### Usuário administrador
O banco de dados inicial inclui um usuário de role `admin` de **id** `1` e **email** `admin@admin.com`.
Este usuário possui permissão de modificar informações da página principal.

- **email**: `admin@admin.com`
- **senha**: `admin`