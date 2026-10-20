---
title: Structure, sections and roles for courts
audience: government, admin, website-creator, project-manager
category: public-sector
order: 40
summary: A content tree for a court site, a separate section for decisions under review, and roles for guidance editors, the decision publishing team, reviewers and a duty publisher for urgent notices.
keywords: content tree, court, sections, roles, decision publishing, review section, approval, duty publisher, urgent notices, state limitation, least privilege
type: help
---
# Structure, sections and roles for courts

This page is for administrators and website creators of a court or tribunal site. Decisions are published by a small
trained team with review; everything else by communications staff. The mechanisms are in
[Sections](../../../../guides/sections.md), [Roles and policies](../../../../guides/roles-and-policies.md),
[Workflows](../../../../guides/workflows.md) and [Object states](../../../../guides/object-states.md).

## Contents

1. [The content tree](#the-content-tree)
2. [Sections](#sections)
3. [Roles](#roles)
4. [Review and urgent publishing](#review-and-urgent-publishing)
5. [Checks](#checks)

## The content tree

```text
Home
├── Going to court        guides by situation, help available, costs
├── Courts and locations  Court location objects
├── Hearings              hearing information where published, notices of changes and closures
├── Decisions             Decision objects by year or area of law
├── Rules and forms       rules, practice directions, forms, fees
├── News
└── About the court       jurisdiction, judges' public roles, annual report, complaints, contact
```

## Sections

| Section | Holds | Anonymous reads |
|---|---|---|
| Standard | guidance, locations, news, about | yes |
| `decisions` | published decisions | yes (add to Anonymous `content/read`) |
| `decisions_review` | decisions being prepared | no |
| `notices` | hearing changes and closures | yes |

A decision is created in a folder in `decisions_review`, reviewed there, and moved to the public Decisions folder when
approved; moving it changes its section if the target folder's section is inherited (check the section on the
**Details** tab after the move, or assign it with **Assign content**) ([Sections, 8](../../../../guides/sections.md#8-which-section-content-is-in-creating-moving-and-assigning)).
An alternative is to keep one folder and let the *Approve* event hold publication; the separate section adds a second
safeguard, because even a mistaken publish is not readable by visitors.

## Roles

| Role | Policies | Assigned to |
|---|---|---|
| Guidance editor | `content/create`, `content/edit`, `content/hide`, `content/versionread` limited by Section Standard | communications |
| Decision preparer | `content/create`, `content/edit` limited by Section `decisions_review`, Class Decision and File | the decision publishing team |
| Decision reviewer | `content/read`, `content/versionread`, `content/diff` limited by Section `decisions_review` and `decisions`; `content/move` | reviewers (registry lawyers or judges' staff, as your rules say) |
| Decision publisher lead | `state/assign` (NewState Published, Corrected, Withdrawn), `content/hide`, `content/remove`, `content/versionremove` limited by Section `decisions` | one or two named people |
| Duty publisher | `content/create`, `content/edit`, `content/hide` limited by Section `notices` | the duty team for closures |
| Administrator | `*/*` | system administrators |

Grant `content/versionremove` sparingly: it removes versions for good, which is needed to take personal data out of
old versions but must be a deliberate act ([Versions of an object](../../../../guides/content-history.md)).

## Review and urgent publishing

| Content | Rule |
|---|---|
| Decisions | *Approve* event, **Affected sections** `decisions_review` and `decisions`, approvers the reviewers |
| Guidance | *Approve* event or none, as your rules require |
| Notices of closures | no approval: the duty team's group in **Excluded user groups**; the audit trail records every publish |

Remember that approvals are resumed by the workflow cronjob; check **Setup > Workflow processes** shows it scheduled.

## Checks

- [ ] A decision in `decisions_review` cannot be read logged out, even after it was published by mistake.
- [ ] A decision preparer cannot set the state *Withdrawn* and cannot remove versions.
- [ ] A duty publisher's closure notice appears at once; a preparer's decision waits for review.
- [ ] Every member of the decision team has two-step sign-in set up ([Two-step sign-in](../../../../guides/two-factor-login.md)).

## Related pages

- [Courts](README.md)
- [Requirements emphasis for courts](requirements-emphasis.md)
- [Security and audit](../../../../guides/security-and-audit.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
