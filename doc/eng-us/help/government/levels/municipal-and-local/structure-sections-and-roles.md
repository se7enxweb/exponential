---
title: Structure, sections and roles for municipal and local government
audience: government, admin, website-creator, project-manager
category: public-sector
order: 40
summary: A task-based content tree for a municipal site, a few sections for departments and the council, and roles sized for a small team with occasional editors in many departments.
keywords: content tree, municipal, council, sections, roles, small team, occasional editors, editor siteaccess, departments, approval, section limitation
type: help
---
# Structure, sections and roles for municipal and local government

This page is for administrators and website creators who set up a city, town or council site. Small teams and many
occasional editors shape the choices. The mechanisms are documented in [Sections](../../../../guides/sections.md),
[Roles and policies](../../../../guides/roles-and-policies.md) and [Workflows](../../../../guides/workflows.md).

## Contents

1. [A task-based tree](#a-task-based-tree)
2. [Sections](#sections)
3. [Roles for a small team](#roles-for-a-small-team)
4. [Occasional editors](#occasional-editors)
5. [Checks](#checks)

## A task-based tree

```text
Home
├── Waste and recycling
├── Roads, parking and transport
├── Housing and planning
├── Taxes and payments
├── Births, marriages and deaths
├── Health and social care
├── Schools and childcare
├── Leisure, culture and libraries
├── Business and licenses
├── Council and democracy        members, meetings, decisions, elections
├── News and notices             news, public notices, alerts
├── Events                       an Event calendar
└── Contact us                   contact points
```

Each top folder is a service area named the way residents speak, not after the department. Services that belong in two
areas get a second location. Keep the depth at three levels where you can; the menu shows the tree
([Menus and navigation](../../../creators/website-creators/menus-and-navigation.md)).

## Sections

Few sections are enough:

| Section | Holds | Anonymous reads |
|---|---|---|
| Standard | most content | yes |
| `council` | Council and democracy | yes (add to Anonymous `content/read`) |
| One per department that edits its own pages | its service area | yes |
| `staging` | prepared pages | no |

Create a department section only when the department has its own editors. Otherwise the web team edits its pages in
Standard.

## Roles for a small team

| Role | Policies | Assigned to |
|---|---|---|
| Web team (copy of Editor, wider) | `content/*` except `cleantrash`; `section/assign`; `rss/edit`; `infocollector/read` | the one or two web officers |
| Department editor | `content/read`; `content/create`, `content/edit`, `content/hide`, `content/versionread` limited by Section | each department's group, Section = its section |
| Council secretariat | `content/create`, `content/edit` limited by Section `council`; `content/remove` for drafts | the democratic services group |
| Administrator | `*/*` | the system administrator (often a supplier) |

Approval: a small team often approves everything itself. A useful middle way is an *Approve* event for department
editors only, with the web team as approvers and the web team and secretariat in **Excluded user groups**
([Workflows](../../../../guides/workflows.md)). Remember the workflow cronjob, or approved pages never appear.

## Occasional editors

Many departments edit a few times a month. The **editor siteaccess** gives them a simpler interface for editing
content ([Logging into the Editor](../../../logging-into-the-editor.md),
[Setup wizard and editor siteaccess](../../../../features/6.0/setup-wizard-and-editor-siteaccess.md)). Help them with:

- a one-page instruction per task ([Publishing tasks](../../tasks/publishing/README.md));
- **My drafts** to find unfinished work ([Drafts and pending](../../../../guides/drafts-and-pending.md));
- two-step sign-in, because an occasional editor's password is the one most likely reused
  ([Two-step sign-in](../../../../guides/two-factor-login.md)).

## Checks

- [ ] A department editor can create and edit in their area, not elsewhere.
- [ ] A department editor's page waits for approval; the web team's does not.
- [ ] Logged out, every top folder opens.
- [ ] **Users > Check user accounts**, filter *Never signed in*: no stale editor accounts.

## Related pages

- [Municipal and local government](README.md)
- [Requirements emphasis for municipal and local government](requirements-emphasis.md)
- [Structuring the content tree](../../../creators/website-creators/structuring-the-content-tree.md)
- [Handing over to editors](../../../creators/website-creators/handing-over-to-editors.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
