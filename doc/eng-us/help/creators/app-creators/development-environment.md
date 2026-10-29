---
title: Setting up a development environment
audience: app-creator, developer
category: app-creation
order: 50
summary: A development setup for Exponential applications - PHP, Composer and Git, a local install on SQLite, Velocity or the built-in server, development settings, the debug bar, template checks and your extension in its own repository.
keywords: development environment, local install, PHP, Composer, Git, SQLite, Velocity, built-in server, DevelopmentMode, debug, debug bar, eztemplatecheck, app creator
type: help
---
# Setting up a development environment

This page is for application creators setting up a machine to build on Exponential. The goal: a local installation you
can reinstall in minutes, with debugging on and your extension in its own Git repository.

## Contents

1. [Tools](#tools)
2. [A local installation](#a-local-installation)
3. [Serving it](#serving-it)
4. [Development settings](#development-settings)
5. [Your extension in Git](#your-extension-in-git)
6. [Daily commands](#daily-commands)

## Tools

| Tool | Version |
|---|---|
| PHP | 8.0 or newer, for the kernel and for Velocity (from its release 0.0.4.54), with the extensions of [2.3](../../../install/02-requirements.md#23-php-extensions) |
| Composer | 2.x |
| Git | any current version ([Develop with Git](../../develop-with-git.md)) |
| A database | SQLite needs nothing; use the engine of production for integration tests |

## A local installation

```bash
git clone https://github.com/se7enxweb/exponential.git exp-dev
cd exp-dev
composer install
php bin/php/console exp:install --apply --package=sevenx_multisite_clean --url=http://127.0.0.1:8088
```

SQLite, generated administrator password shown once ([The console install](../../../install/07-console-install.md)).
Reinstalling with the same command (`--force`) gives a clean state in minutes.

## Serving it

```bash
composer require se7enxweb/exponential-velocity
php bin/php/console exp:velocity config set --apply ServerSettings Engine qbix
php bin/php/console exp:velocity start
```

Velocity keeps classes in memory: after you add or change a PHP class, `exp:velocity restart` (or
`exp:velocity deploy`). For quick template work, `--engine=php` (PHP's built-in server) runs each request fresh
([8.3 Velocity](../../../install/08-serving-the-site.md#83-exponential-velocity-the-recommended-way)).

## Development settings

In `settings/override/site.ini.append.php` of the development machine only:

```ini
[TemplateSettings]
DevelopmentMode=enabled
ShowTemplatePathComments=enabled

[DebugSettings]
DebugOutput=enabled
```

`DevelopmentMode` makes compiled templates follow their sources and turns the packer off. The debug bar shows queries,
templates, settings and timings ([Exp debug bar](../../../features/6.0/exp-debug-bar.md),
[Debugging and logs](debugging-and-logs.md)). Never copy these settings to production.

## Your extension in Git

Keep the application in its own repository, checked out (or symlinked) into `extension/<ext>`, with its own
`composer.json` so it installs like any extension later ([Composer packages and Packagist](composer-and-packagist.md)).
Activate it in `settings/override/site.ini.append.php`:

```ini
[ExtensionSettings]
ActiveExtensions[]=expdemo
```

## Daily commands

```bash
php bin/php/ezpgenerateautoloads.php -e                  # after adding or renaming a class
php bin/php/ezcache.php --clear-tag=ini --allow-root-user  # after a settings change
php bin/php/eztemplatecheck.php -s<siteaccess> <file.tpl> # template syntax
php -l <file.php>                                        # PHP syntax
php bin/php/console exp:velocity deploy                  # everything, in the right order
```

## Related pages

- [Your first extension](your-first-extension.md)
- [Develop with Git](../../develop-with-git.md)
- [PHP and Composer standards](../../../standards/php-and-composer.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
