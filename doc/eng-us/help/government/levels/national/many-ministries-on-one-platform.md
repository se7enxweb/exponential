---
title: Many ministries on one platform
audience: government, project-manager, admin, website-creator
category: public-sector
order: 70
summary: How several ministries or departments can share one Exponential installation - a siteaccess and a section per ministry, a shared design, shared and separate content - and when separate installations are the better choice.
keywords: multisite, shared platform, government portal, ministries, siteaccess, sections, shared design, cross-site links, central web team, separate installations
type: help
---
# Many ministries on one platform

This page is for project managers, administrators and website creators of a central government web team that runs
sites for several ministries or departments. It explains how one Exponential installation serves several sites, how
each ministry is kept to its own content, what can be shared, and when separate installations are better. The
mechanisms are documented in [Multisite setup](../../../creators/website-creators/multisite-setup.md) and the
multi-site feature pages.

## Contents

1. [One installation, several sites](#one-installation-several-sites)
2. [Keeping each ministry to its own content](#keeping-each-ministry-to-its-own-content)
3. [What can be shared](#what-can-be-shared)
4. [When to use separate installations](#when-to-use-separate-installations)
5. [Operating the platform](#operating-the-platform)

## One installation, several sites

In Exponential a site is a **siteaccess**: a name with its own settings (host, design, languages, start node) that
serves part of the shared content tree. Several siteaccesses run on one installation and one database.

| Part | One per ministry | Shared |
|---|---|---|
| Siteaccess (host name, settings in `settings/siteaccess/<name>/`) | yes | |
| Start node of the site (a top folder in the tree) | yes | |
| Design | can be | usually one government design extension |
| Content classes | | yes |
| Users, roles, sections | per ministry groups and sections | one user base |
| Caches, logs | separate paths available ([Multi-site log and cache paths](../../../../features/6.0/multi-site-log-and-cache-paths.md)) | |

Settings can differ per site with INI overrides ([Multi-site INI overrides](../../../../features/6.0/multi-site-ini-overrides.md)).
Links from one site to a page of another are written with the right host
([Cross-site links](../../../../features/6.0/multisite-cross-site-links.md)). Host and language matching decides which
siteaccess answers a request ([Siteaccess host and language matching](../../../../features/6.0/siteaccess-host-and-language-matching.md)).

## Keeping each ministry to its own content

| Measure | How |
|---|---|
| A section per ministry | assign the ministry's top folder to its section ([Sections](../../../../guides/sections.md)) |
| Roles assigned with a Section or Subtree limitation | the shared Editor role, assigned per ministry group with the ministry's section |
| Admin tree starts at the ministry's folder | the content tree top node setting ([Admin content tree top node](../../../../features/6.0/admin-content-tree-top-node.md)) |
| Approvers per ministry | one *Approve* event per ministry, limited to its section |
| Audit per ministry | the audit trail records the user and the object; filter by them ([Audit trail](../../../../features/6.0/audit-trail.md)) |

Test the separation as a user of each ministry: they must not be able to edit, move or remove another ministry's
content, and the admin links they cannot use are hidden ([Admin links follow permissions](../../../../features/6.0/admin-links-follow-permissions.md)).

## What can be shared

| Shared item | Benefit | Care |
|---|---|---|
| The design extension | one accessible design, tested once | each ministry may want a variant: a design per siteaccess on a common base |
| Content classes | one model, one training | a class change affects every ministry |
| Common pages (privacy notice, accessibility statement, cookie information) | written once, placed in several sites with extra locations | the text must fit every ministry |
| Tags | one taxonomy across government | an owner for the vocabulary |
| Search | one index, a search per site or across sites | decide what visitors expect |

## When to use separate installations

| Choose separate installations when | Because |
|---|---|
| Ministries are separate data controllers with separate security accreditation | one database would mix their personal data |
| Release cycles differ (one ministry cannot accept an upgrade when another needs it) | one installation means one version |
| A ministry needs extensions others must not run | extensions are active per installation, partly per siteaccess |
| The platform must keep serving when one site is attacked or overloaded | separate installations fail separately |

Separate installations can still share the design and classes, as an extension and as packages
([Packages](../../../../guides/packages.md)).

## Operating the platform

- **One maintenance window for all.** Maintenance mode takes the whole installation offline
  ([Maintenance mode](../../../../guides/maintenance-mode.md)); announce it to every ministry.
- **Upgrades** affect every site; test each site's templates on the test installation first
  ([Upgrading](../../../../guides/upgrading.md)).
- **Preloading caches** per site after a deploy ([Preloading caches](../../../../guides/preloading-caches.md)).
- **Service levels**: write down which team answers for the platform and which for each ministry's content.

## Related pages

- [National government](README.md)
- [Multisite setup](../../../creators/website-creators/multisite-setup.md)
- [Shared header and footer](../../../creators/website-creators/shared-header-and-footer.md)
- [Multisite and packages course](../../../trainers/multisite-and-packages/README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
