# Setting up the request shield

This guide installs the request shield (`exp_request_shield`) on an existing Exponential site, gives the site its
own rules, checks and tests them, explains when to monitor and when to enforce, how to pause the shield, how to
update its library and how to remove it again. It is written for administrators and hosting staff with shell access
to the installation.

[Guides](README.md) · Feature reference: [The request shield](../features/6.0/request-shield.md) · Upgrade notes:
[Upgrading to 6.0.15: the request shield](../bc/6.0/request-shield.md) · In the help:
[Request shield: start here](../help/request-shield/README.md)

## In short

- Install: `composer require se7enxweb/exp_request_shield`, `ActiveExtensions[]=exp_request_shield`, then the hook
  line in `config.php` above the HTTP cache's early exit. Under Velocity, restart it.
- Give the site its own rule file in `settings/request-shield/`, with copies of the three `exponential*.rules` beside
  it, its own host names and `www.example.org`.
- After every change: `bin/request-shield check` and `test`. To see why a request was decided: `trace`.
- The shield enforces as installed. Watch **Setup > Request shield > Log** in the first days; switch to monitor
  while you look into something; add exceptions with an `expect` example each.
- Off at once: `var/request-shield/PAUSED`, or **Pause** in the administration.

## Contents

1. [Install it on an existing site](#1-install-it-on-an-existing-site)
2. [Give the site its own rules](#2-give-the-site-its-own-rules)
3. [Check, test and trace](#3-check-test-and-trace)
4. [Monitor or enforce](#4-monitor-or-enforce)
5. [Add an exception](#5-add-an-exception)
6. [Pause it](#6-pause-it)
7. [Update the library](#7-update-the-library)
8. [Uninstall](#8-uninstall)
9. [Related pages](#9-related-pages)

## 1. Install it on an existing site

Requirements: Exponential 6.0 and PHP 7.4 to 8.5. Under Exponential Velocity the site needs the 6.0.15 line of
`index.php`, which runs the file named in `EXP_REQUEST_PREPEND_FILE`; on an older `index.php` the shield protects
the PHP-FPM requests only. APCu is optional.

1. Get the package, from the installation's root:

   ```bash
   composer require se7enxweb/exp_request_shield
   ```

   The legacy installer puts it in `extension/exp_request_shield/`. On an installation from
   `se7enxweb/exponential` `main` it is already there, because the root `composer.json` requires it.

2. Activate the extension in `settings/override/site.ini.append.php`:

   ```ini
   [ExtensionSettings]
   ActiveExtensions[]=exp_request_shield
   ```

3. Regenerate the autoloads and clear the caches:

   ```bash
   php bin/php/ezpgenerateautoloads.php -e
   php bin/php/ezcache.php --clear-all
   ```

   On an installation with the `exp:velocity deploy` command, `./console exp:velocity deploy --packer` does both,
   reloads PHP-FPM and restarts Velocity.

4. Give the site its own rules ([section 2](#2-give-the-site-its-own-rules)) and check them
   ([section 3](#3-check-test-and-trace)) before the next step: from then on the rules decide every request.

5. Add the hook to `config.php` (next to `index.php`), **above** the line that loads the HTTP cache's early exit
   (`ezphttpcacheearlyexit.php`), so that cached pages are protected too:

   ```php
   if ( is_file( __DIR__ . '/extension/exp_request_shield/bootstrap/hook.php' ) )
       require __DIR__ . '/extension/exp_request_shield/bootstrap/hook.php';
   ```

   `config.php-RECOMMENDED` has these lines, with their comment, in the section **REQUEST SHIELD**. Under PHP-FPM
   the next request is filtered; nothing to restart. Under Velocity restart it, because its workers read
   `config.php` once:

   ```bash
   ./console exp:velocity restart
   ```

6. See it work. Open **Setup > Request shield**: the dashboard says whether the shield ran for that page, in which
   mode, from which rule file, and what it decided in the last 24 hours. From a shell:

   ```bash
   curl -sI https://www.example.org/settings/site.ini      # 404, decided by the shield
   tail -f var/request-shield/log/shield.log
   ```

The extension and the hook work without each other: without the hook the administration shows the setup but
nothing is filtered; without the activated extension the hook still filters, but there is no admin page.

## 2. Give the site its own rules

The extension's own `rules/request-shield.rules` is used until the site has its own file, and every update of the
extension replaces it. Give the site its own:

- **From the administration**: Setup > Request shield, any mode button (**Enforce**, **Monitor** or **Off**). The
  first switch makes `settings/request-shield/request-shield.rules` from the default and copies the three
  `exponential*.rules` next to it. The web server must be able to write `settings/request-shield/`.
- **By hand**, as the user the site's PHP runs as:

  ```bash
  mkdir -p settings/request-shield
  cp extension/exp_request_shield/rules/request-shield.rules \
     extension/exp_request_shield/rules/exponential*.rules settings/request-shield/
  ```

The copies sit beside the main file because an `include` may not leave its file's directory with `..`; the library
refuses it. Then edit `settings/request-shield/request-shield.rules`:

1. **Pick the admin's file**: `include exponential-admin-uri.rules` when the administration is the siteaccess
   `/admin`, `include exponential-admin-host.rules` when it has a host name of its own.
2. **Fix the paths**: relative paths are relative to the rule file, and from `settings/request-shield/` the
   installation's root is two levels up (the extension's default says three):

   ```
   set store-dir ../../var/request-shield/store
   set log       ../../var/request-shield/log/shield.log
   ```

3. **Name the site's host names**, so that a request for any other name is refused, and keep `www.example.org`:

   ```
   host shop.example.com example.com admin.example.com
   host www.example.org                  # the documentation domain the shipped examples use
   ```

   The library's Exponential examples send `Origin: https://www.example.org`; without that host line
   `bin/request-shield test` fails five of them (403 by `EXP-ORIGIN`). The domain is reserved for documentation,
   so no real visitor ever sends it.
4. **Name the proxy or load balancer** in front, if there is one: `trust 10.0.0.0/8`. Without it every visitor
   seems to come from the proxy's address.
5. **Adapt the site-specific rules**, each one line in `exponential.rules`, replaced in your main file with
   `replace [ID] ...`: `EXP-MODULES` (the URI siteaccesses that may stand in front, for example `(/(ger|eng))?`),
   `EXP-POST` (the modules of your extensions that take forms), `EXP-SEARCHES` (the search budget).

A shorter site file, for reference:

```
ids SITE
version 2026-11-12.1

include exponential-admin-uri.rules

set mode enforce
host www.example.org example.org

set store-dir ../../var/request-shield/store
set log       ../../var/request-shield/log/shield.log
set log-level flag

[SITE-DASH] restrict /rs/** to 127.0.0.1 ::1
expect GET /rs/stats/overview from 198.51.100.27 403 by SITE-DASH
```

To load a file somewhere else, define `EXP_REQUEST_SHIELD_RULES` in `config.php` **before** the hook line. The
other constants (`EXP_REQUEST_SHIELD_ROOT`, `EXP_REQUEST_SHIELD_VAR`, `EXP_REQUEST_SHIELD_DISABLED`) are described in
the extension's [doc/configuration.md](https://github.com/se7enxweb/exp_request_shield/blob/main/doc/configuration.md).
Every line of the rule language: the library's
[rule file reference](https://github.com/cjw-network/request-shield/blob/main/docs/reference/rule-files.md).

The web server must never serve `settings/` or `var/request-shield/`; a standard Exponential virtual host refuses
both.

## 3. Check, test and trace

Run the command line as the user the site's PHP runs as (`sudo -u <web user> ...` when you are root), so that the
files it writes stay readable for the site:

```bash
php extension/exp_request_shield/bin/request-shield rules
php extension/exp_request_shield/bin/request-shield check settings/request-shield/request-shield.rules
php extension/exp_request_shield/bin/request-shield test  settings/request-shield/request-shield.rules
```

- `rules` prints the main rule file the site uses and where it comes from.
- `check` reads everything, names any mistake by file and line, warns about risky setups (the library's `/rs`
  pages open to everyone, for example) and prints the store's tier.
- `test` decides every `expect` example written next to the rules. It must pass after every change.

To see how one request is decided, rule by rule:

```bash
php extension/exp_request_shield/bin/request-shield trace settings/request-shield/request-shield.rules "GET https://www.example.org/the/path"
```

`check` and `test` compile the settings next to the rule file (`.request-shield/` beside it); the web requests use
`var/request-shield/cache/`. Setup > Request shield > Rules shows the same result in the administration.

## 4. Monitor or enforce

The rules ship in **enforce** mode: as soon as the hook is in `config.php`, scanners, floods, stray forms and system
URLs are refused, and the login and the admin's forms ask for the browser check (a second's work for a real
browser, once per pass). Rules marked `monitor` in the rule files (`EXP-BAN`, `EXP-STRICT`) only watch.

In **monitor** mode every decision is logged as it would be taken (`monitor-reject`, `monitor-challenge` ...) and
nobody is refused. Use it:

- on a site with many unusual addresses (page aliases that start with a module name, forms of its own extensions),
  for the first days, while editors work and the search is used;
- while you look into a complaint that a real visitor was refused.

Switch with **Enforce** and **Monitor** on the dashboard (policy `requestshield/manage`), or write `set mode monitor`
or `set mode enforce` in the site's main file. Every server reads a changed rule file within its `recheck` seconds;
nothing to restart. Read **Setup > Request shield > Log**, decision group **Watched (monitor)**, and go back to enforce when
the log shows only what you want refused.

## 5. Add an exception

When a real visitor or editor was refused (the log names the rule, for example 404 by `EXP-MODULES` or
`EXP-SYSVIEW` for a page alias that starts with a module name):

1. Find the line in **Setup > Request shield > Log**, or `trace` the request.
2. Add an exception for it in the site's main file, with an `expect` example of the address that must pass, and
   one of a near miss that must still be refused. The library's
   [Exponential use case](https://github.com/cjw-network/request-shield/blob/main/docs/use-cases/exponential.md)
   shows the patterns.
3. Run `check` and `test`.

For an editor who keeps getting the browser check in the administration: allow JavaScript in the browser, or name a
fixed office address with `exempt <address>`.

## 6. Pause it

| How | Effect | Undo |
|---|---|---|
| **Pause** on the dashboard | creates `var/request-shield/PAUSED`: the shield does nothing, on every server, from the next request | **Switch on** |
| `touch var/request-shield/PAUSED` | the same, from a shell, without editing anything | remove the file |
| **Off**, or `set mode off` in the rules | nothing is decided; the rule file is changed | **Enforce** or **Monitor** |
| `define( 'EXP_REQUEST_SHIELD_DISABLED', true );` before the hook line | the hook does nothing | remove the line; restart Velocity |

Each switch from the administration is written to `var/log/requestshield.log` with the user's login and id and the
outcome.

## 7. Update the library

The library comes with the extension; update the **extension**, never the bundled files with the library's
`self-update`, so the files, `ezinfo.php` and the changelog stay together:

```bash
composer update se7enxweb/exp_request_shield
```

Then run `check` and `test` on the site's rules, and compare the three copies in `settings/request-shield/` with
`extension/exp_request_shield/rules/`: take the new versions over when they changed.

Under Velocity, a worker that already loaded the old classes must not meet the new files: create
`var/request-shield/PAUSED`, update, `./console exp:velocity restart`, then remove `PAUSED`.

Maintainers who build a new release of the extension from a new library tag use `bin/update-library.sh`, which
builds the single files and the PHP 7.4 build and checks the rules on every PHP named:
[doc/library.md](https://github.com/se7enxweb/exp_request_shield/blob/main/doc/library.md).

## 8. Uninstall

1. Remove the hook lines from `config.php`; under Velocity, `./console exp:velocity restart`.
2. Remove `ActiveExtensions[]=exp_request_shield`, then `composer remove se7enxweb/exp_request_shield` (or remove
   the directory). On an installation whose root `composer.json` requires the package, leave it installed and only
   take out the hook and the activation.
3. Regenerate the autoloads and clear the caches.
4. Remove `var/request-shield/` and `settings/request-shield/` when you no longer need the log, the statistics and
   the rules.

## 9. Related pages

- [The request shield](../features/6.0/request-shield.md) (feature reference)
- [Upgrading to 6.0.15: the request shield](../bc/6.0/request-shield.md)
- [Request shield: start here](../help/request-shield/README.md) and its
  [troubleshooting](../help/request-shield/troubleshooting.md)
- [The HTTP cache with request-shield in front](../bc/6.0/httpcache.md#with-request-shield-in-front)
- [Security and audit](security-and-audit.md)
- [Deploying](deploying.md)
