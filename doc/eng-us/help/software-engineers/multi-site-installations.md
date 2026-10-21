---
title: Multi-site installations
audience: software-engineer, admin
category: software-engineering
order: 430
summary: How one Exponential installation serves several sites - siteaccesses per site and language, shared or separate content trees, per-site settings inside extensions, per-site logs, caches and INI caches, cross-site links, and the engineering trade-offs against separate installations.
keywords: multi-site, multisite, several sites, siteaccess, VarDir, per-site settings, extension per site, logs per site, cache per site, cross-site links, hosting, tenancy, software engineer
type: help
---
# Multi-site installations

This page is for engineers who design an installation that serves more than one site, and for those who decide between
one installation and several. Exponential has served several sites from one code base for a long time; 6.0.15 added
per-site settings inside extensions and per-site logs and caches.

## Contents

1. [The building blocks](#the-building-blocks)
2. [Per-site settings inside extensions](#per-site-settings-inside-extensions)
3. [Per-site logs and caches](#per-site-logs-and-caches)
4. [Links between sites](#links-between-sites)
5. [One installation or several](#one-installation-or-several)
6. [Checklist](#checklist)

## The building blocks

| Block | What it gives |
|---|---|
| Siteaccess per site (and per language) | its own settings, design, host or path match ([Siteaccesses and configuration](siteaccesses-and-configuration.md)) |
| Content tree | sites can share one tree or each use a subtree (`content.ini [NodeSettings] RootNode`, `site.ini [SiteSettings] IndexPage` per siteaccess) |
| `VarDir` per site | storage, cache and logs of each site in `var/<site>/` |
| A site in its own extension | its settings, design and code versioned together |
| Roles with subtree or section limitations | editors of one site cannot change another |

## Per-site settings inside extensions

Since January 2026 an extension that holds a site can carry override settings that are active only while one of its
siteaccesses runs: `extension/<site>/settings/override/` applies to all of its siteaccesses, and its
`settings/siteaccess/<name>/` to one. Clearing the INI cache clears it for every project on the installation
([Per-site settings inside extensions](../../features/6.0/multi-site-ini-overrides.md)).

## Per-site logs and caches

All in `site.ini [FileSettings]`; every default keeps the old behavior:

| Setting | Effect |
|---|---|
| `UseGlobalLogDir=disabled` | debug logs go to the site's log directory instead of `var/log` |
| `LogDir`, `LogVarDir` | logs on another file system, or every site's logs under one directory |
| `CacheVarDir` | every site's caches on one memory file system |
| `ExpiryDir` | expiry timestamps kept where the cache storage is |
| `INICacheDir=site` | the INI cache per site, cleared on its own |

Under Velocity and on the command line every value is derived again from `site.ini` when the siteaccess changes;
nothing is kept from one request or site to the next ([Logs, INI cache and expiry timestamps per site](../../features/6.0/multi-site-log-and-cache-paths.md)).

## Links between sites

When several sites share one content tree and a page of one site lists content of another (a tag page, a search
result, a related item), the links point into the wrong site and answer 404 unless the `ezurlmultisite` extension
resolves them to the other site's address ([Links to content of another site](../../features/6.0/multisite-cross-site-links.md)).

## One installation or several

| One installation | Several installations |
|---|---|
| one deploy, one upgrade, one set of extensions | each site upgrades on its own schedule |
| shared content and users are possible | strict isolation of data and code |
| one database and one failure domain | failures stay in one site |
| per-site settings, logs and caches as above | per-site everything by construction |

Choose one installation when the sites share people, content or release schedule; choose several when they must be
isolated from each other.

## Checklist

- [ ] Each site has its siteaccesses, in `AvailableSiteAccessList[]`, with host or path matching.
- [ ] Each site's settings live in its extension, not in a shared `settings/override/`.
- [ ] Logs and caches are per site where operators need them apart.
- [ ] Roles keep each site's editors in their subtree or section.
- [ ] The trusted proxy list and the certificates cover every host name.
- [ ] Cronjobs run once per installation, not once per site, unless a part is site-specific.

## Related pages

- [Multisite and multilingual planning](../leadership/multisite-and-multilingual-planning.md)
- [Siteaccess settings](../extension-development/part-2-builder/siteaccess-settings.md)
- [Internationalization and right to left](i18n-and-right-to-left.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
