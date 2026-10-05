# 2. Requirements

This chapter lists what a machine needs before Exponential can be installed on it, and why each item is needed: the
PHP version (8.0 to 8.5; Velocity needs 8.1 or later), every PHP extension with the place in the code that asks for
it, the `php.ini` settings the setup wizard checks, the database servers and their minimum versions, Composer, a web
server, and notes for Red Hat Enterprise Linux 9 and its rebuilds, Debian and Ubuntu, and Plesk. It ends with what
the documentation says about memory and disk. Everything here is taken from `composer.json`, `settings/setup.ini`,
`settings/site.ini` and `kernel/setup/`; where two documents disagree, the code wins and the chapter says so.

[Contents](README.md) · Previous: [1. Introduction](01-introduction.md) · Next: [3. Getting the code](03-getting-the-code.md)

## 2.1 The checklist

| Need | Minimum | Recommended | Checked by |
|---|---|---|---|
| PHP | 8.0 | the newest 8.x your system offers (8.5 is supported) | Composer (`composer.json`, `require.php`) |
| PHP for Exponential Velocity | 8.1 | as above | Composer (`se7enxweb/exponential-velocity`, `"php": ">=8.1"`) |
| PHP extensions | see [2.3](#23-php-extensions) | plus the recommended ones | Composer (`ext-*`), the setup wizard's system check |
| `php.ini` | `memory_limit` 64M, `max_execution_time` 30, `date.timezone` set, `file_uploads` on, `allow_url_fopen` on | | the setup wizard's system check |
| Database | SQLite 3 (no server) | SQLite, MySQL/MariaDB or PostgreSQL; MongoDB and Oracle are supported | the setup wizard's database step |
| Composer | 2.x | the current 2.x | you |
| Web server | none (Velocity or PHP's built-in server) | Velocity, or Apache/nginx with PHP-FPM | you |
| Image conversion | GD (`imagegd2`) or ImageMagick's `convert` | both | the setup wizard's system check |

The fastest way to check a machine is to let the setup wizard do it: its **System check** page runs every critical
test and prints, for each failure, the cause and the fix (for permissions the exact `chmod` and `chown` commands).
The tests are described in [2.4](#24-the-setup-wizards-system-check).

## 2.2 PHP

### Versions

| PHP | Status |
|---|---|
| 8.0 | The oldest supported version. The kernel and the shipped extensions run on it under Apache with mod_php or PHP-FPM. It is the stock PHP of Red Hat Enterprise Linux 9, AlmaLinux 9 and Rocky Linux 9. Velocity does not run on it. |
| 8.1 to 8.4 | Supported, Velocity included. |
| 8.5 | Supported; the newest version tested. |
| 7.4 and older | Not supported by 6.0.8 and later. A site that must stay on PHP 7.4 stays on 6.0.7. |

`composer.json` declares:

```json
"php": "^8.0 || ^8.1 || ^8.2 || ^8.3 || ^8.4 || ^8.5 || ^8.6 || ^8.7 || ^8.8"
```

so Composer refuses to install on anything older than 8.0. The versions after 8.5 are accepted by the constraint
but have not been released or tested. Use the newest PHP your system offers.

On PHP 8.0, `autoload.php` loads `lib/phpcompat.php`, which defines the few functions PHP 8.0 lacks
(`array_is_list()` among them); on 8.1 and later that file is not even opened. The details, and the places that
needed PHP 8.1 and were made to work on 8.0 in 6.0.15, are in [PHP 8.0 support](../bc/6.0/php-8.0-support.md). The
history of the PHP 8 work since 6.0.0 is in [PHP 8 support](../bc/6.0/php8.md).

> **Documents that say 8.1.** Some pages written before 6.0.15 give PHP 8.1 as the minimum:
> [Getting started](../guides/getting-started.md), the "How to check" part of [PHP 8 support](../bc/6.0/php8.md),
> step 3 of [Upgrading](../guides/upgrading.md), and the `description` line of `composer.json` ("PHP 8.1 through
> 8.4.x+"). They describe the requirement of 6.0.8 to 6.0.14. From 6.0.15 the requirement in `composer.json` is
> `^8.0`, and that is what Composer enforces.

**The setup wizard's PHP check.** The critical test `phpversion` compares the running PHP with
`settings/setup.ini`, `[phpversion] MinimumVersion=5.3.3` and refuses the versions in `UnstableVersions`. That
minimum is historical and lower than what Composer enforces; it never fails on a PHP that Composer accepted.

**64-bit PHP.** `composer.json` suggests `php-64bit`: "For support of more than 30 languages, a 64bit php
installation on all involved prod/dev machines is required". The reason is that the languages of an object are kept
as bits of one integer. The PHP packages of the current Linux distributions are 64-bit builds.

### Command line and web PHP

Exponential uses PHP twice: the web server's PHP (PHP-FPM, mod_php or Velocity's workers) serves pages, and the
command line PHP (`php`) runs Composer, the installers, the console, the cronjobs and Velocity itself. Both must be
of a supported version and have the same extensions. On machines with several PHP versions (Plesk, Remi) the `php`
on the `PATH` is often not the one the web server uses; call the right binary by its path, for example
`/opt/plesk/php/8.5/bin/php bin/php/console ...`.

## 2.3 PHP extensions

Three sources decide which extensions are needed: `composer.json` (`require` and `suggest`), and the setup wizard's
tests, listed in `settings/site.ini`, `[SetupSettings] CriticalTests` (must pass) and `OptionalTests` (reported
only), with their parameters in `settings/setup.ini`.

### Required

| Extension | Required by | Why |
|---|---|---|
| `dom` | `composer.json`; critical test `dom_extension` | XML: the rich text field is stored as XML and parsed with DOM; packages and settings tools read XML too |
| `libxml` | `composer.json` | the XML library under `dom`, `simplexml` and `xsl` |
| `simplexml` | `composer.json` | reading XML files, for example extension metadata (`kernel/private/classes/ezpextension.php`) and image attributes |
| `xsl` | critical test `xsl_extension` | the setup wizard does not continue without it |
| `mbstring` | `composer.json`; critical test `mbstring_extension` | multi-byte strings in every language; the test also lists the character sets mbstring can convert (`eZMBStringMapper`) |
| `iconv` | `composer.json`; critical test `iconv_extension` | character set conversion ("Without it Exponential will not work.") |
| `intl` | critical test `intl_extension` | the setup wizard does not continue without it; the kernel also uses it for international host names (`idn_to_ascii()`) |
| `pcre` | `composer.json` | regular expressions everywhere (part of PHP) |
| `json` | `composer.json` | JSON in the admin, the REST API and the console (part of PHP since 8.0) |
| `reflection`, `spl` | `composer.json` | used by the kernel and the Zeta Components (part of PHP) |
| `session` | `composer.json`; critical test `php_session` | user sessions |
| `zlib` | critical test `zlib_extension` | gzip: package archives, the gzip file handlers in `lib/ezfile/`, compressed audit files |
| one database extension | critical test `database_extensions` | `sqlite3`, `mysqli`, `pgsql`, `mongodb` or `oci8`; at least one (`Require=one`) |
| `gd` or ImageMagick | critical test `image_conversion` | image variations; either `imagegd2()` from GD, or the `convert` program (`Require=one`) |

The wizard's messages for a missing extension read, for example, "Missing DOM extension", "Missing xsl extension",
"Missing intl extension" or "Missing database handler" ("No supported database handlers were found. Exponential
requires a database to store it's data, without one the system will fail."). They come from
`design/standard/templates/setup/tests/`.

### Database extensions

| Database | PHP extension | Notes |
|---|---|---|
| SQLite 3 | `sqlite3` | The default; nothing else needed. |
| MySQL or MariaDB | `mysqli` | `composer.json` suggests `ext-mysqli`. |
| PostgreSQL | `pgsql` | |
| MongoDB | `mongodb` (PECL) | The driver (`lib/ezdb/classes/expmongodb.php`) also needs the PHP library `mongodb/mongodb` (`MongoDB\Client`), which `composer.json` does not require: add it with `composer require mongodb/mongodb`. The [MongoDB page](../features/6.0/mongodb-database-support.md) names PHP 8.2 or newer as the prerequisite. |
| Oracle | `oci8` | Through the `ezoracle` extension, which `composer.json` requires; `oci8` itself comes from your vendor, EPEL or Remi. |

The optional test `database_all_extensions` reports which of `sqlite3`, `mysqli`, `pgsql` and `mongodb` are
missing; it is only information.

### Recommended

| Extension | Source | Why |
|---|---|---|
| `curl` | `composer.json` suggest; optional test `curl_extension` | "better support for interacting with other servers, like downloading packages over SSL" |
| `gd` | `composer.json` suggest; optional test `imagegd_extension` | image manipulation unless ImageMagick is installed |
| `openssl` | `composer.json` suggest | "cryptographically secure random bytes" |
| `pcntl` | `composer.json` suggest | asynchronous publishing (`bin/php/ezasynchronouspublisher.php` forks with `pcntl_fork()`); Velocity's extension manifest lists it among the server extensions |
| `opcache` | (performance) | PHP's compiled code cache; see [Velocity opcode cache](../features/6.0/velocity-opcode-cache-and-profile.md) |
| `redis` | `composer.json` suggest (`se7enxweb/sevenx_valkey`, `sevenx_valkey_cache`) | only for the Valkey/Redis cache extensions |

The optional test `texttoimage_functions` checks `imagettftext()` and `imagettfbbox()` (GD with FreeType), which the
text-to-image template operator needs. `imagemagick_program` looks for `convert` in `/bin`, `/sbin`, `/usr/bin`,
`/usr/sbin`, `/usr/local/bin` and `/usr/local/sbin`.

### Finding what is missing

```bash
php -m
```

lists the extensions the command line PHP has. With Velocity installed, the console also compares the machine with
the extension set of the Velocity package and prints the install command for your system:

```bash
php bin/php/console exp:velocity ext check
php bin/php/console exp:velocity ext install-hint
```

(`exp:velocity ext list|check|plan|install-hint|build` hands over to the engine's `qbixctl ext:<action>`; it needs
the Velocity package, see [chapter 3](03-getting-the-code.md#34-the-optional-velocity-package)).

## 2.4 The setup wizard's system check

The wizard runs the tests in `[SetupSettings] CriticalTests` on its **System check** page and those in
`OptionalTests` on **System finetuning**. A critical failure stops the wizard until it is fixed (or the test is
ignored with its checkbox, which is not recommended).

### Critical tests

| Test | Checks | Setting in `settings/setup.ini` | Fix |
|---|---|---|---|
| `directory_permissions` | The web server can write the directories in `CheckList` | `[directory_permissions] CheckList`: `design`, `extension`, `settings`, `settings/override`, `settings/siteaccess`, `settings/siteaccess/admin`, `var`, `var/cache` and its subdirectories, `var/log`, `var/storage` and its subdirectories, `var/autoload` | the `chmod`/`chown` commands the page prints |
| `phpversion` | PHP at least `MinimumVersion`, not one of `UnstableVersions` | `[phpversion]` | a newer PHP |
| `database_extensions` | at least one database extension | `[database_extensions] Extensions=sqlite3;mysqli;pgsql;mongodb;oci8`, `Require=one` | install one |
| `image_conversion` | GD's `imagegd2()` or ImageMagick's `convert` | `[image_conversion] TestList`, `Require=one` | install `gd` or ImageMagick |
| `safe_mode` | `safe_mode` off | | (removed from PHP; always passes) |
| `memory_limit` | `memory_limit` at least `MinMemoryLimit`; `-1` (unlimited) passes | `[memory_limit] MinMemoryLimit=64M` | raise it in `php.ini` |
| `execution_time` | `max_execution_time` at least `MinExecutionTime`; `0` (unlimited) passes | `[execution_time] MinExecutionTime=30` | raise it in `php.ini` |
| `magic_quotes_runtime` | magic quotes off | | (removed from PHP; always passes) |
| `allow_url_fopen` | `allow_url_fopen` on | | turn it on in `php.ini` |
| `php_session` | the `session` extension | `[php_session]` | install it |
| `file_upload` | `file_uploads` on, and the upload directory exists and is writable | `[file_upload]` | see below |
| `zlib_extension`, `dom_extension`, `iconv_extension`, `mbstring_extension`, `intl_extension`, `xsl_extension` | the extension is loaded | one block each | install it |
| `timezone` | a time zone was chosen | | set `date.timezone` |
| `ezcversion` | the Zeta Components are there: class `ezcBaseFile` with method `walkrecursive` | `[ezcversion]` | `composer install` |

### Optional tests

`variables_order` (must contain `E`, so PHP registers environment variables), `php_magicquotes`, `curl_extension`,
`imagegd_extension`, `imagemagick_program`, `database_all_extensions`, `php_register_globals`, `texttoimage_functions`
and `open_basedir`. The last one warns when `open_basedir` is set: "open_basedir is in use and can give problems
running Exponential due to bugs in some PHP versions."

All test functions are in `kernel/setup/ezsetuptests.php`; the messages are in
`design/standard/templates/setup/tests/<test>_error.tpl`.

## 2.5 php.ini settings

| Setting | Value | Why |
|---|---|---|
| `memory_limit` | at least `64M` | critical test `memory_limit` ("Insufficient memory allocated to install Exponential"). It is a floor: package installs, imports and image work need more, so give PHP what the machine can spare. |
| `max_execution_time` | at least `30` | critical test `execution_time` ("Insufficient execution time allowed to install Exponential"). The command line PHP has no limit by default. |
| `date.timezone` | your time zone, for example `Europe/Berlin` | critical test `timezone` |
| `file_uploads` | `On` | critical test `file_upload` |
| `upload_tmp_dir` | a writable directory, or empty | when empty the test assumes `TMPDIR` or `/tmp` and checks that a file can be created there |
| `allow_url_fopen` | `On` | critical test `allow_url_fopen` ("allow_url_fopen ini setting is disabled") |
| `variables_order` | contains `E`, for example `EGPCS` | optional test `variables_order` ("PHP does not register environment variables") |
| `open_basedir` | empty | optional test `open_basedir` |
| `upload_max_filesize`, `post_max_size` | as large as the files editors upload | not tested; PHP refuses larger uploads |

**The time zone in detail.** `index.php` sets UTC when `php.ini` has no `date.timezone`. The test fails only in that
case: a `php.ini` that sets `date.timezone`, even to UTC, is a decision and passes (`eZSetupTestTimeZone()`). The site
can also set its own time zone in `settings/site.ini`, `[TimeZoneSettings] TimeZone` (applied by the kernel on every
request), and `config.php` can call `date_default_timezone_set()`; the commented example is in
`config.php-RECOMMENDED`. The valid names are listed in the PHP manual's
[List of Supported Timezones](https://www.php.net/manual/en/timezones.php).

After editing `php.ini`, restart or reload what runs PHP: PHP-FPM, Apache with mod_php, or Velocity
(`php bin/php/console exp:velocity restart`). `php --ini` shows which files the command line PHP reads; the web PHP may
read others (PHP-FPM pools have their own `php_admin_value` lines).

## 2.6 Databases

| Database | `DatabaseImplementation` and aliases (`settings/site.ini`, `[DatabaseSettings] ImplementationAlias[]`) | Minimum server version the wizard checks |
|---|---|---|
| SQLite 3 | `sqlite3` | 3.0.1 |
| MySQL or MariaDB | `mysqli`, `mysql`, `ezmysqli`, `ezmysql` | 4.1.1 |
| PostgreSQL | `pgsql`, `postgresql`, `ezpostgresql` | 8.0 |
| MongoDB | `mongodb` | 4.0 |
| Oracle | `oracle`, `ezoracle` (from `extension/ezoracle/settings/site.ini.append.php`) | 19 |

The minimum versions are those of `eZSetupDatabaseMap()` in `kernel/setup/ezsetupcommon.php`. They are floors, not
recommendations: run a version your vendor still supports. The [MongoDB page](../features/6.0/mongodb-database-support.md)
gives MongoDB 6.0 as the practical minimum.

- **SQLite** is the default (`settings/setup.ini`, `[DatabaseSettings] DefaultType=sqlite3`) and needs no server: the
  database is one file under `var/storage/sqlite3/`. It is a full production database; see the
  [SQLite feature page](../features/6.0/sqlite-database.md).
- **MySQL or MariaDB**: the database must use UTF-8. When the database's character set differs from the one the
  wizard wants, the wizard stops with "The database [...] cannot be used, the setup wizard wants to create the site in
  [...] but the database has been created using character set [...]".
- **PostgreSQL** needs the `pgcrypto` extension in the database (its `digest` function). The wizard tries to create
  it; when it cannot, it says so and asks for `CREATE EXTENSION pgcrypto;` by the database owner or a superuser.
- **MongoDB** and **Oracle**: see their pages, [MongoDB](../features/6.0/mongodb-database-support.md) and
  [SQLite and Oracle drivers](../specifications/6.0/database-drivers-sqlite-oracle.md).

Choosing and tuning a database is the subject of [chapter 9](09-databases.md).

## 2.7 Composer

Exponential installs with [Composer](https://getcomposer.org/) 2. Composer downloads the Zeta Components, the
extensions `composer.json` requires and, when you ask for it, Velocity. Without Composer, or without running it,
`vendor/` is missing and the site cannot start.

- Install it as described on [getcomposer.org/download](https://getcomposer.org/download/) and check with
  `composer --version`.
- `composer.json` lists the installer plugin `se7enxweb/exponential-legacy-installer` under `config.allow-plugins`.
  That setting exists since Composer 2.2; an older Composer 2 ignores it. Use a current Composer 2.
- Composer runs with the command line PHP. Its version and extensions are the ones Composer checks the `ext-*` and
  `php` requirements against.

- The development requirements matter on older PHP. `composer.json` has `require-dev`
  `"phpunit/phpunit": "^13.4"`, and PHPUnit 13 declares `"php": ">=8.4.1"`. The repository ships no `composer.lock`,
  so Composer resolves `require-dev` even with `--no-dev`. On PHP 8.0 to 8.3, drop the development requirements first;
  [chapter 3](03-getting-the-code.md#33-installing-on-php-80-to-83) shows how.

`composer install` runs the script `legacy-scripts` afterwards (`post-install-cmd` and `post-update-cmd` in
`composer.json`), which is `php bin/php/ezpgenerateautoloads.php`: the class maps are generated for you.

## 2.8 Web server

| Option | PHP | When |
|---|---|---|
| **Exponential Velocity** (`qbix` engine) | 8.1+ | Recommended for every stage. Serves HTTP and HTTPS itself; no other web server needed. |
| Apache with `mod_rewrite` and PHP-FPM (or mod_php) | 8.0+ | The classic setup; shared hosting. The rewrite rules ship in `.htaccess_root`. |
| nginx, LiteSpeed, Caddy and others with PHP-FPM | 8.0+ | Any server that can rewrite a URL to `index.php`; listed in the [README](../../README.md#the-traditional-options). |
| FrankenPHP (through `exp:velocity`) | the binary's own | Production-ready alternative engine. |
| PHP's built-in server (`exp:velocity start --engine=php`) | 8.0+ | Development only. |

None is needed during a kickstarter or console install. Setting each one up is the subject of
[chapter 8](08-serving-the-site.md) and the [Deploying guide](../guides/deploying.md).

## 2.9 Operating system notes

Exponential runs wherever PHP runs; the installation is plain files. The notes below cover the systems the
documentation and the code name. The package names are the ones Velocity's extension manifest
(`vendor/se7enxweb/exponential-velocity/build/extensions.json`) gives, which is what `exp:velocity ext install-hint`
prints.

### Red Hat Enterprise Linux 9, AlmaLinux 9, Rocky Linux 9

- The stock PHP of the AppStream repository is **8.0**, which runs Exponential under Apache or PHP-FPM. The command
  from [PHP 8.0 support](../bc/6.0/php-8.0-support.md):

  ```bash
  dnf install php php-fpm php-mysqlnd php-gd php-intl php-mbstring php-xml php-pdo php-opcache
  ```

  `php-xml` brings `dom`, `simplexml`, `xsl` and `libxml`; `php-pdo` brings `sqlite3`; `php-common` (a dependency)
  brings `iconv`, `zlib`, `session` and `openssl`; `php-process` brings `pcntl`; `php-pgsql` adds PostgreSQL.
- For Velocity (PHP 8.1+), switch to a newer AppStream module stream: `dnf module list php`, then
  `dnf module reset php`, `dnf module enable php:8.3` and `dnf distro-sync`. The streams `php:8.1`, `php:8.2` and
  `php:8.3` run both Exponential and Velocity.
- The Remi repository (`php:remi-8.x` module streams, or the parallel `php<XY>` packages under `/opt/remi`) provides
  every version from 8.0 to 8.5. PECL extensions (`php-pecl-redis`, `php-pecl-apcu`, `php-pecl-mongodb`) and `oci8`
  come from EPEL, Remi or the vendor.
- The service of the stock PHP-FPM is `php-fpm`; a Remi parallel install is `php<XY>-php-fpm`.

### Debian and Ubuntu

- The packages are named after the PHP version: `php<version>-<extension>`, for example `php8.3-intl`. The extensions
  Exponential needs are in `php8.3-xml` (`dom`, `simplexml`, `xsl`, `libxml`), `php8.3-mbstring`, `php8.3-intl`,
  `php8.3-common` (`iconv`, `zlib`, `session`, `openssl`), `php8.3-sqlite3`, `php8.3-mysql` (`mysqli`),
  `php8.3-pgsql`, `php8.3-gd`, `php8.3-curl` and `php8.3-opcache`; `pcntl` is in `php8.3-cli`.
- Replace `8.3` with the version your release ships.
- The PHP-FPM service is `php<version>-fpm`, with its pools in `/etc/php/<version>/fpm/pool.d/`.
- The Velocity package is also published as a `.deb`; see
  [Velocity packages and binaries](../features/6.0/velocity-packages-and-binaries.md).

### Plesk

- Plesk installs each PHP version under `/opt/plesk/php/<version>/` (for example `/opt/plesk/php/8.5/bin/php`) next
  to the system's PHP. A domain uses the version chosen in its hosting settings; the command line `php` is the
  system's. Run Composer and the console with the domain's PHP.
- The bundled extensions ship with Plesk's PHP and are switched on under **Tools & Settings > PHP Settings**; PECL
  extensions are installed with `/opt/plesk/php/<version>/bin/pecl install <extension>`.
- Each domain has its own PHP-FPM pool in `/opt/plesk/php/<version>/etc/php-fpm.d/<domain>.conf`, run by the service
  `plesk-php<XY>-fpm` (for example `plesk-php85-fpm`), not by `php-fpm`. After a PHP change, reload that one.
  `exp:velocity deploy` finds it by itself (`settings/velocity.ini`, `[DeploySettings] PhpFpmService=auto` looks in
  `/opt/plesk/php/*/etc/php-fpm.d`, `/etc/php-fpm.d`, `/etc/php/*/fpm/pool.d` and `/etc/opt/remi/php*/php-fpm.d`).

### Other systems

The Velocity manifest also knows Alpine (`apk`), FreeBSD (`pkg`), macOS (Homebrew) and Windows. The kernel's setup
tests have Windows paths for ImageMagick (`convertim.exe`, `convert.exe`), but this book is written for Linux.

## 2.10 Memory, CPU and disk

The documentation gives few hard numbers, and this book does not invent others. What it does state:

| Item | Value | Source |
|---|---|---|
| PHP `memory_limit` | at least 64M | `settings/setup.ini`, `[memory_limit] MinMemoryLimit` |
| One Velocity worker | about 10 MB of private memory (measured) | [Velocity web server](../features/6.0/velocity-web-server.md) |
| Velocity pool | `Workers=4` shipped (`settings/velocity.ini`); the engine's own default is what fits in RAM, at most 8 per core and 64, never fewer than 4 | `settings/velocity.ini`, [Velocity web server](../features/6.0/velocity-web-server.md) |
| A MongoDB installation | RAM 512 MB minimum, 2 GB recommended; disk 2 GB minimum, 10 GB recommended | [MongoDB kernel support](../bc/6.0/MONGODB_KERNEL_SUPPORT_EXPANSION.md), section 24.1 |

Disk use grows with `var/storage` (uploaded files and image variations), `var/cache` and `var/log`; the database
grows with the content. Measure a running site rather than guessing: the [benchmark](../features/6.0/benchmark.md)
command times pages, and Velocity's [control panel](../features/6.0/velocity-control-panel.md) shows memory use.
Read Velocity's memory as the control panel shows it, the proportional set size (PSS) of the parent and its workers:
the resident set size of each forked worker counts the memory they share again and again.

## References

In this repository:

- [Installation overview](../INSTALL.md), section 2.
- [PHP 8.0 support](../bc/6.0/php-8.0-support.md) and [PHP 8 support](../bc/6.0/php8.md).
- [SQLite database support](../features/6.0/sqlite-database.md),
  [SQLite 3 driver](../specifications/6.0/sqlite3-database-driver.md),
  [MongoDB](../features/6.0/mongodb-database-support.md),
  [SQLite and Oracle drivers](../specifications/6.0/database-drivers-sqlite-oracle.md),
  [Database drivers, September 2026](../specifications/6.0/database-drivers-2026-09.md).
- [Velocity engines](../bc/6.0/velocity-engines.md), [Velocity web server](../features/6.0/velocity-web-server.md),
  [Velocity packages and binaries](../features/6.0/velocity-packages-and-binaries.md).
- [Deploying](../guides/deploying.md).
- [Quality checks](../features/6.0/quality-checks.md) and [Continuous integration](../specifications/6.0/continuous-integration.md):
  how PHP 8.0 is tested.
- Code: `composer.json`, `settings/setup.ini`, `settings/site.ini` (`[SetupSettings]`), `kernel/setup/ezsetuptests.php`,
  `kernel/setup/ezsetupcommon.php`, `design/standard/templates/setup/tests/`.

External:

- PHP: [supported versions](https://www.php.net/supported-versions.php),
  [installation and configuration](https://www.php.net/manual/en/install.php),
  [php.ini directives](https://www.php.net/manual/en/ini.list.php),
  [file uploads](https://www.php.net/manual/en/ini.core.php#ini.file-uploads),
  [List of Supported Timezones](https://www.php.net/manual/en/timezones.php),
  [PHP 8.0 migration](https://www.php.net/manual/en/migration80.php).
- PHP extensions: [DOM](https://www.php.net/manual/en/book.dom.php), [XSL](https://www.php.net/manual/en/book.xsl.php),
  [intl](https://www.php.net/manual/en/book.intl.php), [mbstring](https://www.php.net/manual/en/book.mbstring.php),
  [SQLite3](https://www.php.net/manual/en/book.sqlite3.php), [mysqli](https://www.php.net/manual/en/book.mysqli.php),
  [PostgreSQL](https://www.php.net/manual/en/book.pgsql.php), [GD](https://www.php.net/manual/en/book.image.php),
  [MongoDB](https://www.php.net/manual/en/set.mongodb.php), [OCI8](https://www.php.net/manual/en/book.oci8.php).
- Composer: [download](https://getcomposer.org/download/),
  [allow-plugins](https://getcomposer.org/doc/06-config.md#allow-plugins).
- Velocity on Packagist: [se7enxweb/exponential-velocity](https://packagist.org/packages/se7enxweb/exponential-velocity).
- Remi repository: [rpms.remirepo.net](https://rpms.remirepo.net/).

[Contents](README.md) · Previous: [1. Introduction](01-introduction.md) · Next: [3. Getting the code](03-getting-the-code.md)
