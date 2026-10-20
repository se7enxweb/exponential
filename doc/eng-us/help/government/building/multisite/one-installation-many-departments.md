---
title: One installation, many departments
audience: government, project-manager, admin
category: public-sector
order: 20
summary: How a public body runs the sites of several departments, agencies or programs from one Exponential installation: the shapes a multisite can take, what is shared and what is separate, and the trade-offs to write into the plan.
keywords: multisite, departments, agencies, one installation, several sites, siteaccess, shared content, trade-offs, central web team, portal
type: help
---
# One installation, many departments

This page is for managers and administrators who host several sites (departments, agencies, programs, campaigns) on
one installation. Each site is one or more **siteaccesses**: a name, its settings in `settings/siteaccess/<name>/`,
and a rule for which requests it answers ([Setting up several sites in one installation](../../../creators/website-creators/multisite-setup.md)).

## Contents

1. [Shapes](#shapes)
2. [What is shared and what is separate](#what-is-shared-and-what-is-separate)
3. [Trade-offs](#trade-offs)
4. [When separate installations are better](#when-separate-installations-are-better)

## Shapes

| Shape | How |
|---|---|
| Separate content per department | one folder per site under the root; each siteaccess uses its folder as root |
| Shared content, different designs | the same root, a different `SiteDesign` per siteaccess |
| One site in several languages | one siteaccess per language (see the [languages section](../../languages/README.md)) |
| Each site in its own extension | settings that apply only while the site's siteaccesses run |

The default site package, `sevenx_multisite`, is itself a multisite installation.

## What is shared and what is separate

| Shared | Separate per site |
|---|---|
| kernel, extensions, updates | siteaccess settings, address, `SiteName` |
| the database and the content tree | the site's folder (root node) |
| users and roles | the design (`SiteDesign`) and its templates |
| media library, search index | languages (`SiteLanguageList[]`) |
| one administration (or several) | logs and caches, if configured per site |

## Trade-offs

| Strength | Constraint |
|---|---|
| one kernel and one set of extensions to update and secure | every site moves with the others on an upgrade |
| shared content and media, one search index | a heavy site slows the others on the same machine |
| one backup | a restore restores all sites |
| one place for roles and the audit trail | administrators of one department can, if roles allow, see others |

Write the trade-off into the plan ([Planning multisite and multilingual projects](../../../leadership/multisite-and-multilingual-planning.md)).

## When separate installations are better

- A department must be isolated by law or policy (separate data controller, separate security zone).
- Release schedules must differ.
- One site's load would harm the others and cannot be separated by servers.

## Related pages

- [Adding a department site](adding-a-department-site.md)
- [Setting up several sites](../../../creators/website-creators/multisite-setup.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
