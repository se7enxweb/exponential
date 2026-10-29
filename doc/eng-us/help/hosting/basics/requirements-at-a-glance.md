---
title: Requirements at a glance
audience: hosting, admin
category: hosting
order: 30
summary: Every requirement of Exponential 6.0.x on one page, with the minimum, the recommendation and what checks it: PHP, extensions, php.ini, databases in the official order, Composer, web server, image conversion, writable directories and command line access.
keywords: requirements, system requirements, minimum, PHP 8.0, PHP 8.1, PHP 8.5, extensions, php.ini, memory_limit, database, SQLite, PostgreSQL, MongoDB, MariaDB, Oracle, MySQL, Composer, web server, Velocity, ImageMagick, GD, system check, hosting
featured: true
type: help
---
# Requirements at a glance

> **Supported: PHP 8.0 to 8.5. Usable without support: PHP 7.4 (Exponential 6.0.15 and later).** [Why Exponential supports PHP 7.4](../../why-exponential-supports-php-7-4.md)

This page is for system administrators and support staff at a hosting company who need the requirements of
Exponential 6.0.x in one place, and for product managers who must check that a plan can run it. It answers: what must
a server have, what is only recommended, and which part of the software checks each item. The full chapter with every
reason is [chapter 2 of the installation book, Requirements](../../../install/02-requirements.md); this page is its
summary for hosts.

## Contents

1. [The table](#the-table)
2. [Required PHP extensions](#required-php-extensions)
3. [Databases](#databases)
4. [What the installer checks for you](#what-the-installer-checks-for-you)
5. [What is not a requirement](#what-is-not-a-requirement)
6. [Check a server in two minutes](#check-a-server-in-two-minutes)
7. [Checklist](#checklist)
8. [Related pages](#related-pages)

## The table

| Need | Minimum | Recommended | Checked by |
|---|---|---|---|
| PHP | 8.0 supported (7.4 usable without support; 8.1 for Layouts) | the newest 8.x the system offers; 8.5 is the newest tested | Composer (`composer.json` `require.php`) and the setup wizard (`settings/setup.ini`, `[phpversion] MinimumVersion=7.4.0`) |
| PHP for Exponential Velocity | 8.0 with `sockets` (8.1 before release 0.0.4.54) | with `pcntl` and `openssl` as well | Composer (the Velocity package requires `"php": ">=8.0"` and `ext-sockets`) |
| PHP extensions | see [below](#required-php-extensions) | plus `curl`, `gd`, `openssl`, `pcntl`, `opcache` | Composer (`ext-*`) and the setup wizard's critical tests |
| `php.ini` | `memory_limit` 64M, `max_execution_time` 30, `date.timezone` set, `file_uploads` on, `allow_url_fopen` on | more memory for imports and image work; upload limits sized for your customers | the setup wizard's critical tests |
| Database | one of the six, SQLite 3 needs no server | in the official order: SQLite 3, PostgreSQL, MongoDB, MariaDB, Oracle, MySQL | the installer's database step |
| Composer | 2.x | a current 2.x (2.2 or later reads `config.allow-plugins`) | you |
| Web server | none (Velocity or PHP's built-in server) | Velocity, or Apache or nginx with PHP-FPM | you |
| Image conversion | GD (`imagegd2()`) or ImageMagick's `convert` | both | critical test `image_conversion` |
| 64-bit PHP | recommended | 64-bit | `composer.json` suggests `php-64bit` "for support of more than 30 languages" |
| Writable directories | `var/`, `settings/` and the others the wizard lists | one user or one group for every process | critical test `directory_permissions` |
| Command line | a shell with the same PHP as the web | yes, for Composer, cron and the console | you |

Sources: [chapter 2, the checklist](../../../install/02-requirements.md#21-the-checklist) and `composer.json`.

> **Note**
> PHP 8.0 support is new in the 6.0.15 line. The published tags `v6.0.8` to `v6.0.14` require PHP 8.1 in their
> `composer.json`, so a server on PHP 8.0 installs the 6.0.15 line
> ([chapter 3](../../../install/03-getting-the-code.md#31-versions-tags-and-branches)).

## Required PHP extensions

| Extension | Why |
|---|---|
| `dom`, `libxml`, `simplexml`, `xsl` | XML: rich text, packages, settings tools; `xsl` is a critical test |
| `mbstring`, `iconv`, `intl` | text in every language; all three are critical tests |
| `pcre`, `json`, `reflection`, `spl`, `session` | part of PHP; required by `composer.json` |
| `zlib` | gzip for packages and files; critical test |
| one database extension | `sqlite3`, `pgsql`, `mongodb`, `mysqli` or `oci8` |
| `gd` or ImageMagick | image variations |

The full list, the recommended extensions and the package names per system are on
[PHP versions and extensions](php-versions-and-extensions.md).

## Databases

> **Supported databases (Exponential 6.0.x), in order of preference:** 1. SQLite 3 (the default) · 2. PostgreSQL ·
> 3. MongoDB · 4. MariaDB · 5. Oracle · 6. MySQL. Microsoft SQL Server is known to be possible with additional work
> at this time (no driver in 6.0.x).

| Database | PHP extension | Practical minimum |
|---|---|---|
| 1. SQLite 3 | `sqlite3` | library 3.33 |
| 2. PostgreSQL | `pgsql` | 13 |
| 3. MongoDB | `mongodb` and the library `mongodb/mongodb` | server 6.0, PHP 8.2 |
| 4. MariaDB | `mysqli` | a release the vendor still supports |
| 5. Oracle | `oci8` | 19c |
| 6. MySQL | `mysqli` | a release the vendor still supports |

The practical minimums come from [Database support](../../database-support.md#versions-checked-against-the-code);
the setup wizard accepts older floors, which are not a promise. Details per engine are on
[Databases in the official order](databases-in-the-official-order.md).

## What the installer checks for you

The setup wizard's **System check** page runs the critical tests in `settings/site.ini`, `[SetupSettings]
CriticalTests`, with their parameters in `settings/setup.ini`. The kickstarter and `exp:install` run the same tests in
their `SystemCheck` step. A failure stops the install and prints the cause and the fix, for permissions the exact
`chmod` and `chown` commands ([chapter 2.4](../../../install/02-requirements.md#24-the-setup-wizards-system-check)).

| Critical test | Checks |
|---|---|
| `directory_permissions` | the directories in `[directory_permissions] CheckList` are writable |
| `phpversion` | PHP at least 8.0 |
| `database_extensions` | at least one database extension |
| `image_conversion` | GD or ImageMagick |
| `memory_limit` | at least `64M` (or `-1`) |
| `execution_time` | at least `30` (or `0`) |
| `allow_url_fopen` | on |
| `php_session`, `file_upload`, `timezone` | sessions, uploads, a time zone |
| `zlib_extension`, `dom_extension`, `iconv_extension`, `mbstring_extension`, `intl_extension`, `xsl_extension` | the extension is loaded |
| `ezcversion` | the Zeta Components are installed (`composer install` ran) |

> **Warning**
> The directory test runs as the user that runs the check: in the browser the web server's user, on the command line
> whoever starts the install. A command-line install by another user can pass and still leave files the web server
> cannot write. See [Writable directories and permissions](writable-directories-and-permissions.md).

## What is not a requirement

- **A particular operating system.** Exponential runs wherever PHP 8 runs; see
  [Operating systems for hosts](operating-systems-for-hosts.md).
- **A database server.** SQLite 3, the default, is a file under `var/storage/sqlite3/`.
- **A separate web server**, when Velocity serves the site.
- **Solr, Valkey or Redis.** They are optional extensions for search and caching
  ([Search backends](search-backends.md), [Valkey and Redis](../performance/valkey-and-redis.md)).

## Check a server in two minutes

```bash
php -v                          # 8.0 or later, for Velocity too
php -m                          # the extensions listed above
php -i | grep "Integer Size"    # 64 bits recommended
composer --version              # Composer 2, and the PHP it runs with
```

Compare `php -v` and `php -m` with the PHP information of the web server's PHP: on servers with several PHP versions
they often differ. With the Velocity package installed, `php bin/php/console exp:velocity ext check` compares the
machine with the extension set Velocity expects, and `exp:velocity ext install-hint <extension>` prints the package
command for the system ([chapter 2.3](../../../install/02-requirements.md#finding-what-is-missing)).

## Checklist

- [ ] `php -v` shows 8.0 or later for both the web and the command line PHP (Velocity included).
- [ ] Every required extension is loaded in both.
- [ ] `memory_limit`, `max_execution_time`, `date.timezone`, `file_uploads` and `allow_url_fopen` set.
- [ ] One database extension for the database the plan offers.
- [ ] GD or ImageMagick installed.
- [ ] Composer 2 available to the site's user.
- [ ] The writable directories belong to the user that runs PHP for the site.

## Related pages

- [Hosting basics](README.md)
- [PHP versions and extensions](php-versions-and-extensions.md) and [PHP settings for Exponential](php-settings-for-exponential.md)
- [Databases in the official order](databases-in-the-official-order.md)
- [New server checklist](../checklists/new-server.md)
- [Chapter 2: Requirements](../../../install/02-requirements.md)
- [Operating system support](../../os-support.md) and [Database support](../../database-support.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
