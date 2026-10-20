---
title: Structure, sections and roles for schools and universities
audience: government, admin, website-creator, project-manager
category: public-sector
order: 40
summary: A public tree by audience for an education site, a section per department and for the intranet, and roles for central communications, department editors, staff and student readers.
keywords: content tree, school, university, sections, roles, departments, intranet section, staff group, student group, department editor, subtree, section limitation
type: help
---
# Structure, sections and roles for schools and universities

This page is for administrators and website creators of a school, college or university. It suggests a tree, sections
and roles in which departments edit their own content and staff and students see an intranet. The mechanisms are in
[Sections](../../../../guides/sections.md) and [Roles and policies](../../../../guides/roles-and-policies.md).

## Contents

1. [The public tree](#the-public-tree)
2. [Sections](#sections)
3. [User groups](#user-groups)
4. [Roles](#roles)
5. [Checks](#checks)

## The public tree

```text
Home
├── Study with us          admissions, programs, open days, fees
├── Departments            one Department object per department, its pages below
├── Research               (universities) groups, projects, publications
├── Life here              campus, support services, accommodation, clubs
├── News and events
├── About us               governance, policies, reports, jobs, contact
└── Intranet               restricted: staff and students (see below)
```

Audiences first (prospective students, current members, the public), departments second. Visitors who do not know the
institution's structure find programs under **Study with us**; a program has a second location under its department.

## Sections

| Section | Holds | Anonymous reads |
|---|---|---|
| Standard | central public content | yes |
| One per department (`dept_<name>`) | the department's public pages | yes |
| `intranet` | staff and student information | no |
| `intranet_staff` | staff-only information | no |

The intranet sections are not in the Anonymous role's `content/read`, so their content gives "access denied" and is
left out of menus for visitors ([Sections, 13.1](../../../../guides/sections.md#131-a-members-area)).

## User groups

```text
Users
├── Staff
│   ├── <department> editors
│   └── ...
├── Students
├── Central web team
└── Members (public registrations, if any; never staff or students)
```

Accounts for staff and students are usually created by import or by a login handler connected to the institution's
directory, which is development work; manual accounts are realistic only for small schools. Students leave every year:
plan deactivation ([Check user accounts](../../../../features/6.0/user-hygiene.md), **Deactivate and move**).

## Roles

| Role | Policies | Assigned to, with limitation |
|---|---|---|
| Intranet reader | `content/read` | Students and Staff, Section = `intranet`; Staff also `intranet_staff` |
| Department editor | `content/create`, `content/edit`, `content/hide`, `content/versionread`, `content/remove` | each department's editors, Section = the department's section (and its intranet pages, Subtree) |
| Communications | `content/*` limited by Section Standard | central communications |
| Web team | `content/*`, `section/assign`, `state/assign`, `rss/edit`, `infocollector/read` | the web team |
| Administrator | `*/*` | system administrators |

Approval is often asked for admissions and policy pages only: an *Approve* event with **Affected sections** limited to
those ([Workflows](../../../../guides/workflows.md)).

## Checks

- [ ] Logged out: department pages open, intranet pages do not, and the intranet is not in the menu.
- [ ] Logged in as a student: intranet pages open, staff-only pages do not.
- [ ] A department editor cannot edit another department's pages.
- [ ] **Setup > Sections**: each department section shows its editors' role assignment.

## Related pages

- [Schools and universities](README.md)
- [Intranets and department subsites](intranets-and-department-subsites.md)
- [Visitor permissions](../../../creators/website-creators/visitor-permissions.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
