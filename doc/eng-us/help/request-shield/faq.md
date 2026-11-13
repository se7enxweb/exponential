---
title: "Request shield FAQ"
audience: admin, hosting, developer
category: administration
order: 80
summary: Short answers to the questions people ask about the request shield - what it costs, whether it replaces a firewall or a CDN, what visitors see, privacy, shared hosting, PHP versions, licensing, switching it off, and which installations get it.
keywords: request shield, FAQ, questions, cost, performance, firewall, WAF, CDN, captcha, browser check, privacy, IP address, GDPR, shared hosting, PHP 7.4, PHP 8.5, license, MIT, GPL, switch off, pause, Velocity, PHP-FPM, 6.0.15, composer
type: help
---
# Request shield FAQ

## What you'll have at the end

An answer to your question, or a link to the page that has it.

## What it is

**What is the request shield?** A filter in front of the site that decides every request before Exponential starts:
it lets real visitors through and turns away scanners, floods, stray form posts and requests for internal files.
It is the library [request-shield](https://github.com/cjw-network/request-shield) 0.4.0 by JAC Systeme GmbH (CJW
Network), packaged for Exponential as the extension `exp_request_shield` 0.0.1.0.

**Does it replace a network firewall or a CDN?** No. It works at the level of web requests, inside PHP, and knows
Exponential's addresses. A network firewall, a CDN or the web server's own limits still have their place in front of
it; the shield also works on hosting where you have none of those.

**What does it cost?** A few microseconds per request. The rules are compiled once into a PHP file that OPcache
keeps. A refused request costs far less than the page it would otherwise have started.

## Visitors and editors

**What does a visitor see?** Usually nothing. Where a browser check is asked (at the login, in the administration
once per pass, for clients that ask too often), a page says "one moment" and the browser solves it by itself in a
fraction of a second. There is no picture puzzle.

**What about visitors without JavaScript?** The browser check needs JavaScript and cookies; a page says what is
missing. Pages that do not ask for the check work without either.

**Does it keep visitors' IP addresses?** The log keeps a client's network (IPv4 /24, IPv6 /48), not a person's full
address. The administration shows that masked value.

## Running it

**Is it on after the installation?** When the hook is in `config.php`, yes, in enforce mode. Without the hook nothing
is filtered, even with the extension active.

**How do I switch it off quickly?** **Pause** on the dashboard, or create `var/request-shield/PAUSED`.
[The switches](the-switches.md).

**Can I try it without refusing anybody?** Yes: monitor mode. [Monitor and enforce](monitor-and-enforce.md).

**Does it work on shared hosting?** Yes. It needs PHP and a writable `var/request-shield/`; APCu is optional.

**Which PHP versions?** PHP 7.4 to 8.5. On PHP 7.4 a build of the same library files with older syntax is loaded.

**Does it work with Exponential Velocity?** Yes, with identical answers, from the 6.0.15 `index.php` on.
[Under Velocity](under-velocity.md).

**Does it work with the HTTP cache?** Yes. It runs before the cache's early exit, so cached pages are protected too,
and a request it marks as not for a cache is never stored.

## Getting it

**Which installations get it?** Any Exponential 6.0 site can install it now with
`composer require se7enxweb/exp_request_shield`. New installations from `se7enxweb/exponential` `main` and the 6.0.15
site packages include it. An existing site's `config.php` is never changed by an upgrade: the hook line is added by
hand. `composer create-project` of the distributions gives v6.0.14 until 6.0.15 is tagged. The full table:
[Who gets it, and when](../../features/6.0/request-shield.md#who-gets-it-and-when).

**What license is it under?** The extension is GPL-2.0-or-later. The bundled library and the three Exponential rule
files copied from it stay under the MIT License.

**Where are the rules documented?** In the library's documentation:
[rule files](https://github.com/cjw-network/request-shield/blob/main/docs/reference/rule-files.md).
