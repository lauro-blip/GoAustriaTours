# GoAustriaTours

Server-rendered Laravel 12 landing site for Austria tours (Google Ads). See [CLAUDE.md](CLAUDE.md) for the map and `docs/reference/` for details.

## Get running (XAMPP, Windows)

```bash
composer install && npm install && copy .env.example .env && php artisan key:generate
php artisan migrate --seed && php artisan storage:link && npm run build
php artisan serve
```

Create the MySQL database `goaustriatours` first (XAMPP defaults: user `root`, empty password). Site: http://127.0.0.1:8000, admin: http://127.0.0.1:8000/admin (login from `ADMIN_SEED_EMAIL` / `ADMIN_SEED_PASSWORD` in `.env`).

Run the tests: `php artisan test`.
