---
title: Web servers and engines
audience: hosting
category: hosting
order: 10
summary: Overview of the web server section for hosting companies: which servers and engines serve Exponential, how they compare, and a one-line guide to every page of the section.
keywords: web server, engine, Velocity, qbix, FrankenPHP, Apache, nginx, Caddy, LiteSpeed, OpenLiteSpeed, IIS, PHP-FPM, overview, hosting
type: help
---
# Web servers and engines

This section is for system administrators and product managers at hosting companies who must decide what serves
an Exponential site on their machines, and set it up. It answers: which web servers and engines Exponential runs
behind, which one the project recommends, which one fits which hosting model, and where the exact settings are.

Back to [Hosting companies: start here](../hosting-companies-start-here.md).

## Contents

1. [The short answer](#the-short-answer)
2. [The servers at a glance](#the-servers-at-a-glance)
3. [Pages in this section](#pages-in-this-section)
4. [What every setup has in common](#what-every-setup-has-in-common)
5. [Related pages](#related-pages)

## The short answer

- **Exponential Velocity** (the `qbix` engine) is the project's **recommended** way to serve a site at every stage,
  from development to production. It is a web server written in PHP that keeps the application loaded in persistent
  workers, answers repeat pages from its own response cache and serves HTTPS itself
  ([Chapter 8](../../../install/08-serving-the-site.md#83-exponential-velocity-the-recommended-way)).
- **FrankenPHP**, driven by the same `exp:velocity` command, is the **production-ready alternative**.
- **Apache with PHP-FPM** and **nginx with PHP-FPM** are the traditional setups. They remain fully supported and are
  what shared hosting usually offers, because Velocity needs a long-running process that shared hosting usually does
  not allow (`README.md`, "When a traditional server is still the right answer").
- Any other web server that can pass a request to PHP and rewrite a URL to `index.php` serves Exponential too
  ([Operating system support, Web servers](../../os-support.md#web-servers)).

## The servers at a glance

The tiers come from [Operating system support](../../os-support.md#web-servers).

| Server | Status in the sources | Typical hosting model | Page |
|---|---|---|---|
| Exponential Velocity (`qbix`) | recommended for every stage; tier 1 | VPS, dedicated, containers, managed | [Velocity for hosts](velocity-for-hosts.md) |
| Apache httpd 2.4 with PHP-FPM | tier 1 | shared, panels, VPS | [Apache with PHP-FPM](apache-with-php-fpm.md) |
| FrankenPHP (through `exp:velocity`) | production-ready alternative | VPS, dedicated, containers | [FrankenPHP](frankenphp.md) |
| nginx with PHP-FPM | tier 2 | VPS, dedicated | [nginx with PHP-FPM](nginx-with-php-fpm.md) |
| LiteSpeed, OpenLiteSpeed | tier 3 | shared hosting on LiteSpeed | [LiteSpeed](litespeed.md) |
| Caddy with PHP-FPM | tier 3 | VPS | [Caddy](caddy.md) |
| Microsoft IIS with FastCGI | tier 3 | Windows hosting | [IIS](iis.md) |
| PHP's built-in server | development only | never for visitors | [Choosing a web server](choosing-a-web-server.md) |

> **Note**
> Tier 3 means "expected to work": a reasoned expectation, not a test result
> ([The four tiers](../../os-support.md#the-four-tiers)). Test a tier 3 server on a staging machine before you sell
> a plan that depends on it.

## Pages in this section

| Page | What it answers |
|---|---|
| [Choosing a web server](choosing-a-web-server.md) | Which server or engine fits which plan, with a decision table |
| [Apache with PHP-FPM](apache-with-php-fpm.md) | The classic setup: `.htaccess_root`, the virtual host, PHP-FPM pools, Plesk |
| [nginx with PHP-FPM](nginx-with-php-fpm.md) | The derived server block, rule for rule, and how to check it |
| [Caddy](caddy.md) | What the sources say about Caddy, and the routing to reproduce |
| [LiteSpeed](litespeed.md) | LiteSpeed and OpenLiteSpeed with the shipped Apache rules |
| [IIS](iis.md) | Windows hosting with IIS, FastCGI and the URL Rewrite module |
| [Velocity for hosts](velocity-for-hosts.md) | Installing, configuring and running Velocity on a hosting server |
| [Velocity workers and pools](velocity-workers-and-pools.md) | Pool size, the dynamic pool, instances, memory measured as PSS |
| [Velocity HTTPS certificates](velocity-https-certificates.md) | Your own certificates, Let's Encrypt, the self-signed stand-in, HSTS |
| [FrankenPHP](frankenphp.md) | The verified binary, its embedded PHP and its limits |
| [Velocity beside Apache or nginx](velocity-beside-apache-or-nginx.md) | Running both on the same files, behind a proxy or during a migration |

## What every setup has in common

Whatever serves the site, the same rules apply ([Chapter 8, How a request is served](../../../install/08-serving-the-site.md#82-how-a-request-is-served)):

- The **document root is the installation root**, the directory that holds `index.php`.
- The routing is written down once, in `.htaccess_root`. Velocity's engines and the generated FrankenPHP
  configuration implement the same list rule for rule.
- No PHP file runs because its path was asked for, except the front controllers at the root (`index.php`,
  `index_rest.php`, `index_treemenu.php`).
- Only listed asset paths are sent as files; `settings/site.ini`, the SQLite database or a kernel source requested by
  path reach `index.php`, never the file.
- The user that runs PHP must be able to write `var/` and the `settings/` directories, and **one** user (or one
  group) should write them, whichever server runs
  ([Chapter 8, File permissions and ownership](../../../install/08-serving-the-site.md#87-file-permissions-and-ownership)).

Check the routing on any server with the four requests of chapter 8, replacing the address with your own:

```bash
curl -s -o /dev/null -w '%{http_code}\n' https://example.com/.git/config                      # 404 (403 from qbix)
curl -s -o /dev/null -w '%{http_code}\n' https://example.com/kernel/classes/ezcontentobject.php  # 404
curl -s -o /dev/null -w '%{http_code}\n' https://example.com/robots.txt                        # 200 when the file exists
curl -s https://example.com/settings/site.ini | grep -c '^\[DatabaseSettings\]'                # 0
```

A `200` for the `.git` or kernel request means the rules are not active. Stop and fix that before the site is
reachable from outside.

## Related pages

- [Hosting companies: start here](../hosting-companies-start-here.md)
- [Performance](../performance/README.md)
- [Chapter 8: Serving the site](../../../install/08-serving-the-site.md)
- [Operating system support](../../os-support.md)
- [Velocity engines](../../../bc/6.0/velocity-engines.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
