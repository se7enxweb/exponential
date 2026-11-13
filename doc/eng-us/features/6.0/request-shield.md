# The request shield (exp_request_shield)

Read this page if you want to know what the request shield in front of an Exponential site does, why it is there,
how it decides a request under PHP-FPM and under Exponential Velocity, which files it keeps, what the administration
shows of it, and from which release on a site gets it.

Related: [Setting up the request shield](../../guides/request-shield.md) (guide),
[Upgrading to 6.0.15: the request shield](../../bc/6.0/request-shield.md) (upgrade notes),
[Request shield: start here](../../help/request-shield/README.md) (help for administrators),
[The log viewer](log-viewer.md), [Request logs](request-logs.md).

## In short

- **exp_request_shield** is a small web application firewall in front of the site. It decides every request
  **before the kernel starts**: it turns away scanners, floods and stray form posts, refuses system URLs and
  internal files, keeps page caches clean and asks suspicious clients for a **browser check**. A request costs a
  few microseconds; it works on any hosting, shared hosting included.
- The filter itself is [request-shield](https://github.com/cjw-network/request-shield) by JAC Systeme GmbH
  (CJW Network), MIT License. The extension bundles **request-shield 0.4.0** as single PHP files, plus a build of the
  same files for PHP 7.4. Nothing else to install, no Composer autoloader needed.
- Package: [`se7enxweb/exp_request_shield`](https://packagist.org/packages/se7enxweb/exp_request_shield) on
  Packagist, version **0.0.1.0**. Supports **PHP 7.4 to 8.5**. The extension is licensed **GPL-2.0-or-later**; the
  bundled library and the three Exponential rule files copied from it stay under their **MIT** License.
- **One line in `config.php`** switches it on, above the HTTP cache's early exit, so cached pages are protected too.
  It runs under Apache or nginx with **PHP-FPM** and under **Exponential Velocity**, with identical answers.
- The rules ship in **enforce mode**: the protection is on as installed. `set mode monitor` logs every decision
  without refusing anybody; the file `var/request-shield/PAUSED` switches the shield off at once.
- **Setup > Request shield** in the administration (right after **Log files**): a dashboard with a chart of the
  decisions per hour, the log, the rule files, and switches to pause, switch on, enforce, monitor or switch off.
- The library's command line, `extension/exp_request_shield/bin/request-shield`, checks, tests and traces the rules.

## Contents

1. [Why a filter before the kernel](#why-a-filter-before-the-kernel)
2. [What it decides](#what-it-decides)
3. [How a request goes through it](#how-a-request-goes-through-it)
4. [PHP-FPM and Velocity](#php-fpm-and-velocity)
5. [Rule files](#rule-files)
6. [Modes and switching it off](#modes-and-switching-it-off)
7. [Files on disk](#files-on-disk)
8. [In the administration](#in-the-administration)
9. [The command line](#the-command-line)
10. [Licensing](#licensing)
11. [Who gets it, and when](#who-gets-it-and-when)
12. [Related pages](#related-pages)

## Why a filter before the kernel

Most unwanted requests a public site receives are not visitors: scanners that try `/wp-login.php` and
`/.env`, bots that fetch every page with every query parameter, form posts from nowhere, floods from one network.
Each of them used to start the whole kernel: the autoloader, the INI files, the database connection, often a page
rendering, and sometimes a new entry in the page cache under an address no real visitor ever uses.

The request shield decides these requests before any of that happens, from rules compiled once into a PHP file
that OPcache keeps. A refused request costs a few microseconds instead of a page rendering; a request the shield
lets through goes on unchanged. Because it runs before the HTTP cache's early exit, it also keeps the cache free of
made-up addresses.

## What it decides

The shipped rules are the library's rule set for Exponential (`exponential.rules` and one of the two admin files).
Among other things they:

- refuse **system URLs** on the public host (`/content/view/full/2`, module addresses a visitor has no reason to
  call) and **internal files** (`settings/`, `var/` outside the served storage, `.git`, `.env` ...);
- accept **form posts** only for the modules that take forms, and only from the site's own pages;
- limit the **pace** per network, the **search** budget and the attempts at the **login**;
- refuse requests for a **host name** the site does not have;
- ask the **browser check** where a real browser is expected: at the login, in the administration once per pass,
  and for clients that ask too often. A real browser solves it by itself in a fraction of a second, without a
  puzzle, and then holds a pass cookie;
- name every decision by a **rule ID** (`EXP-SYSVIEW`, `EXP-LOGIN`, `SITE-DASH` ...) in the log.

Why each rule exists is explained in the library's
[Exponential use case](https://github.com/cjw-network/request-shield/blob/main/docs/use-cases/exponential.md);
the browser check in [The browser check, explained](https://github.com/cjw-network/request-shield/blob/main/docs/explained/browser-check.md).

## How a request goes through it

```
config.php
  └─ extension/exp_request_shield/bootstrap/hook.php
       ├─ PHP-FPM (Apache, nginx): prepend.php runs now
       ├─ Exponential Velocity: EXP_REQUEST_PREPEND_FILE = prepend.php,
       │    which index.php runs at the start of every request
       └─ any other command line: nothing
prepend.php
  └─ expRequestShield::protect()
       ├─ var/request-shield/PAUSED exists: nothing
       ├─ load lib/requestshield/*.php (once per process)
       └─ decide the request with the main rule file
            ├─ allow: on to the HTTP cache's early exit, then the kernel
            └─ refuse, browser check, the shield's own page: the shield answers, the request ends
```

Every entry point that reads `config.php` is covered: `index.php`, the REST and tree menu entry points, ezjscore
calls.

The rule files are compiled once into `var/request-shield/cache/` and compiled again when a rule file changes
(checked every `recheck` seconds, 10 by default). A request then costs one include of the compiled settings and the
checks themselves.

**It fails open.** When the shield itself fails (a rule file that does not compile, a store it cannot write), the
request goes on to the site, uncached, and PHP's error log gets one line a minute. A broken shield never takes the
site down; Setup > Request shield shows the error.

## PHP-FPM and Velocity

| | PHP-FPM (Apache, nginx) | Exponential Velocity |
|---|---|---|
| Where the shield runs | in `config.php`, on every request | `config.php` runs once per worker, so the hook names `bootstrap/prepend.php` in `EXP_REQUEST_PREPEND_FILE`, and `index.php` runs it at the start of every request |
| Needs | nothing from the kernel | an `index.php` that runs `EXP_REQUEST_PREPEND_FILE` (the 6.0.15 line); an older `index.php` never runs it, so Velocity is not filtered while PHP-FPM still is |
| After adding or removing the hook | nothing to restart | `./console exp:velocity restart` |
| After a rule change | nothing: read again within `recheck` seconds | the same |
| Counters | in the store | `set store file` shares them between all workers and PHP-FPM |

The answers are **identical**: the same status, the same headers, the same log line. In a Velocity worker `exit()`
throws Velocity's exit signal so that it ends the request and not the worker; the extension lets that signal pass,
so a refusal, the browser check and the shield's own pages answer exactly as under PHP-FPM, without a false
"shield failed" line in PHP's error log. The details are in the extension's
[doc/velocity.md](https://github.com/se7enxweb/exp_request_shield/blob/main/doc/velocity.md).

## Rule files

The shield is configured in **rule files**, plain text that the library reads. It runs before the kernel, so it
never reads an INI file; `requestshield.ini` only holds settings of the admin pages.

The main rule file is the first that applies:

| Order | File | When |
|---|---|---|
| 1 | the file named by the constant `EXP_REQUEST_SHIELD_RULES` | defined in `config.php` before the hook line |
| 2 | `settings/request-shield/request-shield.rules` | the site has its own file |
| 3 | `extension/exp_request_shield/rules/request-shield.rules` | the extension's default |

A real site uses its own file (2): updates of the extension replace the extension's files. The three
`exponential*.rules` files are copied next to the site's main file, because an `include` may not leave its file's
directory with `..`. The first mode switch in the administration makes these copies by itself; by hand it is two
commands ([the guide](../../guides/request-shield.md#2-give-the-site-its-own-rules)).

| Shipped file | What it is |
|---|---|
| `rules/request-shield.rules` | the default main file: includes the Exponential rules, enforce mode, the store and the log below `var/request-shield/`, the library's own pages (`/rs`) closed to all but the server |
| `rules/exponential.rules` | the frontend: system URLs, internal files, view parameters, forms, the search, the pace. Included, never loaded on its own |
| `rules/exponential-admin-uri.rules` | the admin as the siteaccess `/admin` (URI matching). Includes `exponential.rules` |
| `rules/exponential-admin-host.rules` | the admin on a host of its own (`admin.example.org`). Includes `exponential.rules` |

Keep `www.example.org` in a `host` line of the site's file: the shipped examples (`expect` lines) send it, and
`bin/request-shield test` fails five of them without it. It is a domain reserved for documentation; no real visitor
sends it.

Every line of the rule language is documented upstream:
[rule files](https://github.com/cjw-network/request-shield/blob/main/docs/reference/rule-files.md) and
[settings](https://github.com/cjw-network/request-shield/blob/main/docs/reference/settings.md).

## Modes and switching it off

| Mode | Effect |
|---|---|
| `enforce` | the decisions are carried out; rules marked `monitor` stay watched only. **As installed.** |
| `monitor` | every decision is logged as it would be taken (`monitor-reject`, `monitor-challenge` ...); nobody is refused |
| `strict` | enforce, and the rules marked strict too |
| `off` | nothing is decided |

To switch the shield off at once, on every server, without editing anything, create `var/request-shield/PAUSED`;
remove it to switch the shield on again. For good, remove the hook lines from `config.php` (and restart Velocity).
The constant `EXP_REQUEST_SHIELD_DISABLED` (defined as `true` before the hook line) also makes the hook do nothing.

## Files on disk

| Path | What | Written by |
|---|---|---|
| `extension/exp_request_shield/lib/requestshield/` | the library (and `php74/`, its PHP 7.4 build) | the package |
| `settings/request-shield/` | the site's rule files | you, or the first mode switch |
| `var/request-shield/cache/` | the compiled settings | the shield |
| `var/request-shield/store/` | counters, the secret of the pass cookie, lists, statistics | the shield |
| `var/request-shield/log/shield.log` | one line per decision | the shield |
| `var/request-shield/backup/` | the rule file as it was before each mode switch | the admin's switches |
| `var/request-shield/PAUSED` | switches the shield off while it exists | you, or **Pause** |
| `var/log/requestshield.log` | who switched what, and the outcome | the admin's switches |

All of `var/request-shield/` must be writable by the user the site's PHP runs as. The web server must never serve
`settings/` or `var/request-shield/`; a standard Exponential virtual host refuses both. The log keeps a client's
network (IPv4 /24, IPv6 /48), never a person's full address.

## In the administration

**Setup > Request shield** (module `requestshield`, in admin4's Setup menu in the **System** group, right after
**Log files**) has three pages and the switches:

| Page | View | What it shows |
|---|---|---|
| Dashboard | `requestshield/dashboard` | notices (rules unreadable, paused, did not run for this page, monitor mode), the figures, a chart of the decisions per hour, the setup (library version, rule file, store tier, log), the rules that decided most, the newest log lines |
| Log | `requestshield/log` | the log lines of a period, filtered by decision, a page at a time, with view parameters such as `/requestshield/log/(hours)/72/(group)/refused` |
| Rules | `requestshield/rules` | whether the rules compile, the main file and the files it includes, and the commands that check them |
| Switches | `requestshield/switch` | **Pause** / **Switch on**, **Enforce**, **Monitor**, **Off** at the top of the dashboard |

| Policy | Allows |
|---|---|
| `requestshield/read` | every page |
| `requestshield/manage` | the switches |

Only the Administrator role holds them in a new installation. A mode switch keeps a backup of the rule file,
compiles the result and puts the old file back when the new one does not compile. The pages exist in the admin4
and admin4l designs, exp_adminui's adminui design and the classic admin design, in light and dark mode, without
inline CSS or script. The help for administrators: [Request shield: start here](../../help/request-shield/README.md).

## The command line

Run it as the user the site's PHP runs as, so that the files it writes stay readable for the site:

```bash
php extension/exp_request_shield/bin/request-shield rules                     # which main file the site uses
php extension/exp_request_shield/bin/request-shield check settings/request-shield/request-shield.rules
php extension/exp_request_shield/bin/request-shield test  settings/request-shield/request-shield.rules
php extension/exp_request_shield/bin/request-shield trace settings/request-shield/request-shield.rules "GET https://www.example.org/the/path"
```

`check` names any mistake by file and line, `test` decides every `expect` example next to the rules, `trace` shows
how one request is decided, rule by rule. Every command:
[the library's CLI reference](https://github.com/cjw-network/request-shield/blob/main/docs/reference/cli.md).
Do not use `self-update` on the bundled files; update the extension instead.

## Licensing

| Part | License |
|---|---|
| The extension: bootstrap, classes, admin module, templates, stylesheet, settings, command line wrappers, the default `rules/request-shield.rules`, documentation, translations | GNU General Public License v2.0 or any later version (GPL-2.0-or-later), Copyright (C) 1998 - 2026 Exponential Foundation and 7x |
| `lib/requestshield/*.php`, `lib/requestshield/php74/*.php` | MIT License, Copyright (c) 2026 JAC Systeme GmbH (CJW Network) |
| `rules/exponential.rules`, `rules/exponential-admin-uri.rules`, `rules/exponential-admin-host.rules` | MIT License, copied unchanged from request-shield's `examples/exponential/` |

The MIT License is compatible with the GPL v2 and allows the files to be distributed with the extension.

## Who gets it, and when

| Installation | What it gets |
|---|---|
| Any Exponential 6.0 site, now | `composer require se7enxweb/exp_request_shield`, `ActiveExtensions[]=exp_request_shield` and the hook line in `config.php` ([the guide](../../guides/request-shield.md)) |
| A new installation from `se7enxweb/exponential` `main` | `composer.json` requires the package, and `config.php-RECOMMENDED` loads the hook, so a new `config.php` made from it is protected from the first request |
| An existing installation updated to the `main` line | the package arrives with `composer.json`, but the site's `config.php` is not changed: add the hook line yourself ([upgrade notes](../../bc/6.0/request-shield.md)) |
| The 6.0.15 site packages `sevenx_site`, `sevenx_multisite`, `cjw_multisite` and their `_clean` variants | activate the extension during the installation |
| `composer create-project` of the distributions | resolves to v6.0.14 until a 6.0.15 release is tagged, so the shield is not included; add it with `composer require` |

Under Velocity, a site on 6.0.14 has no `EXP_REQUEST_PREPEND_FILE` in its `index.php`: the shield protects its
PHP-FPM requests, not its Velocity requests, until the site runs the 6.0.15 line.

## Related pages

- [Setting up the request shield](../../guides/request-shield.md)
- [Upgrading to 6.0.15: the request shield](../../bc/6.0/request-shield.md)
- [Request shield: start here](../../help/request-shield/README.md)
- [The HTTP cache with request-shield in front](../../bc/6.0/httpcache.md#with-request-shield-in-front)
- [Security hardening](../../help/security-hardening.md)
- [Content security policy](content-security-policy.md)
- [Glossary](../../glossary.md#r)
- [Changelog of 6.0.15](../../../changelogs/6.0/6.0.15.md)
- The extension's own documentation: [README](https://github.com/se7enxweb/exp_request_shield/blob/main/README.md),
  [INSTALL](https://github.com/se7enxweb/exp_request_shield/blob/main/INSTALL.md)
- The library: [request-shield documentation](https://github.com/cjw-network/request-shield/tree/main/docs)
