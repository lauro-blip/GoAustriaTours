# CLAUDE.md: GoAustriaTours

Google Ads landing site for **GoAustriaTours** (Austria tours: Self Drive, Escorted, Coach; US travellers, USD).
Server-rendered Laravel 12 app. Keep this file a map, not a manual. **Update it when you change anything it describes.**

## Stack
Laravel 12 (PHP 8.2+), Blade (no SPA), Tailwind CSS v4 + Vite, Alpine.js (+ focus plugin), MySQL (SQLite only in tests), Trix editor in admin.
Custom admin at `/admin` (no Filament). Fonts: body = Helvetica (system stack: Helvetica, Helvetica Neue, Arial; nothing to load), headings = Apoc Revelations LT, a licensed font whose files are NOT in the repo yet (until they are added as `@font-face` in `app.css`, headings fall back to self-hosted Fontsource Cormorant Garamond medium (500); the small-text steps `--text-xs`/`--text-sm` are set one size up in `@theme` on purpose, for 35+ readers).

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
- Layout tokens (app.css): `.container-page` = max 1390px incl. padding, 15px side padding (60px from `md`); `.section` = 45px top/bottom (90px from `md`). Change them only there.
- Favicon: the "G" monogram ships as `public/favicon.ico`, `favicon-32.png`, `favicon-192.png`, `apple-touch-icon.png` (linked in `layouts/app.blade.php`); an Admin > Settings favicon upload overrides it
- `public/images/logo-white.png` (footer, dark bg) and `logo-navy.png` (header, light bg): same logo, two colourings; header uses the Admin > Settings `logo` if set
- Home Journal section (latest 3 posts). Journal = `posts` table, `Post` model, `JournalController` (`/journal`, `/journal/{slug}`), admin CRUD at Admin > Journal (`Admin\PostController`), starter articles in `database/seeders/SamplePostData.php`, fallback image `public/images/journal/{slug}.webp`. Home "Gallery" (after guest stories): 6 destination photos in `public/images/gallery/`, Alpine.js lightbox inline in `pages/home.blade.php` (focus trap, Esc, arrows, swipe). Home "Top destinations" (bento grid: first trip is a large 2x2 feature card, 5 smaller photo-overlay cards, static array in `pages/home.blade.php`, reuses images from `public/images/{hero,packages}`) sits after Featured packages. Curated packages (home, ALL published packages, 6 per page = 3x2 grid): `partials/curated-packages.blade.php` is the grid + prev/next/numbered pagination, rendered inline and returned alone by `GET /packages/curated` (`PageController@curated`); `resources/js/curated.js` swaps it via ajax, plain links `/?curated_page=N` work without JS. Card image = uploaded `hero_image` or `public/images/packages/{slug}.webp`. Sample package content (15 packages: the first 6 are featured, the other 9 only appear in the Curated grid; prices etc. illustrative) = `database/seeders/SamplePackageData.php`; refresh a local DB with `(new ContentSeeder)->syncSamplePackages(true)` in tinker.
- `resources/css/app.css` design tokens (`@theme`); `resources/js/forms.js` quiz + form Alpine logic
- `database/seeders/` all starter content (placeholders)
- `docs/reference/` the rest of the documentation

## Business rules that are not obvious from the code
- **Consent**: every form has a REQUIRED, unticked checkbox with the exact wording in `App\Support\Consent` (legal company name comes from the `legal_company_name` setting). Enforced in browser AND server (`consent` => `accepted`). The exact text shown, terms version, timestamp, IP, user agent and page URL are stored on each enquiry. Changing the wording requires sign-off and a bump of `terms_version` in `config/goaustria.php`.
- **reCAPTCHA v2** checkbox on every form, rendered explicitly (several per page, also in the modal), verified server-side, **fails closed** (including Google unreachable). Local `.env` uses Google's public test keys.
- **One form component**: all forms render `<x-enquiry-form>` and post to `EnquiryController@store`. Never fork it. Errors/old input are scoped by `form_name`. Honeypot field `website`; rate limit 5/min + 20/hour per IP.
- **Quiz** steps/options live in `quiz_steps` / `quiz_options` (admin: Quiz). Final contact step is built into the component. Without JS the plain form shows. Answers are stored as JSON (human labels) on the enquiry.
- **Tracking**: `CaptureTracking` middleware stores first touch + last touch (UTMs, gclid, gbraid, wbraid, referrer, landing page) in 30-day cookies; saved on the enquiry. GTM only renders when the `gtm_id` setting is set. dataLayer events: `enquiry_submit` (on submit) and `enquiry_thank_you` (thank-you page).
- **GoHighLevel is OFF** whenever the token or location ID is empty. Both are set in Admin > GoHighLevel (`Admin\GhlController`; token stored ENCRYPTED in `settings`, never shown back, with a read-only "Test connection"); values saved there override `GHL_API_TOKEN` / `GHL_LOCATION_ID` in `.env`, which remain the fallback (resolved in `GhlClient::resolvedToken()` / `resolvedLocationId()`). Changing `APP_KEY` makes a saved token undecryptable, so re-enter it. Field names are defined ONLY in `GhlFields`; matched by exact name. First-touch CRM fields are never overwritten; last-touch fields always update. The sync never blocks or fails an enquiry; failures are recorded on the enquiry and retryable from admin. Tests force-blank the GHL env in `phpunit.xml`.
- **Placeholders**: contact details, prices, testimonials, legal text and images are placeholders. Never invent business facts. Use `[CONFIRM: ...]` and list them in `docs/reference/CONTENT_TO_CONFIRM.md`. Public pages must never show one: `$site` settings, FAQs and prices holding `[CONFIRM` are hidden or replaced ("Price on request", plain modal heading); unconfirmed hero/Why claims (best price, review rating, pricing promise, bonding) are removed from the views until real wording exists. Page/tour-type/package text from the database is still shown, so edit it in Admin.
- Legal pages are starter templates only, not legal advice.

- **Main menu** (desktop, from `xl`): Tour Types, Destinations, Travel Style, Vacations & Experiences, About, Contact. Packages and Journal are deliberately NOT top-level items (reach packages through the mega menus' "View all" links and the home page; Journal sits under About). **About mega menu**: story + Journal (latest article), Testimonials (first guest story; page `/testimonials`, `PageController@testimonials`), Terms & Conditions, Privacy Policy. Data for it comes from the view composer (`$navPost`, `$navStory`).
- **Vacations & Experiences mega menu** (header partial): three columns. *Signature Collections* = `config('goaustria.collections')` (hand-picked package slugs; `/packages?collection=slug`; a test fails if a listed slug does not exist). *By Traveler* = `config('goaustria.traveler_types')`: these are NOT filters (packages carry no traveler data); they open the enquiry modal with `{traveler, traveler_label}` and the chosen type is saved at the top of the enquiry's `special_request` as "Travelling as: ..." (validated against the config list). *Featured Packages* = the first 4 `is_featured` packages. The desktop nav and mega menus show from `xl` (1280px); below that the hamburger menu is used (it has accordions for each menu).
- **Tour Types mega menu** (same header partial): one card per active `TourType` (photo, tagline, summary, its first 3 published packages, "All ... packages" -> `/packages?tour_type=slug`). Data comes from the view composer in `AppServiceProvider` (`$navTourTypes` with `menuPackages`). Both mega menus share ONE Alpine state `mega` (`''|'tours'|'destinations'`) so only one is open; the trigger is omitted when there is nothing to list.
- **Travel Style mega menu + filter**: 6 styles in `config('goaustria.travel_styles')` (name, icon, text, search `terms`); the menu is a split panel (photo + heading left, 3x2 icon cells right, tagline bar under them). `App\Support\TravelStyles` decides which packages belong to a style (case-insensitive word match over title/summary/highlights/itinerary), used by the menu (journey counts, first 2 packages) and by `/packages?style=slug` (also a select in the filter form). Keep `terms` to words that really occur in package content, and keep each style distinctive (a test fails if a menu link would be empty). Same shared `mega` Alpine state as the other menus.
- **Destinations mega menu** (`partials/header.blade.php`, desktop; accordion in the mobile menu): lists every active `Destination` (name, summary, "All ... trips" -> `/packages?destination=slug`) with its places from `config('goaustria.nav_places')` (each links to `/packages?q=<term>`, a plain text search over title/summary/highlights/itinerary in `PackageController@index`). Only list places that really occur in package itineraries; a test checks every menu link returns results. Opens on hover/click, closes on mouse-leave, Esc or outside click.

## Home page (`/`): the former design study 3
- `PageController@home` renders `pages/home.blade.php`: a STANDALONE page (own head, header with mega menus, footer; NOT `layouts.app`; the shared GTM/Ads/Pixel/Clarity snippets live in `partials/tracking-head.blade.php`, also used by the layout). Styles `resources/css/home.css` (everything scoped under `.t3`), behaviour `resources/js/home.js` (Vite entry; imports Playfair Display, Inter, JetBrains Mono from Fontsource), hero quiz `pages/partials/study-quiz.blade.php` + `resources/js/study-quiz.js`, quote popup `pages/partials/study-modal.blade.php` + `resources/js/study-modal.js`, package card `pages/partials/package-card.blade.php`.
- Section order: hero (crossfading winter photos, real quiz on the right, "Where to?" marquee), trust strip (only when Admin > Trust & tracking has content), dark band of four points, Why travel with us, tours grid (destination chips + 6-per-page pager), difference/stats, Where to go (3 destination boxes), Austria, Perfectly Planned (packages), Your Journey. Your Way. (tour types), gallery, Guest Stories, Moments Worth Travelling For, Journal (latest 3 posts), footer. Data: packages, destinations, tour types, testimonials, posts, quiz steps from the DB; stats are real counts.
- Every quote button carries `data-quote` and opens ONE native `<dialog>` popup (form `header-modal`; package cards add `data-package-id`); the href stays as the no-JS fallback. The hero quiz posts as `hero-quiz`. Both go to `EnquiryController@store`.
- Google Ads message match still works: `/?tour=<active tour type slug>` re-words the hero eyebrow, tagline, and the page title; canonical stays `/`. No FAQ section on the home any more, so no FAQPage schema is emitted there.
- Photos: `public/images/winter/` (Wikimedia Commons, all credited on `/photo-credits` and in `docs/reference/IMAGE_CREDITS.md`; add a row in BOTH for every new CC BY / CC BY-SA photo). `way-self-drive/escorted/coach.webp` are AI-generated illustrations (coach has the real logo composited on): replace with real photography.
- The old home view was removed (see git history). The `/packages/curated` ajax endpoint and `resources/js/curated.js` are no longer used by any page.

- **Brand palette (use first on any new design/study):** Yale Blue `#0F3B59`, Cool Steel `#7D929E`, Dust Grey `#DBD4CC`, Goldenrod `#DBA12C`, Dark Coffee `#3E251E` (body text); derived: dark navy `#0A2A40`, bronze `#7A5505` for small gold text on light backgrounds. Gold buttons carry NAVY text. Mood reference: The Fairway Club (warm cream surfaces, deep colour, elegant serif lockups). Used by the home page.

## Conversion features (CRO)
- **Sticky mobile bar** (`partials/sticky-cta.blade.php`, included by the layout): Call + "Plan My Journey", phones/tablets only, appears after scrolling past ~70% of the viewport. Pages with their own bottom bar or a form-only page pass `'noStickyBar' => true` in `@extends` (package page, contact, thank-you).
- **Google Ads message match:** `/?tour=self-drive|escorted|coach` (must be a real active `TourType` slug; anything else is ignored, never echoed) re-words the hero eyebrow, tagline (from `TourType->tagline`), sr-only H1 and the page title. Canonical stays on the clean URL. Use these URLs as the ad group Final URLs. The tour slug is also sent to GoHighLevel as the tag `interest: <slug>`.
- **GoHighLevel follow-up:** every contact gets the tag `nurture: new-lead` (workflow trigger). Workflow itself lives in GoHighLevel: `docs/reference/GHL_FOLLOW_UP.md`.
- **Response promise** (Admin > Trust & tracking > `response_promise`): shown under the quiz and on the thank-you page only when set; never invent one.
- **Optional tracking IDs** (Admin > Trust & tracking): Microsoft Clarity, Google Ads tag (+ conversion label, fired on the thank-you page) and Meta Pixel load only when filled in. Leave Google Ads/Meta blank if GTM handles them.
- **Images:** hero slides ship as `{name}.webp` (1920), `{name}-1280.webp`, `{name}-768.webp` with `srcset` and an LCP preload; the logos are WebP (`logo-white.webp`, `logo-navy.webp`). Add the 768/1280 variants for any new hero image.

## Conventions
- Admin > Settings (`SettingController`, groups in `GROUPS`) and Admin > Trust & tracking (`TrustController extends SettingController`, groups in `TRUST_GROUPS`) share one save/validate routine; add a field to the right `GROUPS` array. Trust & tracking owns phone, phone_tel, email, the planner fields, `established_line`, `trust_mark_N_*`, `review_*` and `gtm_id`.
- Settings are read with `Setting::get('key')` (cached; flushed on write). Views receive `$site` and `$navTourTypes` via a view composer.
- New simple admin list: extend `Admin\CrudController` (model, routeName, title, columns, fields, rules) and add a `$crud(...)` line in `routes/admin.php`.
- Brand colours/fonts live ONLY in the `@theme` block of `resources/css/app.css`.
- Never use `@if` directly after a word with no space in Blade (it is not parsed). Never nest `<noscript>`.
- Uploaded images go through `ImageService` (validated, WebP, 480/960/1600 widths). Alt text is required.

## Gotchas
- Fonts (Fontsource) are imported in `resources/js/app.js`, NOT in `app.css`: Tailwind v4 inlines CSS `@import`s and breaks the font `url()` paths on Windows, so the fonts silently fall back to Georgia. `vite.config.js` also lists the two preloaded woff2 files so `Vite::asset()` resolves.
- Alpine only works inside an `x-data` root: `<body x-data>` in `layouts/app.blade.php` provides it for the top bar and modal triggers.
- `public/robots.txt` must not exist; robots is generated by `SeoController`.
- After changing views/config on a server, run `php artisan optimize:clear`.
- Windows PowerShell 5.1 `-Encoding utf8` writes a BOM which breaks PHP files; write files with the editor tools instead.
