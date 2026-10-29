---
title: Development environment
audience: cms-developer
category: cms-development
order: 39
summary: For CMS developers, a working development setup in a few commands. It covers the code as Git clones, a one-command install on SQLite, Velocity with PHP's built-in server engine, debug output and template settings, a place for your extensions, team workflow, and the rules that keep development apart from live.
keywords: development environment, local setup, Git, Composer, clones, exp:install, SQLite, Velocity, php -S, velocity-router.php, Engine=php, port 8087, debug output, ShowUsedTemplates, TemplateCompile, debug bar, team workflow, staging
type: help
---
# Development environment

This page is for developers setting up the machine they build sites on. A development installation takes a few
commands: the kernel and extensions as Git clones, a database in one SQLite file, and a web server started by one
command. The book's chapter [Setting up a development installation](../extension-development/part-1-from-user-to-developer/setting-up-a-development-installation.md)
has every detail; this page is the CMS developer's summary with the project around it.

## Contents

1. [What you need](#what-you-need)
2. [Step 1: the code as Git clones](#step-1-the-code-as-git-clones)
3. [Step 2: install with one command](#step-2-install-with-one-command)
4. [Step 3: serve it](#step-3-serve-it)
5. [Step 4: make mistakes visible](#step-4-make-mistakes-visible)
6. [Step 5: your project's extensions](#step-5-your-projects-extensions)
7. [Working as a team](#working-as-a-team)
8. [Rules that keep development and live apart](#rules-that-keep-development-and-live-apart)
9. [When something goes wrong](#when-something-goes-wrong)

## What you need

PHP 8.0 or later with the extensions the root `composer.json` requires (dom, mbstring, json, iconv, simplexml and
others; SQLite 3 for the default database, GD or ImageMagick for images), Composer and Git
([Tools you need](../extension-development/part-1-from-user-to-developer/tools-you-need.md),
[Requirements](../../install/02-requirements.md)).

## Step 1: the code as Git clones

```bash
composer config --global 'preferred-install.se7enxweb/*' source
git clone https://github.com/se7enxweb/exponential.git mysite
cd mysite
git switch -c my-dev
composer install
```

With `preferred-install` set to `source`, every `se7enxweb/*` extension is a Git clone under `extension/<name>` with
its history, so you can read why code is the way it is and send fixes back
([Develop with Git](../develop-with-git.md), [Getting the code](../../install/03-getting-the-code.md)).

## Step 2: install with one command

```bash
php bin/php/console exp:install --apply
```

Without options this installs the `sevenx_multisite` site package on SQLite in `eng-US` and prints the site address,
the administration login and a generated administrator password (also in `var/log/initial-admin-password`, readable
by its owner only). Other databases and packages: [The console install](../../install/07-console-install.md). It
refuses to run over an existing installation unless `--force` is given; never point it at a database that matters.

A shortcut many developers add: `ln -s bin/php/console console`, then `./console list`.

## Step 3: serve it

```bash
./console exp:velocity start
```

Velocity, the Exponential application server, ships with `settings/velocity.ini [ServerSettings] Engine=php`: it runs
PHP's built-in server (`php -S`) with the router `bin/php/velocity-router.php`, on the port of `[PHPServerSettings]`
(8087 shipped). Open the address the installer printed. The persistent `qbix` engine, FrankenPHP and Apache or nginx
with PHP-FPM are the options for production and for testing like production
([Serving the site](../../install/08-serving-the-site.md),
[Lesson 13.2: Velocity for developers](../trainers/developers/module-13-rad-velocity-and-contributing/velocity-for-developers.md)).

## Step 4: make mistakes visible

```bash
./console exp:ini set site.ini/DebugSettings/DebugOutput enabled global --allow-root-user
./console exp:ini set site.ini/TemplateSettings/ShowUsedTemplates enabled global --allow-root-user
./console exp:ini set site.ini/TemplateSettings/TemplateCompile disabled global --allow-root-user
php bin/php/ezcache.php --clear-tag=ini --allow-root-user
```

Errors, SQL and the templates used appear below each page, and template edits show at once. On any machine others can
reach, limit debug output to your address (`DebugByIP`, `DebugIPList[]`)
([Debug output and logs for developers](../extension-development/part-1-from-user-to-developer/debug-output-and-logs-for-developers.md),
[Debug bar](../../bc/6.0/debug-bar.md)). Logs are in `var/log/`.

## Step 5: your project's extensions

```bash
mkdir -p extension/exp_mysite/settings extension/exp_mysite/design/exp_mysite/templates
git -C extension/exp_mysite init
./console exp:ini add 'site.ini/ExtensionSettings/ActiveExtensions[]' exp_mysite global --allow-root-user
```

What goes in them: [Extensions you will write](extensions-you-will-write.md). Classes made in the administration
belong in packages in that repository ([Creating a content class](creating-a-content-class.md#step-5-carry-the-class-to-other-installations)).

## Working as a team

- One development installation per developer, each from the same project repositories.
- Classes and demo content travel in packages; layouts in `exp:layouts` transfer files; settings and templates in
  the extension. Rebuild a developer machine from these, never from a copy of live.
- A shared staging installation, deployed like live ([Deploying a site](deploying-a-site.md)), is where editors and
  testers see work before it goes live.
- Run the project's checks before sharing a change ([Checks before you share a change](../trainers/developers/module-12-tests-and-quality/checks-before-you-share-a-change.md)).

## Rules that keep development and live apart

| Rule | Why |
|---|---|
| Never develop on the live site | a typo in a template or a settings file takes the page down |
| Never copy `settings/override/` between machines | it holds passwords and host-specific values |
| Use copies of live content only when you must | live content can hold personal data |
| Test accounts and mail use `example.invalid` | no test may mail a real person |
| Turn debug output off before going live | it reveals paths, SQL and settings |

## When something goes wrong

| Symptom | Check |
|---|---|
| `composer install` stops with a PHP version error | `php -v`; Exponential 6 needs PHP 8.0 or later |
| A blank page | `var/log/error.log` and the PHP error log |
| A template change has no effect | `TemplateCompile`, the template and override caches |
| A new class is not found | `php bin/php/ezpgenerateautoloads.php -e`, then clear the caches and restart the server |

More: [When something goes wrong](../when-something-goes-wrong.md).

## Related pages

- [Your first hour as a developer](../extension-development/part-1-from-user-to-developer/your-first-hour-as-a-developer.md)
- [Module 1: The development installation](../trainers/developers/module-01-the-development-installation/README.md)
- [Demo mode for developers](../extension-development/part-8-reference/demo-mode-for-developers.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
