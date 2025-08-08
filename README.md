# 🛒 Kitanda

A Kitanda é um sistema de gerenciamento para lojas online Open source

---

## 📖 Sobre o Projeto

A **Kitanda** é uma plataforma desenvolvida pela [Quijaua](https://quijaua.com) em consórcio com a **Nômade Tecnologias**, para atender à demanda do **Instituto Terra Dourada** dentro do projeto [Mulheres Empreendedoras da Amazônia](https://plantaformas.org/processes/mulheres-empreendedoras-amazonia) e da **Ankara Moda Afro** — iniciativa da Casa de Cultura AfroGerais, também premiada no programa *Mover-se na Web*.

Este projeto foi contemplado pela chamada pública **Mover-se na Web**, uma iniciativa do [Nic.br](https://nic.br).

A plataforma tem como objetivo apoiar o desenvolvimento de empreendedores locais da Amazônia, promovendo a **sustentabilidade** e o **crescimento de negócios na região**.

O Kitanda conta com **integração nativa com o Asaas**, permitindo a automatização de cobranças e gestão de pagamentos de forma segura e eficiente.

Este projeto teve início como um **fork do [Floema Doar](https://github.com/Quijaua/FloemaDoar)** — uma solução originalmente criada para recebimentos de doações.

---

## ⚙️ Instalação

1. Faça o download ou clone o repositório e coloque a pasta no diretório público da sua hospedagem:

```bash
git clone https://github.com/seuusuario/kitanda.git
```

2. Instale as dependências com o Composer:

```bash
composer install
```

---

## ⚒️ Configuração

1. Copie o arquivo de exemplo `.env_example` e crie um `.env` com suas variáveis de ambiente:

```bash
cp .env_example .env
nano .env
```

2. Crie um banco de dados e importe o arquivo SQL localizado na pasta `sql/`, usando o **phpMyAdmin** ou ferramenta similar.

---

## 🛠️ Migrações do Banco de Dados

Execute as migrações com o Phinx:

```bash
vendor/bin/phinx migrate
```

---

## 🌱 Seeders

**Ambiente de produção:**

```bash
vendor/bin/phinx seed:run -s InsertInitialData -s InsertFuncoes
```

**Ambiente de desenvolvimento:**

```bash
composer require fakerphp/faker
vendor/bin/phinx seed:run
```

---

## 🔐 Acesso Administrador

Um usuário administrador já está incluído no banco inicial:

- **Email**: `admin@admin.com`
- **Senha**: `admin`

Esse usuário possui permissões para gerenciar informações da página principal.

---

## 🧾 Documentação Completa

Acesse a [Wiki do Projeto](https://github.com/Quijaua/Kitanda/wiki) para mais informações sobre

---

## 📄 Licença

Este projeto está licenciado sob os termos da [MIT License](LICENSE).
