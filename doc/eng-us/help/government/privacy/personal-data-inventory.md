---
title: Where personal data lives in Exponential
audience: government, admin, project-manager, developer
category: public-sector
order: 40
summary: An inventory for public bodies of the places an Exponential installation stores personal data - user accounts, visits, sessions, form submissions, mail preferences and consent log, newsletter data, audit trail, logs, search phrases, orders, content and backups - with the tool to read, export or remove each.
keywords: personal data, data inventory, data map, user accounts, ezuser, ezuservisit, sessions, information collection, consent log, newsletter, audit trail, logs, search statistics, orders, backups, records of processing, public sector
type: reference
---
# Where personal data lives in Exponential

This page is for data protection officers, administrators and developers who build the data map of a site. Each row
names a place an installation keeps personal data, the tool that reads or removes it, and the retention that applies
by default. It is the basis for the record of processing, access requests and the retention schedule.

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [The inventory](#the-inventory)
2. [Places outside Exponential](#places-outside-exponential)
3. [Keeping the inventory current](#keeping-the-inventory-current)
4. [Related pages](#related-pages)

## The inventory

| Place | Holds | Read / export | Remove | Default retention |
|---|---|---|---|---|
| User accounts (user content objects, `ezuser`) | name, login, e-mail, whatever the user class adds | the user's node; Users > Check user accounts (CSV) | node's Remove (final, no trash) | none |
| Visits (`ezuservisit`) | last visit, number of sign-ins | Check user accounts | with the account | none |
| Sessions | session data while signed in | Setup > Sessions (with `ezpSessionHandlerDB`) | Setup > Sessions; timeout | `SessionTimeout` (3 days) |
| Form submissions (information collections) | what visitors typed | Setup > Collected information, CSV export | Setup > Collected information, Remove | none: manual |
| Mail preferences and consent log | address, choices, consent history with IP address | Setup > E-mail preferences; `exp:mail:preferences export` | `exp:mail:preferences erase` | consent log 1095 days for people who are gone |
| Suppression list | salted hash of an address | check by typing the address | Lift | kept forever by default |
| Newsletter (cjw_newsletter, if active) | subscribers, statuses, per-person statistics with consent | newsletter user list, subscriber export | erasure of the person; newsletter tools | per the extension |
| Audit trail | actors, truncated addresses, actions on users | Audit tab; `exp:audit export --subject-user=<id>` | not erased; pseudonymized after 90 days | 90 days live, 730 archived |
| Logs (`404.log`, `security.log`, `auth.log`, `error.log` ...) | masked addresses, user agents, URLs | Setup > Log files (masked) | rotation and prune | 30 / 90 / 30 days for 404, security, CSP; others by size only |
| Search statistics | phrases visitors typed | Setup > Search statistics | Reset statistics | none; off by default |
| Shop orders (if used) | customer data | shop views | shop tools | none |
| Content | names and images of people in published content | content tree | editors | your schedule |
| Trash | removed content, including personal data in it | `content/trash` | Empty trash | until emptied |
| Versions | earlier versions of content | versions page | remove versions | version history limit |
| Backups | everything above | your backup system | your rotation | your rotation |

Sources: [Privacy standards](../../../standards/privacy.md), [Check user accounts](../../../features/6.0/user-hygiene.md),
[Sessions](../../../guides/sessions.md), [Collected information](../../../guides/collected-information.md),
[Mail preferences for administrators](../../../guides/mail-preferences-administrator.md), [Audit](../../../bc/6.0/audit.md),
[Log rotation](../../../features/6.0/log-rotation.md), [URLs and search statistics](../../../guides/urls-and-aliases.md#5-search-statistics),
[The trash view](../../../bc/6.0/trash.md).

## Places outside Exponential

- The web server's and PHP-FPM's access and error logs.
- Velocity's own logs and response cache.
- request-shield's log, if used.
- Mail server logs and bounce mailboxes.
- Third-party services your extensions call.
- Exports people made: CSV files of users, consent logs and form submissions on someone's computer.

## Keeping the inventory current

Review it when you activate an extension, add a content class with personal fields, add a form, or change hosting.
Each new place needs a retention decision and a line in your record of processing.

## Related pages

- [Privacy standards](../../../standards/privacy.md)
- [Mapping the GDPR to Exponential](gdpr-mapping.md)
- [Controller, processor and the software](controller-processor-and-exponential.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
