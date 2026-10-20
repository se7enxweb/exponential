---
title: Shared services for the municipalities of a region
audience: government, project-manager, admin, website-creator
category: public-sector
order: 70
summary: How a regional body can host websites for the smaller municipalities of its territory on one Exponential installation - a siteaccess and section per municipality, a shared accessible design, shared content - and what must be agreed before starting.
keywords: shared services, municipalities, regional hosting, multisite, siteaccess per municipality, shared design, shared content, service agreement, data controller
type: help
---
# Shared services for the municipalities of a region

This page is for project managers, administrators and website creators of a regional body (or an association of
municipalities) that hosts websites for several small municipalities. Small municipalities often lack a web team;
a shared platform gives each its own site with a tested design while the region runs the system. This page explains
how Exponential supports that and what must be agreed.

> **Note** This page describes features and common practice; it is not legal advice. Hosting another body's site
> involves data protection and procurement rules; check them with your legal counsel.

## Contents

1. [The model](#the-model)
2. [Setting up a municipality](#setting-up-a-municipality)
3. [Shared content](#shared-content)
4. [Agreements to make](#agreements-to-make)
5. [Limits](#limits)

## The model

| Part | Per municipality | Shared |
|---|---|---|
| Site | a siteaccess with its host name and start node | |
| Content | a top folder in the tree, in its own section | common pages placed with extra locations |
| Editors | a user group and a role assignment limited to its section | the role definitions |
| Design | a color and logo variant through settings or a small design extension | one accessible design |
| Classes | | one model |
| Operations | | backups, upgrades, monitoring by the region |

How siteaccesses, designs and settings combine is in [Multisite setup](../../../creators/website-creators/multisite-setup.md)
and [Multi-site INI overrides](../../../../features/6.0/multi-site-ini-overrides.md). Each site can have its own log
and cache paths ([Multi-site log and cache paths](../../../../features/6.0/multi-site-log-and-cache-paths.md)).

## Setting up a municipality

1. **Content.** Create a folder `<municipality>` under the content root; create its standard pages (services, council,
   news, contact) from your template structure. **Copy subtree** in the item's menu copies a prepared skeleton.
2. **Section.** **Setup > Sections**, **New section**, name and identifier (`muni_<name>`), then **Assign content**
   to the new folder ([Sections](../../../../guides/sections.md)). Add the section to the Anonymous role's
   `content/read` policy.
3. **Group and role.** Create a user group `<municipality> editors`; on the Editor role's page, **Assign with
   limitation**, Section = the new section, then the group.
4. **Siteaccess.** Create `settings/siteaccess/<name>/` with the host, the start node of the folder, the design and
   the languages; add the siteaccess to `[SiteAccessSettings] AvailableSiteAccessList[]` and its host matching.
   Clear the INI cache: `php bin/php/ezcache.php --clear-tag=ini`.
5. **Check.** Open the host; log in as a municipal editor and confirm you can edit only your folder.

## Shared content

| Content | How to share |
|---|---|
| Regional services every municipality links to | link to the region's own pages |
| Common legal pages (privacy notice for the shared platform, accessibility statement for the shared design) | one object, a location in each municipality's tree, adapted text where each municipality is the controller |
| Common news from the region | an RSS export of the region's news, shown on each site, or a shared folder placed in each tree |
| Common tags | one tag tree for services and places ([Tagging with eztags](../../../creators/website-creators/tagging-with-eztags.md)) |

## Agreements to make

| Topic | Question |
|---|---|
| Responsibility for content | Each municipality answers for its own content; who checks accessibility of its documents? |
| Data protection | Who is controller and who is processor for forms, accounts and logs? A processing agreement is usually needed. |
| Service level | Availability, support hours, maintenance windows (one window affects every site) |
| Exit | How a municipality leaves: content export as a package, redirects, account removal ([Packages](../../../../guides/packages.md)) |
| Cost sharing | how hosting and development are shared (decided by the members, not by the software) |

## Limits

- One installation means one version and one maintenance window for all sites.
- An extension is active for the installation (or per siteaccess for access extensions); a feature one municipality
  wants is available to all, or needs care in settings.
- Search: decide whether a municipality's search shows only its own site; the template's fetch decides.
- Separate legal bodies with strict separation requirements may need separate installations; see
  [Many ministries on one platform](../national/many-ministries-on-one-platform.md#when-to-use-separate-installations)
  for the same decision at national level.

## Related pages

- [Regional and state government](README.md)
- [Municipal and local government](../municipal-and-local/README.md)
- [Shared header and footer](../../../creators/website-creators/shared-header-and-footer.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
