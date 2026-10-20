---
title: The first 90 days for national government
audience: government, project-manager, admin
category: public-sector
order: 60
summary: A phased 90-day plan for a ministry or central department starting with Exponential - governance and inventory, model and roles, build and migration, launch - with concrete checks in the product at the end of each phase.
keywords: project plan, 90 days, kick-off, inventory, migration, launch, ministry, governance, checks, training, go live
type: help
---
# The first 90 days for national government

This page is for project managers and administrators who lead the start of a ministry or central department site on
Exponential. It is a skeleton plan in three phases of about 30 days. Adapt the lengths to your size; keep the checks.
The installation itself is covered in [Getting started](../../../../guides/getting-started.md) and the installation
book; this plan starts when you have an installation to work on.

## Contents

1. [Days 1 to 30: governance and inventory](#days-1-to-30-governance-and-inventory)
2. [Days 31 to 60: model, roles and build](#days-31-to-60-model-roles-and-build)
3. [Days 61 to 90: migration, training and launch](#days-61-to-90-migration-training-and-launch)
4. [Risks to watch](#risks-to-watch)

## Days 1 to 30: governance and inventory

| Step | Who | Output |
|---|---|---|
| Name the product owner, the web team and one contact per publishing unit | management | a governance chart |
| Agree who may publish what, and who approves | units, web team | an approval matrix |
| Inventory the current site: pages, documents, owners, last change | web team | a spreadsheet ([Site map and content inventory](../../../creators/website-creators/site-map-and-content-inventory.md)) |
| Decide what not to migrate | units | a list of pages to retire, with redirects |
| Collect requirements with accessibility, security, privacy and records officers | project manager | the requirements table ([Requirements emphasis](requirements-emphasis.md)) |
| Set up development, test and production installations | operations | three installations, the same version ([Deploying](../../../../guides/deploying.md)) |

**Checks at day 30**

- [ ] **Setup > System information** on each installation shows the same Exponential version.
- [ ] The approval matrix names a deputy for every approver.
- [ ] Every page in the inventory has an owner or a "retire" decision.

## Days 31 to 60: model, roles and build

| Step | Output | Read |
|---|---|---|
| Create the content classes (Publication, Consultation, Press release ...) on development | classes in **Setup > Classes** | [Content model](content-model.md) |
| Build the tree, sections and roles | the structure | [Structure, sections and roles](structure-sections-and-roles.md) |
| Set up the approval workflow and its trigger, and the crontab | a pending version that gets published after approval | [Workflows](../../../../guides/workflows.md), [Cronjobs](../../../../guides/cronjobs.md) |
| Build or adapt the public design; test it for accessibility | templates | [Accessibility for site builders](../../../creators/website-creators/accessibility-for-site-builders.md), [Testing accessibility](../../../creators/website-creators/testing-accessibility.md) |
| Configure languages and siteaccesses | one siteaccess per language | [Content languages](../../../../guides/content-languages.md) |
| Turn on two-step sign-in for staff | `Enforce2FA` decided | [Two-step sign-in](../../../../guides/two-factor-login.md) |
| Move classes from development to test as a package | a tested package | [Packages](../../../../guides/packages.md) |

**Checks at day 60**

- [ ] A test editor of one unit cannot edit another unit's page (try it).
- [ ] A page published by a test editor waits in **Setup > Workflow processes** until approved.
- [ ] **Setup > Cronjobs** shows the frequent part scheduled.
- [ ] Your accessibility test of the design templates has no open serious finding.

## Days 61 to 90: migration, training and launch

| Step | Output | Read |
|---|---|---|
| Migrate content, unit by unit, into the staging section | content in place, invisible to visitors | [Sections 13.3](../../../../guides/sections.md#133-staging-content-before-it-goes-public) |
| Train editors and approvers | everyone has published a test page | [Editors 101](../../../trainers/editors-101/README.md) |
| Set up redirects for old addresses | wildcards and aliases | [Redirects and moved content](../../../creators/website-creators/redirects-and-moved-content.md) |
| Load test with caches on | a measured result | [Performance and caching](../../../creators/website-creators/performance-and-caching.md) |
| Rehearse maintenance mode and a restore from backup | a timed rehearsal | [Maintenance mode](../../../../guides/maintenance-mode.md), [Backups and restores](../../../creators/website-creators/backups-and-restores.md) |
| Launch: assign the staging content to Standard, switch DNS | the site is live | [Launch checklist](../../../creators/website-creators/launch-checklist.md) |

**Checks at day 90**

- [ ] Logged out, a sample of 20 migrated pages opens; old addresses answer with a redirect.
- [ ] The audit trail lists the launch day's changes (**Setup > Audit**).
- [ ] Every editor has signed in at least once (**Users > Check user accounts**, filter *Never signed in*).
- [ ] The accessibility statement is published.

## Risks to watch

| Risk | Sign | Response |
|---|---|---|
| Units migrate everything | page count grows instead of shrinking | hold to the "retire" list |
| Approval becomes a bottleneck | **Setup > Workflow processes** shows the oldest waiting process in amber (over a day) | add deputies, narrow **Affected versions** |
| Documents are not accessible | PDF uploads without summaries | require a summary field; train |
| Cronjobs not running after a server move | approved content never appears | check the cronjobs page after every move |

## Related pages

- [National government](README.md)
- [Many ministries on one platform](many-ministries-on-one-platform.md)
- [Planning your website](../../../creators/website-creators/planning-your-website.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
