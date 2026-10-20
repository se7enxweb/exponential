---
title: Intranets and department subsites
audience: government, project-manager, admin, website-creator
category: public-sector
order: 70
summary: How a college or university runs many department subsites and an intranet for staff and students on one Exponential installation - siteaccesses, sections, a shared design with variants, sign-in, and the decisions that keep it manageable.
keywords: intranet, department subsites, multisite, siteaccess, university, faculty sites, restricted section, RequireUserLogin, shared design, cross-site links, staff portal
type: help
---
# Intranets and department subsites

This page is for project managers, administrators and website creators of a college, university or school district
with many departments. It shows how one Exponential installation serves the central site, department subsites and an
intranet, and what to decide so that dozens of sites stay manageable. The mechanisms are in
[Multisite setup](../../../creators/website-creators/multisite-setup.md), [Sections](../../../../guides/sections.md) and
the multi-site feature pages.

## Contents

1. [Three shapes](#three-shapes)
2. [Department subsites](#department-subsites)
3. [The intranet](#the-intranet)
4. [Shared design with variants](#shared-design-with-variants)
5. [Governance that keeps it manageable](#governance-that-keeps-it-manageable)

## Three shapes

| Shape | Departments get | Good for |
|---|---|---|
| Pages in the central site | a folder and a section | most schools and small colleges |
| Subsites on one installation | their own host name and start node, shared classes and design | universities with many faculties |
| Separate installations | everything of their own | units that are separate bodies or need their own release cycle |

The rest of this page is about the middle shape and the intranet.

## Department subsites

Each subsite is a siteaccess ([Multisite setup](../../../creators/website-creators/multisite-setup.md)):

| Part | Setting or place |
|---|---|
| Host name, for example `<department>.example.edu` | host matching ([Siteaccess host and language matching](../../../../features/6.0/siteaccess-host-and-language-matching.md)) |
| Start node | the department's folder, set in the siteaccess's `site.ini` |
| Design | the shared design, with the department's variant |
| Settings that differ | `settings/siteaccess/<name>/` ([Multi-site INI overrides](../../../../features/6.0/multi-site-ini-overrides.md)) |
| Links to other subsites | written with the right host ([Cross-site links](../../../../features/6.0/multisite-cross-site-links.md)) |
| Logs and caches per site | [Multi-site log and cache paths](../../../../features/6.0/multi-site-log-and-cache-paths.md) |

Department editors work in the one administration; their role is assigned with the department's section, so they see
and change only their own content ([Structure, sections and roles](structure-sections-and-roles.md)). Shared items (a
program offered by two departments, a common event) are one object with locations in both subsites.

## The intranet

Two ways to make an intranet:

| Way | How | Effect |
|---|---|---|
| A restricted section in the public siteaccess | the `intranet` section, not readable by Anonymous; staff and students sign in on the site | one site; public and intranet pages side by side after sign-in |
| A separate intranet siteaccess | its own host, `[SiteAccessSettings] RequireUserLogin=true` | everything asks for a sign-in first; the intranet has its own look and menu |

`RequireUserLogin` is a siteaccess setting (the default package sets it to `false` for the public site,
[Siteaccesses and languages](../../../site-packages/sevenx-multisite/siteaccesses-and-languages.md)). Even with it,
keep intranet content in a section that Anonymous cannot read: the section is what protects the content in every
siteaccess, including search and feeds. A siteaccess that requires a login is also left out of static cache
generation ([Static cache generator](../../../../features/6.0/static-cache-generator.md)).

Intranet content that must stay inside the network can also be protected at the web server or Velocity level (by
address); that is an operations decision.

## Shared design with variants

One design extension for all subsites keeps accessibility testing and upgrades to one place. Variants per department
(a color, a logo, a header image) come from settings or from a small design extension listed before the shared one
in the siteaccess's design list ([Designs and the fallback chain](../../../creators/website-creators/designs-and-the-fallback-chain.md)).
Resist one design per department: each one is tested, maintained and upgraded on its own.

## Governance that keeps it manageable

| Rule | Why |
|---|---|
| A subsite needs a named owner and an editor before it is created | orphan subsites go stale |
| Subsites use the shared classes; new classes go through the web team | one model, one training |
| A yearly review: each department confirms its owner and retires stale pages | content rot |
| Accounts follow the academic year | leavers disabled ([Check user accounts](../../../../features/6.0/user-hygiene.md)) |
| One maintenance window, announced to all | maintenance mode affects every site ([Maintenance mode](../../../../guides/maintenance-mode.md)) |

## Related pages

- [Schools and universities](README.md)
- [Many ministries on one platform](../national/many-ministries-on-one-platform.md)
- [Sections, 13.2: an intranet with departments](../../../../guides/sections.md#132-an-intranet-with-departments)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
