---
title: "Siteaccesses: matching a request to a site"
audience: web-framework-developer, developer
category: web-frameworks
order: 50
summary: The siteaccess is the concept framework developers most often miss - a named configuration selected per request by URI, host, port or server variable, before routing - how matching works, what a siteaccess changes, how it compares to tenants, domain routes and Django's sites framework, and how code reads it.
keywords: siteaccess, MatchOrder, URIMatchType, HostMatchType, HostMatchMapItems, HostUriMatchMapItems, AvailableSiteAccessList, multisite, tenant, domain routing, eZSiteAccess, web framework developer
type: help
---
# Siteaccesses: matching a request to a site

This page is for developers who expect one application with one configuration. Exponential answers every request in
the context of a **siteaccess**: a named configuration such as `site`, `admin`, `ger` or `eng`, picked before
routing. The administration is a siteaccess, each language version of a site is usually one, and several sites on one
database are several siteaccesses.

## Contents

1. [What a siteaccess changes](#what-a-siteaccess-changes)
2. [How a request is matched](#how-a-request-is-matched)
3. [The match types](#the-match-types)
4. [Compared with other frameworks](#compared-with-other-frameworks)
5. [Reading the siteaccess in code](#reading-the-siteaccess-in-code)
6. [Practical rules](#practical-rules)

## What a siteaccess changes

A siteaccess has its own settings directory, `settings/siteaccess/<name>/`, and every setting in it overrides the
global default for requests of that siteaccess ([Configuration](configuration-ini-files.md)). Typical differences:

| Setting | Example |
|---|---|
| Design and template overrides | the public design for `site`, an admin design for `admin` |
| Languages | `site.ini [RegionalSettings]` with the site's languages, so `ger` shows German content |
| Content root and tree menu | which node is the front page |
| Extensions | `site.ini [ExtensionSettings] ActiveAccessExtensions[]`: extensions active for this siteaccess only |
| Login | `[SiteAccessSettings] RequireUserLogin`: the administration requires login, a public site does not |
| Caches and logs | per site paths ([Multi-site log and cache paths](../../features/6.0/multi-site-log-and-cache-paths.md)) |

## How a request is matched

`site.ini [SiteAccessSettings] MatchOrder` lists the probes tried in order; the shipped value is `uri;host;port`.
The first probe that yields a siteaccess listed in `AvailableSiteAccessList[]` wins. `eZSiteAccess::match()` does it
in the kernel's constructor, and `eZSiteAccess::change()` switches to the result
([The request lifecycle](the-request-lifecycle.md)).

```ini
# settings/override/site.ini.append.php (a sketch: hosts and names are examples)
<?php /*
[SiteAccessSettings]
AvailableSiteAccessList[]=site
AvailableSiteAccessList[]=admin
MatchOrder=host;uri
HostMatchType=map
HostMatchMapItems[]=www.example.com;site
HostMatchMapItems[]=admin.example.com;admin
*/ ?>
```

## The match types

| Probe | Settings | Matches |
|---|---|---|
| `uri` | `URIMatchType` (`element` shipped, `map`, `text`, `regexp`, `disabled`), `URIMatchElement`, `URIMatchMapItems[]` | the first path segment: `example.com/admin/...` |
| `host` | `HostMatchType` (`map` shipped, `element`, `text`, `regexp`), `HostMatchMapItems[]`, `HostMatchMethod` | the host name: `admin.example.com` |
| `host_uri` | `HostUriMatchMapItems[]=host;uri;siteaccess[;method]` | host and path together: `example.com/ger` |
| `port` | the port entries of `[SiteAccessSettings]` | a port number |
| `servervar` | a server variable set by the web server | a value the web server decides |

When no probe matches, `DefaultHostUriMatchMapItems[]` can pick the siteaccess by host and by the languages the
browser accepts, with a single redirect to the language address
([Siteaccess host and language matching](../../features/6.0/siteaccess-host-and-language-matching.md)).

## Compared with other frameworks

| Framework | Closest concept | Difference |
|---|---|---|
| Symfony | host requirements on routes, a request listener that loads tenant config | a siteaccess changes the whole configuration, including design and active extensions, not only routes |
| Laravel | domain route groups, tenancy packages | built into the kernel and the settings system |
| Django | the sites framework (`SITE_ID`) | chosen per request, not per process |
| Rails | constraints on routes, multiple engines | one database can serve several siteaccesses at once |
| Spring | profiles | a profile is per process; a siteaccess is per request |

Siteaccesses are not environments: the machine's environment (development, test, production) is `EXP_ENV`
([Environments and secrets](environments-and-secrets.md)).

## Reading the siteaccess in code

```php
$access = eZSiteAccess::current();      // array with 'name' and the match details, or null
$name = $access ? $access['name'] : null;
```

Take the current siteaccess from `eZSiteAccess::current()` rather than parsing the URL yourself
([Siteaccess](../../standards/architecture/kernel-architecture.md#siteaccess)). In templates, `$access_type.name`
holds the name. Links built with the `ezurl` operator get the siteaccess prefix when URI matching is used, so never
write absolute paths by hand in templates.

## Practical rules

- Give each siteaccess only the settings that differ; shared settings belong in an extension or `settings/override/`.
- Command line scripts need `-s <siteaccess>` when they depend on siteaccess settings or siteaccess extensions.
- A setting that should apply to one siteaccess must not be placed in an extension's global `settings/` directory:
  extension settings are read after `settings/siteaccess/` and win over it
  ([INI override placements](../../specifications/6.0/ini-override-placements.md)).

## Related pages

- [Multisite setup](../creators/website-creators/multisite-setup.md)
- [Per-site settings in extensions](../../features/6.0/multi-site-ini-overrides.md)
- [Siteaccesses and site addresses](../../install/10-after-installing.md#102-siteaccesses-and-site-addresses)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
