---
title: Structure, sections and roles for parliaments
audience: government, admin, website-creator, project-manager
category: public-sector
order: 40
summary: A content tree for a parliament site organized by business and members, sections for the official record and for communications, and roles for clerks, committee staff, communications and the web team.
keywords: content tree, parliament, sections, roles, official record, clerks, committee staff, communications, member pages, review, correction
type: help
---
# Structure, sections and roles for parliaments

This page is for administrators and website creators of a parliament or legislative assembly. The official record
(agendas, bills, votes, transcripts) is published by clerks' offices; communications staff publish everything else.
The mechanisms are in [Sections](../../../../guides/sections.md), [Roles and policies](../../../../guides/roles-and-policies.md)
and [Workflows](../../../../guides/workflows.md).

## Contents

1. [The content tree](#the-content-tree)
2. [Sections](#sections)
3. [Roles](#roles)
4. [The official record and corrections](#the-official-record-and-corrections)
5. [Checks](#checks)

## The content tree

```text
Home
├── Members              current members; former members (with end dates)
├── Business             sittings by year and month, agendas, transcripts
│   ├── Bills            Bill objects by session
│   └── Votes            Vote records by year
├── Committees           one folder per committee: members, inquiries, reports
├── Petitions and questions
├── Visit and learn      visiting, education, how parliament works
├── News
└── About                administration, budget, jobs, contact
```

## Sections

| Section | Holds | Who edits |
|---|---|---|
| `record` | Business, Bills, Votes | the clerks' offices |
| `committees` | Committees | committee staff |
| `members` | Members | the members' services office |
| Standard | visit and learn, news, about | communications |

All four are public: add them to the Anonymous role's `content/read` policy.

## Roles

| Role | Policies | Assigned to |
|---|---|---|
| Clerk | `content/create`, `content/edit`, `content/versionread` limited by Section `record`; classes Sitting, Bill, Vote record, File | clerks' offices |
| Committee staff | `content/create`, `content/edit`, `content/hide` limited by Section `committees` | committee secretariats |
| Members' services | `content/create`, `content/edit` limited by Section `members` | the members' services office |
| Communications | `content/*` limited by Section Standard | communications |
| Record corrections | `content/versionremove`, `content/remove` limited by Section `record` | one or two senior clerks |
| Web team | `content/*`, `section/assign`, `rss/edit` | the web team |

Members usually do not edit the parliament's site. If members may update parts of their own page, give them
`content/edit` with an Owner limitation on their own object only, and keep the fields that show official roles in a
place they cannot edit.

## The official record and corrections

| Rule | How |
|---|---|
| Record items are published by clerks without approval delay | no *Approve* event on Section `record`, or the clerks' group in **Excluded user groups** |
| Corrections leave a trace | a new version with a correction note; the earlier version stays in the version history |
| Removal of versions only for legal reasons (for example unlawfully published personal data) | the Record corrections role, used under a written procedure |
| Every action attributable | the audit trail, on by default ([Audit trail](../../../../features/6.0/audit-trail.md)) |

Communications content can go through an *Approve* event limited to Section Standard.

## Checks

- [ ] A clerk can publish a vote record at once; a communications editor cannot edit it.
- [ ] Committee staff can edit their committee's pages only.
- [ ] A former member's page still opens and lists the votes related to it.
- [ ] **Setup > Audit** attributes each record item to the clerk who published it.

## Related pages

- [Parliaments](README.md)
- [Requirements emphasis for parliaments](requirements-emphasis.md)
- [Versions of an object](../../../../guides/content-history.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
