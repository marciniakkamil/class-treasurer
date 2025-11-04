# Testing Database (MySQL) — Addendum

This file will be merged into README.md automatically by the maintainer. It documents how to use a dedicated MySQL testing database with Docker.

## Default test setup (fastest)
Tests use SQLite in-memory by default via `.env.testing`:

```
DB_CONNECTION=sqlite
DB_DATABASE=:memory:
```

This is the fastest and most isolated option for most projects.

## Optional: Use MySQL for tests
If you need to validate MySQL-specific behavior (collations, JSON functions, indexing, etc.), you can switch tests to MySQL.

- We provision a dedicated database `class_treasurer_test` automatically on first MySQL container start via `docker/mysql/init/01-init.sql`.
- To switch tests to MySQL, edit `.env.testing` and uncomment the MySQL block:

```
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=class_treasurer_test
DB_USERNAME=laravel
DB_PASSWORD=secret
```

- Then (re)run migrations for the testing database if your tests don’t auto-migrate:

```
docker compose exec app php artisan migrate --env=testing
```

- Finally, run your tests:

```
docker compose exec app php artisan test
```

Notes:
- Ensure containers are up: `docker compose up -d`.
- You can keep development data separate: app DB is `class_treasurer`, tests DB is `class_treasurer_test`.
