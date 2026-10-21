---
title: Settings and INI for CMS developers
audience: cms-developer
category: cms-development
order: 37
summary: For CMS developers, how Exponential is configured with INI files. It covers the file format, the scopes and the order in which they win, where a project's settings belong, reading and changing values safely with exp:ini, settings in the administration, environment-specific values, and the caches to clear.
keywords: settings, INI, ini files, append.php, settings/override, siteaccess settings, extension settings, scopes, order, exp:ini, get, where, set, eZINI, ezini operator, environment settings, secrets, INI cache, configuration
type: help
---
# Settings and INI for CMS developers

This page is for developers who configure Exponential projects. All configuration is INI files read in layers, and
knowing which layer wins is most of the job. The rule for a project: never edit the shipped files in `settings/`;
put your values in your extension, and machine-specific values in `settings/override/`.

## Contents

1. [The format](#the-format)
2. [The scopes and who wins](#the-scopes-and-who-wins)
3. [Where a project's settings belong](#where-a-projects-settings-belong)
4. [exp:ini: read and change safely](#expini-read-and-change-safely)
5. [Settings in the administration](#settings-in-the-administration)
6. [Values that differ per machine](#values-that-differ-per-machine)
7. [Reading settings in templates and PHP](#reading-settings-in-templates-and-php)
8. [Caches and workers](#caches-and-workers)

## The format

```ini
<?php /* #?ini charset="utf-8"?

[DesignSettings]
SiteDesign=exp_mysite
AdditionalSiteDesignList[]
AdditionalSiteDesignList[]=standard

[RegionalSettings]
TranslationSA[eng]=English
*/ ?>
```

Override files end in `.ini.append.php` and wrap their content in a PHP comment, so a web server that serves the file
shows nothing. `Name[]` with no value clears an array before the next lines add to it; `Name[key]=value` sets a hash
entry ([How INI settings work](../extension-development/part-2-builder/how-ini-settings-work.md)).

## The scopes and who wins

After the shipped default in `settings/`, the kernel reads these directories, and a later file wins:

1. an extension's per-siteaccess folders (`extension/<ext>/settings/siteaccess/<sa>/` and its `override/`),
2. `settings/siteaccess/<sa>/`,
3. `extension/<ext>/settings/` of every active extension (an extension earlier in `ActiveExtensions[]` wins),
4. `settings/override/`.

Note that an extension's global settings are read **after** `settings/siteaccess/<sa>/`, so they beat it
([Settings scopes and the order they win in](../extension-development/part-2-builder/settings-scopes-and-their-order.md)).

## Where a project's settings belong

| Setting | Put it in |
|---|---|
| Your design, classes' templates, view modes, image aliases, custom tags | `extension/exp_mysite/settings/` |
| Everything about one public site or language | `extension/exp_mysite/settings/siteaccess/<sa>/` |
| Database, mail transport, debug, host names of this machine | `settings/override/` (never committed) |
| Siteaccess matching (`[SiteAccessSettings] MatchOrder`, host maps, `AvailableSiteAccessList[]`) | `settings/override/site.ini.append.php`: they are read before a siteaccess is chosen |

Keeping site settings inside your extension means the whole site is in one repository and a new machine needs only
its `settings/override/` ([Siteaccess settings](../extension-development/part-2-builder/siteaccess-settings.md),
[Extension settings for builders](../extension-development/part-2-builder/extension-settings-for-builders.md)).

## exp:ini: read and change safely

```bash
./console exp:ini get site.ini/DesignSettings/SiteDesign -s <siteaccess>      # the value in effect
./console exp:ini where site.ini/DesignSettings/SiteDesign -s <siteaccess>    # every file that sets it, in load order
./console exp:ini list site.ini/DebugSettings                                  # a block
./console exp:ini set site.ini/DebugSettings/DebugOutput enabled global --dry-run --allow-root-user
./console exp:ini add 'site.ini/ExtensionSettings/ActiveExtensions[]' exp_mysite global --allow-root-user
```

`where` answers "why is this value not what I set?" in one call. Writes keep a backup in `var/backup/ini/`, clear the
INI cache, and accept `--dry-run`. Scopes: `global`, `siteaccess:<sa>`, `extension:<ext>`,
`extension:<ext>:siteaccess:<sa>`; writing the shipped defaults is refused
([The exp:ini command](../extension-development/part-2-builder/the-exp-ini-command.md),
[exp:ini in the console](../../bc/6.0/console-exp-ini.md)).

## Settings in the administration

**Setup** > **Ini settings** (`settings/view`) shows the settings of each siteaccess with the file each value comes from, and can change
values for administrators ([The settings page](../../guides/settings-page.md),
[Settings in the admin](../extension-development/part-2-builder/settings-in-the-admin.md)). Changes made there land in
files; copy the ones that belong to the project into your extension.

## Values that differ per machine

Database credentials, mail transports and host names differ between development, staging and live. Keep them in each
machine's `settings/override/`, which is not committed and not copied between machines. A few things that must be known
before the settings are read come from environment variables and `config.php` ([Environment variables](../../guides/environment-variables.md),
[Environment settings](../../features/6.0/environment-settings.md)). Never commit secrets to a repository, and never
copy `settings/override/` from live to a development machine.

## Reading settings in templates and PHP

```smarty
{ezini( 'SiteSettings', 'SiteName', 'site.ini' )|wash()}
```

```php
<?php
$ini = eZINI::instance( 'exp_mysite.ini' );
$limit = $ini->hasVariable( 'ListSettings', 'Limit' ) ? (int)$ini->variable( 'ListSettings', 'Limit' ) : 10;
```

A settings file of your own (`exp_mysite.ini`) lives in your extension's `settings/` and is read like the kernel's
([The ezini operator](../designers/templates/ezini-operator.md), [eZINI](../extension-development/part-8-reference/ezini.md),
[Settings in an extension](../extension-development/part-3-extension-developer/settings-in-an-extension.md)).

## Caches and workers

Settings are cached. After editing a file by hand:

```bash
php bin/php/ezcache.php --clear-tag=ini --allow-root-user
```

`--clear-tag=ini` covers the global INI cache too. Velocity's workers keep settings in memory: restart Velocity after
INI changes (`./console exp:velocity restart --allow-root-user`), and reload the PHP-FPM that serves the site for
changes that affect loaded classes ([Deploying a site](deploying-a-site.md)).

## Related pages

- [Siteaccesses and multisite](siteaccesses-and-multisite.md)
- [Append files and safety](../extension-development/part-2-builder/append-php-files-and-safety.md)
- [Module 8: Settings and siteaccesses](../trainers/developers/module-08-settings-and-siteaccesses/README.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
