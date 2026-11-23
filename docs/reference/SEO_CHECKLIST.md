# SEO and launch checklist

## Built in
- Fully server-rendered HTML, one H1 per page (tested), semantic landmarks, skip link.
- Unique title, meta description and canonical per page; Open Graph and Twitter tags.
- JSON-LD: TravelAgency (all pages), BreadcrumbList (packages, tour types), FAQPage (packages with FAQs; the home page has no FAQ section), TouristTrip (packages; `offers` only when a price is set).
- `/sitemap.xml` (home, pages, packages index, tour types, published packages) and `/robots.txt` (blocks `/admin`, `/thank-you`, `/enquiry`). Per-page/package `noindex` toggles in admin; the Thank You page is always noindex.
- Fonts self-hosted in `public/fonts/` (latin subset, `@font-face` at the top of `home.css`), Inter + Playfair preloaded, `font-display: swap`. Minimal JS; reCAPTCHA loads only when a form is visible.
- Performance: inner-page CSS (`inner.css`) is a separate Vite entry loaded only off the home page; below-the-fold sections use `content-visibility: auto`; hero slides 2 and 3 load after the `load` event; the LCP hero image is preloaded with `imagesrcset`; inner banners preload their image; gzip and long cache headers in `public/.htaccess`.
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
- [x] Lighthouse (mobile) on home, a package page and contact run on 2026-11-23, everything at 90+ except Best practices on contact (reCAPTCHA). Re-run after real photos and on the production domain (see below).
- [ ] Google Ads: message match between ad copy and the H1, click-to-call number correct, thank-you URL excluded from indexing.

## Measured in this build
Lighthouse 12, mobile emulation (simulated slow 4G, 4x CPU), Chrome headless, 2026-11-23, local server behind a gzip proxy (the live host cannot be audited: it answers Lighthouse with 403, and `php artisan serve` alone sends no compression and scores about 10 points lower on performance). Placeholder content, `.env` test reCAPTCHA keys.

| Page | Performance | Accessibility | Best practices | SEO |
|---|---|---|---|---|
| Home | 90 to 91 | 100 | 100 | 100 |
| Package detail | 97 | 100 | 100 | 100 |
| Contact | 93 | 99 | 79* | 100 |

\* Contact scores lower only because Google reCAPTCHA v2 sets a third-party cookie and logs an Issues-panel warning; this is inherent to reCAPTCHA v2 and cannot be fixed on our side.

Before this pass the same method gave home 67 (accessibility 94) and package 80. What fixed it:
- Self-hosted fonts with `@font-face` in `home.css` plus preloads (removes the CSS-to-font request chain).
- `inner.css` split out of the home bundle (home CSS 105 kB to 89 kB).
- `content-visibility: auto` on below-the-fold sections, `scrollY` no longer read during script start (forced reflow), carousel controls built when idle.
- Hero: slides 2 and 3 deferred, first slide visible immediately (no fade-in from transparent), `hero-wagrain` 768/1280 re-compressed (82 kB to 45 kB / 236 kB to 179 kB), responsive image preload instead of always fetching the 1280 file.
- Place cards use the `-thumb` photos (up to 285 kB down to about 70 kB).
- Banner image preload on inner pages; footer headings `h4` to `h3`, band titles `h3` to `h2` (heading order); carousel dots no longer carry an invalid `tablist` role; small labels raised to at least 12 px.

Home is close to the 90 line: its LCP is the hero text (about 3.5 s simulated, under 1 s unthrottled). Re-measure after adding real photos, and always against a gzip-enabled server. Contact heading order (one `h2` after the form) was not changed.
