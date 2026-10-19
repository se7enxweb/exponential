---
title: Structure, sections and roles for public libraries
audience: government, admin, website-creator, project-manager
category: public-sector
order: 40
summary: A content tree for a library or library network, one subtree per branch, and roles that let branch staff publish their own events and notices while the web team keeps the rest consistent.
keywords: content tree, library, branches, sections, roles, branch staff, events editor, subtree limitation, class limitation, event calendar
type: help
---
# Structure, sections and roles for public libraries

This page is for administrators and website creators of a library or library network. Many staff in many branches
publish small items; a few people keep the site together. The mechanisms are in [Sections](../../../../guides/sections.md)
and [Roles and policies](../../../../guides/roles-and-policies.md).

## Contents

1. [The content tree](#the-content-tree)
2. [Sections or subtrees](#sections-or-subtrees)
3. [Roles](#roles)
4. [Checks](#checks)

## The content tree

```text
Home
├── Find a book            a link to the catalog search, help with the catalog
├── Events                 Event calendar (all events), with views by branch via tags or relations
├── Branches               one Branch object per branch, with its notices below it
├── Services               joining, borrowing, printing, rooms, home delivery
├── Digital library        Digital resource objects
├── Reading                Recommendations
├── Learning               courses, help sessions
├── News
└── About us               governance, volunteering, jobs, contact
```

One central Event calendar keeps every event in one list, so visitors see everything on one page and filter by
branch. A calendar per branch is the alternative when branches run independently; then each branch's calendar lives
below its Branch object.

## Sections or subtrees

| Choice | When | How |
|---|---|---|
| Subtree limitation | branch staff edit their branch page and its notices | assign the role with a **Subtree** limitation to the branch's node |
| Class limitation | staff may create events but nothing else in the calendar | `content/create` with Class = Event and ParentClass = Event calendar |
| Section | a part of the site needs different read rules (a staff-only area) | a section that Anonymous may not read |

A library network rarely needs a section per branch: all branches are public, and subtree limitations already keep
staff to their part.

## Roles

| Role | Policies | Assigned to |
|---|---|---|
| Events editor | `content/create` (Class Event, ParentClass Event calendar), `content/edit` and `content/remove` (Owner = self) | all staff who run events |
| Branch editor | `content/edit`, `content/create` (classes Article, File), `content/hide` | each branch's staff, Subtree = the branch node |
| Content editor | Editor role, Section Standard | the web or communications team |
| Administrator | `*/*` | the system administrator |

The Owner limitation (*Self*) lets staff edit and remove only the events they created; the web team can edit all.
Policy limitations are described in [Roles and policies](../../../../guides/roles-and-policies.md). Branch staff often
work on shared computers: two-step sign-in and signing out matter ([Two-step sign-in](../../../../guides/two-factor-login.md)).

Approval is usually not needed for events; it can be added for news and service pages with an *Approve* event limited
to those sections ([Workflows](../../../../guides/workflows.md)).

## Checks

- [ ] An events editor can create an event in the calendar and nowhere else.
- [ ] An events editor cannot edit another person's event.
- [ ] A branch editor can update their branch's opening hours, not another branch's.
- [ ] Logged out, the calendar lists the new event (after the view cache is refreshed by publishing).

## Related pages

- [Public libraries](README.md)
- [Requirements emphasis for public libraries](requirements-emphasis.md)
- [Visitor permissions](../../../creators/website-creators/visitor-permissions.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
