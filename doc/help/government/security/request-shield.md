---
title: request-shield in front of a public site
audience: government, admin, hosting
category: public-sector
order: 140
summary: For public bodies whose sites face constant scanning - what request-shield is (a separate MIT-licensed PHP firewall by the CJW Network, not part of Exponential's composer.json), how it complements the kernel's request classifier, and how to introduce it safely in monitor mode first.
keywords: request-shield, web application firewall, WAF, bot protection, rate limiting, browser check, scanners, monitor mode, enforce mode, request classifier, security.log, Velocity, EXP_REQUEST_PREPEND_FILE, public sector
type: help
---
# request-shield in front of a public site

This page is for administrators and hosting teams of public sites. Public sites receive scanners, credential attacks
and scrapers every day. request-shield is a small web application firewall for PHP that decides about each request
**before the Exponential kernel starts**: it refuses scanners and broken requests, slows floods, asks suspicious
clients for an invisible browser check, and can restrict the administration
([Security hardening: A shield in front](../../security-hardening.md#a-shield-in-front-request-shield)).

## Contents

1. [What it is and is not](#what-it-is-and-is-not)
2. [Classifier and shield side by side](#classifier-and-shield-side-by-side)
3. [Introducing it](#introducing-it)
4. [Procurement and governance notes](#procurement-and-governance-notes)
5. [Related pages](#related-pages)

## What it is and is not

- A separate library: <https://github.com/cjw-network/request-shield>, MIT license, by the CJW Network.
- **Not part of Exponential's `composer.json`**; you add it with `composer require cjw-network/request-shield`, or as a
  checkout outside the document root.
- It cannot tell a right password from a wrong one: account lockout and two-step sign-in stay Exponential's job.
- It does not see static files the web server serves itself.

## Classifier and shield side by side

| | Kernel request classifier | request-shield |
|---|---|---|
| Does | classifies only (probe, traversal, injection, scanner, 404 bursts) | decides: refuse, slow, challenge, ban, or only log |
| Runs | inside the kernel | before the kernel |
| Log | `var/log/security.log` | its own log and a live view under `/rs/` |
| Settings | `settings/requestclassifier.ini` | its rule files |

They use the same rule names where they overlap, so a line in one can be found in the other.

## Introducing it

1. Install the library; copy the Exponential example rules from its `examples/exponential/` directory to a directory
   the web server never serves (the library suggests `settings/request-shield/`).
2. Choose the main file: `exponential-admin-uri.rules` (administration at `/admin` on the same host) or
   `exponential-admin-host.rules` (administration on its own host).
3. Switch it on for every request: with PHP-FPM a `Shield::protectFile()` call at the top of `config.php` or
   `auto_prepend_file`; under Velocity the file named in the constant `EXP_REQUEST_PREPEND_FILE` (since 6.0.15).
4. **Monitor mode first** (`set mode monitor`, as the examples ship): read the log for a few days while editors work.
5. Adapt the site-specific lines (siteaccesses, extension modules that take forms, host names, load balancer).
6. Check and test after every change: `bin/request-shield check <main file>`, `bin/request-shield test <main file>`,
   `bin/request-shield trace <main file> "GET https://www.example.gov/..."`.
7. Switch to `set mode enforce`; keep the ban and strict rules watched until their log is clean.
8. To switch it off, use `set mode off`, not removing the include.

## Procurement and governance notes

- It is third-party open source software: record it in your software inventory with its license and version, and
  follow its releases like any dependency.
- Its log holds addresses of visitors; include it in your records of processing and its retention in your retention
  plan.
- The example rules restrict system URLs and administration modules on the public site, which also reduces the
  attack surface for scanners.

## Related pages

- [Security hardening](../../security-hardening.md)
- [Request logs](../../../features/6.0/request-logs.md)
- [request-shield and request rules for hosts](../../hosting/security/request-shield-and-request-rules.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
