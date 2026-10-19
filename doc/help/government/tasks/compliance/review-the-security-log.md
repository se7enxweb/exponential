---
title: Review the security log
audience: government, admin, hosting
category: public-sector
order: 340
summary: Step by step - the weekly read of Exponential's security.log in Setup > Log files - what the request classifier records, which lines need a closer look (injection or traversal with status 200 or 500), and what to do about them.
keywords: security.log, request classifier, log viewer, probes, injection, traversal, scanner, threshold, weekly review, exp:logs, request-shield, public sector
type: help
---
# Review the security log

For administrators and hosting teams, weekly. The request classifier writes every request it takes for a probe or an
attack to `var/log/security.log`, masked; it blocks nothing
([Request logs and the security log](../../security/request-logs-and-the-security-log.md)).

## Contents

1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [What needs action](#what-needs-action)
4. [Related pages](#related-pages)

## Before you start

- The policy `setup/logs`; masking stays on (unmasking needs `setup/unmasklogs` and is recorded).
- Last week's note, to compare volumes.

## Steps

1. **Setup > Log files** > "Security (request classifier)" (`/setup/logs/view/security`).
2. Look at the counts per rule (`<category>/<rule>`, for example `probe/wp-login`, `injection/sql-union`,
   `traversal/...`, `threshold/404-rate`) and compare with last week.
3. Filter or search for lines whose status is not 404: `200` and `500` on `injection/*` and `traversal/*` lines.
4. For each such line, note the URL, the siteaccess and the time; open the matching audit records or error log lines
   around that time.
5. Write the weekly note: volumes, anything investigated, actions.

## What needs action

| You see | Do |
|---|---|
| a steady flow of `probe/*` with 404 | normal internet noise; nothing |
| a sudden rise of one rule | check whether one network causes it; consider a request-shield or web server rule |
| `injection/*` with 200 or 500 | check the module or extension that answered; if custom code, review it; consider it an incident if data may be exposed |
| `traversal/*` with 200 | check that no file outside the document root was served; run the file consistency check |
| `abuse/csp-report-flood` | someone floods the CSP collector; rate limits work, nothing else needed |

## Related pages

- [Request logs](../../../../features/6.0/request-logs.md)
- [Log review routine](../../security/log-review-routine.md)
- [Security log for hosts](../../../hosting/operations/security-log.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
