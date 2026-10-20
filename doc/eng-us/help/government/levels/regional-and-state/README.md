---
title: Regional and state government
audience: government, project-manager, admin, website-creator
category: public-sector
order: 10
summary: Index for states, provinces, regions and counties planning an Exponential site - what they publish, the content model, structure and roles, requirements, a 90-day plan and shared services for the municipalities of a region.
keywords: regional government, state government, province, region, county, regional authority, shared services, municipalities, regional portal
type: help
---
# Regional and state government

This folder is for project managers, administrators and website creators at a state, province, region or county.
Such organizations sit between national and local government: they run their own services (often transport, health,
education, environment or planning, depending on the country), publish their own decisions, and often support the
municipalities in their territory. The pages answer what they publish, how to build it in Exponential, which
requirements weigh most, how to run the first 90 days, and how to offer shared services to municipalities.

Product facts come from the documentation of this repository; descriptions of regional government are general
public-sector practice, and the split of responsibilities between levels differs from country to country.

## Contents

1. [The pages](#the-pages)
2. [Reading order](#reading-order)
3. [What sets regional government apart](#what-sets-regional-government-apart)

## The pages

| Page | The answer in one line |
|---|---|
| [What a regional or state government publishes](what-it-publishes.md) | Services by theme, decisions of the regional assembly, plans and maps, funding programs, news, and information for municipalities |
| [Content model for regional and state government](content-model.md) | Shipped classes for pages, files and events; add Plan, Funding program and Decision classes with real datatypes |
| [Structure, sections and roles for regional and state government](structure-sections-and-roles.md) | A tree by theme and place, sections per department, roles per department and for the assembly secretariat |
| [Requirements emphasis for regional and state government](requirements-emphasis.md) | Accessibility, regional and minority languages, security, records of decisions |
| [The first 90 days for regional and state government](first-90-days.md) | A phased plan with checks, including the municipalities as stakeholders |
| [Shared services for the municipalities of a region](shared-services-for-municipalities.md) | Hosting sites for smaller municipalities on one installation, with separation, shared design and shared content |

## Reading order

1. **What it publishes** to agree the scope with departments.
2. **Content model** and **Structure, sections and roles** with the site builders.
3. **Requirements emphasis** with the officers who answer for accessibility, security, privacy and records.
4. **First 90 days** as the project plan.
5. **Shared services for the municipalities of a region** if you host or plan to host municipal sites.

Up: [Levels of government](../README.md). The landing page of the Governments section, [Governments: start here](../../government-start-here.md),
is the starting point for every reader.

## What sets regional government apart

| Topic | Why it matters |
|---|---|
| Place | Much content is about places: roads, sites, areas, offices; visitors look for "near me" |
| Two audiences | citizens and businesses, and the municipalities and partners of the region |
| Elected assembly | decisions, agendas and minutes of the regional assembly need a permanent, citable home |
| Languages | regional and minority languages often have a legal status next to the national language |
| Funding | grant and funding programs with calls, deadlines and published results |

Exponential has no geographic information system. For places it offers a map location datatype (`ezgmaplocation`,
which uses Google Maps, a third-party service) and plain addresses in text fields
([ezgmaplocation](../../../../features/6.0/extensions/ezgmaplocation.md)). Link to your existing GIS or map portal
for anything beyond a point on a map.

## Related pages

- [Levels of government](../README.md)
- [Municipal and local government](../municipal-and-local/README.md)
- [Multisite setup](../../../creators/website-creators/multisite-setup.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
