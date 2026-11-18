# GoHighLevel follow-up sequence (speed to lead)

Replying fast is the biggest conversion lever after the site itself. The website does the first half; you build the second half inside GoHighLevel (workflows cannot be created from the website).

## What the website already sends
Every enquiry creates or updates a GoHighLevel contact with these **tags** (see `App\Services\GhlPayload`):

| Tag | Meaning |
|---|---|
| `nurture: new-lead` | **Workflow trigger.** Added to every enquiry, from every form. |
| `source: <utm_source>` | Where the visitor came from (`google`, `direct`, ...). |
| `form: <form name>` | `hero-quiz`, `header-modal`, `package-quote`, `ask-question` or `contact`. |
| `package: <title>` | Only when the enquiry was about one package. |
| `interest: <tour type slug>` | Only when the visitor arrived on a message-matched page (`/?tour=self-drive`, `escorted`, `coach`). |
| the brand name | e.g. `GoAustriaTours`. |

Quiz answers, UTMs, gclid and consent evidence go to the contact's custom fields.

## Build this workflow in GoHighLevel
**Automation > Workflows > Create**. Trigger: *Contact Tag* is added: `nurture: new-lead`. Enable "Allow re-entry" off.

| When | Action | Notes |
|---|---|---|
| Immediately | Send **email** to the contact | Short, personal, from a named person (the planner). Confirm what happens next and the response promise you set in Admin > Trust & tracking. |
| Immediately | Send **SMS** (only if a phone number exists) | Consent is recorded on the enquiry. Include STOP wording per your carrier rules. |
| Immediately | **Create task / notify** the owner: "Call new lead within 1 hour" | Include the contact's quiz answers and the `interest:` tag. |
| +1 day (if tag `status: contacted` is NOT present) | Email: "Did you get my note?" | Add a calendar link if you use one. |
| +3 days (same condition) | Email with one useful, genuine piece of content | For example the journal article that matches their `interest:` tag. |
| +7 days (same condition) | SMS or email: last gentle check-in | Then stop. |
| Any time | **Remove from workflow** when the tag `status: contacted`, `status: booked` or `status: not-interested` is added | Add these tags by hand or from a pipeline stage change. |

Tips:
- Use **If/Else** branches on `interest: self-drive`, `interest: escorted`, `interest: coach` to change the wording.
- Send between 8:00 and 20:00 in the contact's time zone (Workflow settings > Time window).
- Keep every message short and human. No "limited time" pressure: it does not suit this audience.

## Message drafts (edit freely; fill in the real names and promises)
**Email 1 (instant)**
> Subject: Your Austria journey, [First name]
> Hello [First name],
> Thank you for telling us about your Austria plans. I'm [Planner name], and I'll be personally putting your itinerary together. [Response promise, e.g. "You'll hear from me within one business day."]
> In the meantime, is there anything you already know you'd love to include, or want to avoid?
> [Planner name] · [Phone]

**SMS 1 (instant)**
> Hi [First name], it's [Planner name] at [Brand]. Thanks for your Austria enquiry. [Response promise] Reply here with any questions. Reply STOP to opt out.

## Remarketing (optional)
Add your **Meta Pixel ID** and/or **Google Ads tag ID** in Admin > Trust & tracking to build remarketing audiences of site visitors. If you later install Google Tag Manager, move these into GTM and clear them in the admin to avoid counting twice.
