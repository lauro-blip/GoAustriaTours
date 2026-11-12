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
- **Settings:** brand and legal company name (used in the consent text), logo, favicon, phone, email, address, social links, staff notification emails (comma separated), Google Tag Manager ID, reCAPTCHA keys, footer text, default SEO text and image. GoHighLevel shows ON/OFF only: its keys are set on the server.
- **Admin users:** add or remove staff. You cannot delete yourself or the last admin.

## Good to know
- The consent wording on forms is fixed on purpose. Only the company name changes (Settings > legal company name).
- Prices shown as `[CONFIRM: price]` appear because no price has been entered yet.
- Changes appear on the site straight away.
