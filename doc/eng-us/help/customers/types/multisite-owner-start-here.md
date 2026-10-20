---
title: Owners of several sites: start here
audience: customer, admin
category: customer-success
order: 90
summary: For organizations that run several sites, brands or regions from one Exponential installation: what is shared and what is per site, roles per site, cross-site links, staging and production, and the decisions that keep many sites manageable.
keywords: multisite, several sites, brands, regions, one installation, siteaccess, shared content, per-site settings, cross-site links, staging, installation name
type: help
---
# Owners of several sites: start here

Exponential has "Multi-site support" built in (`README.md`, "Main Exponential features"): one installation can serve
several sites, each with its own address, design and languages, from one content tree or from several. Owning
several sites this way saves work (one upgrade, one set of accounts, shared content) and adds one new kind of
question: is this change for one site or for all of them?

This page is for the owner of a group of sites (brands, regions, departments, campaign sites) and the people who
manage each one. Planning such a setup is covered in [Several brands for businesses](../../businesses/multisite/README.md).

## What is shared and what is per site

| Shared by all sites of one installation | Can be per site |
|---|---|
| The Exponential version and its upgrades | The design and templates (each site its own design) |
| The database and the content tree (or parts of it) | The part of the content tree a site shows |
| User accounts, roles and the administration | Languages and addresses (siteaccesses by host or path) |
| Extensions installed in the installation | Settings, including those carried in a site's own extension (`doc/eng-us/features/6.0/multi-site-ini-overrides.md`) |
| Backups, unless your host separates them | Logs and caches, with a VarDir per site (`doc/eng-us/features/6.0/multi-site-log-and-cache-paths.md`) |

Ask your partner to fill in this table for your installation and keep it with your contracts. It answers most of
the questions a new manager will ask.

## A working example

The demonstration installation at <https://alpha.se7enx.com> serves several sites from one content tree: the "Fit &
Healthy" site on `/site` or `/` and the "Bold Agency" site on `/bold`, with their translations
(`doc/eng-us/features/6.0/multisite-cross-site-links.md`). When a page of one site lists content of another (a tag page, a
search result, a related item), the links have to point into the right site; the `ezurlmultisite` extension does
that. If you see links of one site that open in another or answer "not found", mention that page to your partner.

## Roles per site

Give each site its own managers and editors, limited to that site's part of the content tree and its sections
([Roles for your team](../day-to-day/roles-for-your-team.md), [Roles and policies](../../../guides/roles-and-policies.md)).
Keep the few people who may change all sites, or the shared parts, small and named.

## Staging and production

Groups of sites usually have a test copy next to the live installation. The installation name feature puts the
name of the installation in front of the page title on every installation that is not production, so nobody
changes the wrong one (`doc/eng-us/features/6.0/installation-name-in-pages.md`). Ask your partner to switch it on if your
tabs look the same.

## Decisions that keep many sites manageable

- **One owner per site**, written down, plus one owner of the shared parts.
- **A rule for shared content**: who may change an item that appears on several sites
  ([Shared content across brands](../../businesses/multisite/shared-content-across-brands.md)).
- **One upgrade calendar for all sites**, agreed with every site owner, since an upgrade reaches all of them at
  once ([How updates work](../healthy/how-updates-work.md)).
- **A rule for new sites**: what a new brand or region needs before it starts
  ([Adding a site](../growth/adding-a-site.md)).

## Your next step

Follow the [reading path for owners of several sites](multisite-owner-reading-path.md). Most groups of sites are also
multilingual: add the [multilingual reading path](multilingual-site-reading-path.md).

## Related pages

- [Reading path for owners of several sites](multisite-owner-reading-path.md)
- [One installation, many sites (businesses)](../../businesses/multisite/one-installation-many-sites.md)
- [Per-site settings and logs (businesses)](../../businesses/multisite/per-site-settings-and-logs.md)
- [Multisite setup (builders)](../../creators/website-creators/multisite-setup.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
