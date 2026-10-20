---
title: Composer and Git on servers
audience: hosting, admin
category: hosting
order: 150
summary: How Exponential's code arrives on a server with Composer and Git, why extensions are Composer packages, which install way fits which plan, what a host must provide for Composer, and what never to run casually on a live site.
keywords: Composer, Composer 2, Git, git clone, composer install, composer create-project, composer require, composer update, preferred-install, allow-plugins, exponential-legacy-installer, extensions, vendor, no-dev, release tags, Packagist, ezupdate, git_manager, autoloads, hosting
type: help
---
# Composer and Git on servers

This page is for system administrators and support staff at a hosting company who install and update Exponential
sites, and for product managers deciding whether customers get shell access with Composer and Git. It answers: how
the code arrives, what Composer does that a plain copy does not, which install way fits which kind of plan, and which
commands are dangerous on a live site. The source is
[chapter 3 of the installation book, Getting the code](../../../install/03-getting-the-code.md).

## Contents

1. [Why Composer is part of the platform](#why-composer-is-part-of-the-platform)
2. [What a host provides](#what-a-host-provides)
3. [Three ways to get the code](#three-ways-to-get-the-code)
4. [Versions and tags](#versions-and-tags)
5. [Extensions are packages](#extensions-are-packages)
6. [Updating safely](#updating-safely)
7. [Updates from the administration](#updates-from-the-administration)
8. [Checklist](#checklist)
9. [Related pages](#related-pages)

## Why Composer is part of the platform

Exponential installs with [Composer](https://getcomposer.org/) 2. Composer downloads the Zeta Components, every
extension `composer.json` requires and, when asked, Velocity. Without it, `vendor/` is missing and the site cannot
start. After `composer install`, the script `legacy-scripts` runs `php bin/php/ezpgenerateautoloads.php`, which
generates the class maps ([chapter 2.7](../../../install/02-requirements.md#27-composer)).

## What a host provides

| Item | Why |
|---|---|
| Composer 2, current | `composer.json` lists the installer plugin under `config.allow-plugins`, which Composer reads from 2.2 on |
| The site's PHP as Composer's PHP | Composer checks `php` and `ext-*` against the PHP it runs with; a newer command line PHP picks versions the web PHP cannot run |
| Outbound HTTPS from the server | Composer fetches from Packagist and GitHub |
| Git | for the recommended clone-based way, and for updates of clones |
| Disk for history | a kernel clone with its whole history is about 370 MB; extension clones add to it |

On servers with several PHP versions, run Composer with the site's PHP, for example
`/opt/plesk/php/8.5/bin/php /usr/local/bin/composer install` (adjust both paths to your server).

## Three ways to get the code

From [chapter 3.2](../../../install/03-getting-the-code.md#32-three-ways-to-get-the-code):

| Way | Gives | Fits |
|---|---|---|
| A. `git clone` and `composer install` (recommended) | the kernel and every se7enxweb extension as Git clones with full history | customers who build and change sites; development and staging; managed sites a host maintains with Git |
| B. `composer create-project se7enxweb/exponential` | release archives without history | sites that are only run, quick evaluations, simple provisioning |
| C. `composer require se7enxweb/exponential` into an empty directory | the kernel as a package of the customer's own project | installations managed as a project of their own |

For way A, tell Composer once per user account to install `se7enxweb/*` packages as clones:

```bash
composer config --global 'preferred-install.se7enxweb/*' source
git clone https://github.com/se7enxweb/exponential.git
cd exponential
composer install
```

For way B on a production server, add `--no-dev`, which leaves out the test tools:

```bash
composer create-project se7enxweb/exponential:v6.0.14 exponential --no-dev
```

> **Note**
> On PHP 8.0, no published tag installs: the tags `v6.0.8` to `v6.0.14` require PHP 8.1. Take the 6.0.15 line
> (`dev-main`) until its tag is published. The tags `v6.0.13` and `v6.0.14` also need extra steps on PHP 8.1 to 8.3
> because of their PHPUnit requirement ([chapter 3.3](../../../install/03-getting-the-code.md#33-installing-on-php-80-to-83)).

## Versions and tags

- Exponential is published as the Composer package `se7enxweb/exponential` from
  [github.com/se7enxweb/exponential](https://github.com/se7enxweb/exponential). Release tags are `v6.0.0` and later
  in the 6.0 line; `main` carries the current development.
- **A release tag never changes.** A fix always arrives as the next version, so a pinned version gives the same files
  every time: the right choice for production.
- Sort versions by version, never by name: by name `v6.0.10` sorts before `v6.0.8`.

  ```bash
  git tag -l 'v*' --sort=version:refname | tail -5
  ```

## Extensions are packages

Every extension except the four shipped in the kernel repository (`ezjscore`, `ezoe`, `ezformtoken`,
`expservices`) is a Composer package that the installer plugin places in `extension/<name>`. So `composer.json`
decides which extensions an installation has and their versions
([chapter 3.5](../../../install/03-getting-the-code.md#35-extensions-are-composer-packages)).

Adding one is three steps:

```bash
composer require se7enxweb/ezfind
php bin/php/ezpgenerateautoloads.php -e
php bin/php/ezcache.php --clear-tag=ini
```

and activating it in `[ExtensionSettings] ActiveExtensions[]` (in the administration under **Setup > Extensions**, or
in `settings/override/site.ini.append.php`); restart Velocity if it serves the site.

The `suggest` block of `composer.json` lists optional extensions a host may want to offer as add-ons, among them
`ezfind` (Solr search), `sevenx_valkey_cache` (cache on Valkey or Redis), `ezscriptmonitor` and `hcaptcha`.

## Updating safely

> **Warning**
> Do not run `composer update` casually on a live site. It moves every package to the newest version its constraint
> allows. Update one package deliberately (`composer update se7enxweb/<package>`) and read its release notes first.

- Clones that Composer made itself are safe: Composer refuses to update a clone with uncommitted changes or unpushed
  commits ([When Composer refuses](../../develop-with-git.md#when-composer-refuses)).
- A clone made **by hand** in place of a package Composer installed as an archive is not safe: Composer still treats
  the folder as an archive and replaces it, history and commits included. Use `composer reinstall <package>` after
  setting `preferred-install` instead.
- Moving the code is half of an upgrade; the database update files are the other half
  ([chapter 11](../../../install/11-upgrading.md)).
- Back up before every update ([What to back up](../operations/backups-what-to-back-up.md)).

A safe update of a clone-based site:

```bash
git fetch origin --tags
git merge v6.0.<n>                 # or origin/main
composer install
```

## Updates from the administration

The `ezupdate` extension, required by `composer.json`, is a package manager in the administration (**Setup >
Updates and packages**) and on the command line. **Updating and installing are off until switched on** in
`settings/override/ezupdate.ini.append.php` (`[UpdateSettings] AllowUpdate`, `AllowInstall`), because Composer
rewrites `vendor/` and every package it manages ([ezupdate](../../../features/6.0/extensions/ezupdate.md)).

For a host this is a policy decision per plan: leave it off where the host manages updates, or switch it on for
customers who manage their own. See [Automated updates with ezupdate](../operations/automated-updates-with-ezupdate.md).

## Checklist

- [ ] Composer 2, current, available to the site's user.
- [ ] Composer runs with the site's PHP version.
- [ ] Outbound HTTPS to Packagist and GitHub allowed.
- [ ] Production installs use `--no-dev`.
- [ ] Customer sites pinned to release tags unless they follow `main` on purpose.
- [ ] No casual `composer update` on live sites; a backup before every update.
- [ ] A policy per plan for `ezupdate`'s `AllowUpdate` and `AllowInstall`.

## Related pages

- [Chapter 3: Getting the code](../../../install/03-getting-the-code.md)
- [Develop with Git](../../develop-with-git.md)
- [Upgrading customer sites](../operations/upgrading-customer-sites.md) and [Deploying changes](../operations/deploying-changes.md)
- [Extensions guide](../../../guides/extensions.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
