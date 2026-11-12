# Running locally on XAMPP (Windows)

## Requirements (verified on this machine)
- PHP 8.2+ (XAMPP PHP 8.2.12 at `C:\xampp\php`), Composer 2, Node 20+.
- Enable in `C:\xampp\php\php.ini` (remove the leading `;`): `mbstring`, `openssl`, `pdo_mysql`, `fileinfo`, `curl`, `gd` (needs WebP support), `intl`, `zip`. Check with `php -m`.

## Database
phpMyAdmin (http://localhost/phpmyadmin) or SQL:
```sql
CREATE DATABASE goaustriatours CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```
`.env.example` already uses XAMPP defaults (`root`, empty password, `127.0.0.1:3306`, database `goaustriatours`).

## Install
```bash
composer install
npm install
copy .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
npm run build          # production assets; use "npm run dev" for live editing
```
The seeder creates the admin from `ADMIN_SEED_EMAIL` / `ADMIN_SEED_PASSWORD` (`.env`). Change that password before any deploy.

## Viewing the site
**A) artisan serve:** `php artisan serve` then http://127.0.0.1:8000. If it fails on Windows, use `php -S 127.0.0.1:8000 -t public server.php` (copy `vendor/laravel/framework/src/Illuminate/Foundation/resources/server.php` to the project root first).

**B) Apache virtual host (goaustriatours.test):**
1. Add to `C:\xampp\apache\conf\extra\httpd-vhosts.conf`:
```apache
<VirtualHost *:80>
    ServerName goaustriatours.test
    DocumentRoot "C:/code/goaustriatours/public"
    <Directory "C:/code/goaustriatours/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```
2. As Administrator, add to `C:\Windows\System32\drivers\etc\hosts`: `127.0.0.1 goaustriatours.test`
3. Make sure `Include conf/extra/httpd-vhosts.conf` is enabled in `httpd.conf` and `mod_rewrite` is loaded. Restart Apache. Set `APP_URL=http://goaustriatours.test`.

## Mail, reCAPTCHA, queue
- **Mail:** `MAIL_MAILER=log` writes enquiry emails to `storage/logs/laravel.log`. For a mailbox UI, run Mailpit and set `MAIL_MAILER=smtp`, `MAIL_HOST=127.0.0.1`, `MAIL_PORT=1025`. Emails only send when staff addresses are set in Admin > Settings.
- **reCAPTCHA:** `.env.example` holds Google's public test keys (always pass; needs internet because the server still calls Google). Replace them for production.
- **Queue:** `QUEUE_CONNECTION=sync` locally (jobs run inline). Production: see DEPLOYMENT.md.

## Tests
`php artisan test` uses in-memory SQLite (forced in `phpunit.xml`) and blank GHL values, so it never touches MySQL or the CRM.

## Troubleshooting
- **Port 80 in use** (Skype, IIS, World Wide Web Publishing): stop that service or change Apache `Listen` to 8080.
- **Missing extension / "could not find driver":** enable it in php.ini, restart Apache/terminal, check `php -m`.
- **Storage permissions / images 404:** run `php artisan storage:link`; make sure `storage/` and `bootstrap/cache/` are writable.
- **419 Page Expired:** session or CSRF problem: clear cookies, run `php artisan optimize:clear`, check `APP_URL` matches the URL you browse.
- **Styles missing:** run `npm run build` (or keep `npm run dev` running).
- **Forms say "confirm you are not a robot" always:** the server cannot reach google.com or the keys are wrong.
- **Config change ignored:** `php artisan optimize:clear`.
