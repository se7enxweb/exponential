---
title: Siteaccesses and configuration
audience: software-engineer, developer
category: software-engineering
order: 130
summary: How Exponential is configured - INI files read through eZINI, the order in which override directories win, siteaccess matching, per-environment variants with EXP_ENV, configuration managed by the deployment, secrets in settings/override, and the commands that show where a value comes from.
keywords: configuration, settings, INI, eZINI, cascade, override, siteaccess, MatchOrder, ActiveExtensions, EXP_ENV, environment, ManagedByDeployment, exp:ini, secrets, software engineer
type: help
---
# Siteaccesses and configuration

This page is for engineers who need to reason about configuration: where a value comes from, why it differs between
two sites of one installation, and how to keep secrets and machine-specific values out of version control. The full
reference is [The INI settings cascade](../../standards/architecture/kernel-architecture.md#the-ini-settings-cascade)
and [INI override directories and placements](../../specifications/6.0/ini-override-placements.md).

## Contents

1. [INI files and eZINI](#ini-files-and-ezini)
2. [The order of the cascade](#the-order-of-the-cascade)
3. [Siteaccesses](#siteaccesses)
4. [Environments, deployments and secrets](#environments-deployments-and-secrets)
5. [Finding where a value comes from](#finding-where-a-value-comes-from)
6. [Rules for configuration changes](#rules-for-configuration-changes)

## INI files and eZINI

All configuration is INI files read through `eZINI` (`lib/ezutils/classes/ezini.php`). The defaults ship in
`settings/<file>.ini`; overrides are named `<file>.ini.append.php` (the `.php` suffix keeps them from being served as
text). Code reads a value with:

```php
$value = eZINI::instance( 'site.ini' )->variable( 'SiteSettings', 'SiteName' );
```

No code in `kernel/`, `lib/` or `bin/` parses INI files itself. The merged result is cached as PHP arrays in the INI
cache; after a settings change clear it with the tag, which covers both the per-siteaccess and the global INI cache:

```bash
php bin/php/ezcache.php --clear-tag=ini --allow-root-user
```

## The order of the cascade

Later wins:

| Order | Scope | Directories |
|---|---|---|
| 0 | defaults | `settings/<file>.ini` |
| 1 | `sa-extension` | per-siteaccess directories inside extensions (`extension/<ext>/settings/siteaccess/<sa>/` and related) |
| 2 | `siteaccess` | `settings/siteaccess/<sa>/` |
| 3 | `extension` | `extension/<ext>/settings/` of each active extension |
| 4 | `override` | `settings/override/` |

Two consequences engineers trip over:

- An extension's `settings/` wins over `settings/siteaccess/<sa>/`.
- `settings/override/` wins over everything.

Extensions are switched on in `site.ini [ExtensionSettings] ActiveExtensions[]` (global) and
`ActiveAccessExtensions[]` (per siteaccess); these two cannot be set from inside an extension. The order of
extensions can follow their declared dependencies ([Extension loading order](../../features/6.0/extension-loading-order.md)).

## Siteaccesses

A siteaccess is a named configuration selected per request: a public site, the administration, a language version.
`eZSiteAccess` (`kernel/classes/ezsiteaccess.php`) matches it from `site.ini [SiteAccessSettings]`:

| Setting | Purpose |
|---|---|
| `MatchOrder` | the match types tried, shipped `uri;host;port` |
| `AvailableSiteAccessList[]` | only these siteaccesses are matched |
| `RelatedSiteAccessList[]` | related siteaccesses sharing the same database |
| `HostMatchMapItems[]` | host name to siteaccess |
| `AnonymousAccessList[]` | modules or module views that do not require a sign-in on a siteaccess that otherwise does (`user/register`, `csp/report` ...) |

Code takes the current siteaccess from `eZSiteAccess::current()` and never parses the URL itself. Host and language
matching: [Siteaccess host and language matching](../../features/6.0/siteaccess-host-and-language-matching.md).

## Environments, deployments and secrets

| Need | Mechanism |
|---|---|
| Different values on a developer machine, a test copy and the live site | `EXP_ENV` in `config.env.php` (from `config.env.php.example`), and variant files such as `settings/override/site.ini.test.append.php` ([Settings per environment](../../features/6.0/environment-settings.md)) |
| Configuration that only version control may change | `[ConfigurationSettings] ManagedByDeployment=enabled` per siteaccess: the administration no longer writes settings, templates or packages on the server ([Configuration managed by the deployment](../../features/6.0/managed-by-deployment.md)) |
| Secrets (database passwords, API keys, signing keys) | `settings/override/`, which is never committed; `config.env.php` is in `.gitignore` |
| Several sites in one installation | per-site settings inside extensions ([Multi-site installations](multi-site-installations.md)) |

## Finding where a value comes from

```bash
./console exp:ini where site.ini/SiteSettings/SiteName
./console exp:ini get site.ini/SiteSettings/SiteName
```

`where` lists every file that sets the value, in load order, and the value in effect; `exp:ini` also sets, adds and
removes values in a chosen scope ([exp:ini](../../bc/6.0/console-exp-ini.md)). On a running installation the
administration shows the merged settings under Setup ([The settings page](../../guides/settings-page.md)).

## Rules for configuration changes

- A new setting gets its default in the shipped `settings/<file>.ini` with a comment above it, a safe default, a
  section on its feature page and a line in the changelog.
- New switches use `enabled`/`disabled`.
- Settings are written with `./console exp:ini` or `eZINI::save()`, never by string replacement in a script.
- A new class referenced from a shipped INI file should be guarded with `class_exists()` in the kernel, because a
  Velocity worker started before the deploy does not know it ([Deploying a change](deploying-a-change.md)).

## Related pages

- [Settings and INI files](../creators/app-creators/settings-and-ini-files.md)
- [Settings scopes and their order](../extension-development/part-2-builder/settings-scopes-and-their-order.md)
- [Multi-site installations](multi-site-installations.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
