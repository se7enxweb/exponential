---
title: Deploying with Velocity
audience: app-creator, developer, admin
category: app-creation
order: 530
summary: Ship code changes to a site served by Exponential Velocity - exp:velocity deploy and its fourteen steps in order, the options for kernel classes, packed assets and Apache-only setups, template-only changes, and checking the result.
keywords: deploy, Velocity, exp:velocity deploy, restart, persistent workers, engine.phar, autoloads, caches, PHP-FPM, OPcache, response cache, packer, app creator
type: help
---
# Deploying with Velocity

This page is for application creators who put a change live on a site served by Exponential Velocity. Velocity keeps the
application in memory between requests, so a new class or setting only takes effect after a restart, and caches must be
cleared in the right order. One command does all of it
([7. Ship a PHP change with one command](../../../guides/deploying.md#7-ship-a-php-change-with-one-command)).

## Contents

1. [The command](#the-command)
2. [What it does, in order](#what-it-does-in-order)
3. [Options](#options)
4. [Template and stylesheet changes](#template-and-stylesheet-changes)
5. [After the deploy](#after-the-deploy)

## The command

```bash
./console exp:velocity deploy --dry-run --allow-root-user   # what it would do
./console exp:velocity deploy --allow-root-user             # the usual case
./console exp:velocity deploy --kernel --allow-root-user    # a kernel class was added or renamed
```

A real run prints PASS, FAIL or SKIP and the time for each step, stops at the first failure and exits with 1.

## What it does, in order

1. Extension autoloads (`--kernel`: kernel autoloads too).
2. INI caches, template, template-override, translation and design caches.
3. The engine archive, rebuilt only when kernel files changed (every file is parsed first, so a broken file stops the
   deploy before any server is touched).
4. PHP-FPM sees the new code: a reload only when OPcache does not revalidate by itself (`[DeploySettings] PhpFpmReload`).
5. Velocity restart; the new parent must answer.
6. Content, HTTP and template-block caches, then Velocity's response cache: last, so a page rendered by old code is
   never cached again.
7. Warm-up requests in the background (`[DeploySettings] WarmUpURL[]`).

## Options

| Option | Use |
|---|---|
| `--kernel` | a kernel class was added or renamed |
| `--packer` | a stylesheet, script or `ezjscServer_*` function changed: also clear packed assets |
| `--no-fpm` | Velocity only |
| `--fpm-reload` | reload PHP-FPM anyway (php.ini or pool changed, files renamed) |
| `--no-velocity` | Apache or nginx only |
| `--no-autoload` | no class changes |
| `--rebuild-phar` | force the engine archive rebuild |

## Template and stylesheet changes

Templates need no restart:

```bash
./console exp:cache clear --id=template --allow-root-user
./console exp:velocity cache clear --apply --allow-root-user
```

A new template file or override rule also needs `template-override`; a stylesheet needs `--packer` or the
`ezjscore-packer` and `template-block` caches cleared.

## After the deploy

- `./console exp:velocity status`: the new parent answers.
- Open a page of each type you changed, logged out and logged in.
- Watch `var/log/error.log` for a few minutes.
- A class referenced by settings must be deployed before the setting goes live, or workers that do not know it fail.

## Related pages

- [A deployment pipeline](deployment-pipeline.md)
- [Velocity engines: deploying a PHP change](../../../bc/6.0/velocity-engines.md)
- [Velocity web server](../../../features/6.0/velocity-web-server.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
