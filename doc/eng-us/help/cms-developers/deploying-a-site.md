---
title: Deploying a site
audience: cms-developer
category: cms-development
order: 40
summary: For CMS developers, getting a site from the repository onto staging and live and shipping changes safely. It covers choosing the web server, what a deployment carries, settings per environment, exp:velocity deploy for PHP changes, the lighter steps for templates and settings, maintenance mode, locking configuration on deployed sites, and checks afterwards.
keywords: deploy, deployment, release, staging, live, production, exp:velocity deploy, Velocity, FrankenPHP, Apache, PHP-FPM, reload, autoloads, caches, EXP_ENV, config.env.php, environment settings, maintenance mode, ManagedByDeployment, go live
type: help
---
# Deploying a site

This page is for developers who move a project from the repository to staging and live, and ship changes after
launch. The rule behind every step: the server must run the new code **before** the caches that hold finished pages
are cleared, or old output is cached again.

## Contents

1. [Choose how the site is served](#choose-how-the-site-is-served)
2. [What a deployment carries](#what-a-deployment-carries)
3. [Settings per environment](#settings-per-environment)
4. [The first deployment](#the-first-deployment)
5. [Shipping a PHP change](#shipping-a-php-change)
6. [Shipping templates, styles and settings](#shipping-templates-styles-and-settings)
7. [Maintenance mode](#maintenance-mode)
8. [Lock configuration on deployed sites](#lock-configuration-on-deployed-sites)
9. [After every deployment](#after-every-deployment)

## Choose how the site is served

| Server | When |
|---|---|
| Exponential Velocity (`qbix` engine) | the recommended engine: a PHP application server that ships with Exponential, persistent workers, its own response cache and HTTPS |
| FrankenPHP | the production-ready alternative |
| Apache or nginx with PHP-FPM | where the host's stack is fixed |

Each with its setup: [Deploying Exponential](../../guides/deploying.md) and
[Choosing a web server](../hosting/servers/choosing-a-web-server.md).

## What a deployment carries

| Comes from | Carries |
|---|---|
| Composer and Git | the kernel and the shipped extensions, at the versions in your `composer.json` and `composer.lock` |
| Your repositories | your extensions: design, templates, settings, code, packages |
| The machine itself | `settings/override/` (database, mail, hosts), `config.env.php`, `var/` (storage, caches, logs) |
| The database | content, classes, users, layouts |

Content is never deployed from development to live: editors make it on live. Classes and layouts reach live through
packages and transfer files, applied once ([Site packages and installers](site-packages-and-installers.md),
[Layouts for CMS developers](layouts-for-cms-developers.md#layouts-between-installations)).

## Settings per environment

`EXP_ENV` names the environment of a machine in `config.env.php` (copied from `config.env.php.example`, kept out of
version control), and any settings file can have a variant for it, such as
`settings/override/site.ini.test.append.php`. Mail transport, site addresses and debug settings then differ per
machine without edits after each deployment ([Settings per environment](../../features/6.0/environment-settings.md)).

## The first deployment

1. Check the machine: PHP version and extensions, writable directories, database
   ([Requirements at a glance](../hosting/basics/requirements-at-a-glance.md)).
2. Get the code (`composer install` from your project), add your extensions, write `settings/override/`.
3. Install or import the database, then your packages and layouts.
4. Generate autoloads, clear caches, start the server.
5. Schedule the cronjobs ([Cronjobs](cronjobs.md)) and set up backups ([What to back up](../hosting/operations/backups-what-to-back-up.md)).
6. Go through the [Project launch checklist](project-launch-checklist.md).

## Shipping a PHP change

One command does every step in the right order and stops at the first failure:

```bash
./console exp:velocity deploy --dry-run --allow-root-user   # what it would do
./console exp:velocity deploy --allow-root-user             # the usual case
./console exp:velocity deploy --kernel --allow-root-user    # a kernel class was added or renamed
```

It regenerates autoloads, clears the INI and template caches, makes PHP-FPM and Velocity run the new code, then clears
the content and response caches and warms the first pages. Options such as `--no-fpm`, `--no-velocity` and `--packer`
(also clear packed scripts and styles) are explained in
[Ship a PHP change with one command](../../guides/deploying.md#7-ship-a-php-change-with-one-command) and, for hosts,
in [Deploying changes](../hosting/operations/deploying-changes.md).

## Shipping templates, styles and settings

| Change | Steps |
|---|---|
| An existing template | `./console exp:cache clear --id=template --allow-root-user`, then `./console exp:velocity cache clear --allow-root-user` |
| A new template or override rule | clear `ini`, `template-override` and `content`, then Velocity's cache |
| A stylesheet or script | deploy with `--packer`, or clear the packer and template-block caches; never add `?v=` to asset links |
| An INI file | `--clear-tag=ini`, restart Velocity |

When unsure which cache, see [Caching for CMS developers](caching-for-cms-developers.md#which-cache-after-which-change).

## Maintenance mode

For changes that need the site closed (a database upgrade, a large import):

```bash
./console exp:maintenance on --allow-root-user
./console exp:maintenance status --allow-root-user
./console exp:maintenance off --allow-root-user
```

`php bin/php/maintenance.php on --message="Back at 06:00" --until=2h` sets a message and an expected end; the
administration stays reachable. **Setup** > **Maintenance** does the same in the browser
([Maintenance mode](../../features/6.0/maintenance-mode.md)).

## Lock configuration on deployed sites

When settings live in version control, edits in the administration would be lost at the next deployment. With
`site.ini [ConfigurationSettings] ManagedByDeployment=enabled` in the administration siteaccess, the settings editor,
template editor, extension activation, package installation and the code generators are closed in the browser;
command-line scripts and the deployment still write ([Configuration managed by the deployment](../../features/6.0/managed-by-deployment.md)).

## After every deployment

- The front page and a few deep pages answer 200 (`curl -sI https://www.example.com/`).
- `./console exp:velocity status` or the web server status shows the expected processes.
- `./console exp:cache status --allow-root-user` shows the layers you expect.
- `var/log/error.log` has no new errors.
- Cronjobs still run.

## Related pages

- [Velocity for hosts](../hosting/servers/velocity-for-hosts.md)
- [Upgrading](../../guides/upgrading.md)
- [Performance](performance.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
