---
title: Exponential 6 as the base of a bundle
audience: bundler, developer, hosting
category: bundling
order: 40
summary: What a bundle built on Exponential 6 already contains before you add anything - the kernel, the extensions the root composer.json requires, Velocity, the default theme and the site packages the installers offer - and how to read and adjust that list.
keywords: Exponential 6, composer.json, require, suggest, default extensions, Velocity, sevenx_themes_simple, site package, sevenx_multisite, base, distribution
type: help
---
# Exponential 6 as the base of a bundle

This page is for bundlers who build on the plain Exponential 6 package. It tells you what you get "for free" before
you add your own parts, so you neither ship something twice nor miss something you thought was there.

## Contents

1. [What the root package brings](#what-the-root-package-brings)
2. [Read the list for your version](#read-the-list-for-your-version)
3. [Required versus suggested](#required-versus-suggested)
4. [What the installers add](#what-the-installers-add)
5. [What it does not bring](#what-it-does-not-bring)

## What the root package brings

`composer.json` of `se7enxweb/exponential` "decides which extensions arrive with `composer create-project`"
([The default extension distribution](../../features/6.0/default-extension-distribution.md)):

| Part | What | Source |
|---|---|---|
| The kernel | `kernel/`, `lib/`, `bin/php/`, `design/`, `settings/`, `share/` | the repository |
| Kernel-shipped extensions | `ezjscore`, `ezoe`, `ezformtoken`, `expservices` | [3.2 A](../../install/03-getting-the-code.md#a-git-clone-and-composer-install-recommended) |
| Required extensions | every `se7enxweb/*` package under `require`, each its own Composer package | `composer.json` |
| Velocity | `se7enxweb/exponential-velocity ~0.0.4.57` (through `se7enxweb/exponential-php-floor`, on PHP 8.0 and later) is required since 6.0.15 | [Velocity engines](../../bc/6.0/velocity-engines.md#velocity-is-part-of-exponential-as-of-6015) |
| The default theme | `se7enxweb/sevenx_themes_simple`, required since 6.0.10 "so the default theme is always distributed" | [Default extension distribution](../../features/6.0/default-extension-distribution.md#timeline-of-the-distribution-root-composerjson) |
| The Oracle driver | `se7enxweb/ezoracle`, required, so it is present in every installation | [Database support](../database-support.md#the-supported-databases-in-order) |
| The documentation | `doc/`, this help included | the repository |

## Read the list for your version

The list grows from release to release. Read it from the exact version you bundle, never from memory:

```bash
grep -n '"se7enxweb/' composer.json
```

"The first block of the output is `suggest`, the second `require`"
([The default extension distribution](../../features/6.0/default-extension-distribution.md#see-the-current-list)).
After `composer install`, `composer show` lists what was really installed, with versions.

## Required versus suggested

- `require` is installed with the product. Leaving a required package out of your bundle means changing
  `composer.json`; record why ([Adding and removing extensions](adding-and-removing-extensions.md)).
- `suggest` "is only a hint printed by Composer; you install a suggested package yourself with
  `composer require se7enxweb/<name>`". Examples from `composer.json`: `se7enxweb/ezfind` (Solr search),
  `se7enxweb/sevenx_valkey_cache` (Redis/Valkey caches), `se7enxweb/ezauthorize`.
- The PHP extensions in `suggest` (for example `ext-sqlite3`, `ext-pgsql`, `ext-mysqli`, `ext-gd`) are the ones the
  databases and image handling need. Your runtime decides which of them you ship
  ([Chapter 2.3 PHP extensions](../../install/02-requirements.md#23-php-extensions)).

## What the installers add

The installers build the site from a **site package**. The package index of 6.0.15 offers twelve site packages; the
default of `exp:install --package` is `sevenx_multisite`, with `sevenx_multisite_clean` as the same site without demo
content ([4.3 Site packages](../../install/04-choosing-an-install-method.md#43-site-packages-what-is-installed)).
A site package activates its own extensions in `settings/override/site.ini.append.php`. Your bundle chooses the
package; see [Site packages in a bundle](site-packages-in-a-bundle.md).

## What it does not bring

| Not included | Who provides it |
|---|---|
| PHP and its extensions | the operating system or your image |
| A database server (PostgreSQL, MariaDB ...) | you or the recipient; SQLite needs none |
| A web server other than Velocity | the operating system or your image |
| A `composer.lock` | you: the lock file "is not part of the repository" ([3.2 A](../../install/03-getting-the-code.md#a-git-clone-and-composer-install-recommended)) |
| Cron scheduling of `runcronjobs.php` | your image or panel ([Cron jobs](../hosting/operations/cron-jobs.md)) |
| Backups and monitoring | your bundle or the recipient |

## Related pages

- [Choosing the base](choosing-the-base.md)
- [Composer-based bundles](composer-based-bundles.md)
- [Chapter 3: Getting the code](../../install/03-getting-the-code.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
