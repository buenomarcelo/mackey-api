# Mackey API

API em Laravel para o sistema Mackey.

## Stack

- Laravel 13 / PHP 8.4
- MySQL 8 + Redis
- Laravel Sanctum (autenticação via sessão/cookie — SPA stateful)
- Docker (nginx + php-fpm + mysql + redis)

## Arquitetura

A lógica de negócio vive inteiramente em `domain/` (namespace PSR-4 `MAC\`), não em `app/`. `app/` guarda apenas o essencial do skeleton do Laravel (providers, exception handling básico).

Cada entidade de domínio segue este layout em `domain/Models/{Entidade}/`:

```
{Entidade}.php          # Model Eloquent
Controllers/            # Controllers finos (recebem Request, chamam Action, retornam Resource)
Actions/                # Regra de negócio (unidade principal de lógica)
Requests/               # Form Requests de validação
Resources/               # Serialização da resposta da API
DTO/                    # Data Transfer Objects
Enums/
```

**Regra do controller (nunca violar):** um método de controller só pode (1) receber a request via FormRequest, (2) chamar uma ou mais Actions, (3) retornar uma resposta. Nada de query Eloquent, `DB::`, ou lógica de negócio diretamente no controller.

Todo model expõe `uuid` como identificador externo (rota e resposta da API) e esconde `id` — use as traits `Illuminate\Database\Eloquent\Concerns\HasUuids` + `MAC\Base\Traits\HasUuidRouteKey` juntas, com resolução de conflito:

```php
use HasUuids, HasUuidRouteKey {
    HasUuidRouteKey::uniqueIds insteadof HasUuids;
}
```

E lembre de declarar `#[Hidden(['id', ...])]` na classe do model.

## Rodando localmente

```bash
cp .env.example .env   # se ainda não existir
docker compose up -d --build
docker compose exec -u web php composer install
docker compose exec -u web php php artisan migrate --seed
```

A API sobe em `http://localhost:8081`.

## Comandos úteis

```bash
docker compose exec -u web php php artisan test     # rodar os testes
docker compose exec -u web php php artisan tinker
docker compose exec -u web php ./vendor/bin/pint     # code style
```

## Autenticação

Sanctum SPA (stateful, via cookie de sessão — não usa Bearer token). O front precisa:
1. Rodar na mesma "site" (registrable domain) da API — configurado em `SANCTUM_STATEFUL_DOMAINS` (`.env`).
2. Enviar `withCredentials: true` em todas as requisições.
3. Buscar `GET /sanctum/csrf-cookie` antes do login (a rota de login roda sob o middleware `web`, com CSRF).

Fluxo:

- `GET /sanctum/csrf-cookie` — inicializa o cookie `XSRF-TOKEN`
- `POST /api/auth/login` — `{ email, password }` → `{ data: { uuid, name, email } }`
- `GET /api/auth/me` — requer sessão autenticada
- `POST /api/auth/logout` — invalida a sessão

`config/cors.php` tem `supports_credentials: true` e `allowed_origins` restrito a `FRONTEND_URL` (obrigatório com credentials — não pode ser `*`).

## Debug (Xdebug + VSCode)

Xdebug já vem instalado no container `php`, desligado por padrão (`xdebug.mode=debug` + `xdebug.start_with_request=trigger` — só conecta quando explicitamente disparado, pra não poluir o output de `artisan`/`composer` a cada comando).

1. Instale a extensão **PHP Debug** (Xdebug) no VSCode.
2. Abra a pasta `mackey-api` como workspace (o `launch.json` usa `${workspaceFolder}` mapeado para `/var/www/html`).
3. Rode "Listen for Xdebug (docker)" (`.vscode/launch.json`) — porta `9003`.
4. Dispare o trigger na requisição: extensão de browser "Xdebug helper", ou manualmente com `?XDEBUG_TRIGGER=1` na URL, ou `XDEBUG_TRIGGER=1 php artisan ...` via CLI.

`extra_hosts: host.docker.internal:host-gateway` está no `docker-compose.yml` — necessário porque esse ambiente roda Docker Engine puro (não Docker Desktop), que não resolve `host.docker.internal` automaticamente.

## i18n

`APP_LOCALE=pt_BR` (fallback `en`). Mensagens de validação e auth traduzidas em `lang/pt_BR/`.
