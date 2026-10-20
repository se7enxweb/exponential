---
title: "Request logs: 404 and security"
audience: government, admin, hosting
category: public-sector
order: 50
summary: How Exponential separates not-found answers (404.log) and requests that look like probes or attacks (security.log) from real faults, what each line holds, how personal data is masked, and how a public body uses them for broken-link work and threat awareness.
keywords: 404.log, security.log, request classifier, probes, attacks, injection, traversal, scanner, not found, broken links, masking, MaskIP, threat monitoring
type: help
---
# Request logs: 404 and security

This page is for administrators and security staff of public websites, which attract a steady stream of automated
probes. Exponential writes not-found answers and suspected attacks to logs of their own, so `error.log` keeps the real
faults. Reference: [Request logs](../../../../features/6.0/request-logs.md); hosting view:
[Security log (hosting)](../../../hosting/operations/security-log.md).

## Contents
1. [Three logs, one line per request](#three-logs-one-line-per-request)
2. [What security.log tells you](#what-securitylog-tells-you)
3. [What 404.log tells you](#what-404log-tells-you)
4. [Privacy](#privacy)
5. [Settings](#settings)
6. [Related pages](#related-pages)

## Three logs, one line per request

| Log | What | Level |
|---|---|---|
| `error.log` | real faults (database error, template that does not compile, PHP error) | error |
| `404.log` | answers 404 and 410, with the reason the kernel found | notice |
| `security.log` | requests the classifier names a probe, traversal, injection, attack tool or a burst of not-found answers, with any status | warning |

A request is in at most one of them. **Nothing is blocked**: the classifier only classifies; the request gets the
answer it would get anyway. Blocking belongs in front of the site
([Security hardening, request-shield](../../../security-hardening.md#a-shield-in-front-request-shield)).

## What security.log tells you

Each line names `<category>/<rule>` (`probe/wp-login`, `injection/sql-union`, `threshold/404-rate` ...), a confidence
(low, medium, high), the status, the request with query values kept as evidence (secrets and addresses masked), the
siteaccess and a short user agent. Read it for:

- **Trends**: a rule suddenly firing much more often, or high-confidence injections that got a `200`.
- **Floods**: `threshold/404-rate` marks one client with more than `Max404PerMinute` (30) not-found answers in a
  minute ([DDoS and probe floods](../incidents/ddos-and-probe-floods.md)).
- **Targets**: which paths attackers try; a successful-looking status on an injection line deserves a developer's look.

The error digest groups it by category and rule; review it weekly
([Review the security log weekly](../../tasks/operations/review-the-security-log-weekly.md)).

## What 404.log tells you

Each line has the status (404 or 410), the request, the siteaccess, the reason (`module:`, `view:`, `node:<id>`,
`object:<id>`, `rule:`, `file:` ...), the node id when known and the referrer. Use it for:

- **Broken links** on your own site (the referrer is your site): fix the link or add a URL alias or wildcard.
- **Moved content** that citizens still look for (the referrer is a search engine or another public body's site).
- **Removed content** that should answer 410 Gone rather than 404.

## Privacy

Client addresses are masked (`203.0.x.x`, `2001:db8:x:x`); every query value is replaced by `***` in `404.log`; user
agents are a hash and a short prefix. The raw address is never written to a file. `MaskIP=disabled` writes addresses
in full "only for a site that has a reason and a legal basis for it". Rotation: 5 archives, compressed, 10 MB, pruned
after 30 days (404) and 90 days (security) by default.

## Settings

| Setting | Default | Effect |
|---|---|---|
| `site.ini [RequestLogSettings] NotFoundLog` | `enabled` | `disabled`: not-found answers go to `error.log` as before |
| `site.ini [RequestLogSettings] MaskIP` | `enabled` | address masking |
| `requestclassifier.ini [ClassifierSettings] Classification` | `enabled` | `disabled`: nothing classified |
| `requestclassifier.ini [ThresholdSettings] Max404PerMinute` | `30` | the 404 burst rule; `0` switches it off |

## Related pages
- [Error digest and log health](error-digest-and-log-health.md)
- [CSP reports](csp-reports.md)
- [Audit alerts](audit-alerts.md)
- [Request rules](../../../../features/6.0/request-rules.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
