---
title: Structure, sections and roles for regional and state government
audience: government, admin, website-creator, project-manager
category: public-sector
order: 40
summary: A content tree by theme and place for a regional or state site, sections for departments, the assembly secretariat and restricted municipal material, and roles for department editors, the secretariat and municipal readers.
keywords: content tree, sections, roles, region, state, departments, assembly secretariat, restricted area, municipal users, section limitation, subtree limitation
type: help
---
# Structure, sections and roles for regional and state government

This page is for administrators and website creators who set up the tree, sections and roles of a regional or state
site. The mechanisms are those of [Sections](../../../../guides/sections.md) and
[Roles and policies](../../../../guides/roles-and-policies.md).

## Contents

1. [The content tree](#the-content-tree)
2. [Sections](#sections)
3. [Roles and groups](#roles-and-groups)
4. [The assembly secretariat](#the-assembly-secretariat)
5. [Checks](#checks)

## The content tree

```text
Home
├── Living and working      services by theme (transport, environment, health ...)
├── Business and funding    funding programs, permits, procurement
├── Places                  areas, offices, sites (folders tagged with the area)
├── The assembly            members, committees, meetings, decisions
├── Plans and strategies    Plan objects
├── News                    news, road works and disruptions
├── For municipalities      guidance and toolkits (public or restricted)
└── About us                organization, budget, jobs, contact
```

Themes, not departments, at the top: a visitor does not know which department runs bus subsidies. Departments own
content through sections. Road works and disruptions sit in their own folder so they can have an RSS feed and an
expiry rule of their own.

## Sections

| Section | Holds | Anonymous may read |
|---|---|---|
| Standard | everything public | yes (shipped) |
| One per department, for example `dept_mobility` | the department's pages | yes, add it to Anonymous `content/read` |
| `assembly` | meetings, agendas, minutes, decisions | yes |
| `municipal_restricted` | material for municipal staff only | no |
| `staging` | pages prepared for a launch | no |

A section that Anonymous may not read makes the content give "access denied" to visitors and disappear from menus.
Check the section of every top folder on its **Details** tab after creating the tree.

## Roles and groups

| Role | Key policies | Assigned to, with limitation |
|---|---|---|
| Department editor (copy of Editor) | `content/read`, `content/create`, `content/edit`, `content/hide`, `content/versionread` | each department's group, limitation Section = that department |
| Disruptions editor | `content/create`, `content/edit`, `content/remove` | the traffic team, limitation Subtree = the disruptions folder |
| Secretariat | `content/create`, `content/edit` for classes Decision, File, Event, Folder | the assembly secretariat, Section = `assembly` |
| Municipal reader | `content/read` | the Municipal users group, Section = `municipal_restricted` |
| Web team | `content/*`, `section/assign`, `state/assign`, `rss/edit` | the web team |

A Subtree limitation follows the tree; a Section limitation follows the section label. Use Subtree for a small team
with a fixed folder, Section for a department whose pages are spread over several themes.

Municipal staff accounts are created by your administrators (or by registration into a group you approve) and placed
in the Municipal users group. Never let public registration place users there; registrations go to
`[UserSettings] DefaultUserPlacement` ([Member registration and members-only areas](../../../creators/website-creators/member-registration-and-areas.md)).

## The assembly secretariat

The secretariat publishes the official record. Two rules protect it:

- **No approval delay for formal documents.** Exclude the secretariat's group in the *Approve* event
  (**Excluded user groups**), because the documents were already approved by the assembly.
- **No silent changes.** Corrections of published decisions are made as a new version with a note in the summary;
  the earlier version stays in the version history ([Versions of an object](../../../../guides/content-history.md)).
  Consider an object state group "Record status" (current, corrected, revoked) shown on the page
  ([Object states](../../../../guides/object-states.md)).

## Checks

- [ ] A department editor cannot edit another department's page.
- [ ] Logged out, a page in `municipal_restricted` gives "access denied"; logged in as a municipal user it opens.
- [ ] The secretariat publishes a decision without waiting for approval; a department editor's page waits.
- [ ] Every section has an identifier (**Setup > Sections** marks those without one as needing attention).

## Related pages

- [Regional and state government](README.md)
- [Requirements emphasis for regional and state government](requirements-emphasis.md)
- [Visitor permissions](../../../creators/website-creators/visitor-permissions.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
