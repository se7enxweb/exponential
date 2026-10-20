---
title: Choosing a web server
audience: hosting
category: hosting
order: 20
summary: A decision guide for hosts: which web server or engine to offer for which plan, with the facts that decide it (PHP version, long-running processes, isolation, HTTPS, caching) and a decision record to copy.
keywords: web server, choose, decision, Velocity, qbix, FrankenPHP, Apache, nginx, LiteSpeed, Caddy, IIS, PHP-FPM, shared hosting, VPS, isolation, long-running process, PHP 8.0, PHP 8.1
featured: true
type: help
---
# Choosing a web server

This page is for product managers and system administrators at hosting companies who have to decide what serves
the Exponential sites on a plan. It answers: which servers and engines are possible, which questions decide between
them, what each choice means for operations, and how to write the decision down. The facts come from
[Chapter 8: Serving the site](../../../install/08-serving-the-site.md), [Operating system support](../../os-support.md)
and the repository's `README.md`.

## Contents

1. [The choice in one table](#the-choice-in-one-table)
2. [Five questions that decide it](#five-questions-that-decide-it)
3. [By hosting model](#by-hosting-model)
4. [Isolation between customers](#isolation-between-customers)
5. [What each choice costs you in operations](#what-each-choice-costs-you-in-operations)
6. [Mixing servers](#mixing-servers)
7. [A decision record to copy](#a-decision-record-to-copy)
8. [Checklist](#checklist)

## The choice in one table

Taken from [Chapter 8, The choice in one table](../../../install/08-serving-the-site.md#81-the-choice-in-one-table):

| | Exponential Velocity (`qbix`) | FrankenPHP (through Velocity) | Apache + PHP-FPM | nginx + PHP-FPM |
|---|---|---|---|---|
| Role | **recommended** for every stage | production-ready alternative | traditional | traditional |
| PHP | 8.1 or later | the PHP built into the binary | 8.0 or later | 8.0 or later |
| Separate web server needed | no | no (Caddy is built in) | yes | yes |
| Separate HTTPS or certificate tool needed | no: own certificates, self-signed fallback, ACME | no: your certificate or a self-signed one | yes (`mod_ssl` plus certbot or a panel) | yes |
| Application kept in memory between requests | yes (persistent workers) | no (clean state per request) | no | no |
| Response cache in front of PHP | yes | not yet | no (the kernel's own caches only) | no |
| Rewrite rules | built in | generated Caddyfile | `.htaccess_root` | written by you |
| Suits shared hosting | usually not | usually not | yes | rarely |

Other servers that can rewrite a URL to `index.php` work as well. The README lists LiteSpeed and OpenLiteSpeed,
Caddy, Microsoft IIS, lighttpd, H2O and Traefik (the last usually in front of another server), and says plainly:
"All of these work. Exponential does not require any particular one." [Operating system
support](../../os-support.md#web-servers) puts LiteSpeed, Caddy with PHP-FPM, IIS and lighttpd in tier 3, expected to
work.

> **Note**
> PHP's built-in server (`exp:velocity start --engine=php`) is the shipped default engine only because it needs
> nothing installed. It is for development, has no TLS and must never face visitors.

## Five questions that decide it

| Question | If the answer is ... | Then |
|---|---|---|
| May a customer's site run a long-lived process on this plan? | no (typical shared hosting) | Apache or LiteSpeed with `.htaccess`. "Shared hosting usually will not" allow one (`README.md`) |
| Is the machine's PHP 8.0? | yes (the stock PHP of Red Hat Enterprise Linux 9 and its rebuilds) | either engine: Velocity runs on PHP 8.0 too ([Chapter 2](../../../install/02-requirements.md#29-operating-system-notes)) |
| Must customers on one machine be isolated from each other by the operating system? | yes | one Velocity per site, each as its own user, or one PHP-FPM pool per site |
| Do you already run a tuned Apache or nginx you want to keep? | yes | keep it; Velocity can sit behind it or beside it |
| Do you want the fastest answer for repeat pages without adding a cache server? | yes | Velocity with its response cache |

These are the conditions chapter 8 names for a traditional server: "when your hosting does not allow long-running
processes, when you already run a tuned Apache or nginx you want to keep, when the machine only has PHP 8.0 ... or
when several customers on one machine need operating-system isolation from each other".

## By hosting model

| Hosting model | Usual choice | Why |
|---|---|---|
| Shared hosting with a control panel | Apache (or LiteSpeed) with PHP-FPM and `.htaccess` | no long-running customer processes; the rules ship as `.htaccess_root` |
| VPS or dedicated server per customer | Velocity | one site per machine, root available for ports 80 and 443 |
| Several customers on one VPS or dedicated server | Velocity per site (own user, own ports, a proxy in front) or PHP-FPM pool per site | operating-system isolation |
| Containers | Velocity or FrankenPHP in the container | one process tree per site; images exist for Velocity ([Velocity packages and binaries](../../../features/6.0/velocity-packages-and-binaries.md)) |
| Windows hosting | IIS or Apache with FastCGI PHP | `pcntl` and `posix` do not exist on Windows ([Operating system support](../../os-support.md#windows)) |

The models themselves are compared on [Choosing a hosting model](../models/choosing-a-hosting-model.md).

## Isolation between customers

The README is explicit: "A single server process serving several sites is a routing convenience, not a security
boundary — the process that serves one site can read every file the others hold." For a plan that puts several
customers on one machine:

- **Velocity**: run one Velocity per site, each with `[ServerSettings] User` and `Group` set to that customer's user,
  on its own ports, behind a proxy. See [Velocity beside Apache or nginx](velocity-beside-apache-or-nginx.md).
- **PHP-FPM**: give each site its own pool running as the site's user. See [Apache with PHP-FPM](apache-with-php-fpm.md)
  and [Per-customer users and pools](../security/per-customer-users-and-pools.md).

The security side is on [Multi-tenant isolation](../security/multi-tenant-isolation.md).

## What each choice costs you in operations

| | Velocity | FrankenPHP | Apache or nginx with PHP-FPM |
|---|---|---|---|
| Install | `composer require se7enxweb/exponential-velocity`, then `exp:velocity start` | `exp:velocity install` downloads and verifies the binary | distribution packages or the panel |
| After a PHP class or INI change | `exp:velocity deploy` (restart included) | `exp:velocity deploy` (the same command, `--engine=frankenphp`) | reload the PHP-FPM that serves the site, or `exp:velocity deploy --no-velocity` |
| Certificates | Velocity itself, including ACME | your files or self-signed; renewed by you | `mod_ssl` or nginx plus certbot or the panel |
| Boot service | a systemd unit you write (chapter 8 gives a derived one) or the engine's own units | the same | the distribution's units |
| What to monitor | `exp:velocity status --json`, `/Q/health`, the logs in `var/vc/qbix/` | `status`, Caddy's JSON logs in `var/vc/frankenphp/log/` | the server's and PHP-FPM's logs |

> **Warning**
> Under Velocity the workers keep classes, INI values and compiled templates in memory. A changed class or setting
> reaches them only after a restart. Train support staff on `exp:velocity deploy` before you sell Velocity plans
> ([Chapter 8, Deploying a change](../../../install/08-serving-the-site.md#8314-deploying-a-change-expvelocity-deploy)).

## Mixing servers

The choices are not exclusive. A common shape is Velocity serving the site while Apache or nginx stays in place on
the same files during a migration; both then share `var/`, the caches and the sessions, so they must write as the
same user. `exp:velocity deploy` brings both up to date in one run because it also reloads the PHP-FPM that serves the
site. Details: [Velocity beside Apache or nginx](velocity-beside-apache-or-nginx.md).

## A decision record to copy

```text
Plan name:                      <plan>
Server or engine:               Velocity (qbix) | FrankenPHP | Apache + PHP-FPM | nginx + PHP-FPM | other: ...
PHP version and source:         <version>, <distribution package | panel | binary>
Long-running processes allowed: yes | no
Isolation between customers:    one machine per customer | one Velocity per site | one FPM pool per site
TLS terminated by:              Velocity | FrankenPHP | Apache/nginx | load balancer or CDN
Response caching:               Velocity response cache | kernel caches only | proxy in front
Who restarts after a deploy:    <role>
Tested on staging on:           <date>, by <role>
```

## Checklist

- [ ] The plan's answer to "may a long-running process run?" is written down.
- [ ] The PHP version on the machine is 8.0 or later, also if Velocity is offered.
- [ ] Isolation between customers decided: per machine, per Velocity instance or per PHP-FPM pool.
- [ ] The four routing checks of [Web servers and engines](README.md#what-every-setup-has-in-common) pass.
- [ ] Support knows how a PHP change reaches the server (`exp:velocity deploy`, or the FPM reload).
- [ ] A tier 3 server was tested on staging before it was sold.

## Related pages

- [Web servers and engines](README.md)
- [Velocity for hosts](velocity-for-hosts.md)
- [Apache with PHP-FPM](apache-with-php-fpm.md)
- [Choosing a hosting model](../models/choosing-a-hosting-model.md)
- [Choosing hosting, database and operating system](../../leadership/choosing-hosting-database-and-os.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
