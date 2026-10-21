---
title: Multi-site offers
audience: reseller, partner, hosting
category: reselling
order: 220
summary: How to sell several sites on one Exponential installation - siteaccesses matched by URL, host or port, the sevenx_multisite site package, per-site logs and caches, when separate installations are better, how to define and price "a site", and an offer checklist.
keywords: multisite, multi-site, siteaccess, several sites, multi-brand, host matching, HostMatchMapItems, sevenx_multisite, per-site settings, logs per site, pricing per site, one installation
type: help
---
# Multi-site offers

This page is for resellers who sell offers with several sites: a group of brands, a company with country sites, a
school with department sites. Exponential serves many sites from one installation through **siteaccesses**, which is
a strong selling point, and a pricing question.

## Contents

1. [How multi-site works](#how-multi-site-works)
2. [One installation or several](#one-installation-or-several)
3. [Defining and pricing a site](#defining-and-pricing-a-site)
4. [What to tell the customer](#what-to-tell-the-customer)
5. [Offer checklist](#offer-checklist)

## How multi-site works

| Concept | What it is | Source |
|---|---|---|
| Siteaccess | "one way into the installation: a name, a set of settings in `settings/siteaccess/<name>/`, and a rule for which requests it answers" | [Multisite on one installation](../../hosting/provisioning/multisite-on-one-installation.md) |
| Matching | `site.ini [SiteAccessSettings]`, `MatchOrder` is `uri;host;port` as shipped; by URL (`example.com/admin`), host (`admin.example.com`) or port | same |
| Host matching by start, end or part of a host name | `HostMatchMethod` (`strict` default, `start`, `end`, `part`) | [Siteaccess by host prefix](../../../features/6.0/siteaccess-host-and-language-matching.md) |
| A ready-made starting point | Site package `sevenx_multisite` ("7x MultiSite Default Installation"), or `sevenx_multisite_clean` without demo content | [Site packages for hosts](../../hosting/provisioning/site-packages.md) |
| Logs and caches per site | `site.ini [FileSettings]` `UseGlobalLogDir`, `LogVarDir`, `CacheVarDir`, `INICacheDir` | [Logs, INI cache and expiry timestamps per site](../../../features/6.0/multi-site-log-and-cache-paths.md) |
| Links between sites | Cross-site links | [Cross-site links](../../../features/6.0/multisite-cross-site-links.md) |

All sites on one installation share one content repository and one user base, so content can be shared across
brands ([Shared content across brands](../../businesses/multisite/shared-content-across-brands.md)).

## One installation or several

| | One installation, several siteaccesses | One installation per site |
|---|---|---|
| Shared content and users | Yes | No |
| One update for all sites | Yes | One per site |
| Operating-system isolation between sites | No | Yes |
| One site's load affects the others | Yes | No, with separate pools |
| Backups and restores | Together | Per site |

The hosting section's rule of thumb: several siteaccesses on one installation for **one customer** with several
sites; separate installations for **different customers**
([Multisite on one installation](../../hosting/provisioning/multisite-on-one-installation.md#one-installation-or-several)).

> **Note**
> Never put two different customers on one installation to save money. They would share users, content repository
> and updates, and neither could leave without the other.

## Defining and pricing a site

"Per site" pricing needs a definition. Choose one and write it into the offer:

| Definition | Clear because | Watch out |
|---|---|---|
| A public siteaccess | Easy to count in `settings/siteaccess/` | Language siteaccesses multiply the count |
| A host name with its own design | Matches what the customer sees | A brand with two languages may be one "site" |
| A brand (design + host names + languages) | Matches the customer's business | Needs a written list |

Price setup per site (host names, certificates, design, testing) and run cost per installation, plus a small amount
per site for support. More options: [Billing and metering options](../../hosting/business/billing-and-metering-options.md).

## What to tell the customer

```text
All your sites run on one Exponential installation. They share content and user accounts where you want,
each has its own address, design and settings, and one update keeps all of them current.
```

And the honest limits: one installation is updated and restored as a whole, and a heavy load on one site is felt by
the others unless you plan capacity for it.

## Offer checklist

- [ ] Number of sites, host names and languages listed.
- [ ] Definition of "a site" written in the offer.
- [ ] Every host name in DNS, the web server or Velocity, and the certificate.
- [ ] Each siteaccess has its own `[SiteSettings] SiteURL` and was tested after an INI cache clear
      (`php bin/php/ezcache.php --clear-tag=ini`).
- [ ] Per-site logs and caches decided.
- [ ] Editors' roles limited to their own site where needed.

## Related pages

- [Multisite on one installation](../../hosting/provisioning/multisite-on-one-installation.md)
- [One installation, many sites](../../businesses/multisite/one-installation-many-sites.md)
- [Multi-brand planning](../../businesses/multisite/multi-brand-planning.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
