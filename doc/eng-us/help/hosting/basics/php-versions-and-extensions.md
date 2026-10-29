---
title: PHP versions and extensions
audience: hosting, admin
category: hosting
order: 40
summary: Which PHP versions Exponential 6.0.x and Velocity run on, which PHP extensions are required, recommended or needed per database, the package names on the RHEL family, Debian, Ubuntu and Plesk, and why the web PHP and the command line PHP must match.
keywords: PHP, PHP versions, PHP 8.0, PHP 8.1, PHP 8.5, extensions, dom, xsl, intl, mbstring, iconv, zlib, sqlite3, pgsql, mongodb, mysqli, oci8, gd, ImageMagick, curl, openssl, pcntl, sockets, opcache, redis, fileinfo, Remi, AppStream, Plesk, php-fpm, command line PHP
type: help
---
# PHP versions and extensions

This page is for system administrators who build the PHP of a server for Exponential, and for product managers who
decide which PHP versions a plan offers. It answers: which PHP versions run Exponential and Velocity, which
extensions are required or recommended, where they come from on the common Linux families, and why a server's two
PHPs (web and command line) must agree. The source is
[chapter 2 of the installation book](../../../install/02-requirements.md#22-php).

## Contents

1. [PHP versions](#php-versions)
2. [Required extensions](#required-extensions)
3. [Database extensions](#database-extensions)
4. [Recommended extensions](#recommended-extensions)
5. [Packages per system family](#packages-per-system-family)
6. [Web PHP and command line PHP](#web-php-and-command-line-php)
7. [Checklist](#checklist)
8. [Related pages](#related-pages)

## PHP versions

| PHP | Status |
|---|---|
| 8.0 | Supported; the kernel runs on it under Apache with mod_php or PHP-FPM. It is the stock PHP of RHEL 9, AlmaLinux 9 and Rocky Linux 9. Velocity runs on it from its release 0.0.4.54 (until then it needed 8.1). |
| 8.1 to 8.4 | Supported, Velocity included. |
| 8.5 | Supported; the newest version tested. |
| 7.4 | Usable without support from 6.0.15 (no bug-fix or security commitment, without the features that need PHP 8); not by 6.0.8 to 6.0.14. |

`composer.json` declares `"php": "^7.4 || ^8.0 || ^8.1 || ^8.2 || ^8.3 || ^8.4 || ^8.5 || ^8.6 || ^8.7 || ^8.8"`. The
versions after 8.5 are accepted by the constraint but have not been released or tested.

> **Tip**
> Offer the newest PHP your platform provides as the default for new Exponential sites, and keep older versions for
> sites that need them. Each newer version brings speed, and the older ones lose security support first.

**Which Exponential release needs which PHP** ([chapter 3](../../../install/03-getting-the-code.md#31-versions-tags-and-branches)):

| Exponential | `"php"` in its `composer.json` | On PHP 8.0 |
|---|---|---|
| `v6.0.0` to `v6.0.7` | `^7.4 \|\| ^8.1` | not installable |
| `v6.0.8` to `v6.0.14` | `^8.1` and later 8.x | not installable |
| 6.0.15 line (`main`) | `^8.0` and later 8.x | installable |

**64-bit.** `composer.json` suggests `php-64bit`: "For support of more than 30 languages, a 64bit php installation on
all involved prod/dev machines is required". The languages of an object are kept as bits of one integer.

## Required extensions

| Extension | Required by | Why |
|---|---|---|
| `dom` | `composer.json`; critical test `dom_extension` | the rich text field is stored as XML |
| `libxml` | `composer.json` | the XML library under `dom`, `simplexml` and `xsl` |
| `simplexml` | `composer.json` | reading XML files such as extension metadata |
| `xsl` | critical test `xsl_extension` | the setup wizard does not continue without it |
| `mbstring` | `composer.json`; critical test | multi-byte strings in every language |
| `iconv` | `composer.json`; critical test | character set conversion |
| `intl` | critical test `intl_extension` | the setup wizard does not continue without it; international host names |
| `pcre`, `json`, `reflection`, `spl` | `composer.json` | part of PHP |
| `session` | `composer.json`; critical test `php_session` | user sessions |
| `zlib` | critical test `zlib_extension` | package archives, gzip file handlers, compressed audit files |
| one database extension | critical test `database_extensions` | see below |
| `gd` or ImageMagick | critical test `image_conversion` | image variations |

**For Velocity** the engine package requires `sockets` and suggests `pcntl` (graceful shutdown and the worker pool)
and `openssl` (HTTPS); in practice all three are needed for a production Velocity.

## Database extensions

Install only the extension of the database the site uses; at least one is required
(`settings/setup.ini`, `[database_extensions] Extensions=sqlite3;mysqli;pgsql;mongodb;oci8`, `Require=one`).

| Database | Extension | Where it comes from |
|---|---|---|
| 1. SQLite 3 | `sqlite3` | bundled with PHP; RHEL family `php-pdo`, Debian and Ubuntu `php<version>-sqlite3` |
| 2. PostgreSQL | `pgsql` | `php-pgsql`, `php<version>-pgsql` |
| 3. MongoDB | `mongodb` (PECL) and the Composer library `mongodb/mongodb` | `pecl install mongodb` or `php-pecl-mongodb`; the library with `composer require mongodb/mongodb`; PHP 8.2 or later |
| 4. MariaDB | `mysqli` | `php-mysqlnd`, `php<version>-mysql` |
| 5. Oracle | `oci8` (PECL) | built against an Oracle client (the Instant Client is enough); PECL oci8 3.4 for PHP 8.4 and 8.5, 3.2 for 8.1 to 8.3 |
| 6. MySQL | `mysqli` | as MariaDB |

Source: [Database support, PHP extensions](../../database-support.md#php-extensions).

## Recommended extensions

| Extension | Why |
|---|---|
| `curl` | "better support for interacting with other servers, like downloading packages over SSL" (`composer.json` suggest) |
| `gd` | image manipulation without ImageMagick; the default first image converter |
| `openssl` | cryptographically secure random bytes; HTTPS in Velocity |
| `pcntl` | asynchronous publishing (`bin/php/ezasynchronouspublisher.php`); Velocity's worker pool |
| `opcache` | PHP's compiled code cache; always compiled in from PHP 8.5 |
| `fileinfo` | reads the type of an uploaded image from its content (`settings/image.ini`, `[ImageUploadSettings]`) |
| `redis` | only for the Valkey and Redis cache extensions (`sevenx_valkey`, `sevenx_valkey_cache`) |

The administration's **Setup > System information** page warns when `curl`, `fileinfo`, `intl`, `xsl`, OPcache or
APCu is missing, or when both `gd` and `imagick` are ([System information](../../../guides/system-information.md)).
`pcntl` and `posix` do not exist on Windows.

## Packages per system family

From [chapter 2.9](../../../install/02-requirements.md#29-operating-system-notes):

**RHEL 9, AlmaLinux 9, Rocky Linux 9.** The stock AppStream PHP is 8.0:

```bash
dnf install php php-fpm php-mysqlnd php-gd php-intl php-mbstring php-xml php-pdo php-opcache
```

`php-xml` brings `dom`, `simplexml`, `xsl` and `libxml`; `php-pdo` brings `sqlite3`; `php-common` brings `iconv`,
`zlib`, `session` and `openssl`; `php-process` brings `pcntl`; `php-pgsql` adds PostgreSQL. Velocity runs on this
stock 8.0 (from release 0.0.4.54; the el9 rpm installs without a module stream). For a newer PHP switch to another
module stream (`dnf module reset php`, `dnf module enable php:8.3`, `dnf distro-sync`). On RHEL 8 (default PHP 7.2)
enable `php:8.0` first. The Remi repository
provides 8.0 to 8.5; PECL extensions and `oci8` come from EPEL, Remi or the vendor.

**Debian and Ubuntu.** Packages are named `php<version>-<extension>`: `php8.3-xml`, `php8.3-mbstring`,
`php8.3-intl`, `php8.3-common`, `php8.3-sqlite3`, `php8.3-mysql`, `php8.3-pgsql`, `php8.3-gd`, `php8.3-curl`,
`php8.3-opcache`; `pcntl` is in `php8.3-cli`. Replace `8.3` with the version of your release.

**Plesk.** Each PHP version lives under `/opt/plesk/php/<version>/`; bundled extensions are switched on in the panel's
PHP settings, PECL extensions with `/opt/plesk/php/<version>/bin/pecl install <extension>`. See
[Plesk](../panels/plesk.md).

**Other panels.** Use the panel's PHP selector and check your panel's documentation for how it enables extensions.

## Web PHP and command line PHP

Exponential uses PHP twice: the web server's PHP (PHP-FPM, mod_php or Velocity's workers) serves pages, and the
command line PHP runs Composer, the installers, the console, the cronjobs and Velocity itself. Both must be of a
supported version and have the same extensions ([chapter 2](../../../install/02-requirements.md#command-line-and-web-php)).

What goes wrong when they differ:

- Composer, run with a newer command line PHP, picks package versions the web PHP cannot run.
- A command line install succeeds, and the browser then shows a white page because the web PHP lacks an extension.
- A cronjob runs with another PHP than the site and fails on an extension only the web PHP has.

On machines with several PHP versions, call the right binary by its path, for example
`/opt/plesk/php/8.5/bin/php bin/php/console ...`, and use the same path in the crontab.

> **Warning**
> When you upgrade the PHP of a site, upgrade the command line PHP its cron and console use at the same time. See the
> [PHP upgrade runbook](../runbooks/php-upgrade.md).

## Checklist

- [ ] The plan's PHP versions are 8.0 or later, also wherever Velocity is offered.
- [ ] 64-bit PHP builds.
- [ ] Every required extension in both the web and the command line PHP.
- [ ] Exactly the database extension(s) the plan offers.
- [ ] `curl`, `gd`, `openssl`, `pcntl`, `opcache` and `fileinfo` installed.
- [ ] `sockets`, `pcntl` and `openssl` for Velocity.
- [ ] The crontab and the console use the same PHP binary as the site.

## Related pages

- [Requirements at a glance](requirements-at-a-glance.md) and [PHP settings for Exponential](php-settings-for-exponential.md)
- [PHP selection and FPM pools](../panels/php-selection-and-fpm-pools.md)
- [PHP 8.0 support](../../../bc/6.0/php-8.0-support.md)
- [Chapter 2: Requirements](../../../install/02-requirements.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
