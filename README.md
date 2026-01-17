# 🛒 Kitanda

A Kitanda é um sistema de gerenciamento para lojas online Open source

---

## 📖 Sobre o Projeto

A **Kitanda** é uma plataforma desenvolvida pela [Quijaua](https://quijaua.com.br), em consórcio com a [Nômade Tecnologias](https://nomade.tec.br/), para atender à demanda do **Instituto Terra Dourada** dentro do projeto [Mulheres Empreendedoras da Amazônia](https://plantaformas.org/processes/mulheres-empreendedoras-amazonia) e da **Ankara Moda Afro** — iniciativa da Casa de Cultura AfroGerais, projetos fomentados pelo [Mover-se na Web](https://moverse.ceweb.br).

A plataforma busca impulsionar o trabalho de empreendedoras da Amazônia e de Minas Gerais, fortalecendo a **sustentabilidade** e ampliando a **visibilidade do artesanato local**.

O Kitanda conta com **integração nativa com o Asaas**, permitindo a automatização de cobranças e gestão de pagamentos de forma segura e eficiente.

Este projeto teve início como um **fork do [Floema Doar](https://github.com/Quijaua/FloemaDoar)** — uma solução originalmente criada para recebimentos de doações.

---

## **Estrutura Do Projeto**

A seguir uma visão resumida da estrutura principal do projeto e a função das pastas mais relevantes:

```
kitanda/
├── .env_example          # Exemplo de variáveis de ambiente
├── .gitignore            # Regras de ignore do Git
├── .htaccess             # Regras Apache/URL rewrite
├── Dockerfile            # Imagem Docker usada para configurar ambiente docker
├── LICENSE               # Licença do projeto
├── README.md             # Este documento
├── admin/                # Painel administrativo (rotas, uploads, templates)
│   ├── .htaccess         # Regras específicas do painel
│   ├── back-end/         # Endpoints do admin (criar/editar conteúdo)
│   ├── images/           # Imagens usadas no painel
│   ├── index.php         # Entrada do painel
│   ├── pages/            # Views do painel (PHP)
│   └── template-parts/   # Cabeçalho, rodapé e sidebars do admin
├── assets/               # Arquivos públicos: CSS, JS, imagens e bibliotecas front-end
│   ├── Ankara/           # Ativos do tema Ankara (css, thumbs, imagens)
│   ├── Oralituras/       # Ativos do tema Oralituras (css e thumbs)
│   ├── TerraDourada/     # Ativos do tema TerraDourada (css, thumbs, imagens)
│   ├── ajax/             # Bibliotecas AJAX (jQuery, popper, etc.)
│   ├── bootstrap/        # Bootstrap e variantes vendorizadas
│   ├── css/              # CSS global/custom
│   ├── google/           # Fonts e recursos ligados ao Google
│   ├── img/              # Imagens públicas (logos, banners)
│   ├── js/               # Scripts front-end
│   └── preview-image/    # Imagens de preview usadas em listings
├── back-end/             # Endpoints e lógica PHP (pagamentos, assinaturas, APIs)
├── composer.json         # Dependências PHP e autoload (Composer)
├── composer.lock         # Versões travadas das dependências
├── config.php            # Configurações centrais do projeto
├── db/                   # Migrações e seeders (Phinx)
│   ├── migrations/       # Arquivos de migração do banco
│   └── seeds/            # Seeders para popular dados iniciais
├── dist/                 # Assets compilados / distribuídos (builds)
│   ├── css/
│   ├── img/
│   ├── js/
│   └── libs/
├── docker-compose.yml    # Configuração do ambiente Docker
├── fonts/                # Fontes usadas pelo projeto
├── index.php             # Front controller público
├── log/                  # Logs da aplicação (geralmente vazio no VCS)
├── login/                # Páginas e lógica de autenticação/recuperação
├── nginx/                # Exemplos de configuração Nginx (kitanda.conf)
├── nginx.local.conf      # Config local de Nginx para desenvolvimento
├── pages/                # Páginas públicas (home, produto, checkout, blog)
├── phinx.php             # Configuração do Phinx (migrations)
├── robots.txt            # Regras de indexação
├── services/             # Jobs, cron e handlers de webhook
├── templates/            # Temas e layouts do site
│   ├── Ankara/           # Tema Ankara
│   ├── Oralituras/       # Tema Oralituras
│   ├── TerraDourada/     # Tema TerraDourada
│   └── _fallback/        # Tema fallback usado quando não há tema ativo
└── user/                 # Área e recursos para usuários/empreendedoras
```

---

## ⚙️ Instalação

1. Faça o download ou clone o repositório e coloque a pasta no diretório público da sua hospedagem:

```bash
git clone https://github.com/Quijaua/Kitanda.git
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
