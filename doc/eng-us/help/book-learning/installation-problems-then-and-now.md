---
title: "Installation problems, then and now"
audience: user, developer, admin, builder
category: books
order: 120
summary: The two problems the Learning book's first chapter troubleshoots (PHP memory limit, PHP as CGI) and how they look in Exponential 6, with where to look first.
keywords: troubleshooting, memory limit, memory_limit, CGI, FPM, front page for every link, php.ini, error log, Learning book
type: help
---
# Installation problems, then and now

The book ends its first chapter with two common problems. Both still happen in some form.

> **Warning**
> **`.htaccess` slows down every request.** When `.htaccess` files are enabled (`AllowOverride` anything but `None`), let alone
> used, Apache looks for them and reads and evaluates them on every request, in every directory of the path, and
> page loading times go up drastically ([measured](../../guides/setup-files-from-the-console.md#measured-htaccess-against-rules-in-the-virtual-host)). The only real fix is to switch the feature off for
> the virtual host: `AllowOverride None` in the HTTPS virtual host, and the rules in the virtual host's own
> configuration as mod_rewrite rules. That is far faster in production speed tests and well worth the time it takes
> to optimize a production configuration. Switch over right away: copy the rules of the
> [example HTTPS virtual host](../../examples/web-servers/apache-virtual-host.md) into your virtual host (with the static cache, also
> [`staticcache_offload.conf`](../../examples/staticcache_offload.conf)), set `AllowOverride None`, run `apachectl configtest`, reload Apache and
> test the site. The same rules for other servers: [nginx](../../examples/web-servers/nginx.md), [LiteSpeed](../../examples/web-servers/litespeed.md),
> [Caddy](../../examples/web-servers/caddy.md), [IIS](../../examples/web-servers/iis.md), [Velocity](../../examples/web-servers/velocity.md) (built in); where virtual host rules go in a control
> panel: [Plesk](../hosting/panels/plesk.md), [cPanel](../hosting/panels/cpanel.md), [DirectAdmin](../hosting/panels/directadmin.md), [ISPConfig](../hosting/panels/ispconfig.md).

## PHP memory limit

**Then.** The default PHP limit was 8 MB, too little. The symptom was a fatal "Allowed memory size ... exhausted"
message. The book raised it to 16 MB with `php_value memory_limit "16M"` in an `.htaccess` file and said that
32 MB would be plenty.

**Now.** Exponential needs far more, and the setup wizard's system check looks at it: below 128 MB is a failure and
below 256 MB a warning ([System information](../../guides/system-information.md)). Set `memory_limit` where PHP
reads its settings, which depends on how PHP runs:

| PHP runs as | Where to set it |
|---|---|
| PHP-FPM or CLI | `php.ini` of that PHP, or a pool file; reload FPM afterwards |
| Apache module | `php.ini`, or `php_value` in `.htaccess` (only when the host allows it) |
| Exponential Velocity | the PHP that starts it; its own `php.ini` |

`php -i | grep memory_limit` shows the command line value; the page **Setup > System information** shows the value the
web server's PHP uses, which is the one that matters for the site (the two are often different).
A command line script such as an import may need more than a page does; run it with
`php -d memory_limit=1G bin/php/<script>.php`.

## PHP running as CGI

**Then.** Whatever link you clicked, you got the front page. The cause was how old versions read the request path
when PHP ran as CGI instead of an Apache module, and the fix was to edit one kernel file.

**Now.** This symptom is rare with PHP-FPM or an Apache module. When you see the front page for every address,
look at the rewrite rules first: all requests that are not a real file must reach `index.php` (see the shipped
`.htaccess`, and the row "404 for every page under Apache" in
[Troubleshooting](../../install/12-troubleshooting.md#129-pages-and-errors-at-run-time)). The page **Setup >
System information** shows how the web server runs PHP (the SAPI), which is the way to tell a module from FPM or CGI.


## Where to look first

1. `var/log/error.log` ([Where the logs are](../../install/12-troubleshooting.md#122-where-the-logs-are)).
2. The debug output for yourself, not for visitors.
3. [Installation troubleshooting](../../install/12-troubleshooting.md), the full list by symptom.

Back to [the Learning book chapters](README.md).


## Source and license

Adapted from chapter 1, "Installing eZ publish", of *Learning eZ publish: Building Content Management Solutions* by Paul Borgermans, Tony Wood,
Paul Forsyth, Martin Bauer, Björn Dieding and Ben Pirt (additional material by Bruce Morrison), second edition, 2024,
[Book-Learning-eZPublish](https://github.com/se7enxweb/Book-Learning-eZPublish). Copyright (c) 2004-2024 7x and Graham
Brookins. Licensed under the GNU Free Documentation License, version 1.3 or any later version, with no Invariant
Sections, no Front-Cover Texts and no Back-Cover Texts ([the license](../../LICENSE-GFDL-1.3.txt)).
Changes: both problems restated for Exponential 6 with current limits and tools.
