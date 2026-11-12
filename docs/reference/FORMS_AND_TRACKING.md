# Forms and tracking

## Forms
Hero quiz (final step), header modal, package "Get a Quote", package "Ask Us a Question", tour-type landing page quote, Contact page. All render `<x-enquiry-form>` and POST to `/enquiry` (`EnquiryController@store`), so behaviour is identical.

`form_name` values: `hero-quiz`, `header-modal`, `package-quote`, `ask-question`, `contact`.

Server pipeline: honeypot (`website`) > CSRF > rate limit (5/min, 20/hour per IP) > validation > reCAPTCHA (fails closed) > phone to E.164 (libphonenumber, using the selected country) > sanitise (strip tags) > save > queue staff email > queue GHL sync > redirect to `/thank-you`.

Validation failure returns to the page with inline errors; only the form that was submitted shows them, and the modal/quiz re-open at the right place.

### Consent evidence (per enquiry)
`consent_given`, `consent_text` (exact text shown), `terms_version`, `consented_at`, `ip_address`, `user_agent`, `page_url`. Visible in Admin > Enquiries > an enquiry.

### reCAPTCHA
v2 checkbox. Site key / secret come from Admin > Settings if filled, otherwise `.env` (`RECAPTCHA_SITE_KEY`, `RECAPTCHA_SECRET_KEY`). The script loads only when a form scrolls into view or the modal opens. Without JavaScript the Google noscript fallback is used.

## Attribution capture
`CaptureTracking` middleware (GET page views):
- **First touch** cookie `ga_first_touch` is written once (30 days).
- **Last touch** cookie `ga_last_touch` is refreshed whenever a request carries any of `utm_source, utm_medium, utm_campaign, utm_term, utm_content, gclid, gbraid, wbraid`.
- Each touch stores those params plus `referrer` (external only) and `landing_page`.
Both are saved on the enquiry (`tracking_first`, `tracking_last`). Last touch falls back to first touch.

## Google Tag Manager and Google Ads
1. Admin > Settings > Google Tag Manager ID (`GTM-XXXXXXX`). The snippet renders only when set.
2. dataLayer events:
   - `enquiry_submit` with `form_name`, pushed when a valid form is submitted.
   - `enquiry_thank_you` with `form_name`, pushed on `/thank-you` (noindex page). **Attach the Google Ads conversion to this event.**
3. In GTM: create a Custom Event trigger `enquiry_thank_you`; add a "Google Ads Conversion Tracking" tag (conversion ID/label from Google Ads) and a Conversion Linker tag (All Pages) on that trigger. Optionally add GA4 event tags. Preview in GTM Tag Assistant, submit a test enquiry, confirm the tag fires once.
4. Google Ads: set auto-tagging on so `gclid` is appended. Final URL suffix for manual UTMs, e.g. `utm_source=google&utm_medium=cpc&utm_campaign={campaignid}&utm_term={keyword}&utm_content={creative}`.
