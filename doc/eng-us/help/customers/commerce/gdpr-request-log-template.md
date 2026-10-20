---
title: GDPR request log template
audience: customer, admin, businesses
category: customer-success
order: 210
summary: A template log for requests from people about their personal data in an Exponential store - one line per request, with dates, the kind of request, the data found, what was done and the proof - plus how to keep the log itself safe. Template, not legal advice.
keywords: GDPR request log, data subject request log, template, privacy log, erasure log, access request, accountability, record keeping, not legal advice
type: help
---
# GDPR request log template

This page is a template for the person who answers privacy requests. Keeping a log of every request, and what was
done about it, lets you show later that requests were handled. The routine that fills the log is on
[GDPR requests](gdpr-requests.md).

> **Warning**
> Template, not legal advice. What you must record, and for how long, is for your lawyer or data protection officer to
> decide. Adapt the columns to their advice.

## How to keep the log

- Keep it where only the people who handle requests can read it, not in a shared folder for the whole team.
- Record what you did, not more personal data than you need: a reference to the person is often enough. Ask your
  adviser what reference to use.
- Do not paste exported data into the log. Exports are deleted when the request is closed
  ([Data retention and deletion](../../businesses/security/data-retention-and-deletion.md)).
- Review the log when you review your privacy notice, at least once a year.

## The log

```text
GDPR REQUEST LOG - [organization]                 Owner of this log: [role]

No. | Received   | Channel  | Kind of request            | Identity confirmed (how, when) | Due (per adviser)
----|------------|----------|----------------------------|--------------------------------|------------------
1   |            |          | access / correction /      |                                |
    |            |          | export / erasure / object  |                                |

No. | Data found (tick)                                              | Decision (by whom)
----|----------------------------------------------------------------|-------------------
1   | [ ] user account   [ ] orders        [ ] form submissions      |
    | [ ] mail prefs     [ ] newsletter    [ ] other: ______         |

No. | Action taken                         | Tool used                       | Done on | By
----|--------------------------------------|---------------------------------|---------|----
1   |                                      |                                 |         |

No. | Answer sent on | Export files deleted on | Kept, and why (e.g. bookkeeping rule) | Closed on
----|----------------|-------------------------|----------------------------------------|----------
1   |                |                         |                                        |
```

## The tools column

Name the tool in the log, so the next person can repeat the step. The tools the software offers, from
[GDPR requests](gdpr-requests.md) and [Compliance and GDPR](../../hosting/security/compliance-and-gdpr.md):

| Data | Tool to name in the log |
|---|---|
| Mail preferences and consent | `exp:mail:preferences export` or `erase`; the person's own download |
| Marketing objection | `exp:mail:suppression add`, or the person's own unsubscribe link |
| Audit records about a user | `exp:audit export --subject-user=<id>` |
| User account | Removal in **Users** (final) |
| Form submissions | **Setup > Collected information** |
| Orders | **Store > Customers**, customer order view; `shop/archiveorder` or `shop/removeorder` |
| Newsletter subscribers | The `cjw_newsletter` administration |
| Backups | Your host, under your agreement |

## The shape of one entry

One entry in plain text, with the field names in square brackets. Keep entries free of more personal data than the
request itself needs.

```text
No. [number] | Received [date] | [channel] | [kind of request] | identity confirmed [how, date] | due [date per adviser]
Data found: [which of the places above hold data about the person]
Decision: [what is erased, exported or kept, and why] ([who decided], [date])
Action: [commands and admin pages used] | [date] | [who did it]
Answer sent [date] | [export files, if any] | [what is kept, and the rule for keeping it] | closed [date]
```

## Related pages

- [GDPR requests](gdpr-requests.md)
- [Data export and erasure](data-export-and-erasure.md)
- [GDPR basics](../../businesses/security/gdpr-basics.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
