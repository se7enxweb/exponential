---
title: PHP, PHP-FIG and Composer standards
audience: developer, admin
category: standards
order: 70
summary: Which PHP versions Exponential supports, which PHP-FIG recommendations (PSR-1 to PSR-20) it follows or not, how it uses Composer and Packagist, and how its version numbers relate to Semantic Versioning.
keywords: PHP, PHP 8.0, PHP 8.5, PHP-FIG, PSR, PSR-1, PSR-2, PSR-3, PSR-4, PSR-6, PSR-7, PSR-11, PSR-12, PSR-14, PSR-15, PSR-16, PSR-17, PSR-18, PER Coding Style, Composer, Packagist, Semantic Versioning, SemVer, autoload, class map, version constraint
type: reference
---
# PHP, PHP-FIG and Composer standards

This page is for developers who write code for Exponential or package extensions for it, and for administrators who
choose a PHP version or install with Composer. It answers: which PHP versions are supported and tested, which
PHP-FIG standard recommendations (PSRs) the code follows, how Composer and Packagist are used, and how Exponential's
version numbers compare with Semantic Versioning. Each statement names the file it was checked against.

## Summary

| Standard | Version | Status | Evidence (short) |
|---|---|---|---|
| Supported PHP versions | PHP 8.0 to 8.5 | **Complies** (tested) | `composer.json` `"php"`, `.github/workflows/quality.yml` job `php80`, `phpunit.yml` matrix 8.1 to 8.5 |
| PSR-1 Basic Coding Standard | PSR-1 | **Partly complies** | `<?php` tags, no side effects in class files; class names like `expHelpAudience` are not StudlyCaps |
| PSR-2 / PSR-12 / PER Coding Style | PSR-12, PER-CS 2.0 | **Does not comply** (deliberately) | `phpcs.xml.dist` enforces the kernel's own style |
| PSR-4 Autoloading | PSR-4 | **Does not comply** | no `psr-4` in `composer.json`; generated class maps `autoload/ezp_kernel.php` |
| PSR-3 Logger Interface | PSR-3 | **Does not comply** (not used) | no `Psr\Log` in `kernel/`, `lib/`, `bin/` |
| PSR-6 / PSR-16 Caching | PSR-6, PSR-16 | **Does not comply** (not used) | no `Psr\Cache`, `Psr\SimpleCache` |
| PSR-7 / PSR-15 / PSR-17 / PSR-18 HTTP | - | **Does not comply** (not used) | no `Psr\Http` in repository code or in Exponential Velocity |
| PSR-11 Container | PSR-11 | **Does not comply** (not used) | no `Psr\Container` |
| PSR-14 Event Dispatcher | PSR-14 | **Does not comply** (not used) | no `Psr\EventDispatcher` |
| Composer `composer.json` schema | Composer 2 | **Complies** | `composer validate` reports "./composer.json is valid" |
| Packagist | - | **Complies** | `se7enxweb/exponential` and every `se7enxweb/*` dependency resolve from Packagist |
| Semantic Versioning | SemVer 2.0.0 | **Partly complies** | kernel `6.0.x` is three-part; many extensions and Velocity use four parts (`0.0.4.10`) |

## Supported PHP versions

**What it is.** PHP is maintained by the PHP Group; each minor version gets two years of active support and one or
two of security fixes.

**How Exponential complies.**

- `composer.json` `require`: `"php": "^8.0 || ^8.1 || ^8.2 || ^8.3 || ^8.4 || ^8.5 || ^8.6 || ^8.7 || ^8.8"`. The
  ranges for 8.6 to 8.8 are forward-looking; those versions do not exist yet and are not tested.
- **PHP 8.0 is the oldest supported version**, because it is the stock PHP of Red Hat Enterprise Linux 9; see
  [PHP 8.0 support](../bc/6.0/php-8.0-support.md). PHP functions newer than 8.0 are defined in `lib/phpcompat.php`,
  which `autoload.php` loads only when `PHP_VERSION_ID < 80100`.
- Exponential Velocity, the application server, runs on PHP 8.0 to 8.5 from its release 0.0.4.54 and is in
  `require` at `~0.0.4.55` (until then it needed PHP 8.1 and was only in `suggest`).
- Required PHP extensions in `composer.json`: `dom`, `libxml`, `mbstring`, `pcre`, `json`, `iconv`, `reflection`,
  `session`, `spl`, `simplexml`. Database and image extensions are in `suggest`.

**How it is tested** (`.github/workflows/`):

| Check | PHP | Workflow, job |
|---|---|---|
| Syntax of every PHP file in git, production `composer install --no-dev`, boot smoke test (`tests/bin/bootsmoke.php`) | 8.0 | `quality.yml`, `php80` |
| PHPCompatibility, `testVersion` `8.0-` | (static) | `phpcs.xml.dist`, warning-only code style step |
| PHPUnit without a database | 8.1, 8.2, 8.3, 8.4, 8.5 | `phpunit.yml`, `phpunit` |
| PHPUnit with MySQL 8.0 and PostgreSQL 15 | 8.3 | `phpunit.yml`, `phpunit-db` |
| PHPStan | 8.3 (analysis `phpVersion: 80100`) | `quality.yml`, `phpstan` |
| Micro benchmark | 8.5 | `performance.yml`, `micro` |

**Status: Complies** for PHP 8.0 to 8.5.

**Known gaps.**

- PHPUnit does not run on PHP 8.0 (PHPUnit 10 and later need 8.1); 8.0 is covered by syntax, install and boot only.
- `phpstan.neon.dist` analyses as PHP 8.1 (`phpVersion: 80100`), not 8.0.
- [Default extension distribution](../features/6.0/default-extension-distribution.md) still says the PHP
  requirement is `^8.1 || ^8.2 ...`; `composer.json` says `^8.0`.

## PHP-FIG recommendations (PSRs)

**What they are.** The PHP Framework Interop Group (PHP-FIG) publishes PHP Standard Recommendations. Exponential's
kernel was inherited from eZ Publish 4, which predates most of them; the project has chosen to keep the code style
of that kernel and not to adopt the interface PSRs. `CONTRIBUTING.md` still says "Follow the PSR standards"; the
rules that are actually checked are those of `phpcs.xml.dist` (see [Coding standards](coding-standards.md)).

### PSR-1, Basic Coding Standard

- Files start with the full `<?php` tag (`Generic.PHP.DisallowShortOpenTag` in `phpcs.xml.dist`) and are UTF-8.
- Class files declare classes and do not produce output; scripts in `bin/php/` are separate.
- Class names do **not** follow StudlyCaps: `expHelpAudience`, `eZContentObject`, `ezpAutoloader` start with a
  lower-case prefix.
- Class constants are upper case (`ExponentialSDK::VERSION_MAJOR` in `lib/version.php`). Methods are camelCase.
- PSR-1 requires classes to be in a namespace for autoloading by PSR-0 or PSR-4; the kernel classes are global.

**Status: Partly complies.**

### PSR-2 (deprecated), PSR-12 and PER Coding Style

`phpcs.xml.dist` enforces the opposite of several PSR-12 rules on purpose: one space inside parentheses
(`foo( $a )`, `if ( $a )`), `array( ... )` instead of `[ ... ]`, opening braces of functions on their own line
(PSR-12 has that too, but also for classes and not for control structures), and no type declarations. It reuses one
PSR-2 sniff (`PSR2.ControlStructures.ControlStructureSpacing`) with changed properties.

**Status: Does not comply**, by design.

### PSR-4, Autoloading Standard

- The root `composer.json` has no `psr-4` or `psr-0` section; its `autoload` only lists `files: ["autoload.php"]`.
- `autoload.php` registers `ezpAutoloader` (class maps) and `ezcBase::autoload` (Zeta Components).
- The kernel class map `autoload/ezp_kernel.php` and the extension class map (under `var/autoload/`) are generated by
  `bin/php/ezpgenerateautoloads.php`; Composer's `post-install-cmd` and `post-update-cmd` run it
  (`"legacy-scripts"` in `composer.json`).
- About 390 files in `kernel/` and `lib/` declare a namespace (for example `Exponential\Command\Kernel`,
  `Exponential\View\Kernel\Content`), but their paths (such as `kernel/private/classes/commands/clusterpurge.php`)
  do not mirror the namespace; they are loaded through the class map too.

**Status: Does not comply.** Class maps are compatible with any file layout, which is why the kernel uses them.

> **Note** After adding or renaming a kernel class, run `php bin/php/ezpgenerateautoloads.php -k`; for an extension
> class, `-e`. A class missing from the map is not found.

### PSR-3, PSR-6, PSR-7, PSR-11, PSR-14, PSR-15, PSR-16, PSR-17, PSR-18

A search of `kernel/`, `lib/`, `bin/` and the extensions in the repository for `Psr\Log`, `Psr\Http`,
`Psr\Container`, `Psr\Cache`, `Psr\SimpleCache`, `Psr\EventDispatcher`, `Psr\Clock` and `Psr\Link` found nothing.

| PSR | What Exponential uses instead |
|---|---|
| PSR-3 logging | `eZDebug::writeError()`, `writeWarning()`, `writeNotice()`, `eZLog` and the request logs |
| PSR-6 / PSR-16 caching | file caches under `var/`, cache handlers configured in INI files |
| PSR-7, PSR-15, PSR-17, PSR-18 HTTP | the kernel's own request handling (`ezpKernelWeb`, `eZHTTPTool`); Velocity requires only `php` and `ext-sockets` (its `composer.json`) and does not use `Psr\Http` |
| PSR-11 container | static singletons such as `eZINI::instance()`, `eZDB::instance()` |
| PSR-14 events | `ezpEvent` and the kernel's own listeners |

`vendor/psr/log` is present only as a dependency of `mongodb/mongodb`.

**Status: Does not comply** (not used). This means Exponential does not plug into PSR-based libraries directly; an
adapter is needed.

## Composer

**What it is.** Composer is the dependency manager for PHP; `composer.json` follows its published JSON schema.

**How Exponential complies.**

- `composer validate --no-check-all --no-check-lock --no-check-publish` on the repository prints
  `./composer.json is valid` (run for this page).
- `name`: `se7enxweb/exponential`, `type`: `ezpublish-legacy` (the type the legacy installer plugin understands),
  `license`: `GPL-2.0-or-later` (an SPDX identifier).
- `require` pins every `se7enxweb/*` extension with a tilde constraint (`~6.0.28`, `~1.4.43`, `~1.0.0.50`): the last
  position floats, the positions above it do not.
- `require-dev`: `phpunit/phpunit` `^9.6 || ^10.5 || ^11.5 || ^12.0 || ^13.4`, nothing else.
- `extra.branch-alias`: `"dev-main": "6.0.x-dev"`, so packages that require `se7enxweb/exponential: ^6.0.14` can
  resolve against `main`. CI sets `COMPOSER_ROOT_VERSION=dev-main` for the same reason.
- `config.allow-plugins` allows only the legacy installer plugins.
- `composer.lock` is not committed (`.gitignore`); every installation resolves from the constraints.
- The development tools (PHP_CodeSniffer, PHPCompatibility, PHPStan) are in a separate `composer.dev.json`, installed
  into `.devtools/` with `make devtools`, never into `vendor/`. Its `config.platform.php` is `8.0.30`.

**Status: Complies.**

## Packagist

**What it is.** Packagist.org is the default public Composer repository.

**How Exponential complies.**

- The root package `se7enxweb/exponential` is listed on Packagist (its `p2` metadata answered on 2026-10-18).
- The GitHub repository has a Packagist webhook configured, so a pushed tag reaches Packagist (checked through the
  GitHub API on 2026-10-18).
- The push gate checks that every `se7enxweb/*` constraint in `composer.json` matches a release Packagist already
  lists, before CI's `composer update` would fail on it ([Continuous integration](../bc/6.0/ci.md), "The local gates
  and CI").

**Status: Complies.**

> **Warning** Packagist reads a tag once. A tag that is moved after Packagist has read it produces two different
> packages with the same version. See [Source control and releases](source-control-and-releases.md).

## Semantic Versioning

**What it is.** Semantic Versioning 2.0.0 (semver.org) defines `MAJOR.MINOR.PATCH`: exactly three numbers, with the
meaning of each increment.

**How Exponential's versions work.**

- **The kernel** is versioned `6.0.x`: `lib/version.php` (`ExponentialSDK`, `VERSION_MAJOR = 6`,
  `VERSION_MINOR = 0`, `VERSION_RELEASE = 15`) and the tags `v6.0.0` to `v6.0.14`. 6.0.15 is in development
  ([6.0.15 changelog](../../changelogs/6.0/6.0.15.md)). Feature work lands in patch releases of the 6.0 line, which is
  not what SemVer intends for PATCH.
- **Extensions** use three or four positions: `exp_adminui ~1.0.0.50`, `bcgooglesitemaps ~1.1.6.6`,
  `expai ~0.0.1.18`, and Exponential Velocity `0.0.4.x`. A four-part number is not valid SemVer 2.0.0, but Composer
  accepts up to four numeric positions and compares each numerically.
- **The last position counts on.** After `0.0.4.9` comes `0.0.4.10`, never `0.0.5.0`
  ([Extensions guide](../guides/extensions.md), "Release it";
  [Velocity changelog](../../changelogs/extensions/exponential-velocity.md)). Moving a higher position is a separate
  decision about what the release means.
- **Tags are permanent**: a bad release is followed by a new one, never re-cut.

**Status: Partly complies.** Versions are ordered and immutable, and Composer handles them correctly, but four-part
numbers and features in patch releases are outside SemVer 2.0.0.

> **Note** Sort tags by version, not by name: `git tag -l 'v*' --sort=version:refname`. By name, `0.0.4.10` sorts
> before `0.0.4.8`.

## Known gaps

1. No PSR-4 autoloading; new classes need the autoload generator.
2. No PSR-3, PSR-7, PSR-11, PSR-14 or caching PSR interfaces; integrating a PSR-based library needs an adapter.
3. `CONTRIBUTING.md` asks for "the PSR standards" and links `CODING_STYLE.md`, which does not exist in the repository.
4. PHPUnit does not run on PHP 8.0; PHPStan analyses as PHP 8.1.
5. Four-part version numbers are not SemVer 2.0.0.
6. The `php` constraint names PHP versions (8.6 to 8.8) that cannot have been tested.

## Related pages

- [PHP 8.0 support](../bc/6.0/php-8.0-support.md)
- [PHP 8 support](../bc/6.0/php8.md)
- [Coding standards](coding-standards.md)
- [Testing and continuous integration](testing-and-ci.md)
- [Source control and releases](source-control-and-releases.md)
- [Develop with Git](../help/develop-with-git.md)
