# Google Sheet log of form submissions

Every submission of every form is also added as a new row in a Google Sheet, one tab per form
(**Hero Quiz**, **Quote Popup**, **Package Quote**, **Ask a Question**, **Contact**). Tabs are created automatically.
Every stored field is a column: contact details, travel details, quiz answers (one column each), package, page URL,
the consent record (given, time, wording version, exact text, IP, browser) and all UTM / click-ID values for first and
last touch (`utm_source_first`, `utm_source_last`, `gclid_first`, ...). New columns are added on the right when a new
field appears. The sheet is a log: the enquiry is always saved in the admin first, and a sheet failure never affects the visitor.

## Set-up (about 3 minutes, once)
1. Open the Google Sheet > **Extensions > Apps Script**.
2. Delete the sample code and paste the whole of `docs/reference/google-sheet-script.gs`.
3. Replace `PASTE_A_LONG_RANDOM_SECRET_HERE` with a long random text (at least 12 characters, no spaces). Keep a copy.
4. **Deploy > New deployment > type: Web app**. Execute as: **Me**. Who has access: **Anyone**. Deploy, and allow the permissions Google asks for.
5. Copy the **Web app URL** (starts with `https://script.google.com/macros/s/` and ends in `/exec`).
6. In the website admin go to **Google Sheet**, paste the URL and the same secret, **Save**, then **Send a test row**.
   A "Connection test" tab appears in the sheet.
7. To add earlier enquiries to the sheet, on the server run `php artisan sheets:sync` (safe to re-run: rows already in a tab are skipped).

If you ever change the script code, use **Deploy > Manage deployments > Edit > New version** so the same URL keeps working.

## Notes
- "Anyone" only means the address can be called; every call must carry the secret or it is refused.
- The sheet holds personal data (names, emails, phone numbers, IP addresses). Share it only with people who need it.
- Failed rows are retried and can be re-sent from the enquiry page in the admin ("Send to Google Sheet again").
- Instead of the admin page you can set `GOOGLE_SHEET_WEBHOOK_URL` and `GOOGLE_SHEET_SECRET` in `.env` (the admin values win).
