---
title: Multisite on one installation
audience: hosting, admin
category: hosting
order: 80
summary: How one Exponential installation serves several sites through siteaccesses, matched by URL, host name or port; what that means for a host's isolation, logs, caches, billing and backups; and when separate installations are the better choice.
keywords: multisite, multi-site, siteaccess, siteaccesses, host matching, HostMatchMapItems, URI matching, port matching, SiteURL, VarDir, logs per site, UseGlobalLogDir, LogVarDir, CacheVarDir, INICacheDir, isolation, billing
type: help
---
# Multisite on one installation

This page is for hosts and administrators who run several sites from one Exponential installation, or who must
decide whether to. It answers: how siteaccesses work, what the host has to configure, how logs and caches can be kept
per site, and when separate installations are better.

## Contents

1. [Siteaccesses in short](#siteaccesses-in-short)
2. [Matching by URL, host or port](#matching-by-url-host-or-port)
3. [Logs and caches per site](#logs-and-caches-per-site)
4. [One installation or several?](#one-installation-or-several)

## Siteaccesses in short

"A **siteaccess** is one way into the installation: a name, a set of settings in `settings/siteaccess/<name>/`, and a
rule for which requests it answers" ([10.2](../../../install/10-after-installing.md#102-siteaccesses-and-site-addresses)).
The installer creates at least the public siteaccess and the administration (and an editor siteaccess with
`exp:install`). More public siteaccesses can serve more sites, languages or designs from the same content repository.

The site package `sevenx_multisite` is built for this ("7x MultiSite Default Installation"; [Site packages](site-packages.md)).

## Matching by URL, host or port

The rules live in `site.ini [SiteAccessSettings]`; `MatchOrder` is `uri;host;port` as shipped.

| Layout | Example | For a host |
|---|---|---|
| URL | `www.example.com/admin` | one host name, simplest behind any web server |
| Host | `admin.example.com` | "best for production and for HTTPS certificates per host" |
| Port | `:8081` | local work |

Host matching, as in 10.2:

```ini
# settings/override/site.ini.append.php
[SiteAccessSettings]
MatchOrder=host;uri
HostMatchType=map
HostMatchMapItems[]
HostMatchMapItems[]=www.example.com;site
HostMatchMapItems[]=www.example.org;site_org
HostMatchMapItems[]=admin.example.com;admin
```

Each siteaccess sets its own `[SiteSettings] SiteURL`. Add every host name to the web server or Velocity, to DNS and
to the certificate, clear the INI cache (`php bin/php/ezcache.php --clear-tag=ini --allow-root-user`) and test that
each name reaches the right siteaccess. "The host name the browser sends must be exactly the one in
`HostMatchMapItems[]`, without a port and in lower case."

Behind a proxy or load balancer, list it in `site.ini [HTTPHeaderSettings] TrustedProxies[]`, or the site sees the
proxy's scheme and address ([10.2](../../../install/10-after-installing.md#102-siteaccesses-and-site-addresses)).

## Logs and caches per site

When each site has a `VarDir` of its own, these `site.ini [FileSettings]` settings keep its files apart
([Logs, INI cache and expiry timestamps per site](../../../features/6.0/multi-site-log-and-cache-paths.md)):

| Setting | Default | Effect |
|---|---|---|
| `UseGlobalLogDir` | `enabled` | `disabled` writes the debug logs into the log directory of the site instead of `var/log` |
| `LogVarDir` | empty | a tree for the logs of every site, `var_log/<site>/log` |
| `CacheVarDir` | empty | a tree for the caches of every site, `var_cache/<site>/cache` (for example on a memory file system) |
| `INICacheDir` | `global` | `site` caches each site's INI files in its own cache directory, so clearing one leaves the others alone |

"Every setting keeps the behaviour of before" unless you set it. Settings per site inside extensions are described in
[Per-site settings inside extensions](../../../features/6.0/multi-site-ini-overrides.md).

## One installation or several?

| | One installation, several siteaccesses | One installation per site |
|---|---|---|
| Shared content and users | yes | no |
| One update for all sites | yes | one per site |
| Operating-system isolation between sites | no: one system user, one code base | yes ([Multi-tenant isolation](../security/multi-tenant-isolation.md)) |
| One customer's load affects the others | yes | no, with separate pools or instances |
| Backups and restores | together | per site |
| Billing "per site" | define what a site is ([Billing and metering options](../business/billing-and-metering-options.md)) | simple |

Rule of thumb for a host: several siteaccesses on one installation for **one customer** with several sites;
separate installations for **different customers**.

## Related pages

- [Site packages](site-packages.md)
- [Multi-tenant isolation](../security/multi-tenant-isolation.md)
- [Chapter 10: After installing](../../../install/10-after-installing.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
