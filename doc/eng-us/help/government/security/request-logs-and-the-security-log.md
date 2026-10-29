---
title: Request logs and the security log
audience: government, admin, hosting
category: public-sector
order: 150
summary: For administrators of public sites - what Exponential writes to 404.log and security.log (probes, traversal, injection, scanners, 404 bursts), how addresses and query values are masked, how long the logs are kept, and how to read them in Setup > Log files.
keywords: security.log, 404.log, request classifier, probes, traversal, injection, scanner, threshold, masking, MaskIP, log viewer, log rotation, MaxAgeDays, requestclassifier.ini, monitoring, public sector
type: help
---
# Request logs and the security log

This page is for administrators and hosting teams. Exponential separates real faults from noise: not-found answers go
to `404.log`, requests the classifier takes for a probe or an attack go to `security.log`, and `error.log` keeps the
real faults ([Request logs](../../../features/6.0/request-logs.md)). For a public site this gives you a quiet error
log and a separate, readable record of attacks.

## Contents

1. [The logs](#the-logs)
2. [Reading a security.log line](#reading-a-securitylog-line)
3. [Privacy built in](#privacy-built-in)
4. [Retention](#retention)
5. [Acting on what you see](#acting-on-what-you-see)
6. [Related pages](#related-pages)

## The logs

| Log | Holds | Log viewer label |
|---|---|---|
| `error.log` | real faults only | error |
| `404.log` | 404 and 410 answers with the reason the kernel found | "Not found requests (404)" |
| `notice.log` | 401 and 403 answers | notice |
| `security.log` | probes, traversal, injection, attack tools, 404 bursts, with any status | "Security (request classifier)" |

Every request is classified once and ends up in one log only. **Nothing is blocked**: the classifier only classifies.
Blocking is the job of request-shield, the web server, or a listener in `requestclassifier.ini [ClassifierSettings]
Listeners[]`.

## Reading a security.log line

```
[ Oct 17 2026 09:00:00 ] [203.0.x.x] probe/wp-login confidence=high 404 GET /wp-login.php siteaccess=site namespace=/wp-login.php ua=3f2a9c1b "curl/8.5.0"
```

| Part | Means |
|---|---|
| `probe/wp-login` | category and rule: `probe`, `traversal`, `injection`, `scanner`, `threshold` |
| `confidence=high` | low, medium or high |
| `404` | the final status; a `200` on an injection attempt deserves a closer look |
| `GET /wp-login.php` | method and URI; query values kept as evidence, secrets and addresses in them masked |
| `siteaccess=`, `namespace=` | where it arrived |
| `ua=` | 8 hex digits of the user agent's hash and its first 60 characters |

## Privacy built in

- Client addresses masked (`203.0.x.x`) with `site.ini [RequestLogSettings] MaskIP=enabled` (the default); the raw
  address is never written to a file.
- In `404.log` every query value is replaced by `***`.
- The log viewer masks e-mail addresses, IP addresses and secrets by default; **Show unmasked** needs
  `setup/unmasklogs` and is itself recorded ([Log viewer: Privacy](../../../features/6.0/log-viewer.md#privacy-what-is-masked)).

## Retention

`404.log` and `security.log` keep 5 compressed archives of up to 10 MB, pruned after 30 days (404) and 90 days
(security) ([Log rotation](../../../features/6.0/log-rotation.md)). Change them per log in
`settings/override/logviewer.ini.append.php`, `[RotationSettings] MaxAgeDays[security]=<days>`. Pruning by age runs
with `./console exp:logs --apply --prune` or the cronjob part `logs` (`php runcronjobs.php logs`).

## Acting on what you see

- A steady flow of `probe/*` with 404 is normal internet noise; watch for changes in volume.
- `injection/*` or `traversal/*` with status 200 or 500: check the URL, the module and any custom extension that
  handles it.
- Many `threshold/404-rate` from one network: consider a rule in request-shield or the web server.
- Read the weekly routine in "Log review routine" in this section.

## Related pages

- [Request logs](../../../features/6.0/request-logs.md)
- [The log viewer](../../../features/6.0/log-viewer.md)
- [request-shield in front of a public site](request-shield.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
