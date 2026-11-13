# CLAUDE.md: GoAustriaTours

Google Ads landing site for **GoAustriaTours** (Austria tours: Self Drive, Escorted, Coach; US travellers, USD).
Server-rendered Laravel 12 app. Keep this file a map, not a manual. **Update it when you change anything it describes.**

## Stack
Laravel 12 (PHP 8.2+), Blade (no SPA), Tailwind CSS v4 + Vite, Alpine.js (+ focus plugin), MySQL (SQLite only in tests), Trix editor in admin.
Custom admin at `/admin` (no Filament). Fonts self-hosted via Fontsource (DM Sans body, Cormorant Garamond bold headings).

## Run on XAMPP (Windows)
```
composer install && npm install
php artisan key:generate && php artisan migrate --seed && php artisan storage:link
npm run build        # or: npm run dev
php artisan serve    # http://127.0.0.1:8000   (fallback: php -S 127.0.0.1:8000 -t public server.php)
```
Full detail: `docs/reference/LOCAL_XAMPP.md`. Tests: `php artisan test` (in-memory SQLite, never touches MySQL).

## Folder map
- `routes/web.php` public routes; `routes/admin.php` all `/admin` routes
- `app/Http/Controllers/` public controllers; `Admin/` admin controllers (`CrudController` = config-driven CRUD base)
- `app/Http/Controllers/EnquiryController.php` the ONE endpoint every form posts to
- `app/Services/` `RecaptchaVerifier`, `ImageService` (WebP), `GhlClient`, `GhlPayload`
- `app/Jobs/SyncEnquiryToGhl.php` queued CRM sync; `app/Console/Commands/Ghl*.php` `ghl:setup`, `ghl:test`
- `app/Support/` `Consent` (legal wording), `Tracking` (first/last touch), `GhlFields` (CRM field names), `Schema` (JSON-LD), `Countries`
- `resources/views/components/` button, section-heading, card, form-field, faq-item, icon, enquiry-form, enquiry-modal, hero-quiz, package-card
- `public/images/logo-white.png` (footer, dark bg) and `logo-navy.png` (header, light bg): same logo, two colourings; header uses the Admin > Settings `logo` if set
- Home order: hero, journey intro, Ways to travel (tour type cards; image = uploaded one or `public/images/tours/{slug}.webp`), Why, ... The journey intro (image + 3 numbered points + quote button) (image + 3 numbered points + quote button). Hero background = CSS crossfade of `public/images/hero/*.webp` (Wikimedia Commons, attribution required: see `docs/reference/IMAGE_CREDITS.md`). Home (`transparentHeader => true`): topbar+header are `fixed`, header transparent with white logo/text until scrolled 40px, then solid ivory + green logo and the topbar folds away (Alpine `scrolled` in `layouts/app.blade.php`); other pages keep the solid ivory header. Logo is 250px wide.
- Home sections after the gallery: Seamless Payments, "Discover Austria" package carousel (CSS scroll-snap + Alpine arrows, all published packages), Journal (latest 3 posts). Journal = `posts` table, `Post` model, `JournalController` (`/journal`, `/journal/{slug}`), admin CRUD at Admin > Journal (`Admin\PostController`), starter articles in `database/seeders/SamplePostData.php`, fallback image `public/images/journal/{slug}.webp`. Home "Seamless Payments" section follows the gallery. Home "Gallery" (after testimonials): 6 destination photos in `public/images/gallery/`, Alpine.js lightbox inline in `pages/home.blade.php` (focus trap, Esc, arrows, swipe). Home "Top destinations" (6 trip cards, static array in `pages/home.blade.php`, reuses images from `public/images/{hero,packages}`) sits after Featured packages. Featured packages (home, 3 per page): `partials/featured-packages.blade.php` is the grid + prev/next/numbered pagination, rendered inline and returned alone by `GET /packages/featured` (`PageController@featured`); `resources/js/featured.js` swaps it via ajax, plain links `/?featured_page=N` work without JS. Card image = uploaded `hero_image` or `public/images/packages/{slug}.webp`. Sample package content (prices etc.) = `database/seeders/SamplePackageData.php`; refresh a local DB with `(new ContentSeeder)->syncSamplePackages(true)` in tinker.
- `resources/css/app.css` design tokens (`@theme`); `resources/js/forms.js` quiz + form Alpine logic
- `database/seeders/` all starter content (placeholders)
- `docs/reference/` the rest of the documentation

## Business rules that are not obvious from the code
- **Consent**: every form has a REQUIRED, unticked checkbox with the exact wording in `App\Support\Consent` (legal company name comes from the `legal_company_name` setting). Enforced in browser AND server (`consent` => `accepted`). The exact text shown, terms version, timestamp, IP, user agent and page URL are stored on each enquiry. Changing the wording requires sign-off and a bump of `terms_version` in `config/goaustria.php`.
- **reCAPTCHA v2** checkbox on every form, rendered explicitly (several per page, also in the modal), verified server-side, **fails closed** (including Google unreachable). Local `.env` uses Google's public test keys.
- **One form component**: all forms render `<x-enquiry-form>` and post to `EnquiryController@store`. Never fork it. Errors/old input are scoped by `form_name`. Honeypot field `website`; rate limit 5/min + 20/hour per IP.
- **Quiz** steps/options live in `quiz_steps` / `quiz_options` (admin: Quiz). Final contact step is built into the component. Without JS the plain form shows. Answers are stored as JSON (human labels) on the enquiry.
- **Tracking**: `CaptureTracking` middleware stores first touch + last touch (UTMs, gclid, gbraid, wbraid, referrer, landing page) in 30-day cookies; saved on the enquiry. GTM only renders when the `gtm_id` setting is set. dataLayer events: `enquiry_submit` (on submit) and `enquiry_thank_you` (thank-you page).
- **GoHighLevel is OFF** whenever `GHL_API_TOKEN` or `GHL_LOCATION_ID` is empty. Secrets live only in `.env`. Field names are defined ONLY in `GhlFields`; matched by exact name. First-touch CRM fields are never overwritten; last-touch fields always update. The sync never blocks or fails an enquiry; failures are recorded on the enquiry and retryable from admin. Tests force-blank the GHL env in `phpunit.xml`.
- **Placeholders**: contact details, prices, testimonials, legal text and images are placeholders. Never invent business facts. Use `[CONFIRM: ...]` and list them in `docs/reference/CONTENT_TO_CONFIRM.md`.
- Legal pages are starter templates only, not legal advice.

## Conventions
- Settings are read with `Setting::get('key')` (cached; flushed on write). Views receive `$site` and `$navTourTypes` via a view composer.
- New simple admin list: extend `Admin\CrudController` (model, routeName, title, columns, fields, rules) and add a `$crud(...)` line in `routes/admin.php`.
- Brand colours/fonts live ONLY in the `@theme` block of `resources/css/app.css`.
- Never use `@if` directly after a word with no space in Blade (it is not parsed). Never nest `<noscript>`.
- Uploaded images go through `ImageService` (validated, WebP, 480/960/1600 widths). Alt text is required.

## Gotchas
- Alpine only works inside an `x-data` root: `<body x-data>` in `layouts/app.blade.php` provides it for the top bar and modal triggers.
- `public/robots.txt` must not exist; robots is generated by `SeoController`.
- After changing views/config on a server, run `php artisan optimize:clear`.
- Windows PowerShell 5.1 `-Encoding utf8` writes a BOM which breaks PHP files; write files with the editor tools instead.
