---
title: Multi-brand checklist
audience: businesses, project-manager, admin
category: business-solutions
order: 70
summary: A checklist for adding a brand, region or microsite to an Exponential installation that already serves other sites - decisions, build, content, links, operations, launch and the first month.
keywords: checklist, multi-brand, multisite, new brand, launch, siteaccess, design, cross-site links, logs, caches, business
type: help
---
# Multi-brand checklist

This page is for project leads, brand owners and administrators who add a brand to an installation that already runs
other sites. Copy it into your project tracker and tick it off. Every item links to the page that explains it. The
checklist assumes the decisions on [Planning several brands](multi-brand-planning.md) have been taken.

## Contents

1. [Decisions](#decisions)
2. [Build](#build)
3. [Content and teams](#content-and-teams)
4. [Links and languages](#links-and-languages)
5. [Operations](#operations)
6. [Launch day](#launch-day)
7. [The first month](#the-first-month)

## Decisions

- [ ] The brand has a named owner who approves content and the launch.
- [ ] One installation was chosen deliberately; the trade-offs (upgrades together, restores together, shared load)
      are written into the plan ([One installation or several](multi-brand-planning.md#one-installation-or-several)).
- [ ] What the brand shares with the others, and what it keeps apart, is decided
      ([Deciding what to share](shared-content-across-brands.md#deciding-what-to-share)).
- [ ] Address scheme chosen: own host, path below another brand, or both
      ([How visitors reach a brand](one-installation-many-sites.md#how-visitors-reach-a-brand)).
- [ ] Languages of the brand decided (Multilingual checklist).

## Build

- [ ] A folder for the brand exists under the content root.
- [ ] The brand's siteaccess(es) exist, with site name, address, start page, root node and design
      ([Step by step: a second site](../../creators/website-creators/multisite-setup.md#step-by-step-a-second-site)).
- [ ] The siteaccess is registered and matched (host map or path).
- [ ] The brand's design is in its own extension, with no theme files edited in place
      ([A design per brand](per-brand-designs.md)).
- [ ] Per-brand settings live in the brand's site extension where possible
      ([Settings, logs and caches per site](per-site-settings-and-logs.md)).
- [ ] The administration knows the new siteaccess (`SiteList[]`), so previews work.

## Content and teams

- [ ] Start page, menu folders and legal pages (imprint, privacy notice, terms) are published.
- [ ] Shared content is placed or related, not copied.
- [ ] Shared folders, users and media are marked always available where the brand uses other languages.
- [ ] Editors of the brand have a role limited to its folder; central teams keep their own rights
      ([Keeping teams in their brand](shared-content-across-brands.md#keeping-teams-in-their-brand)).
- [ ] An approval workflow is in place if the brand's content must be approved
      ([Workflows](../../../guides/workflows.md)).

## Links and languages

- [ ] `ezurlmultisite` lists the new brand with the node id of **this** installation
      ([Links between your sites](cross-site-links.md)).
- [ ] Links from other brands into the new one, and back, open the right site (tag pages, search, related items).
- [ ] If the brand has several languages: the language switcher links each page to the same page in the other
      language ([Language switchers and addresses per language](../../creators/website-creators/language-switcher-and-language-urls.md)).

## Operations

- [ ] DNS for the brand's host names points to the server.
- [ ] Certificates cover every host name
      ([Going live: hosts, HTTPS and siteaccesses](../../creators/website-creators/going-live-hosts-https.md)).
- [ ] Logs and caches of the brand go where the administrator expects; retention is agreed.
- [ ] Monitoring checks the brand's address, not only the main brand's
      ([Health checks and uptime](../../hosting/operations/health-checks-and-uptime.md)).
- [ ] The backup covers the brand, and everyone understands a restore restores all brands.
- [ ] Caches are warmed for the new brand before announcing it
      ([Preloading caches](../../../guides/preloading-caches.md)).

## Launch day

- [ ] INI cache cleared after the last settings change; under Velocity, Velocity restarted.
- [ ] The brand's start page answers `200` over HTTPS at every address.
- [ ] A test order, form or sign-up works if the brand has one.
- [ ] The other brands still look and behave exactly as before.
- [ ] Sitemaps and feeds of the brand point to its own address
      ([Sitemaps](../../creators/website-creators/sitemaps.md),
      Feeds and syndication).

## The first month

- [ ] The brand's error log is read weekly ([Reading logs without shell access](per-site-settings-and-logs.md#reading-logs-without-shell-access)).
- [ ] Broken links are checked ([Broken links and content checks](../../creators/website-creators/link-and-content-checks.md)).
- [ ] The brand owner confirms that editors can work without help.
- [ ] Lessons learned are added to this checklist for the next brand.

> **Tip**
> Keep a filled-in copy of this checklist with each brand's project documents. When the installation is upgraded,
> the same list tells you which brands and which features to test.

## Related pages

- [Several brands on one installation](README.md)
- [Planning several brands](multi-brand-planning.md)
- [Planning multisite and multilingual projects: checklists](../../leadership/multisite-and-multilingual-planning.md#checklists)
- [Launch checklist for websites](../../creators/website-creators/launch-checklist.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
