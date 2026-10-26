---
title: Composer for PHP developers
audience: php-developer, developer
category: php-development
order: 460
summary: How Exponential uses Composer - the root composer.json with PHP 8.0 to 8.5, required extensions and packages, the legacy-scripts hook that regenerates autoloads, the installer plugin that puts extensions into extension/, composer.dev.json for the development tools, an extension's own composer.json, version constraints, and what not to do with vendor/.
keywords: Composer, composer.json, Packagist, se7enxweb, exponential-legacy-installer, ezpublish-legacy-extension, require, require-dev, suggest, post-install-cmd, legacy-scripts, composer.dev.json, .devtools, version constraint, tilde, PHP constraint, vendor
type: help
---
# Composer for PHP developers

This page is for PHP developers who add a dependency, package an extension or read the root `composer.json`. Exponential
installs with Composer from Packagist, but it is not a PSR-4 Composer project: Composer installs the extensions and the
libraries, and the kernel's own class maps load the classes.

## Contents

1. [The root composer.json](#the-root-composerjson)
2. [How extensions are installed](#how-extensions-are-installed)
3. [The development tools](#the-development-tools)
4. [An extension's composer.json](#an-extensions-composerjson)
5. [Version constraints](#version-constraints)
6. [Rules](#rules)

## The root composer.json

| Part | Holds |
|---|---|
| `name`, `type` | `se7enxweb/exponential`, type `ezpublish-legacy` |
| `license` | `GPL-2.0-or-later` |
| `require` `php` | `^8.0 \|\| ^8.1 \|\| ... \|\| ^8.5` (and forward-looking ranges to 8.8) |
| `require` `ext-*` | `dom`, `libxml`, `mbstring`, `pcre`, `json`, `iconv`, `reflection`, `session`, `spl`, `simplexml` |
| `require` packages | the installer plugin, Velocity (`se7enxweb/exponential-velocity`), the shipped `se7enxweb/*` extensions, the Zeta Components |
| `require-dev` | `phpunit/phpunit` (`^9.6 \|\| ^10.5 \|\| ^11.5 \|\| ^12.0 \|\| ^13.4`) |
| `suggest` | database extensions (`ext-sqlite3`, `ext-pgsql`, `ext-mysqli`, `mongodb/mongodb`), `ext-gd`, `ext-curl`, optional extensions |
| `autoload` | `files: autoload.php`, so Composer's autoloader loads the kernel's |
| `scripts` | `legacy-scripts` runs `@php bin/php/ezpgenerateautoloads.php` after `install` and `update` |

## How extensions are installed

Packages of type `ezpublish-legacy-extension` are put into `extension/<name>/` (not `vendor/`) by the installer plugin
`se7enxweb/exponential-legacy-installer`, which `config.allow-plugins` permits. After the install the `legacy-scripts`
hook regenerates the class maps, so the extension's classes are found. Libraries (`zetacomponents/*`, `mongodb/mongodb`)
go to `vendor/` and are loaded by Composer's own autoloader.

## The development tools

PHP_CodeSniffer, PHPCompatibility and PHPStan are **not** in the root `require-dev`. They live in `composer.dev.json`,
resolved for PHP 8.0.30 and installed into `.devtools/vendor` by `make devtools`
(`COMPOSER=composer.dev.json composer install`), so they never touch the site's `vendor/`
([Git hooks and make targets](git-hooks-and-make-targets.md)).

## An extension's composer.json

```json
{
    "name": "acme/acmereport",
    "description": "Monthly reports for Exponential",
    "type": "ezpublish-legacy-extension",
    "license": "GPL-2.0-or-later",
    "require": {
        "php": "^8.0",
        "se7enxweb/exponential-legacy-installer": "*"
    }
}
```

- `type` makes the installer put it into `extension/`; the directory name is the part of the package name after the
  `/`, unless `extra.ezpublish-legacy-extension-name` names another one (`LegacyExtensionInstaller::getInstallPath()`).
- Declare `php` `^8.0` unless you need more ([Supported PHP versions: 8.0 to 8.5](supported-php-versions.md)).
- Declare the libraries your code uses; do not ship `vendor/` inside the extension.
- Keep `ezinfo.php` and `extension.xml` versions equal to the release
  ([composer.json for extensions](../extension-development/part-3-extension-developer/composer-json-for-extensions.md),
  [Composer packages and Packagist](../creators/app-creators/composer-and-packagist.md)).

## Version constraints

Exponential packages use three- and four-part versions (`6.0.15`, `0.0.4.54`). The tilde floats the last position:

| Constraint | Allows |
|---|---|
| `~6.0.15` | `>=6.0.15 <6.1.0` |
| `~0.0.4.55` | `>=0.0.4.55 <0.0.5.0` |
| `^8.0` | `>=8.0 <9.0` |

After `.9` comes `.10` in the last position (`0.0.4.9`, `0.0.4.10`). Packagist compares each position as a number; a
plain lexical sort of tags does not, so use `sort -V` or `git tag --sort=version:refname` when a script picks the newest
tag ([Versioning rules](../extension-development/part-7-releasing/versioning-rules.md)).

## Rules

- **A published version never changes.** A tag on Packagist is read once and cached; fix a mistake with the next version
  ([Tags are permanent](../extension-development/part-7-releasing/tags-are-permanent.md)).
- **Check Packagist before repinning.** CI's PHPUnit job resolves dependencies fresh; a constraint pointing to a release
  that is not on Packagist yet fails every leg.
- **Do not edit `vendor/`**, and be careful with `composer update` on an installed site: some installations keep
  extensions as live git checkouts that Composer would replace.

## Related pages

- [PHP, PHP-FIG and Composer standards](../../standards/php-and-composer.md)
- [Modernizing composer.json](../extension-development/part-4-upgrading/modernizing-composer-json.md)
- [The Packagist webhook](../extension-development/part-7-releasing/the-packagist-webhook.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
