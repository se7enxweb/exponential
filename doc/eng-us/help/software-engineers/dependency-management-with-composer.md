---
title: Dependency management with Composer
audience: software-engineer, developer
category: software-engineering
order: 630
summary: How Exponential manages its dependencies with Composer - the root package and its type, extensions as se7enxweb packages with tilde constraints, no committed lock file, the branch alias for main, the development tools kept out of vendor/, Packagist and its webhook, repinning in the right order, Git clones instead of archives, and the PSR status of the code base.
keywords: Composer, dependencies, composer.json, Packagist, tilde constraint, branch-alias, composer.lock, composer.dev.json, devtools, repin, legacy installer, extensions as packages, PSR, software engineer
type: help
---
# Dependency management with Composer

This page is for engineers who add, upgrade or audit dependencies of an Exponential installation, or who maintain a
package the root requires. The standard is [PHP, PHP-FIG and Composer standards](../../standards/php-and-composer.md).

## Contents

1. [The root package](#the-root-package)
2. [Extensions are packages](#extensions-are-packages)
3. [Constraints and the lock file](#constraints-and-the-lock-file)
4. [Repinning in the right order](#repinning-in-the-right-order)
5. [Development tools](#development-tools)
6. [Clones instead of archives](#clones-instead-of-archives)
7. [PSR status](#psr-status)
8. [Checklist for a new dependency](#checklist-for-a-new-dependency)

## The root package

| Field | Value |
|---|---|
| `name` | `se7enxweb/exponential` |
| `type` | `ezpublish-legacy` (the type the legacy installer plugin understands) |
| `license` | `GPL-2.0-or-later` |
| `php` | `^8.0` up to `^8.8` |
| `extra.branch-alias` | `"dev-main": "6.0.x-dev"` |
| `config.allow-plugins` | only the legacy installer plugins |

Installing Exponential means `composer install` in the root of the repository; the README says the software does not
run without it.

## Extensions are packages

The root `require` lists the extensions every installation gets as `se7enxweb/*` packages, each with a tilde
constraint, for example `se7enxweb/exponential-velocity` at `~0.0.4.55` and `se7enxweb/sevenx_authentication_2fa`
at `~1.0.14`. The legacy installer plugin places them in `extension/`. `suggest` lists optional ones, such as
`se7enxweb/sevenx_valkey_cache` and `se7enxweb/ezfind`, with the PHP extensions each database needs.

## Constraints and the lock file

- A tilde constraint floats the last position only: `~0.0.4.54` accepts `0.0.4.55` and later, never `0.0.5.0`.
- `composer.lock` is not committed; every installation resolves from the constraints. CI's PHPUnit job resolves fresh
  on every run, so a package release can change a result without a change in the repository ([CI gates](ci-gates.md)).
- Packages that require `se7enxweb/exponential` with a version constraint resolve against `main` through the branch
  alias, and CI sets `COMPOSER_ROOT_VERSION=dev-main` for pull requests.

## Repinning in the right order

1. Release the extension ([Release engineering](release-engineering.md)).
2. See the new version on Packagist. A repository without the Packagist webhook stays at an old version until it is
   updated by hand.
3. Commit the root's new constraint.
4. Run the gates on that commit and push it.

A repin to a version Packagist does not list yet breaks the install step of every CI leg at once; the push gate refuses
it for that reason.

## Development tools

PHP_CodeSniffer, PHPCompatibility and PHPStan are resolved from a separate `composer.dev.json` into `.devtools/` with
`make devtools`, for platform PHP 8.0.30, never into `vendor/`. `require-dev` of the root holds only PHPUnit and a code
generator ([Static analysis and code style](static-analysis-and-code-style.md)).

## Clones instead of archives

Composer can install the kernel and the extensions as Git clones instead of archives, so you can see what changed,
bisect a regression, use a fix before it is released and send changes back. Composer refuses to update a clone with
uncommitted or unpushed changes, but a folder cloned by hand where Composer installed an archive is replaced without
asking at the next update; let Composer make the clone with `composer reinstall <package>`. On a live site, update one
package at a time ([Develop with Git](../develop-with-git.md), [Update with Git](../update-with-git.md)).

## PSR status

The project states it plainly: no PSR-4 autoloading (generated class maps instead), no PSR-3, PSR-6, PSR-7, PSR-11,
PSR-14, PSR-15 to PSR-18 interfaces in the kernel, and its own code style instead of PSR-12. Plan adapters if your
system expects those interfaces.

## Checklist for a new dependency

- [ ] Is it needed, or does the kernel or a shipped extension already do it?
- [ ] Its license is compatible with the GNU GPL, version 2 or later.
- [ ] It supports PHP 8.0 (the oldest supported version), or it is optional and guarded.
- [ ] It is maintained, and released on Packagist.
- [ ] The constraint names a released version.
- [ ] Its security advisories are watched (`composer audit` is recommended in the hardening guide).

## Related pages

- [Composer and Packagist, for application creators](../creators/app-creators/composer-and-packagist.md)
- [composer.json for extensions](../extension-development/part-3-extension-developer/composer-json-for-extensions.md)
- [Composer and Git on servers](../hosting/basics/composer-and-git-on-servers.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
