# SEO and launch checklist

## Built in
- Fully server-rendered HTML, one H1 per page (tested), semantic landmarks, skip link.
- Unique title, meta description and canonical per page; Open Graph and Twitter tags.
- JSON-LD: TravelAgency (all pages), BreadcrumbList (packages, tour types), FAQPage (home, packages with FAQs), TouristTrip (packages; `offers` only when a price is set).
- `/sitemap.xml` (home, pages, packages index, tour types, published packages) and `/robots.txt` (blocks `/admin`, `/thank-you`, `/enquiry`). Per-page/package `noindex` toggles in admin; the Thank You page is always noindex.
- Fonts self-hosted, latin subset preloaded, `font-display: swap`. Minimal JS; reCAPTCHA loads only when a form is visible.
- Security headers (`nosniff`, frame, referrer, permissions; HSTS on HTTPS).

## Before launch
- [ ] Replace every `[CONFIRM: ...]` (see CONTENT_TO_CONFIRM.md) and every `[PLACEHOLDER ...]`.
- [ ] Add real, licensed images with alt text; hero image preload (`@push('preload')`) and `width`/`height`; fill IMAGE_CREDITS.md.
- [ ] Set brand/legal name, phone, email, address, logo, favicon, default social image in Admin > Settings.
- [ ] Real reCAPTCHA keys for the production domain; test a real submission.
- [ ] GTM ID set; conversion tag on the `enquiry_thank_you` event verified in Tag Assistant.
- [ ] GHL token/location set; `ghl:setup` and `ghl:test` pass.
- [ ] Legal pages reviewed by a qualified professional; bump `terms_version` if wording changes.
- [ ] Unique SEO title (about 50 to 60 chars) and meta description (about 150 chars) on home, tour types and each package.
- [ ] `APP_ENV=production`, `APP_DEBUG=false`, caches built, HTTPS forced, www/non-www redirect decided.
- [ ] Submit sitemap in Google Search Console; confirm `robots.txt` is the generated one.
- [ ] Mobile check at 360, 768 and 1280 px; keyboard tab through quiz and modal; test with prefers-reduced-motion.
- [ ] Run Lighthouse (mobile) on home, a package page and contact; investigate anything under 90.
- [ ] Google Ads: message match between ad copy and the H1, click-to-call number correct, thank-you URL excluded from indexing.

## Measured in this build
Lighthouse 12, mobile emulation, Chrome headless, served by Apache with gzip (production-like, `APP_DEBUG=false`, caches on), placeholder content, no photos yet:

| Page | Performance | Accessibility | Best practices | SEO |
|---|---|---|---|---|
| Home | 98 | 100 | 100 | 100 |
| Package detail | 96 | 100 | 100 | 100 |
| Contact | 89 | 96* | 79* | 100 |

\* Contact scored lower because Google reCAPTCHA loads there (third-party cookies and an Issues-panel warning are inherent to reCAPTCHA v2) and, at the time, empty placeholder phone/email links; the empty-link cause was fixed afterwards but the page was not re-measured. Re-measure after adding real images, as photos will change LCP.
Served by `php artisan serve` (no compression, debug on) the home page scored 76 performance: always test against a production-like server.
