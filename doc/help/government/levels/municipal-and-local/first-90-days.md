---
title: The first 90 days for municipal and local government
audience: government, project-manager, admin
category: public-sector
order: 60
summary: A realistic 90-day plan for a city, town or council starting with Exponential with a small team - top tasks first, council publishing, departments, launch - with checks in the product.
keywords: project plan, 90 days, municipality, council, small team, top tasks, migration, launch, training, checks
type: help
---
# The first 90 days for municipal and local government

This page is for project managers and web officers at a city, town or council who start a site on Exponential with a
small team. It puts the most-used services first and leaves the long tail for after launch. It assumes an
installation exists, hosted by you or a supplier ([Getting started](../../../../guides/getting-started.md)).

## Contents

1. [Days 1 to 30: top tasks and the plan](#days-1-to-30-top-tasks-and-the-plan)
2. [Days 31 to 60: build the core](#days-31-to-60-build-the-core)
3. [Days 61 to 90: departments, council, launch](#days-61-to-90-departments-council-launch)
4. [What can wait](#what-can-wait)

## Days 1 to 30: top tasks and the plan

| Step | Output |
|---|---|
| Find the top tasks from search logs, call center topics and current site statistics | a list of about 20 services residents need most |
| Inventory the current site; mark pages to keep, rewrite or retire | an inventory ([Site map and content inventory](../../../creators/website-creators/site-map-and-content-inventory.md)) |
| Agree with democratic services how meetings are published | the meeting cycle ([Council meetings and local services](council-meetings-and-local-services.md)) |
| Name one editor per department that will edit | a contact list |
| Agree who may publish an urgent alert out of hours | an alert rota |
| If a supplier hosts: agree update, backup and support terms | a service agreement |

Checks: **Setup > System information** shows the installed version; you can sign in to the administration and the
editor siteaccess.

## Days 31 to 60: build the core

| Step | Check |
|---|---|
| Create the Service, Meeting, Public notice and Contact point classes ([Content model](content-model.md)) | one test object of each publishes |
| Create the task-based tree ([Structure](structure-sections-and-roles.md)) | the menu shows the service areas |
| Write the top 20 service pages, in plain language | each has every required field |
| Set up `[HideSettings]` for public notices | a test notice with a past hide date is hidden after `hide.php` runs |
| Turn on required alternative text for image attributes | storing an image field without it is refused in the edit form (multi-upload does not ask) |
| Test the design's accessibility | no open serious finding |
| Enable two-step sign-in for staff | the web team has set it up |

## Days 61 to 90: departments, council, launch

| Step | Read |
|---|---|
| Create department sections and roles for departments that edit | [Sections](../../../../guides/sections.md) |
| Train department editors with one real task each | [Editors 101](../../../trainers/editors-101/README.md) |
| Publish the current year's meetings | [Council meetings and local services](council-meetings-and-local-services.md) |
| Redirect the old addresses of the top tasks | [Redirects and moved content](../../../creators/website-creators/redirects-and-moved-content.md) |
| Publish the accessibility statement and privacy notice | [Accessibility for site builders](../../../creators/website-creators/accessibility-for-site-builders.md) |
| Launch | [Launch checklist](../../../creators/website-creators/launch-checklist.md) |

Checks at launch:

- [ ] Each top task page opens logged out and is found by the site search with the words residents use.
- [ ] The alert procedure was rehearsed: an alert published, shown on the front page, and removed.
- [ ] The cronjobs page shows the global and frequent parts scheduled (**Setup > Cronjobs**).

## What can wait

| Item | Why it can wait |
|---|---|
| Rewriting rarely used pages | link the old documents and improve them by usage |
| A newsletter | RSS and news pages first |
| Many languages | start with the top tasks in the most needed language |
| An events calendar for every venue | one calendar first |

## Related pages

- [Municipal and local government](README.md)
- [Council meetings and local services](council-meetings-and-local-services.md)
- [Your first site step by step](../../../creators/website-creators/your-first-site-step-by-step.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
