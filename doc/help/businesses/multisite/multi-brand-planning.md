---
title: Planning several brands
audience: businesses, project-manager, sales-marketing
category: business-solutions
order: 10
summary: The business decisions to take before running several brands, regions or microsites on one Exponential installation - shared or separate, teams, designs, upgrades, and when separate installations are the better choice.
keywords: multi-brand, multisite, planning, brands, regions, microsites, one installation, separate installations, trade-offs, decision, business
type: help
---
# Planning several brands

This page is for owners and managers who are about to put a second brand, a regional site or a set of campaign
microsites next to their main website. It lists the decisions that shape the project, explains what Exponential gives
you when all sites share one installation, and names the cases where separate installations serve you better. The
technical details are on the pages linked at the end of each section.

## Contents

1. [What "one installation" means for a business](#what-one-installation-means-for-a-business)
2. [The five questions to settle first](#the-five-questions-to-settle-first)
3. [Common shapes](#common-shapes)
4. [One installation or several](#one-installation-or-several)
5. [Teams and permissions](#teams-and-permissions)
6. [A planning sheet you can copy](#a-planning-sheet-you-can-copy)

## What "one installation" means for a business

Multi-site support is listed among the main features of Exponential in the project README. One installation is one
kernel, one set of extensions and (usually) one database. Each of your brands is one or more **siteaccesses** with
its own host or path, language and design
([Planning multisite and multilingual projects](../../leadership/multisite-and-multilingual-planning.md#multisite-one-installation-several-sites)).

What that means in practice:

| You gain | You accept |
|---|---|
| One place to update and secure every brand | Every brand moves with the others on an upgrade |
| Shared content and media, one search index | A heavy site slows the others on the same machine |
| One administration for all editors, if you want it | A restore from backup restores all brands at once |
| One backup routine | Brand-specific code must be kept tidy, or it affects all |

These trade-offs are taken from [Setting up several sites in one installation](../../creators/website-creators/multisite-setup.md#trade-offs).
Write them into your project charter, so nobody is surprised later.

## The five questions to settle first

- [ ] **Do the brands share content**, such as one article shown on two sites, or only the installation?
- [ ] **Does each brand have its own design**, or do some share one with different colors and logos?
- [ ] **Do the brands share editors**, or does each have its own team that must not see the others' content?
- [ ] **Do the brands share one database?** Siteaccesses that share one are listed together in
      `RelatedSiteAccessList[]`
      ([10.2 Siteaccesses and site addresses](../../../install/10-after-installing.md#102-siteaccesses-and-site-addresses)).
- [ ] **Will the brands be upgraded together?** One installation means one kernel version for every brand.

If you answer "no" to most of these, read [One installation or several](#one-installation-or-several) carefully.

## Common shapes

| Shape | How it is built | Typical for |
|---|---|---|
| Separate content per brand | One folder per brand under the content root; each siteaccess uses its folder as its root | Several brands of one company |
| Shared content, different designs | The same root, a different design per siteaccess | A trade site and a consumer site showing the same products |
| One brand in several languages | One siteaccess per language | Exporters, international associations |
| Each brand in its own extension | Settings that apply only while that brand's siteaccesses run | Agencies and organizations with many small sites |

Sources: [Setting up several sites in one installation](../../creators/website-creators/multisite-setup.md#shapes-of-a-multisite)
and [Per-site settings inside extensions](../../../features/6.0/multi-site-ini-overrides.md).

> **Note**
> The shapes combine. A common pattern is two brands, each in two languages: four public siteaccesses and one
> administration on the same installation.

## One installation or several

The hosting documentation compares the two options
([Multisite on one installation](../../hosting/provisioning/multisite-on-one-installation.md#one-installation-or-several)):

| | One installation, several siteaccesses | One installation per site |
|---|---|---|
| Shared content and users | yes | no |
| One update for all sites | yes | one per site |
| Operating-system isolation between sites | no: one system user, one code base | yes |
| One site's load affects the others | yes | no, with separate pools or instances |
| Backups and restores | together | per site |

Its rule of thumb: several siteaccesses on one installation for **one customer** with several sites; separate
installations for **different customers**. For a business that means: your own brands usually belong together;
a brand you might sell, spin off or hand to a different owner is safer on its own installation.

When separate installations still need to exchange content, the `syndication` extension exports part of the content
tree of one installation as a feed that another imports, kept in step by cron
([syndication](../../../features/6.0/extensions/syndication.md)).

## Teams and permissions

- One administration can serve every brand; roles limited by subtree keep each team in its brand's folder
  ([Setting up several sites in one installation](../../creators/website-creators/multisite-setup.md#editors-and-the-administration)).
- Several administration siteaccesses are possible when teams should not see each other's sites at all.
- An editor can be limited to signing in to one administration only, with `user/login` limited by siteaccess
  ([Planning multisite and multilingual projects](../../leadership/multisite-and-multilingual-planning.md#roles-for-translators-and-site-teams)).

Decide who owns each brand's content, who may publish, and who approves changes, before roles are built.

## A planning sheet you can copy

```text
Brand plan for <organization>                                  <date>

| Brand | Address (host or path) | Languages | Design | Content root | Editors | Owner |
|-------|------------------------|-----------|--------|--------------|---------|-------|
|       |                        |           |        |              |         |       |

Shared between brands: <content, media, users, products ...>
Kept apart: <...>
Upgraded together: yes / no (if no: separate installations)
Cross-brand links needed: yes / no
Open questions: <...>
```

The leadership section has a longer sheet with languages and siteaccess details
([The planning sheet](../../leadership/multisite-and-multilingual-planning.md#the-planning-sheet)).

## Related pages

- [One installation, many sites](one-installation-many-sites.md)
- [Sharing content across brands](shared-content-across-brands.md)
- [Multi-brand checklist](multi-brand-checklist.md)
- Multilingual planning
- [Planning multisite and multilingual projects](../../leadership/multisite-and-multilingual-planning.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
