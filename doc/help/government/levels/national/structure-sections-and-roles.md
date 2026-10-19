---
title: Structure, sections and roles for national government
audience: government, admin, website-creator, project-manager
category: public-sector
order: 40
summary: How to arrange the content tree of a ministry site, which sections to create for units and for staging, and which roles to give editors, approvers, publishers and the central web team, using real Exponential policies.
keywords: content tree, sections, roles, policies, ministry units, approver, publisher, web team, section limitation, subtree limitation, approval workflow, staging
type: help
---
# Structure, sections and roles for national government

This page is for administrators and website creators who set up the content tree, the sections and the roles of a
ministry or central department site. It uses the mechanisms described in [Sections](../../../../guides/sections.md),
[Roles and policies](../../../../guides/roles-and-policies.md) and [Workflows](../../../../guides/workflows.md); read
those for every screen and field.

## Contents

1. [The content tree](#the-content-tree)
2. [Sections](#sections)
3. [Roles](#roles)
4. [Approval](#approval)
5. [User groups](#user-groups)
6. [Checks](#checks)

## The content tree

Visitors navigate by topic; staff think in units. The tree has to serve visitors first, because menus are made from
it ([Menus and navigation](../../../creators/website-creators/menus-and-navigation.md)). A structure that works for
many national sites:

```text
Home (frontpage or landing page)
├── Topics                 one folder per policy area, the visitor's entry
├── Services               how to apply, eligibility, links to transactions
├── News                   press releases, statements, speeches
├── Publications           reports, strategies, guidance (Publication class)
├── Consultations          open, closed, outcomes
├── About us               structure, leadership, budget, contact, jobs
└── Transparency           disclosure logs, spending, contracts
```

Units do not get their own top-level folders. Their content lives in the shared folders and is owned by them through
sections and object ownership. A page that belongs in two places gets a second location, not a copy.

## Sections

Sections group content for permissions ([Sections](../../../../guides/sections.md)). A new installation has Standard,
Users, Media, Setup and Design. Add:

| Section | Identifier | Holds | Why |
|---|---|---|---|
| One per publishing unit | for example `unit_policy_a` | the pages that unit maintains | editors of the unit edit only these |
| Press office | `press_office` | News and statements | a separate publishing team and approval rule |
| Staging | `staging` | pages prepared before a launch | visitors cannot read them until they are assigned to Standard ([13.3](../../../../guides/sections.md#133-staging-content-before-it-goes-public)) |

Anonymous visitors may read only the sections their role lists; the shipped Anonymous role reads Standard (and the
Media section in the default package). A unit section must therefore be added to the Anonymous role's `content/read`
policy before its pages are public, or the pages give "access denied". Plan this when you create the section.

## Roles

Start from the shipped roles (**Users > Roles and policies**): Administrator, Anonymous, Editor and Member exist on a
default installation ([Roles and policies of sevenx_multisite](../../../site-packages/sevenx-multisite/roles-and-policies.md)).
Copy Editor rather than changing it, so an upgrade or a package import does not surprise you.

| Role | Policies (module / function, limitation) | Assigned to |
|---|---|---|
| Unit editor | `content/read`; `content/create`, `content/edit`, `content/hide`, `content/versionread`, `content/translate` limited by Section (the unit's section); `content/remove` limited the same way if units may remove | the unit's editor group, with the role assigned with a Section limitation |
| Press officer | the Unit editor policies for Section press office, plus `content/publish_without_notification` if corrections should not mail subscribers ([Publish without notification](../../../../features/6.0/publish-without-notification.md)) | the press office group |
| Approver | the Unit editor policies plus `content/versionread` for pending versions | the approvers of a unit |
| Web team | `content/*`, `section/assign`, `state/assign`, `rss/edit`, `infocollector/read` | the central web team |
| Administrator | `*/*` | a small group of system administrators |

One role assigned several times with different Section limitations saves you a role per unit: on the role's page
choose **Section** in the limitation list, **Assign with limitation**, then the section and the group
([13.2](../../../../guides/sections.md#132-an-intranet-with-departments)).

## Approval

An *Approve* event in a workflow on **Before publishing content** holds versions until an approver decides
([Workflows](../../../../guides/workflows.md)). Its fields fit national sites well:

| Field | Suggested use |
|---|---|
| Affected sections | the unit sections, not Staging |
| Affected versions | *All versions* for policy pages; consider *Publishing new object* only for low-risk folders |
| Groups who approve content | the approvers of the unit |
| Excluded user groups | the web team and the press office for urgent statements |

Only one workflow runs per trigger. For several units with different approvers, put one *Approve* event per unit in
the workflow (each limited to its sections), or use a *Multiplexer* event. The workflow cronjob must run, or approved
content is never published.

## User groups

Create the groups below **Users**: one per unit for editors, one per unit for approvers, Web team, Press office.
Never put staff in the Members group that public registrations use (`[UserSettings] DefaultUserPlacement`). Keep
accounts tidy with **Users > Check user accounts** ([Check user accounts](../../../../features/6.0/user-hygiene.md)).

## Checks

- [ ] Log in as a unit editor: you can edit your unit's pages and not another unit's.
- [ ] Log out: unit pages that should be public are readable; staging pages are not.
- [ ] Publish as an editor: the version is pending; **Setup > Workflow processes** shows it waiting.
- [ ] Approve as an approver and run the frequent cronjob part: the page is published.
- [ ] **Users > Roles and policies**: every role lists the groups it is assigned to, with their limitations.

## Related pages

- [National government](README.md)
- [Requirements emphasis for national government](requirements-emphasis.md)
- [Object states](../../../../guides/object-states.md)
- [Site administrators course](../../../trainers/site-administrators/README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
