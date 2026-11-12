# GoHighLevel (GHL) integration

After an enquiry is saved, a queued job (`SyncEnquiryToGhl`) upserts the contact by email through the GHL API v2 (`https://services.leadconnectorhq.com`, header `Version: 2021-07-28`) using a **Private Integration Token (PIT)**.

**OFF** whenever `GHL_API_TOKEN` or `GHL_LOCATION_ID` is empty. Never blocks or fails an enquiry. Failures are logged (token scrubbed), recorded on the enquiry (`ghl_status`, `ghl_error`) and can be retried in Admin > Enquiries > an enquiry. Queue retries use backoff (30s, 2m, 10m, 30m; 5 tries; permanent 4xx errors are not retried). Field IDs are cached for an hour.

## Setup
1. In GHL open the sub-account > **Settings > Private Integrations > Create new integration**. Name it e.g. "GoAustriaTours website".
2. Tick these scopes: **contacts.readonly, contacts.write, locations/customFields.readonly, locations/customFields.write** (the Private Integrations screen labels them roughly "View Contacts", "Edit Contacts", "View Custom Fields", "Edit Custom Fields"). Nothing else is needed.
3. Copy the token (shown once) and the sub-account/location ID (Settings > Business Profile, or the URL after `/location/`).
4. Put them in `.env` **only** (never in code or git):
```
GHL_API_TOKEN=pit-xxxxxxxx
GHL_LOCATION_ID=xxxxxxxx
```
5. `php artisan config:clear`, then `php artisan ghl:setup` (creates every missing custom field; lists existing fields first; safe to re-run; `--dry-run` previews).
6. `php artisan ghl:test` checks the token and location, creates a clearly labelled test contact (`ZZ TEST ...`, tag `ghl-test`) and deletes it again (`--keep` to leave it).

> These endpoints and scope names follow GHL's public v2 API but were **not exercised against a live GHL account** in this build (no token was available). Run `ghl:test` first and check the output.

## What is sent
Contact: first name, last name, name, email, phone (E.164), source `Website: <form>`.
Tags (added, never replacing staff tags): brand name, `source: <utm_source or direct>`, `package: <name>` (if any), `form: <form name>`.

Custom fields (defined in ONE place: `app/Support/GhlFields.php`, matched by **exact name**):
- Enquiry: Preferred Travel Date, Second Quote Preference, Special Request, Package, Form Name, Quiz Answers (Full), Consent Given, Consent Timestamp, Consent Text Version
- One field per quiz step, e.g. `Quiz - Destination`, `Quiz - Season`, `Quiz - Travel Style`, `Quiz - Group Size`, `Quiz - Nights`, `Quiz - Flights Booked`, `Quiz - Budget`, `Quiz - Likelihood` (follows whatever steps exist in the admin quiz editor; re-run `ghl:setup` after adding a step)
- Tracking, each as **First** and **Last**: UTM Source, UTM Medium, UTM Campaign, UTM Term, UTM Content, GCLID, GBRAID, WBRAID, Referrer, Landing Page URL (e.g. `UTM Source First`, `UTM Source Last`)

Every form (hero quiz, header modal, package quote, ask a question, contact) sends the tracking fields plus its form name.

**Returning contacts:** the job looks the contact up by email first. `... Last` fields are always updated; `... First` fields are only written if the contact has no value yet, so first-touch data is never erased.

## Folders and renaming
The API cannot create custom-field folders. Staff may drag fields into folders in GHL by hand. **Do not rename** them: if a field must be renamed, change it in GHL and in `GhlFields.php` together, otherwise it will be re-created / skipped. Missing fields are skipped with a log warning rather than failing the sync.

## Tests
`phpunit.xml` force-blanks `GHL_API_TOKEN` / `GHL_LOCATION_ID` so tests never reach the CRM. `tests/Feature/GhlTest.php` checks that the payload carries the UTM/tracking fields for each of the five form types, first-touch preservation, the off-switch, success and failure handling (HTTP is faked).
