---
title: The first 90 days for agencies
audience: government, project-manager, admin
category: public-sector
order: 60
summary: A 90-day plan for a regulator, inspectorate or executive agency starting with Exponential - guidance inventory and owners, legal review workflow, status states, migration and launch - with checks.
keywords: project plan, 90 days, agency, regulator, guidance inventory, legal review, object states, migration, launch, checks
type: help
---
# The first 90 days for agencies

This page is for project managers and administrators who lead the start of an agency site on Exponential. Guidance is
the core asset, so the plan starts there. It assumes an installation exists
([Getting started](../../../../guides/getting-started.md)).

## Contents

1. [Days 1 to 30: guidance inventory and governance](#days-1-to-30-guidance-inventory-and-governance)
2. [Days 31 to 60: model, review and states](#days-31-to-60-model-review-and-states)
3. [Days 61 to 90: migration and launch](#days-61-to-90-migration-and-launch)
4. [Risks](#risks)

## Days 1 to 30: guidance inventory and governance

| Step | Output |
|---|---|
| List every piece of guidance with its owner, reference, effective date and status | the guidance register (a spreadsheet) |
| Decide which guidance is retired, merged or rewritten | a disposition per item |
| Agree the legal review rule: which changes need review, who reviews, how fast | the review rule |
| Agree the enforcement publication policy with legal and data protection | a written policy |
| Identify registers and data published, and their source systems | a data list |

Check: every guidance item has an owner; the review rule names deputies.

## Days 31 to 60: model, review and states

| Step | Check in the product |
|---|---|
| Create Guidance, Enforcement notice and Program classes ([Content model](content-model.md)) | **Setup > Classes** lists them |
| Create the state group `guidance_status` with In force, Superseded, Withdrawn | **Setup > States**; a test object shows *In force* |
| Create sections and roles ([Structure](structure-sections-and-roles.md)) | a guidance author cannot set *Withdrawn* |
| Set up the legal review workflow and its trigger | a test change waits in **Setup > Workflow processes** |
| Raise the version history limit if every guidance version must be kept | `[VersionManagement]` in an override; INI cache cleared |
| Set up `hide.php` for enforcement notices with a publication end | a test notice with a past date is hidden |
| Set notifications or RSS on the guidance library | a test subscriber gets a mail after the notification run ([Notifications](../../../../guides/notifications-administrator.md)) |

## Days 61 to 90: migration and launch

| Step | Read |
|---|---|
| Migrate guidance in force first, with references and effective dates; superseded guidance as separate objects marked *Superseded* | [Object states](../../../../guides/object-states.md) |
| Train authors on change notes, reviewers on the collaboration inbox and **Compare** | [Versions of an object](../../../../guides/content-history.md) |
| Redirect old guidance addresses to the new objects; regulated parties have bookmarked them | [Redirects and moved content](../../../creators/website-creators/redirects-and-moved-content.md) |
| Launch | [Launch checklist](../../../creators/website-creators/launch-checklist.md) |

Checks at launch:

- [ ] A sample of 20 old guidance addresses redirect to the right new page.
- [ ] Each guidance page shows its reference, effective date, status and change note.
- [ ] **Setup > Audit** shows the review decisions of the migration week.

## Risks

| Risk | Response |
|---|---|
| Legal review slows everything | narrow **Affected versions** or sections; allow minor corrections by the web team with a record |
| Old versions lost at the version limit | raise the limit before migrating; check the versions page of a busy guidance object |
| Superseded guidance looks current | state shown prominently in the template; superseded items out of search or labeled |

## Related pages

- [Agencies](README.md)
- [Guidance for regulated parties](guidance-for-regulated-parties.md)
- [Planning your website](../../../creators/website-creators/planning-your-website.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
