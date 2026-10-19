---
title: Settings, logs and caches per site
audience: businesses, admin, hosting
category: business-solutions
order: 60
summary: Why and how each brand on one Exponential installation can keep its own settings, log files and caches - per-site overrides in a site extension, logs per site, caches on a memory file system, INI caches cleared per site, and the log viewer.
keywords: per-site settings, INI overrides, site extension, logs per site, UseGlobalLogDir, LogVarDir, CacheVarDir, INICacheDir, ExpiryDir, log viewer, multisite operations, business
type: help
---
# Settings, logs and caches per site

This page is for managers who are responsible for running several brands on one Exponential installation, and for the
administrators who do it. It explains in business terms why each brand's settings, logs and caches can be kept apart,
what that changes in daily operations and support, and which settings the technical team uses. Nothing here is
switched on by default: every setting "keeps the behaviour of before" until someone sets it
([Logs, INI cache and expiry timestamps per site](../../../features/6.0/multi-site-log-and-cache-paths.md#in-short)).

## Contents

1. [Why keep brands apart operationally](#why-keep-brands-apart-operationally)
2. [Settings that belong to one brand](#settings-that-belong-to-one-brand)
3. [Logs per brand](#logs-per-brand)
4. [Caches per brand](#caches-per-brand)
5. [Reading logs without shell access](#reading-logs-without-shell-access)
6. [What to agree with your administrator](#what-to-agree-with-your-administrator)

## Why keep brands apart operationally

| Without separation | With separation |
|---|---|
| One error log for every brand; support searches through all of them | Each brand's errors in its own directory |
| Clearing the settings cache affects every brand | One brand's settings cache cleared on its own |
| All brands' settings in one big override directory | Each brand's settings travel with its own extension |
| Caches on the same disk as everything else | A brand's cache can live in memory for speed |

For a business, separation shortens incident handling ("which brand is affected?") and lets a brand team change its
own settings with less risk to the others.

## Settings that belong to one brand

Since January 2026 an extension that holds a site can carry **override settings that are active only while one of
its own siteaccesses runs**
([Per-site settings inside extensions](../../../features/6.0/multi-site-ini-overrides.md)). For an extension
`site_example`:

| Directory | Applies to |
|---|---|
| `extension/site_example/settings/override/` | every siteaccess of the brand |
| `extension/site_example/settings/override__<group>/` | every siteaccess of the brand whose name starts with `<group>__`, such as one language group |
| `extension/site_example/settings/siteaccess/<siteaccess>/` | that siteaccess only |

The installation's own `settings/override/` still has the last word over all three. The business benefit: a brand's
design, settings and templates live together in one repository, and moving or retiring a brand is moving or retiring
one extension.

> **Note**
> For installations with many site extensions, the same page describes an optional speed-up constant that skips the
> override directories of other `site_` extensions. "Leave it off unless you host many sites."

## Logs per brand

All in `site.ini [FileSettings]`, from the feature page:

| Setting | Default | Effect |
|---|---|---|
| `UseGlobalLogDir` | `enabled` | `disabled` writes the debug logs and the default logs into the brand's own log directory instead of `var/log` |
| `LogDir` | `log` | The log directory; an absolute path, for example on another file system, is used as it is |
| `LogVarDir` | empty | One tree for the logs of every site, such as `var_log/<site>/log`, with one line of settings |

Messages written before the brand is known (while a request is being matched to a siteaccess) still go to `var/log`.

## Caches per brand

| Setting | Default | Effect |
|---|---|---|
| `CacheVarDir` | empty | One tree for the caches of every site, such as `var_cache/<site>/cache`, for example on one memory file system |
| `ExpiryDir` | empty | Keeps the file with the cache-clear timestamps where the storage is, so a restart that empties a memory cache does not lose them |
| `INICacheDir` | `global` | `site` caches the brand's settings in its own cache directory, so clearing them leaves the other brands alone |

The feature page reports the cache commands checked per siteaccess: the brand's settings cache was removed only by
its own clear and by the full clears, and the global one only by its own clear and the full clears. Under Velocity,
after clearing a settings cache, Velocity is restarted, as Setup > Caches says.

> **Warning**
> A cache directory outside the installation must be one the cache clear is allowed to delete in. The feature page
> describes the rules for `AllowedDeletionDirs`; an entry that does not exist now allows nothing. Ask your
> administrator to check this when moving caches.

## Reading logs without shell access

**Setup > Log files** shows the log files of the installation, including the logs of every site
(`var/<site>/log/*.log`) and those extensions write, with a navigator grouped into kernel, site, extensions, web server
and rotated archives ([The log viewer](../../../features/6.0/log-viewer.md)). Every view has a permalink, so support
can send a colleague the exact filtered view. An error page shows a reference that leads to the lines of the failed
request ([Finding an error by its reference](../../../guides/finding-an-error-by-its-reference.md)).

Another useful feature for teams with staging copies: the installation name puts the name of a non-production
installation in front of the page title, so nobody edits the wrong copy
([Installation name in pages](../../../features/6.0/installation-name-in-pages.md)).

## What to agree with your administrator

- [ ] Does each brand live in its own site extension, with its settings there?
- [ ] Are logs kept per brand, and where (which file system, which retention)?
- [ ] Are caches per brand, and on which storage?
- [ ] Who may clear caches for a brand, and how is Velocity restarted after a settings change?
- [ ] Who reads the logs, how often, and how is a brand owner told about errors on their site?
- [ ] Is log retention in line with your privacy policy ([Security](../security/README.md))?

## Related pages

- [One installation, many sites](one-installation-many-sites.md)
- [Multi-brand checklist](multi-brand-checklist.md)
- [Logs, INI cache and expiry timestamps per site](../../../features/6.0/multi-site-log-and-cache-paths.md)
- [Per-site settings inside extensions](../../../features/6.0/multi-site-ini-overrides.md)
- [Multisite on one installation (for hosts)](../../hosting/provisioning/multisite-on-one-installation.md#logs-and-caches-per-site)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
