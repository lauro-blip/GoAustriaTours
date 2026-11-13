# Content to confirm

Nothing below was invented as fact; each is a placeholder in the site. Search the code and admin for `[CONFIRM` and `PLACEHOLDER`.

## Business facts
- Phone number (and tap-to-call number), public email, postal address: Admin > Settings
- Staff notification emails: Admin > Settings (no emails are sent until set)
- Header modal discount ("save up to X%"): Admin > Settings > modal discount
- Years in business, awards, review counts/ratings, licences/bonding/protection details: none shown; add only when confirmed
- Trust badges on the home hero: "Best Price Guarantee" wording, "Guest Reviews" source
- "Why choose us" cards: pricing promise, support/protection claim
- Response time promised in the FAQ ("usually within [CONFIRM]"), customisation options, what is included in price (FAQ)

## Packages (all 6 are SAMPLE DATA)
- Names, durations, summaries, descriptions, highlights, itineraries, inclusions/exclusions, FAQs
- "From" prices (blank until entered; pages show `[CONFIRM: price]`)
- Tour type page copy for Self Drive, Escorted, Coach

## Pages
- About us copy
- Terms and Conditions and Privacy Policy: **starter templates only, not legal advice**. Needs professional review (booking, payment, cancellation, liability, data processors, user rights, SMS terms)

## Reviews
- Testimonials: the 3 reviews (Michael R., Sarah & James, The Nguyen Family) were supplied by the owner: confirm they are genuine and permitted, and that they are Google reviews if the Google "G" icon stays on the cards.

## Brand and assets
- Logo, favicon, final fonts (colours now follow the supplied navy/gold palette)
- All photography (see IMAGE_CREDITS.md)

## Accounts and keys
- Google Tag Manager ID and Google Ads conversion tag
- Production reCAPTCHA v2 keys
- GoHighLevel PIT and location ID (then `php artisan ghl:setup`)
- SMTP mail settings for production
- Hero photo credits (CC BY / BY-SA) must be shown publicly before launch.
- Journey intro section photo (`public/images/hero/why-alps.webp`) is a low-res crop from a design mock-up: replace with the original/licensed image.
- Sample package data in `database/seeders/SamplePackageData.php`: all 6 "from" prices (USD), nights, summaries, highlights, itineraries, inclusions/exclusions are ILLUSTRATIVE. Replace with real packages in Admin > Packages before launch. The home section subtitle still says "Sample packages shown".
- Seamless Payments section (home, after gallery): payment wording supplied by the owner. Confirm per-traveller online payment, secure payments, instant confirmation emails and flexible options are real before launch; the site itself does not process payments.
- Journal: the 4 starter articles (database/seeders/SamplePostData.php) are general-guidance DRAFTS written as placeholders: review/replace in Admin > Journal. Check the seasonal claims (e.g. Grossglockner opening times) before launch. Their images reuse the gallery photos.
- Package card: shown price now has a 'pp' (per person) suffix, copied from the design reference. Confirm that is how prices are quoted.
