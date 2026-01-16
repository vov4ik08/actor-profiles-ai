### Quick start

0) Create `.env` (a minimal template is in `.env.example`):

```bash
cp .env.example .env
```

1) Start the app (build images if needed, install frontend deps, start containers, run migrations):

```bash
make up
```

Open the app: `http://localhost:8080`

### Makefile commands

Run `make help` to see the list in your terminal. The available targets are:

```bash
make help
```

- **`make up`**: Start containers (build if needed), install frontend deps, start `node`, run migrations.
- **`make build`**: Build the `app` image.
- **`make down`**: Stop and remove containers.
- **`make logs`**: Follow logs (tail 200).
- **`make yarn_install`**: Install frontend deps into `node_modules/` (runs in the `node` container).
- **`make npm_install`**: Alias for `make yarn_install`.
- **`make composer_install`**: Install PHP deps into `vendor/` (runs in the `app` container).
- **`make key_generate`**: Generate `APP_KEY` (`php artisan key:generate`).
- **`make migrate`**: Run DB migrations.
- **`make test`**: Run tests (`php artisan test`).

### Notes

### MySQL settings

By default, `docker-compose.yml` uses:

- **host**: `db`
- **port**: `3306`
- **database**: `actor_profile_ai`
- **user**: `actor`
- **password**: `secret`

To make Laravel use MySQL, ensure your `.env` has:

- `DB_CONNECTION=mysql`
- `DB_HOST=db`

### OpenAI

Environment variables are required (in your host `.env` or exported in your shell — they are passed into the `app` container):

- `OPENAI_API_KEY=...`
- `OPENAI_MODEL=gpt-4o-mini` (optional)

Prompt endpoint check:

```bash
curl http://localhost:8080/api/actors/prompt-validation
```

