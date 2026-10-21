---
title: "Packages: extensions, Composer and bundles"
audience: web-framework-developer, developer
category: web-frameworks
order: 390
summary: The extension is Exponential's unit of code, like a Symfony bundle, a Laravel package, a Django app or a Rails engine - its directory layout, metadata, activation globally or per siteaccess, loading order, installing it with Composer from Packagist, and using ordinary Composer libraries inside it.
keywords: extension, bundle, package, Django app, Rails engine, Composer, Packagist, ezpublish-legacy-extension, extension.xml, ezinfo.php, ActiveExtensions, ActiveAccessExtensions, autoloads, loading order, web framework developer
type: help
---
# Packages: extensions, Composer and bundles

This page is for developers who structure code as bundles, packages, apps or engines. In Exponential that unit is the
**extension**: a directory in `extension/<name>/` that can carry modules, classes, templates, settings, datatypes,
workflow event types, cronjob parts, commands, translations, schema files and tests. Extensions are distributed as
Composer packages ([Anatomy of an extension](../creators/app-creators/extension-anatomy.md)).

## Contents

1. [The directory layout](#the-directory-layout)
2. [Metadata](#metadata)
3. [Activation and order](#activation-and-order)
4. [Composer](#composer)
5. [Compared with other frameworks](#compared-with-other-frameworks)

## The directory layout

| Directory | Holds | Registered by |
|---|---|---|
| `modules/<module>/` | `module.php`, views, `function_definition.php` | `module.ini [ModuleSettings] ExtensionRepositories[]`, `ModuleList[]` |
| `classes/` | PHP classes | `php bin/php/ezpgenerateautoloads.php -e` |
| `autoloads/` | template operator registration | `site.ini [TemplateSettings] ExtensionAutoloadPath[]` |
| `design/<design>/` | templates, overrides, stylesheets, scripts, images | `design.ini [ExtensionSettings] DesignExtensions[]` |
| `settings/` | `*.ini.append.php`; `siteaccess/<name>/` per siteaccess | being active |
| `datatypes/`, `eventtypes/event/` | field types, workflow event types | `content.ini`, `workflow.ini` |
| `cronjobs/` | cronjob scripts | `cronjob.ini` |
| `bin/php/` | commands, found as `ext:<ext>:<name>` | nothing |
| `share/`, `sql/<engine>/` | schema and seed data | your install command |
| `translations/<locale>/` | interface translations | `site.ini [RegionalSettings] TranslationExtensions[]` |
| `tests/` | PHPUnit tests | your CI |

## Metadata

| File | Content |
|---|---|
| `extension.xml` | `<metadata>` with name, version, copyright, license, info URL; `<dependencies>` on other extensions |
| `ezinfo.php` | class `<extension>Info` with a static `info()` (Name, Version, Copyright, License, Info_url) |
| `composer.json` | the Composer package, type `ezpublish-legacy-extension`, with `extra.ezpublish-legacy-extension-name` |

The about page (`/ezinfo/about`) reads `extension.xml` first, so both version fields must agree
([Versions and releases](../creators/app-creators/versions-and-releases.md)).

## Activation and order

- `site.ini [ExtensionSettings] ActiveExtensions[]` activates an extension for every siteaccess;
  `ActiveAccessExtensions[]` in a siteaccess's settings activates it there only.
- Order matters: the order of `ActiveExtensions` decides which extension's settings, templates and design files win
  when two provide the same thing; with `ExtensionOrdering=enabled` the declared dependencies decide most of the real
  loading order ([Extension loading order](../../features/6.0/extension-loading-order.md)).
- **Setup > Extensions** shows and switches them ([Extensions guide](../../guides/extensions.md)).
- After activating: regenerate autoloads, clear caches, deploy.

## Composer

```bash
composer require yourvendor/expdemo:~1.0.0
php bin/php/ezpgenerateautoloads.php -e
```

The package lands in `extension/<name>`, the name given by `extra.ezpublish-legacy-extension-name`. Versions come from Git tags;
a published tag is permanent ([Composer packages and Packagist](../creators/app-creators/composer-and-packagist.md)).
`autoload.php` loads Composer's autoloader, so ordinary libraries required by your extension's `composer.json` are
available to its classes.

Content classes and demo content travel separately, as packages (`.ezpkg`) ([Migrations and seed data](migrations-and-dba-schema-files.md#seed-data-and-content)).

## Compared with other frameworks

| Framework | Unit | Registered by | Exponential |
|---|---|---|---|
| Symfony | bundle | `config/bundles.php` | `ActiveExtensions[]` |
| Laravel | package with a service provider | package discovery | Composer plus `ActiveExtensions[]` |
| Django | app | `INSTALLED_APPS` | `ActiveExtensions[]` |
| Rails | engine, gem | `Gemfile`, `mount` | Composer, `ActiveExtensions[]`, modules |
| Spring | starter | auto-configuration | settings in the extension |

Per-siteaccess activation has no common equivalent in other frameworks: one installation can run an extension on one
site and not on another.

## Related pages

- [Your first extension](../creators/app-creators/your-first-extension.md)
- [Packaging your application as an extension](../creators/app-creators/packaging-as-an-extension.md)
- [Extension development: the book](../extension-development/README.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
