# Launch fill-in sheet

Everything still needed before real traffic. Fill the **Your answer** column (or send the answers to the developer), then tick the box once it is live. Items marked **Admin** you can enter yourself; items marked **Code** need a developer edit (or ask to make them editable in Admin).

Companion docs: `CONTENT_TO_CONFIRM.md` (full list), `IMAGE_CREDITS.md`, `FORMS_AND_TRACKING.md`.

---

## 1. Blockers: no leads or no measurement without these

| Done | Item | Where it shows / why | Where to enter | Your answer |
|---|---|---|---|---|
| [ ] | reCAPTCHA v2 **site key** and **secret key** (production domain registered with Google) | Forms fail closed with empty keys: **no enquiry can be submitted** | Admin > Settings | |
| [ ] | Google Tag Manager ID (`GTM-XXXXXXX`) | No tracking at all until set | Admin > Settings | |
| [ ] | Google Ads conversion tag in GTM, fired on event `enquiry_thank_you` | Conversion measurement | GTM | |
| [ ] | GoHighLevel PIT token and Location ID, then run `php artisan ghl:setup` and `ghl:test` | Leads reach the CRM (off while empty) | Server `.env` only | |
| [ ] | Staff notification email(s), comma separated | No staff emails are sent until set | Admin > Settings | |
| [ ] | Production mail (SMTP) settings | Confirmation and notification emails | Server `.env` | |
| [ ] | Production `.env`: `APP_ENV=production`, `APP_DEBUG=false`, real `APP_URL`, MySQL credentials, queue worker running | Site runs and background jobs (CRM sync) execute | Server | |

## 2. Contact details (visible on every page)

| Done | Item | Where it shows | Where to enter | Your answer |
|---|---|---|---|---|
| [ ] | Phone number (display format) | Top bar, footer, thank-you page | Admin > Settings > phone | |
| [ ] | Phone number for tap-to-call (digits with country code, e.g. +1...) | The tap-to-call link is **empty and does nothing** until set | Admin > Settings > phone_tel | |
| [ ] | Public email | Top bar, footer | Admin > Settings > email | |
| [ ] | Postal / business address | Footer | Admin > Settings > address | |
| [ ] | Legal company name | Consent wording on every form, legal pages, copyright line | Admin > Settings > legal_company_name | |
| [ ] | Facebook / Instagram / YouTube URLs (optional) | Social links | Admin > Settings | |
| [ ] | Logo upload (optional: the built-in logo is used if blank) and favicon | Header, browser tab | Admin > Settings | |
| [ ] | Default social sharing image | Link previews (WhatsApp, Facebook) | Admin > Settings | |

## 3. Claims and proof (only enter what is true and provable)

| Done | Item | Where it shows | Where to enter | Your answer |
|---|---|---|---|---|
| [ ] | "Best Price Guarantee" wording, or tell us to remove it | Home hero, first trust box (`[CONFIRM: wording]`) | **Code** (`pages/home.blade.php`) | |
| [ ] | Review source and rating, or remove the box (`[CONFIRM: rating source]`) | Home hero, third trust box | **Code** | |
| [ ] | Pricing promise (`[CONFIRM: pricing promise]`) | Home > "Why travellers choose us" > Transparent pricing | **Code** | |
| [ ] | Financial protection / bonding details, or remove (`[CONFIRM: protection or bonding details]`) | Home > "Why travellers choose us" > Peace of mind | **Code** | |
| [ ] | Confirm these are TRUE: "No pressure, no obligation", "A real person to talk to", "Support from enquiry to return home" | Home > "Why travellers choose us" | **Code** | |
| [ ] | Confirm the Seamless Payments claims are how you really work: each traveller pays their share online, secure transactions, instant confirmation email, flexible payment options | Home > Seamless Payments | **Code** | |
| [ ] | Years in business, number of travellers planned, awards, memberships, review count (only if real) | Not shown yet: recommended near the hero | Tell the developer | |
| [ ] | Confirm the 3 testimonials are genuine and permitted; confirm they are Google reviews (the Google "G" shows on each card), or ask to remove the "G" | Home > Guest stories | Admin > Testimonials | |
| [ ] | A name and photo of a real planner or founder ("Meet your planner"), optional but strongly recommended | Not built yet | Tell the developer | |

## 4. Offer and policies

| Done | Item | Where it shows | Where to enter | Your answer |
|---|---|---|---|---|
| [ ] | Promised **response time** for a quote (e.g. "within 1 business day") | FAQ "How does it work?" (`[CONFIRM: response time]`), thank-you page says only "soon" | Admin > FAQs; thank-you text is **Code** | |
| [ ] | FAQ answer: how a tour can be customised (`[CONFIRM: customisation options.]`) | Home FAQ | Admin > FAQs | |
| [ ] | FAQ answer: what the price includes (`[CONFIRM: standard inclusions.]`) | Home FAQ | Admin > FAQs | |
| [ ] | Header popup discount percentage (`[CONFIRM: discount %]`), or tell us to remove the discount line | "Start Your Adventure" popup | Admin > Settings > modal_discount | |
| [ ] | Decide: keep or remove the form question "Would you like a second quote from a partner?" | Every enquiry form (can reduce trust) | **Code** | |
| [ ] | Terms and Conditions: booking, payment, cancellation, refund and liability terms, **reviewed by a professional** (page currently says "STARTER TEMPLATE ONLY") | /terms-and-conditions | Admin > Pages | |
| [ ] | Privacy Policy: data processors and partners, user rights and contact details, **reviewed by a professional** | /privacy-policy | Admin > Pages | |
| [ ] | State seller-of-travel registration number(s), if your business needs them | Not shown yet | Tell the developer | |

## 5. Packages and tour pages (all 6 packages are SAMPLE data)

| Done | Item | Where it shows | Where to enter | Your answer |
|---|---|---|---|---|
| [ ] | Real package list: names, nights, destinations | Featured packages, carousel, Packages page | Admin > Packages | |
| [ ] | **Real "from" prices (USD).** Confirm whether the "pp" (per person) suffix is right | Package cards and pages. Sample prices ($1,790 to $4,290) are illustrative only | Admin > Packages | |
| [ ] | Descriptions, highlights, itineraries, inclusions, exclusions for each package | Package pages (descriptions say `[CONFIRM: full description.]`) | Admin > Packages | |
| [ ] | Package photos (upload your own or keep the stock photos) | Package cards and pages | Admin > Packages | |
| [ ] | Tour type page copy: Self Drive, Escorted, Coach | /tours/self-drive, /escorted, /coach | Admin > Tour types | |
| [ ] | Remove or change the subtitle "Sample packages shown. Prices and itineraries to be confirmed." once packages are real | Home > Featured packages | **Code** | |
| [ ] | "Top destinations" trip names and place lists: confirm they are real trips you sell, and where each "Explore Now" should link | Home > Top destinations | **Code** | |

## 6. Pages and content

| Done | Item | Where it shows | Where to enter | Your answer |
|---|---|---|---|---|
| [ ] | About us: who you are, where you are based, years in business | /about (`[CONFIRM: about us copy...]`) | Admin > Pages | |
| [ ] | Journal: review the 4 starter articles (general advice, written as drafts). Check seasonal facts such as Grossglockner road opening dates | /journal and home | Admin > Journal | |
| [ ] | Replace the low-resolution journey-section photo with the original file | Home, below the hero | Send the original image | |
| [ ] | Optional: real hero photography | Home hero slideshow | Send images | |

## 7. Photo credits (legal requirement, not optional)

| Done | Item | Where | Your answer |
|---|---|---|---|
| [ ] | Show a public photo credit for the CC BY / CC BY-SA images (author, licence, link). Details are in `IMAGE_CREDITS.md` | Footer, About page, or a Photo credits page | Where to put it? |

## 8. Before ads go live

| Done | Check |
|---|---|
| [ ] | Submit a real test enquiry on a phone: it appears in the admin, in GoHighLevel, and the conversion shows in GTM |
| [ ] | Click the phone number on a phone: it dials |
| [ ] | Run Lighthouse on mobile for the home page and fix what it flags |
| [ ] | No `[CONFIRM` text left on any public page: search the live site |
| [ ] | Domain, SSL and `sitemap.xml` submitted to Google Search Console |
| [ ] | Landing pages chosen for each ad group (`/tours/self-drive`, `/tours/escorted`, `/tours/coach`) |

---

### Fastest path
If you only have 15 minutes, send these first, in this order: **(1)** reCAPTCHA keys and GTM ID, **(2)** phone, email, address, legal company name, **(3)** the response time you can honestly promise, **(4)** which claims in section 3 are true, **(5)** real prices for your first 3 packages.
