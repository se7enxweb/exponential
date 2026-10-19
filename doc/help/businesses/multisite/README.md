---
title: Several brands on one installation
audience: businesses, project-manager, admin
category: business-solutions
order: 1
summary: The landing page for organizations that run several brands, regions or microsites from one Exponential installation - what the section covers, every page in it, and a suggested reading order.
keywords: multisite, multi-brand, several sites, brands, regions, microsites, siteaccess, one installation, shared content, per-brand design, business
type: help
---
# Several brands on one installation

This section is for business owners, managers and project leads whose organization has more than one website: several
brands, a site per region or country, a main site with campaign microsites, or a shop beside a corporate site. It
explains in plain language how Exponential serves all of them from one installation, what that saves, what it ties
together, and which decisions to take before anyone starts building. Each page ends with links to the technical pages
for whoever does the work.

## Contents

1. [What this section covers](#what-this-section-covers)
2. [Who it is for](#who-it-is-for)
3. [Pages in this section](#pages-in-this-section)
4. [Suggested reading order](#suggested-reading-order)
5. [Neighboring sections](#neighboring-sections)

## What this section covers

Multisite support is one of the main kernel features listed in the project README, and the default site package of the
installer, `sevenx_multisite`, is itself a multisite installation
([Planning multisite and multilingual projects](../../leadership/multisite-and-multilingual-planning.md#multisite-one-installation-several-sites)).
In Exponential each site is one or more **siteaccesses**: a name, a set of settings in `settings/siteaccess/<name>/`,
and a rule for which requests it answers
([10.2 Siteaccesses and site addresses](../../../install/10-after-installing.md#102-siteaccesses-and-site-addresses)).

The pages in this folder translate that into business questions:

- Should our brands share one installation at all, or run separately?
- Which content do the brands share, and which is kept apart?
- How does each brand keep its own look?
- How do links between the brands work without broken pages?
- Where do the settings, logs and caches of each brand live, and who can clear them?
- What do we check before a new brand goes live?

## Who it is for

| Reader | What you get here |
|---|---|
| Owner or manager | The trade-offs in plain language, so you can decide and budget |
| Marketing or brand lead | What stays shared, what each brand controls, how designs differ |
| Project manager | Decisions, checklists and the links to hand to the build team |
| Administrator | A short summary and the exact technical page for each task |

Organizations in the public sector have their own section:
Governments start here.

## Pages in this section

| Page | What it answers |
|---|---|
| [Planning several brands](multi-brand-planning.md) | The questions to settle first, and when one installation is the wrong choice |
| [One installation, many sites](one-installation-many-sites.md) | How siteaccesses, hosts and paths turn one installation into several sites |
| [Sharing content across brands](shared-content-across-brands.md) | One content tree, separate folders, relations, several locations and editors per brand |
| [A design per brand](per-brand-designs.md) | How each brand gets its own look while sharing templates where it helps |
| [Links between your sites](cross-site-links.md) | Why links into another brand's content break, and how `ezurlmultisite` fixes it |
| [Settings, logs and caches per site](per-site-settings-and-logs.md) | Keeping each brand's settings, logs and caches apart for clean operations |
| [Multi-brand checklist](multi-brand-checklist.md) | A launch and operations checklist for adding a brand |

## Suggested reading order

1. Start with [Planning several brands](multi-brand-planning.md) to decide whether one installation suits you.
2. Read [One installation, many sites](one-installation-many-sites.md) for the model behind it.
3. Then [Sharing content across brands](shared-content-across-brands.md) and [A design per brand](per-brand-designs.md),
   the two decisions brand owners care about most.
4. Hand [Links between your sites](cross-site-links.md) and
   [Settings, logs and caches per site](per-site-settings-and-logs.md) to your technical team.
5. Use the [Multi-brand checklist](multi-brand-checklist.md) every time a brand is added.

> **Tip**
> Most organizations with several brands also publish in several languages. Read the
> [multilingual section](../multilingual/README.md) together with this one: a language is often just one more
> siteaccess on the same installation.

## Neighboring sections

- Businesses start here and the reading path
- [Selling in several languages and markets](../multilingual/README.md)
- [Mobile apps, headless use and networks](../mobile-and-headless/README.md)
- [Reliability](../reliability/README.md) and Costs for the operational side of many sites
- [Extranets](../extranets/README.md), when a partner portal runs beside your public brands
- For the technical team: [Setting up several sites in one installation](../../creators/website-creators/multisite-setup.md)
  and, for hosting companies, [Multisite on one installation](../../hosting/provisioning/multisite-on-one-installation.md)

## Related pages

- [Planning multisite and multilingual projects](../../leadership/multisite-and-multilingual-planning.md)
- [Setting up several sites in one installation](../../creators/website-creators/multisite-setup.md)
- [Per-site settings inside extensions](../../../features/6.0/multi-site-ini-overrides.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
