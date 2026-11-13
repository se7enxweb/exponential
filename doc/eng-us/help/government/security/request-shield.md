---
title: request-shield in front of a public site
audience: government, admin, hosting
category: public-sector
order: 140
summary: For public bodies whose sites face constant scanning - what request-shield is (a small PHP firewall by the CJW Network, part of the default installation since Exponential 6.0.15 through the extension exp_request_shield), how it complements the kernel's request classifier, and how to check it, adapt it and switch to monitor mode while you look at an unusual site.
keywords: request-shield, exp_request_shield, Setup > Request shield, web application firewall, WAF, bot protection, rate limiting, browser check, scanners, monitor mode, enforce mode, request classifier, security.log, Velocity, EXP_REQUEST_PREPEND_FILE, public sector
type: help
---
# request-shield in front of a public site

This page is for administrators and hosting teams of public sites. Public sites receive scanners, credential attacks
and scrapers every day. request-shield is a small web application firewall for PHP that decides about each request
**before the Exponential kernel starts**: it refuses scanners and broken requests, slows floods, asks suspicious
clients for an invisible browser check, and can restrict the administration
([Security hardening: A shield in front](../../security-hardening.md#a-shield-in-front-request-shield)). Since
Exponential 6.0.15 it is part of the default installation and protects a new site from its first request
([Request shield](../../../features/6.0/request-shield.md)).

## Contents

1. [What it is and is not](#what-it-is-and-is-not)
2. [Classifier and shield side by side](#classifier-and-shield-side-by-side)
3. [Checking and adapting it](#checking-and-adapting-it)
4. [Procurement and governance notes](#procurement-and-governance-notes)
5. [Related pages](#related-pages)

## What it is and is not

- The library request-shield 0.4.0 by JAC Systeme GmbH (CJW Network), MIT license,
  <https://github.com/cjw-network/request-shield>. It stays a CJW Network project.
- **Part of the default installation since 6.0.15**: the root `composer.json` requires the extension
  `exp_request_shield` (`se7enxweb/exp_request_shield` 0.0.1.0, GPL-2.0-or-later), which bundles the library (and a
  PHP 7.4 build of it; PHP 7.4 to 8.5) and runs it from one hook line in `config.php`. A new installation gets that
  line from `config.php-RECOMMENDED`; an upgraded installation adds it itself, because `config.php` is never
  overwritten ([Upgrading to the request shield](../../../bc/6.0/request-shield.md)).
- It **enforces as installed**: scanners, floods, stray form posts and system URLs are refused, and the sign-in and
  the administration's forms ask for the browser check.
- It runs before the HTTP cache's early exit, so cached pages are protected too, and PHP-FPM and Exponential Velocity
  give identical answers.
- It cannot tell a right password from a wrong one: account lockout and two-step sign-in stay Exponential's job.
- It does not see static files the web server serves itself.

## Classifier and shield side by side

| | Kernel request classifier | request-shield |
|---|---|---|
| Does | classifies only (probe, traversal, injection, scanner, 404 bursts) | decides: refuse, slow, challenge, ban, or only log |
| Runs | inside the kernel | before the kernel |
| Log | `var/log/security.log` | `var/request-shield/log/shield.log`, read in **Setup > Request shield > Log** |
| Settings | `settings/requestclassifier.ini` | its rule files, the site's own in `settings/request-shield/` |

They use the same rule names where they overlap, so a line in one can be found in the other.

## Checking and adapting it

1. Open **Setup > Request shield** (policy `requestshield/read`): the dashboard says whether the shield ran for that
   page, in which mode, from which rule file, and what it decided in the last 24 hours. The switches (policy
   `requestshield/manage`) pause it, switch it on again, or set the mode to enforce, monitor or off.
2. Give the site its own rule file in `settings/request-shield/` (the first mode switch in the administration makes it
   from the default), and choose the administration's rules: `exponential-admin-uri.rules` (administration at `/admin`
   on the same host) or `exponential-admin-host.rules` (administration on its own host).
3. Adapt the site-specific lines (host names, load balancer, siteaccesses, extension modules that take forms).
4. Read **Setup > Request shield > Log** in the first days. A site with many unusual addresses can switch to
   **monitor** while you look: every decision is then logged as it would be taken and nobody is refused. Add an
   exception for what a real visitor or editor did, then go back to **enforce**.
5. Check and test after every change, as the user the site's PHP runs as:
   `php extension/exp_request_shield/bin/request-shield check settings/request-shield/request-shield.rules`, `test`
   with the same file, and `trace <main file> "GET https://www.example.gov/..."` for one request.
6. To switch it off at once on every server, create `var/request-shield/PAUSED` (or use **Pause**); remove the file to
   switch it on again.

The step by step guide: [Running the request shield](../../../guides/request-shield.md).

## Procurement and governance notes

- It is open source software of a third party, shipped inside an Exponential extension: record `exp_request_shield`
  and the bundled request-shield version with their licenses in your software inventory, and follow their releases
  like any dependency.
- Its log holds the network of each client (by default the address masked to its /24 or /48); include it in your
  records of processing and its retention in your retention plan.
- The shipped rules restrict system URLs and administration modules on the public site, which also reduces the
  attack surface for scanners.

## Related pages

- [Security hardening](../../security-hardening.md)
- [Request shield (feature)](../../../features/6.0/request-shield.md) and [Running the request shield (guide)](../../../guides/request-shield.md)
- [Request logs](../../../features/6.0/request-logs.md)
- [request-shield and request rules for hosts](../../hosting/security/request-shield-and-request-rules.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
