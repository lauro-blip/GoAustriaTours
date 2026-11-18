# Admin guide (plain English)

Go to **/admin** and sign in. Your first login comes from the server's `.env` file (`ADMIN_SEED_EMAIL`, `ADMIN_SEED_PASSWORD`). Change the password under **Admin users** straight away (12+ characters). After 5 wrong attempts you are locked out for a few minutes.

## Menu
- **Dashboard:** enquiries this week, how many are still *New*, and the latest enquiries.
- **Enquiries:** every lead. Search, filter by status/date/source, open one to see everything: contact details, quiz answers, consent evidence (what they ticked, when, from which IP), ad tracking and CRM status. Change the status (New, Contacted, Quoted, Won, Lost) and add private notes. **Export CSV** downloads the current filter. **Retry GoHighLevel sync** re-sends a lead that failed.
- **Packages:** add, edit, duplicate, publish/unpublish, feature (shows on the home page) and reorder (arrows). Each package has title, tour type, destination, nights, "from" price (leave blank until confirmed), summary, full description, highlights (one per line), day-by-day itinerary, included/not included, FAQs, a main image and a gallery (alt text is required for every image), plus SEO title, meta description and social image. Unpublished packages are hidden from the site.
- **Tour types / Destinations:** the lists packages use. Each tour type also has its own landing page.
- **Quiz:** the hero quiz steps. Add, rename, reorder, switch off, choose tiles or dropdown. Click **Options** on a step to edit its answers (label shown to visitors, stored value, icon, active). The final contact-details step is built in.
- **Pages:** home page text blocks, About, Contact, Terms, Privacy and Thank You (title, SEO fields, content, and a "hide from search engines" switch). Terms and Privacy are **starter templates only**: have them reviewed professionally.
- **Testimonials / FAQs:** home page reviews and questions. Untick "placeholder" only for real, permitted reviews.
- **Media:** upload images (JPG/PNG/WebP up to 8 MB). They are converted to WebP and resized automatically. Always write alt text.
- **Settings:** brand and legal company name (used in the consent text), logo, favicon, address, social links, staff notification emails (comma separated), reCAPTCHA keys, footer text, default SEO text and image. GoHighLevel has its own menu item (below).
- **Admin users:** add or remove staff. You cannot delete yourself or the last admin.

## Good to know
- The consent wording on forms is fixed on purpose. Only the company name changes (Settings > legal company name).
- Prices shown as `[CONFIRM: price]` appear because no price has been entered yet.
- Changes appear on the site straight away.
- **GoHighLevel:** paste the Private Integration Token (PIT) and the Location ID, press Save, then Test connection (read-only; it creates nothing). The token is stored encrypted and is never shown again (only its last 4 characters); leave it blank to keep the saved one. Values saved here override the server `.env` ones; tick "Remove the saved values" to fall back to `.env`. After the first successful connection, run `php artisan ghl:setup` on the server once to create the custom fields.
- **Trust & tracking:** everything that builds buyer confidence. Phone (display + tap-to-call number) and public email appear in the top bar, footer and contact page. "Who you are": an established line ("Planning Austria journeys since 2012"), and the planner name, role, bio and photo (the "Meet your planner" section appears only when name and bio are filled in). Up to four memberships/protection marks (name, optional logo, optional link) and a review summary (for example "Rated 4.9 from 120 Google reviews") appear in a strip right under the hero. Only enter true, current facts; blank slots are hidden, and the whole strip disappears when empty. The Google Tag Manager ID goes here too: leave it blank to keep tracking OFF, and add it when your Google Ads conversions are ready.
- **Forms:** the phone number is optional on every form; a phone that is given must be valid for the chosen country.
