---
title: Setting up a development installation
audience: user, developer, designer, app-creator
category: extension-development
order: 40
summary: A development installation in a few commands: Git clones, Composer, exp:install on SQLite, Velocity, debug settings, and the rules that keep it apart from live.
keywords: development installation, local, laptop, git clone, composer install, exp:install, sqlite, velocity, php -S, debug, sandbox, staging
type: help
---
# Setting up a development installation

Part 1, chapter 4. You never develop on a live site. This chapter sets up an installation of your own where you can
break things, read the debug output and try every example in this book. It takes a few commands; everything after
the clone is the same whether you work on a laptop, a virtual machine or a container.

## What you get

- The kernel and every extension as **Git clones**, so you can read their history and keep your changes on a branch.
- A database in **one SQLite file**, so there is no database server to set up.
- A **web server** started with one command.
- **Debug output** switched on, so errors are shown instead of hidden.

## Step 1: get the code as Git clones

Tell Composer once to install every `se7enxweb/*` package as a Git clone:

```bash
composer config --global 'preferred-install.se7enxweb/*' source
```

Then clone the kernel, start your own branch and install the packages:

```bash
git clone https://github.com/se7enxweb/exponential.git
cd exponential
git switch -c my-dev
composer install
```

The first `composer install` takes a few minutes: it clones every extension the kernel's `composer.json` requires into
`extension/<name>`, each a Git repository of its own. The full explanation, including how to update later, is in
[Getting the code](../../../install/03-getting-the-code.md) and [Develop with Git](../../develop-with-git.md).

Check:

```bash
ls -d extension/*/.git | wc -l
git -C extension/eztags log --oneline -3
```

Expected: a number of extension clones, and the last three commits of one of them.

## Step 2: install with one command

```bash
php bin/php/console exp:install --apply
```

With no options this installs the `sevenx_multisite` site package on **SQLite**, in `eng-US`, and prints a summary
with the site address, the administration login and a generated administrator password (also written to
`var/log/initial-admin-password`, readable by its owner only). Every option, and the other databases, are documented
in [The console install](../../../install/07-console-install.md).

Many developers add a shortcut so that `./console` works from the root:

```bash
ln -s bin/php/console console
./console list | head
```

The shortcut is not part of the distributed files; this book writes `./console` and assumes you made it.

> **Warning**
> `exp:install` refuses to run over an existing installation unless you pass `--force`, and its default
> `--db-action=remove` empties the database it is given. On your own development machine with SQLite that is what
> you want. Never point it at a database that holds anything you care about.

## Step 3: start a web server

The quickest is Velocity with PHP's built-in server engine, the shipped default:

```bash
./console exp:velocity start
```

`settings/velocity.ini` ships with `[ServerSettings] Engine=php`, which runs `php -S` with the router
`bin/php/velocity-router.php`; its port is set in `[PHPServerSettings]` (8087 by default, see
[Serving the site](../../../install/08-serving-the-site.md)). Open the address the summary of `exp:install` printed,
then the administration. For an Apache or nginx setup, the same chapter has complete examples.

## Step 4: make mistakes visible

A live site hides its errors from visitors. A development site should show them. Make the changes in **your own
settings extension or the override directory of your development machine**, never in `settings/site.ini` itself:

```bash
./console exp:ini set site.ini/DebugSettings/DebugOutput enabled global --allow-root-user
./console exp:ini set site.ini/TemplateSettings/ShowUsedTemplates enabled global --allow-root-user
./console exp:ini set site.ini/TemplateSettings/TemplateCompile disabled global --allow-root-user
php bin/php/ezcache.php --clear-tag=ini --allow-root-user
```

| Setting | Default | On a development machine | What it does |
|---|---|---|---|
| `[DebugSettings] DebugOutput` | `disabled` | `enabled` | Errors, warnings, timing and SQL appear below each page |
| `[TemplateSettings] ShowUsedTemplates` | `disabled` | `enabled` | The debug output lists every template a page used, and which override chose it |
| `[TemplateSettings] TemplateCompile` | `enabled` | `disabled` while you edit templates | Templates are read fresh on every request instead of from the compiled cache |
| `[TemplateSettings] ShowTemplatePathComments` | `disabled` | `enabled` when you hunt for a template | HTML comments in the page source name the template of each part |

`exp:ini set ... global` writes `settings/override/site.ini.append.php` and keeps a backup; `--dry-run` shows the
change first. The settings are explained in
[Debug output and logs for developers](debug-output-and-logs-for-developers.md).

> **Tip**
> Debug output is shown to everyone who can open the page unless you limit it. On a machine only you can reach,
> that is fine. Anywhere else, use `DebugByIP=enabled` with `DebugIPList[]`, or `DebugByUser=enabled` with
> `DebugUserIDList[]`, both in `[DebugSettings]`.

## Step 5: a place for your own extensions

Create a folder for the extension you will build in the tutorials and put it under version control from the start:

```bash
mkdir -p extension/mysite_hello
git -C extension/mysite_hello init
```

If you prefer to keep your own extensions outside `extension/`, the kernel can search additional folders; see
[Extensions in more than one folder](../../../features/6.0/additional-extension-directories.md).

## Rules that keep development and live apart

| Rule | Why |
|---|---|
| Never develop on the live site | A typo in a template or a settings file takes the page down for every visitor. |
| Never copy `settings/override/` from live to development or back | It holds database passwords and host-specific values. Each machine keeps its own. |
| Use a copy of live content only when you must | Live content can contain personal data. A fresh `exp:install` with the demo package is usually enough. |
| Commit your extension, not the kernel | Your work lives in `extension/<yours>`; the kernel is updated through Composer and Git, not by your edits. |
| Turn debug output off before anything goes live | Debug output reveals paths, SQL and settings. |

## When something goes wrong

| Symptom | What to check |
|---|---|
| `composer install` stops with a PHP version error | `php -v`; the kernel needs PHP 8.0 or later |
| `exp:install` stops before the summary | its own message names the check that failed; the log is in `var/log/` |
| The site shows a blank page | `var/log/error.log`, and the PHP error log of your web server |
| A change to a template has no effect | template compilation: clear the template cache, or set `TemplateCompile=disabled` while developing |
| A new class is not found | `php bin/php/ezpgenerateautoloads.php -e`, then clear the caches |

## Related pages

- Previous: [The tools you need](tools-you-need.md)
- Next: [Your first hour as a developer](your-first-hour-as-a-developer.md)
- [Getting the code](../../../install/03-getting-the-code.md), [The console install](../../../install/07-console-install.md), [Serving the site](../../../install/08-serving-the-site.md)
- [Develop with Git](../../develop-with-git.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
