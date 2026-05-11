# Titanium Rental Car

SaaS de locação de veículos em **PHP 8.3+** com **MySQL 8.0+**, MVC sem framework pesado, interface em HTML/CSS/JS e i18n **pt-BR** / **en-US**.

## Requisitos

- PHP 8.3 ou superior (extensões `pdo_mysql`, `json`, `fileinfo`)
- MySQL 8.0+
- Apache com `mod_rewrite` **ou** servidor embutido do PHP apontando para a pasta `public`

## Instalação

1. Clone ou copie o projeto e entre na pasta `titanium-rental-car`.
2. Copie o ambiente: `cp .env.example .env` (no Windows: `copy .env.example .env`) e ajuste `APP_URL`, `DB_*` e, se necessário, `APP_BASE` (subpasta publicada).
3. Crie o banco e as tabelas:

   ```bash
   mysql -u root -p < database/schema.sql
   mysql -u root -p < database/seed.sql
   ```

4. Garanta permissão de escrita em `public/assets/uploads` e `storage/logs`.
5. Servidor embutido (desenvolvimento):

   ```bash
   php -S localhost:8888 -t public
   ```

   Acesse `http://localhost:8888` (ajuste conforme `APP_URL`). A landing integrada e as páginas **LGPD** (`/privacidade`, `/termos`) só funcionam com este servidor (ou Apache) a apontar para **`public`**. Se pré-visualizar a pasta `site/` fora do PHP, edite o atributo **`data-dev-login-base`** no `site/index.html` para o mesmo URL do passo 5 (por defeito `http://localhost:8888`) — assim **Minha conta** abre o login correcto. Em `file://` sem esse URL, abre-se `site/login.html` com instruções. Para LGPD offline use `privacidade.html` e `termos.html` na pasta `site/`.

6. Com Apache, defina o *DocumentRoot* para a pasta `public` ou use o `.htaccess` na raiz do projeto que encaminha para `public/index.php`.

## Contas de demonstração (seed)

| Perfil   | E-mail                    | Senha       |
|----------|---------------------------|------------|
| Dono     | `owner@titaniumrental.com` | `password123` |
| Operador | `operator@titaniumrental.com` | `password123` |

## Logo

A marca usa `public/assets/img/logo.jpeg` (copiada da raiz do repositório do cliente).

## Segurança

- Senhas com `password_hash` (bcrypt, custo 12).
- CSRF em formulários POST e em cadastro rápido de cliente via API.
- Sessão com regeneração de ID no login.
- Limite de tentativas de login por IP (arquivos em `storage/logs`).
- PDO com prepared statements e saída escapada com `htmlspecialchars`.

## Estrutura principal

- `public/index.php` — front controller.
- `config/routes.php` — rotas (método + caminho).
- `app/controllers`, `app/models`, `app/views`, `app/middleware`, `app/helpers`.
- `lang/pt-BR.php` e `lang/en-US.php` — traduções.
- `database/schema.sql` e `database/seed.sql` — schema e dados de exemplo.

## Idioma

Use o seletor no topo ou o parâmetro `?lang=en-US` / `?lang=pt-BR`. O idioma fica na sessão e, para usuários logados, é gravado em `users.lang_pref`.

## Landing e leads

- O formulário na página inicial (`POST /lead`) grava pedidos em `storage/leads/leads.jsonl` (uma linha JSON por envio). Faça backup desta pasta em produção.
- SEO: `GET /sitemap.xml` e `GET /robots.txt` são gerados pela app com base em `APP_URL` / `APP_BASE`.

## Backup e base de dados

- Faça cópia regular do MySQL (`mysqldump`) e inclua `storage/leads/` e `storage/logs/` se usar leads e auditoria em ficheiro.
- Após atualizar o código, aplique migrações novas, por exemplo: `mysql -u root -p titanium_rental_car < database/migrations/003_privacy_login_consent.sql` (registo LGPD no login).

## Relatórios

- Na página **Relatórios** (perfil dono), use **Exportar CSV** para descarregar o agregado mensal do intervalo de datas seleccionado.
