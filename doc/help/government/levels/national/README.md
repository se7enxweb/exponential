---
title: National government
audience: government, project-manager, admin, website-creator
category: public-sector
order: 10
summary: Index for ministries, departments and central government portals planning an Exponential site - what they publish, the content model, structure and roles, requirements, a 90-day plan and running many ministries on one platform.
keywords: national government, central government, ministry, department, federal, portal, government website, cabinet, minister
type: help
---
# National government

This folder is for project managers, administrators and website creators at a ministry, a central government
department or a central government portal team. It answers what such an organization usually publishes, how to model
and structure it in Exponential, which requirements weigh most, how to run the first 90 days, and how one platform can
serve several ministries.

The product facts come from the documentation of this repository; the descriptions of what national governments
publish are general public-sector practice.

## Contents

1. [The pages](#the-pages)
2. [Reading order](#reading-order)
3. [What sets national government apart](#what-sets-national-government-apart)

## The pages

| Page | The answer in one line |
|---|---|
| [What a national government publishes](what-it-publishes.md) | Policy, news and press releases, publications, consultations, services and corporate information, for a national audience at high volume |
| [Content model for national government](content-model.md) | The shipped Article, Folder, File and Event classes cover much; add Publication, Consultation and Press release classes with real datatypes |
| [Structure, sections and roles for national government](structure-sections-and-roles.md) | A tree by topic and by organization, a section per ministry unit, and Editor, approver and publisher roles limited by section |
| [Requirements emphasis for national government](requirements-emphasis.md) | Accessibility law, several official languages, high availability, audit and records retention |
| [The first 90 days for national government](first-90-days.md) | Governance and inventory, content model and roles, migration and launch, each with checks |
| [Many ministries on one platform](many-ministries-on-one-platform.md) | One installation with a siteaccess and a section per ministry, or one installation per ministry, and how to decide |

## Reading order

1. **What it publishes** to agree on scope.
2. **Content model** and **Structure, sections and roles** with the people who will build the site.
3. **Requirements emphasis** with your accessibility, security and records officers.
4. **First 90 days** as the project plan.
5. **Many ministries on one platform** when the site is shared or will be.

Up: [Levels of government](../README.md). The landing page of the Governments section, government-start-here.md,
is the starting point for every reader.

## What sets national government apart

| Topic | Why it matters at national level |
|---|---|
| Scale | Many editors in many units, many pages, peaks of traffic when news breaks |
| Authority | Publications are cited by the press, courts and other governments; their history must be kept |
| Languages | Several official languages are common, with equal status for each |
| Coordination | A central portal team sets rules that every ministry follows |
| Scrutiny | Accessibility, security and transparency are checked by oversight bodies and the public |

Exponential's answers to these are in the pages above: roles and sections for many units, versions and the audit
trail for history ([Versions of an object](../../../../guides/content-history.md),
[Audit trail](../../../../features/6.0/audit-trail.md)), content languages for equal-status translations
([Content languages](../../../../guides/content-languages.md)), and caching and Velocity for load
([Performance and caching](../../../creators/website-creators/performance-and-caching.md)).

## Related pages

- [Levels of government](../README.md)
- [Planning your website](../../../creators/website-creators/planning-your-website.md)
- [Multisite setup](../../../creators/website-creators/multisite-setup.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
