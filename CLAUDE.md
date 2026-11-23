# CLAUDE.md: GoAustriaTours

Google Ads landing site for **GoAustriaTours** (Austria tours: Self Drive, Escorted, Coach; US travellers, USD).
Server-rendered Laravel 12 app. Keep this file a map, not a manual. **Update it when you change anything it describes.**

## Stack
Laravel 12 (PHP 8.2+), Blade (no SPA), Vite, MySQL (SQLite only in tests). PUBLIC site: hand-written CSS scoped under `.t3` (`resources/css/home.css` + `inner.css`) and plain JS (`resources/js/home.js`); fonts Playfair Display (headings), Inter (body), JetBrains Mono (small labels), self-hosted from `public/fonts/` via `@font-face` at the top of `home.css` (Inter + Playfair preloaded in `layouts/site.blade.php`). ADMIN only: Tailwind CSS v4 (`resources/css/app.css`), Alpine.js (+ focus plugin, `resources/js/app.js` via `admin.js`), Trix editor.
Custom admin at `/admin` (no Filament). The old public fonts (Helvetica / Apoc Revelations LT / Cormorant) are no longer used on the public site; Cormorant stays only for the admin.

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
- `resources/views/components/` button, form-field, icon (used by the ADMIN only). Public pages use `layouts/site.blade.php` + `pages/partials/*`.
- Layout tokens (home.css, `.t3`): `.wrap` = max 1390px incl. padding, 15px side padding (60px from 768px); `.sec` = 56px top/bottom (90px from 768px). Change them only there.
- Favicon: the "G" monogram ships as `public/favicon.ico`, `favicon-32.png`, `favicon-192.png`, `apple-touch-icon.png` (linked in `layouts/site.blade.php`); an Admin > Settings favicon upload overrides it
- `public/images/logo-white.png` (footer, dark bg) and `logo-navy.png` (header, light bg): same logo, two colourings; header uses the Admin > Settings `logo` if set
- Journal = `posts` table, `Post` model, `JournalController` (`/journal`, `/journal/{slug}`), admin CRUD at Admin > Journal (`Admin\PostController`), starter articles in `database/seeders/SamplePostData.php`, fallback image `public/images/journal/{slug}.webp`. Package card image = uploaded `hero_image` or `public/images/packages/{slug}.webp`. Sample package content = `database/seeders/SamplePackageData.php`. Public `/photo-credits` lists the Commons photo credits: add a row there AND in `docs/reference/IMAGE_CREDITS.md` for every new CC BY / CC BY-SA photo.
- `resources/css/home.css` + `inner.css` public design (tokens at the top of `.t3`); `resources/css/app.css` admin tokens (`@theme`); `resources/js/home.js` public behaviour; `resources/js/study-quiz.js` hero quiz; `resources/js/study-modal.js` quote popup; `resources/js/inline-forms.js` inline forms; `resources/js/carousel.js` turns card grids (`.grid .ways .dgrid .mom .jr .band-grid .stats .plc .tgrid`) into swipeable carousels with arrows + dots under 700px (styles at the end of `home.css`; the home tours pager is replaced by the carousel on mobile; dispatch `car:refresh` on a grid after filtering)
- `database/seeders/` all starter content (placeholders)
- `docs/reference/` the rest of the documentation

## Business rules that are not obvious from the code
- **Consent**: every form has a REQUIRED, unticked checkbox with the exact wording in `App\Support\Consent` (legal company name comes from the `legal_company_name` setting). Enforced in browser AND server (`consent` => `accepted`). The exact text shown, terms version, timestamp, IP, user agent and page URL are stored on each enquiry. Changing the wording requires sign-off and a bump of `terms_version` in `config/goaustria.php`.
- **reCAPTCHA v2** checkbox on every form, rendered explicitly (several per page, also in the modal), verified server-side, **fails closed** (including Google unreachable). Local `.env` uses Google's public test keys.
- **One endpoint, three form shells**: every form posts to `EnquiryController@store` (same consent, honeypot `website`, reCAPTCHA, 5/min + 20/hour per IP): the hero quiz (`hero-quiz`), the quote popup (`header-modal`, opened by any `[data-quote]` link, package cards add `data-package-id`) and the inline forms on inner pages (`pages/partials/quote-form.blade.php`: `contact`, `package-quote`, `ask-question`). Never fork the validation. Errors/old input are scoped by `form_name`.
- **Quiz** steps/options live in `quiz_steps` / `quiz_options` (admin: Quiz). Final contact step is built into the component. Without JS the plain form shows. Answers are stored as JSON (human labels) on the enquiry.
- **Tracking**: `CaptureTracking` middleware stores first touch + last touch (UTMs, gclid, gbraid, wbraid, referrer, landing page) in 30-day cookies; saved on the enquiry. GTM only renders when the `gtm_id` setting is set. dataLayer events: `enquiry_submit` (on submit) and `enquiry_thank_you` (thank-you page).
- **GoHighLevel is OFF** whenever the token or location ID is empty. Both are set in Admin > GoHighLevel (`Admin\GhlController`; token stored ENCRYPTED in `settings`, never shown back, with a read-only "Test connection"); values saved there override `GHL_API_TOKEN` / `GHL_LOCATION_ID` in `.env`, which remain the fallback (resolved in `GhlClient::resolvedToken()` / `resolvedLocationId()`). Changing `APP_KEY` makes a saved token undecryptable, so re-enter it. Field names are defined ONLY in `GhlFields`; matched by exact name. First-touch CRM fields are never overwritten; last-touch fields always update. The sync never blocks or fails an enquiry; failures are recorded on the enquiry and retryable from admin. Tests force-blank the GHL env in `phpunit.xml`.
- **Placeholders**: contact details, prices, testimonials, legal text and images are placeholders. Never invent business facts. Use `[CONFIRM: ...]` and list them in `docs/reference/CONTENT_TO_CONFIRM.md`. Public pages must never show one: `$site` settings, FAQs and prices holding `[CONFIRM` are hidden or replaced ("Price on request", plain modal heading); unconfirmed hero/Why claims (best price, review rating, pricing promise, bonding) are removed from the views until real wording exists. Page/tour-type/package text from the database is still shown, so edit it in Admin.
- Legal pages are starter templates only, not legal advice.

- **Header menus** (`layouts/site.blade.php`, desktop from 1100px; accordion-free mobile menu): Destinations (region cards with journey counts, places from `config('goaustria.nav_places')`, promo card), Austria Vacations (tour type cards with counts), Travel Style (`config('goaustria.travel_styles')` -> `/packages?style=slug`, decided by `App\Support\TravelStyles`), About Us (about, journal, testimonials, terms, privacy), Contact. Nav data (`$destinations`, `$tourTypes`, `$pkgCounts`, `$navPlaces`, helpers `$w`, `$img`) comes from the `layouts.site` view composer in `AppServiceProvider`. The old "Vacations & Experiences" menu (signature collections, By Traveler) was dropped; `/packages?collection=` and the `traveler` field still work server-side.

## Public pages: shared layout (`layouts/site.blade.php`)
- EVERY public page extends `layouts.site` (head/SEO/tracking, transparent-then-solid header with mega menus, footer, quote popup, "Consult" bar). Args: `metaTitle`, `metaDescription`, `canonical`, `ogImage`, `noindex`, `noStickyBar` (pages with their own bottom bar or a form: package, contact, thank-you). Pushes: `head`, `tracking`, `preload`, `jsonld`, `scripts`. GTM/Ads/Pixel/Clarity snippets live in `partials/tracking-head.blade.php`.
- Inner pages start with `pages/partials/banner.blade.php` (vars `$bTitle`, `$bEyebrow`, `$bLead`, `$bImage`, `$bCrumbs`, `$bClass`), then `.sec` sections. Styles: `inner.css`.
- **Verified place information** (`App\Support\Places`): UNESCO listings, regions, heights, coordinates for the places the tours visit; matched by word over a package's text so a place card only shows when the trip goes there; OpenStreetMap link per place (no embed). Photos only where we hold a photo of that place. Add a place with `fact`, `region`, `lat`/`lon`, `terms`; a test checks coordinates are inside Austria and image files exist. Only well-established public facts, never business claims.
- Package page: banner, facts bar, highlights, places, day-by-day, included/not included, FAQ (`<details>`), sticky quote card + question form, mobile enquire bar. Tour type page: banner, body (hidden while it holds `[CONFIRM`), journeys, quote form, places. Packages index: banner changes with the active filter; places panel for a destination.

## Home page (`/`)
- `PageController@home` renders `pages/home.blade.php` (extends `layouts.site`). Hero quiz `pages/partials/study-quiz.blade.php`; quote popup `pages/partials/study-modal.blade.php`; package card `pages/partials/package-card.blade.php` (card data via `Places::card($package)`).
- Section order: hero (crossfading winter photos, real quiz on the right, "Where to?" glass strip), trust strip (only when Admin > Trust & tracking has content), light band of four points, Why travel with us, tours grid (destination chips + 6-per-page pager), difference/stats, Where to go, Austria, Perfectly Planned, CTA, Your Journey. Your Way. (tour types), gallery, Guest Stories, Moments Worth Travelling For, Journal (latest 3 posts), footer. Data from the DB; stats are real counts.
- Google Ads message match: `/?tour=<active tour type slug>` re-words the hero eyebrow, tagline and page title; canonical stays `/`. No FAQ section on the home, so no FAQPage schema there.
- Photos: `public/images/winter/` (Wikimedia Commons, credited on `/photo-credits` and in `docs/reference/IMAGE_CREDITS.md`). `way-self-drive/escorted/coach.webp` are AI-generated illustrations (coach has the real logo composited on): replace with real photography.

- **Brand palette (use first on any new design/study):** Yale Blue `#0F3B59`, Cool Steel `#7D929E`, Dust Grey `#DBD4CC`, Goldenrod `#DBA12C`, Dark Coffee `#3E251E` (body text); derived: dark navy `#0A2A40`, bronze `#7A5505` for small gold text on light backgrounds. Gold buttons carry NAVY text. Mood reference: The Fairway Club (warm cream surfaces, deep colour, elegant serif lockups). Used by the home page.

## Conversion features (CRO)
- **Consult bar** (bottom of every page except package, contact, thank-you): phone number + "Request a quote" (opens the popup). Pages that need their own bottom bar or a form pass `noStickyBar`.
- **Google Ads message match:** `/?tour=self-drive|escorted|coach` (must be a real active `TourType` slug; anything else is ignored, never echoed) re-words the hero eyebrow, tagline (from `TourType->tagline`), sr-only H1 and the page title. Canonical stays on the clean URL. Use these URLs as the ad group Final URLs. The tour slug is also sent to GoHighLevel as the tag `interest: <slug>`.
- **GoHighLevel follow-up:** every contact gets the tag `nurture: new-lead` (workflow trigger). Workflow itself lives in GoHighLevel: `docs/reference/GHL_FOLLOW_UP.md`.
- **Response promise** (Admin > Trust & tracking > `response_promise`): shown under the quiz and on the thank-you page only when set; never invent one.
- **Optional tracking IDs** (Admin > Trust & tracking): Microsoft Clarity, Google Ads tag (+ conversion label, fired on the thank-you page) and Meta Pixel load only when filled in. Leave Google Ads/Meta blank if GTM handles them.
- **Images:** hero slides ship as `{name}.webp` (1920), `{name}-1280.webp`, `{name}-768.webp` with `srcset` and an LCP preload; the logos are WebP (`logo-white.webp`, `logo-navy.webp`). Add the 768/1280 variants for any new hero image.

## Conventions
- Admin > Settings (`SettingController`, groups in `GROUPS`) and Admin > Trust & tracking (`TrustController extends SettingController`, groups in `TRUST_GROUPS`) share one save/validate routine; add a field to the right `GROUPS` array. Trust & tracking owns phone, phone_tel, email, the planner fields, `established_line`, `trust_mark_N_*`, `review_*` and `gtm_id`.
- Settings are read with `Setting::get('key')` (cached; flushed on write). Public views receive `$site` (settings; `[CONFIRM` values blanked) from a view composer; `layouts.site` also gets the nav data.
- New simple admin list: extend `Admin\CrudController` (model, routeName, title, columns, fields, rules) and add a `$crud(...)` line in `routes/admin.php`.
- Public brand colours/fonts live ONLY in the variables at the top of `.t3` in `resources/css/home.css`; the admin has its own tokens in `app.css` (`@theme`).
- Never use `@if` directly after a word with no space in Blade (it is not parsed). Never nest `<noscript>`.
- Uploaded images go through `ImageService` (validated, WebP, 480/960/1600 widths). Alt text is required.

## Gotchas
- Public fonts are plain `@font-face` rules pointing at `/fonts/*.woff2` (copied from Fontsource; on the server `public_html/fonts` must be a symlink to `laravel_app/public/fonts` like `build` and `images`). Admin Cormorant is still imported from Fontsource in `app.js`, NOT in CSS: Tailwind v4 inlines CSS `@import`s and breaks the font `url()` paths on Windows.
- Performance rules (Lighthouse mobile 90+): `inner.css` is its own Vite entry, loaded by `layouts/site.blade.php` on every page except home; do not import it from `home.js`. Carousel CSS lives in `home.css`. Below-the-fold sections use `content-visibility: auto`. Hero slides 2 and 3 use `data-src` and load after `window load`.
- Headless Chrome cannot render windows narrower than 500px: check mobile layouts at 500px.
- `public/robots.txt` must not exist; robots is generated by `SeoController`.
- After changing views/config on a server, run `php artisan optimize:clear`.
- Windows PowerShell 5.1 `-Encoding utf8` writes a BOM which breaks PHP files; write files with the editor tools instead.
