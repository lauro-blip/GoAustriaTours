# SEO and launch checklist

## Built in
- Fully server-rendered HTML, one H1 per page (tested), semantic landmarks, skip link.
- Unique title, meta description and canonical per page; Open Graph and Twitter tags.
- JSON-LD: TravelAgency (all pages), BreadcrumbList (packages, tour types), FAQPage (packages with FAQs; the home page has no FAQ section), TouristTrip (packages; `offers` only when a price is set).
- `/sitemap.xml` (home, pages, packages index, tour types, published packages) and `/robots.txt` (blocks `/admin`, `/thank-you`, `/enquiry`). Per-page/package `noindex` toggles in admin; the Thank You page is always noindex.
- Fonts self-hosted in `public/fonts/` (latin subset, `@font-face` at the top of `home.css`), Inter + Playfair preloaded, `font-display: swap`. Minimal JS; reCAPTCHA loads only when a form is visible.
- Performance: inner-page CSS (`inner.css`) is a separate Vite entry loaded only off the home page; below-the-fold sections use `content-visibility: auto` (except the last section before the footer); hero slides 2 and 3 load after the `load` event; the LCP hero image is preloaded with `imagesrcset` (768 / 1280 / 1600 / 1920 px); the H1's italic font is preloaded on the home page; inner banners preload their image; gzip and long cache headers in `public/.htaccess`.
- No layout shift from the hero quiz: an inline `<head>` script adds `qjs` to `<html>`, so CSS shows only the starting quiz step (`.q-start`) from the first paint; `study-quiz.js` then adds `q-ready` and takes over. Without JS the full form still shows. Covered by a test in `CroFeaturesTest`.
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
- [x] Lighthouse (mobile) on home, a package page and contact run on 2026-11-23, everything at 90+ except Best practices on contact (reCAPTCHA). Re-run on home + package on 2026-11-25 (see "Re-measured 2026-11-25").
- [ ] After deploying the 2026-11-25 fixes, re-check the live site in PageSpeed Insights (desktop and mobile). Lighthouse run locally still gets a 403 from the live host (Hostinger CDN bot check), so PSI is the only way to audit production.
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

## Re-measured 2026-11-25
Trigger: PageSpeed Insights gave the live home page 69 on desktop.

**Cause found on the live site (desktop, 1350x940): CLS 0.97.** The hero quiz painted first as the full no-JS form (844 px tall). About 2 s later `home.js` collapsed it to one step, and the whole hero jumped up 156 px: the quiz, the H1, the "Where to?" strip and the band below. Mobile had the same collapse. Fixed with the `qjs` / `.q-start` rule above. CLS is now 0 on desktop and mobile.

Other fixes in this pass:
- Desktop hero: new 1600 px variants (`hero-*-1600.webp`, 118 to 237 kB). Screens 1350 to 1600 px wide no longer download the 1920 file (543 kB for Wagrain).
- Playfair italic (used in the H1) is preloaded on the home page. First paint in Lighthouse mobile was 1.3 to 1.4 s with it and 1.7 s without.
- Accessibility `target-size` (home scored 96): the Journal section's "View all" button overlapped the footer CTA. The section was still a 720 px `content-visibility` placeholder, shorter than its real content. The last section in `main` now always renders, and home accessibility is back to 100.

Lighthouse 12.8 (mobile defaults and `--preset=desktop`), local server behind a gzip proxy, same method as 2026-11-23:

| Page | Performance | Accessibility | Best practices | SEO |
|---|---|---|---|---|
| Home (mobile) | 77 to 90 | 100 | 100 | 100 |
| Package detail (mobile) | 92 to 95 | 100 | 100 | 100 |
| Home (desktop preset) | 59 to 85 | 100 | 100 | 100 |

The performance ranges reflect the test machine more than the page. Lighthouse's CPU benchmark swung between about 600 and 1,800 during the session, and two runs of the same unchanged home page scored 59 and 85 on desktop. The swing comes from Total Blocking Time (80 to 800 ms): style and layout work on the home page (1,775 elements), which depends on CPU speed. The one home run at normal machine speed (benchmark 1,625) scored 90 with 80 ms TBT. Home LCP is painted together with the first paint. Locally it includes about 1 s of `php artisan serve` response time; the live server responds in about 0.4 s.

Still open:
- [ ] Confirm with PageSpeed Insights on the live site after deploy. Desktop should now pass CLS, and a large jump from 69 is expected.
- [ ] If home mobile stays below 90 in PSI, two next steps: (1) lighten the hero animations that never stop: the 24 s zoom and crossfade, the marquee, and the `backdrop-filter` blur on the strip; PSI renders without a GPU. (2) Trim home DOM size: the quiz renders all of its icon-tile steps up front.
- [ ] Host settings that code cannot fix (`server-response-time`, `uses-long-cache-ttl`): Hostinger's CDN sends `max-age=604800` (7 days) and overrides the 1-year header in `.htaccess`. HTML takes about 0.4 to 0.5 s.
