# Class Treasurer — Development with Docker Compose

This project is prepared to run locally in Docker containers with a ready-to-use setup for PHP (Artisan) and Node (Vite/Tailwind). This way you don’t need to install matching PHP/Node versions on your host.

## Requirements
- Docker Desktop (or another Docker engine)
- Docker Compose v2 (usually bundled with Docker Desktop)

## Quick start

1. Copy the environment file (it will also be created automatically on first run):
   ```bash
   cp .env.example .env
   ```

2. Start the containers:
   ```bash
   docker compose up --build
   ```

   - PHP application server (Artisan): http://localhost:8000
   - Vite (HMR) for the frontend: http://localhost:5173

3. On the first run the `app` container will automatically:
   - run `composer install`
   - run `php artisan key:generate`
   - run `php artisan migrate` (if migrations exist)
   - start `php artisan serve --host 0.0.0.0 --port 8000`

If you don’t see changes in the browser UI:
- Make sure the `class-treasurer-node` container is running (it runs Vite HMR).
- Hard refresh the page to clear cache.

## Useful commands

- Open a shell in the app (PHP) container:
  ```bash
  docker compose exec app bash
  ```

- Run tests (Pest/PHPUnit):
  ```bash
  docker compose exec app php artisan test
  ```

- Format code (Pint):
  ```bash
  docker compose exec app vendor/bin/pint --dirty
  ```

- Install JS packages or build for production:
  ```bash
  docker compose exec node npm install
  docker compose exec node npm run build
  ```

## Containers

- `app` (PHP 8.4 CLI)
  - Includes Composer and PHP extensions required by Laravel.
  - Runs the development server `php artisan serve` on port 8000.
  - In Docker, the app is configured to use MySQL by default via container environment variables.

- `node` (Node 22)
  - Runs Vite in dev mode (`npm run dev`) on port 5173 and listens on `0.0.0.0`.
  - Avoids the npm `EBADENGINE` warning that requires Node ^20.19.0 or >=22.12.0.

- `mysql` (MySQL 8.4)
  - Exposes port 3306 to the host and persists data in the `mysql-data` Docker volume.
  - Default credentials (see docker-compose):
    - DB_HOST: `mysql`
    - DB_PORT: `3306`
    - DB_DATABASE: `class_treasurer`
    - DB_USERNAME: `laravel`
    - DB_PASSWORD: `secret`
    - ROOT password: `root`

## Using MySQL in Docker

The app container is configured to use the MySQL service automatically via environment variables defined in `docker-compose.yml`. You do not need to edit `.env` for Docker usage, but you can override values there if you prefer.

- Migrations/seeders:
  ```bash
  docker compose exec app php artisan migrate
  docker compose exec app php artisan db:seed # if you have seeders
  ```

- Connect from your host (e.g., TablePlus, MySQL Workbench):
  - Host: `127.0.0.1`
  - Port: `3306`
  - Username: `laravel`
  - Password: `secret`
  - Database: `class_treasurer`

- Switching back to SQLite (optional):
  - Stop containers: `docker compose down`
  - Edit `docker-compose.yml` app environment to `DB_CONNECTION=sqlite` and remove MySQL variables, or set these in your `.env`.
  - Start containers again: `docker compose up --build`

Note: If you previously cached the Laravel config, clear it after changing DB settings:
```bash
docker compose exec app php artisan config:clear
```

## Vite configuration

The `vite.config.js` contains:
```js
server: {
  host: '0.0.0.0',
  port: 5173,
  strictPort: true,
  cors: true,
}
```
This allows Vite to listen inside the container and be available from the host at `http://localhost:5173`.

## Troubleshooting

- Warning `npm warn EBADENGINE ... required: { node: '^20.19.0 || >=22.12.0' } current: { node: 'v20.18.0' }`:
  - The `node` container uses Node 22, so this warning should not appear inside Docker.
  - If you run `npm` on your host instead of Docker, upgrade your local Node to 20.19+ or 22.12+ (22 LTS recommended), or use the `node` container.

- CSS/JS changes are not visible:
  - Ensure the `node` container is running: `docker compose ps`.
  - Check logs: `docker compose logs -f node`.

- Permission errors for `storage` or `bootstrap/cache`:
  - Run: `docker compose exec app php artisan storage:link`
  - Fix permissions on the host, e.g.: `chmod -R u+rw storage bootstrap/cache`

## Stopping and cleaning up

- Stop containers (without deleting them):
  ```bash
  docker compose down
  ```

- Remove containers and cache volumes (e.g. after larger updates):
  ```bash
  docker compose down -v
  ```

## Project notes
- Don’t change project dependencies without justification. This setup resolves tooling version differences (PHP/Node) via Docker.
- If you want MySQL/PostgreSQL, add the service to `docker-compose.yml` and adjust `.env` and `config/database.php`. By default the project uses SQLite.


## Testing database (MySQL)

By default, tests run against fast in-memory SQLite. If you need to validate MySQL-specific behavior, a dedicated testing database is provisioned automatically in Docker.

- A `class_treasurer_test` database is created on first MySQL start by the init script at `docker/mysql/init/01-init.sql`.
- To switch tests to MySQL, edit `.env.testing` and uncomment the MySQL block:
  ```env
  DB_CONNECTION=mysql
  DB_HOST=mysql
  DB_PORT=3306
  DB_DATABASE=class_treasurer_test
  DB_USERNAME=laravel
  DB_PASSWORD=secret
  ```
- (Re)run migrations for the testing environment if needed:
  ```bash
  docker compose exec app php artisan migrate --env=testing
  ```
- Run tests:
  ```bash
  docker compose exec app php artisan test
  ```

Notes:
- Ensure containers are running: `docker compose up -d`.
- Dev DB remains `class_treasurer`; test DB is `class_treasurer_test` to keep data isolated.
