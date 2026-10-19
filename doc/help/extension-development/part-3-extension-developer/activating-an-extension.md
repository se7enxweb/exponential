---
title: Activating an extension
audience: developer, app-creator, admin
category: extension-development
order: 40
summary: Switching an extension on for the installation or one siteaccess, what it writes, and the steps that make the running site see it: autoloads, caches, PHP-FPM and Velocity.
keywords: activate, ActiveExtensions, ActiveAccessExtensions, setup extensions, exp:ini add, ezpgenerateautoloads, clear cache, velocity deploy, deploy, deactivate
type: help
---
# Activating an extension

Part 3, chapter 4. An extension on disk does nothing until it is **active**. This chapter shows the two ways to
activate one, what each writes, and the steps after activation that make the running site see your code. Users of the
administration find the same in [The Extensions page](../../../guides/extensions-page.md).

## Installation wide or one siteaccess

| Setting (`site.ini [ExtensionSettings]`) | Where | Scope |
|---|---|---|
| `ActiveExtensions[]` | `settings/override/site.ini.append.php` | every siteaccess |
| `ActiveAccessExtensions[]` | `settings/siteaccess/<sa>/site.ini.append.php` | that siteaccess only |

Use `ActiveAccessExtensions[]` for an extension that belongs to one face only: an administration tool, one site's
design. Everything else is installation wide.

## In the browser

**Setup > Extensions**: press **Activate** on the card, then **Review changes** (the page shows the risks and the
resulting lines) and **Apply changes**. The page writes the override file as text, keeps comments, keeps a backup copy
and clears the caches that depend on the list.

## On the command line

```bash
./console exp:ini add 'site.ini/ExtensionSettings/ActiveExtensions[]' mysite_hello global --dry-run
./console exp:ini add 'site.ini/ExtensionSettings/ActiveExtensions[]' mysite_hello global
```

For one siteaccess:

```bash
./console exp:ini add 'site.ini/ExtensionSettings/ActiveAccessExtensions[]' mysite_hello siteaccess:admin
```

`exp:ini add` appends to the end of the list. Where in the list an extension stands matters; see
[Extension load order](extension-load-order.md).

## After activating: make the system see it

```bash
php bin/php/ezpgenerateautoloads.php -e
php bin/php/ezcache.php --clear-all
```

1. **Autoloads**: your classes are added to `var/autoload/ezp_extension.php`. Expected output:
   `Found <n> classes, added <n> of them to the autoload array.` (`-n` shows the count without writing.)
2. **Caches**: the INI cache (your settings), the template override and design caches (your templates), the content
   cache (pages built before).
3. **The PHP processes**: PHP-FPM workers with OPcache may keep old files for a few seconds; Velocity workers keep
   the classes and some settings they loaded at start and need a restart.

On an installation served by Velocity one command does all of it in the right order:

```bash
./console exp:velocity deploy
```

It regenerates the extension autoloads, clears the INI, template, override, translation and design caches, makes
PHP-FPM see the new code, restarts Velocity, then clears the content and HTTP caches and prints PASS or FAIL per step.
See [Deploying](../../../guides/deploying.md).

## Check that it is active

```bash
./console exp:ini get site.ini/ExtensionSettings/ActiveExtensions
php bin/php/checkclasses.php --quiet-ok
```

`checkclasses.php` loads every class of the installation and lists the ones PHP refuses; no output is good. The RAD
health check on `/setup/rad` lists settings of your extension that name a class that cannot load.

## Deactivating

Remove the line (`exp:ini rem 'site.ini/ExtensionSettings/ActiveExtensions[]' mysite_hello global`) or press
**Deactivate**, then regenerate autoloads and clear caches as above. Deactivating does not remove the extension's
database tables, content classes or content: an extension that created them should document how to remove them.

## What can go wrong

| Symptom | Cause |
|---|---|
| A module of the extension says "not found" | caches not cleared, or `ExtensionRepositories[]` missing in its `module.ini.append.php` |
| `Class ... not found` | autoloads not regenerated, or the PHP processes still run old code |
| Works on the command line, not in the browser | PHP-FPM or Velocity not reloaded |
| The site breaks right after activation | a class that does not load on PHP 8; run `checkclasses.php`, deactivate, fix (Part 4) |

## Related pages

- Previous: [extension.xml: metadata and dependencies](extension-xml.md)
- Next: [Extension load order](extension-load-order.md)
- [The Extensions page](../../../guides/extensions-page.md), [Deploying](../../../guides/deploying.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
