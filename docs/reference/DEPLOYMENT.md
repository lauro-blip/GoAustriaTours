# Deployment: shared cPanel hosting

Assumes PHP 8.2+, MySQL, SSH or the cPanel Terminal, and Composer available. If the host has no Node, build assets locally.

1. **Build locally:** `npm ci && npm run build` (uploads `public/build`). `vendor/` can be built on the host with `composer install --no-dev --optimize-autoloader`, or uploaded.
2. **Upload** the project to a folder *outside* `public_html` (e.g. `~/goaustriatours`). Point the domain's document root at `~/goaustriatours/public` (cPanel > Domains), or put the contents of `public/` in `public_html` and edit `index.php` paths. Do not upload `.env`, `node_modules`, `.git`.
3. **Production `.env`:** copy `.env.example`, then set `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://goaustriatours.com`, database credentials, a strong `ADMIN_SEED_EMAIL`/`ADMIN_SEED_PASSWORD`, real `RECAPTCHA_*` keys (create a v2 checkbox key for the domain), real SMTP mail settings and `QUEUE_CONNECTION=database`. Add `GHL_API_TOKEN` and `GHL_LOCATION_ID` only when ready.
4. `php artisan key:generate --force` (first deploy only), then `php artisan migrate --force --seed` (seed only on first deploy), `php artisan storage:link`.
5. **Cache:** `php artisan config:cache && php artisan route:cache && php artisan view:cache` (re-run after every deploy; `php artisan optimize:clear` if something looks stale).
6. **Queue worker (cPanel):** cron every minute: `* * * * * cd ~/goaustriatours && php artisan queue:work --stop-when-empty --max-time=55 >> /dev/null 2>&1`. (With sync you would delay page loads; keep `database`.)
7. **Scheduler:** cron every minute: `* * * * * cd ~/goaustriatours && php artisan schedule:run >> /dev/null 2>&1`. (No scheduled tasks yet; the line is future-proofing.)
8. **Sitemap:** generated live at `/sitemap.xml`; submit it in Google Search Console. `robots.txt` is generated too.
9. **SSL:** enable AutoSSL/Let's Encrypt; the app sends HSTS only on HTTPS.
10. **Writable:** `storage/` and `bootstrap/cache/` must be writable by PHP.
11. Run `php artisan ghl:setup` then `php artisan ghl:test` once the GHL values are set.
12. Walk through `docs/reference/SEO_CHECKLIST.md` before launch.

The bundled `public/.htaccess` enables gzip and long cache headers for `/build` assets (needs mod_deflate/mod_expires/mod_headers, all guarded).
