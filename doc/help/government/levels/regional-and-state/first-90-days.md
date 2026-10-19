---
title: The first 90 days for regional and state government
audience: government, project-manager, admin
category: public-sector
order: 60
summary: A 90-day plan for a regional or state government site on Exponential - scope with departments and the assembly, model and roles, migration and launch - with the checks to make in the product at each step.
keywords: project plan, 90 days, regional government, state government, departments, assembly, municipalities, migration, launch, checks
type: help
---
# The first 90 days for regional and state government

This page is for project managers and administrators who lead the start of a regional or state site on Exponential.
It follows three phases of about 30 days. It assumes an installation exists ([Getting started](../../../../guides/getting-started.md)).

## Contents

1. [Days 1 to 30: scope and stakeholders](#days-1-to-30-scope-and-stakeholders)
2. [Days 31 to 60: build](#days-31-to-60-build)
3. [Days 61 to 90: content, training, launch](#days-61-to-90-content-training-launch)
4. [After day 90](#after-day-90)

## Days 1 to 30: scope and stakeholders

| Step | Output |
|---|---|
| Map responsibilities: which services are regional, which municipal, which national | a scope list with links for what is not yours |
| Agree with the assembly secretariat how decisions and minutes are published | a publication rule for the official record |
| Decide on languages and their legal status | a language list per siteaccess |
| Inventory the current site and documents, with owners | an inventory ([Site map and content inventory](../../../creators/website-creators/site-map-and-content-inventory.md)) |
| Decide whether municipal sites will be hosted | a yes, no or later ([Shared services](shared-services-for-municipalities.md)) |
| Decide on third-party services (maps, CAPTCHA, video) with the privacy officer | a list of allowed services |

Checks:

- [ ] Every department has named one editor and one approver.
- [ ] The secretariat has confirmed where the official record is kept.

## Days 31 to 60: build

| Step | Check in the product |
|---|---|
| Create the classes (Plan, Funding program, Decision, Office) | **Setup > Classes** lists them; create one test object of each |
| Create the tree and sections ([Structure](structure-sections-and-roles.md)) | **Setup > Sections**: every section has an identifier and the right number of items |
| Create roles and assign them with limitations | **Users > Roles and policies**: each role lists its groups |
| Set up approval and the workflow cronjob | **Setup > Workflow processes** shows a waiting test version; **Setup > Cronjobs** shows the frequent part scheduled |
| Configure the expiry of disruptions (`[HideSettings]`) | a test notice with a past date is hidden after the cronjob runs ([Cronjobs](../../../../guides/cronjobs.md)) |
| Set up the language siteaccesses | a translated test page opens in both languages |
| Accessibility test of the design | no open serious finding ([Testing accessibility](../../../creators/website-creators/testing-accessibility.md)) |

## Days 61 to 90: content, training, launch

| Step | Read |
|---|---|
| Migrate department by department into the staging section | [Sections, 13.3](../../../../guides/sections.md#133-staging-content-before-it-goes-public) |
| Load the assembly's decisions of the current term; link older ones in the archive | [Files and downloads](../../../creators/website-creators/files-and-downloads.md) |
| Train department editors, approvers and the secretariat separately | [Editors 101](../../../trainers/editors-101/README.md) |
| Set up redirects for old addresses | [Redirects and moved content](../../../creators/website-creators/redirects-and-moved-content.md) |
| Rehearse a restore and maintenance mode | [Backups and restores](../../../creators/website-creators/backups-and-restores.md), [Maintenance mode](../../../../guides/maintenance-mode.md) |
| Launch with the launch checklist | [Launch checklist](../../../creators/website-creators/launch-checklist.md) |

Checks at launch:

- [ ] Logged out, every top-level theme and the assembly pages open; the restricted municipal area does not.
- [ ] An RSS reader shows the disruptions feed ([RSS feeds](../../../../guides/rss-feeds.md)).
- [ ] **Setup > Audit** records the launch-day changes.
- [ ] The accessibility statement and privacy notice are published.

## After day 90

- Monthly: review pages past their review date, accounts not seen for 180 days (**Users > Check user accounts**),
  and **Setup > Collected information** for form submissions that may be removed.
- Quarterly: an accessibility spot check of new templates and documents.
- Before each assembly term: archive the previous term's meeting pages as a clearly labeled archive section.

## Related pages

- [Regional and state government](README.md)
- [Shared services for the municipalities of a region](shared-services-for-municipalities.md)
- [Launch checklist](../../../creators/website-creators/launch-checklist.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
