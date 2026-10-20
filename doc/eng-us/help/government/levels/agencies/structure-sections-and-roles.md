---
title: Structure, sections and roles for agencies
audience: government, admin, website-creator, project-manager
category: public-sector
order: 40
summary: An audience-based content tree for an agency site, sections that separate guidance, enforcement and corporate content, and roles for policy owners, legal reviewers, communications and the web team.
keywords: content tree, agency, regulator, sections, roles, legal review, approval workflow, guidance owners, enforcement, object state assignment, NewState
type: help
---
# Structure, sections and roles for agencies

This page is for administrators and website creators of a regulator, inspectorate or executive agency. It suggests a
tree, sections and roles in which guidance passes legal review and its status is set only by those allowed to. The
mechanisms are in [Sections](../../../../guides/sections.md), [Roles and policies](../../../../guides/roles-and-policies.md),
[Workflows](../../../../guides/workflows.md) and [Object states](../../../../guides/object-states.md).

## Contents

1. [The content tree](#the-content-tree)
2. [Sections](#sections)
3. [Roles](#roles)
4. [Review before publishing](#review-before-publishing)
5. [Checks](#checks)

## The content tree

```text
Home
├── For the public            what we do for you, how to complain, warnings
├── For <regulated parties>   guidance, how to comply, forms, deadlines (one folder per audience if needed)
├── Guidance library          every Guidance object, by topic (the second location of guidance listed above)
├── Decisions and enforcement Enforcement notices by year
├── Programs                  Program objects
├── Publications and data     reports, statistics, data exports
├── Consultations
├── News and warnings
└── About us                  board, annual report, budget, jobs, contact
```

A Guidance object lives once in the Guidance library and gets a second location under each audience folder where it
belongs, so it has one canonical address and appears where people look.

## Sections

| Section | Holds | Why separate |
|---|---|---|
| `guidance` | Guidance library and audience folders | legal review applies |
| `enforcement` | Decisions and enforcement | a small, trained team; personal data care |
| `corporate` | About us, publications | communications team |
| Standard | news, the rest | |
| `staging` | prepared releases of guidance packages | invisible until released |

Add the public sections to the Anonymous role's `content/read` policy.

## Roles

| Role | Key policies | Assigned to |
|---|---|---|
| Guidance author | `content/create`, `content/edit`, `content/versionread` limited by Section `guidance` | policy teams |
| Legal reviewer | `content/read`, `content/versionread`, `content/diff` limited by Section `guidance` and `enforcement` | the legal team (approvers) |
| Status keeper | `state/assign` with a `StateGroup_guidance_status` limitation and `NewState` *Superseded*, *Withdrawn* | the guidance owners' lead |
| Enforcement publisher | `content/create`, `content/edit`, `content/hide` limited by Section `enforcement` | the enforcement team |
| Communications | `content/*` limited by Section Standard and `corporate` | communications |
| Web team | `content/*`, `section/assign`, `state/assign`, `rss/edit`, `infocollector/read` | the web team |

`content/diff` lets reviewers use **Compare** on the versions page ([Versions of an object](../../../../guides/content-history.md)).
The `NewState` limitation of `state/assign` decides which states a user may set
([Object states, 8](../../../../guides/object-states.md#8-states-in-roles-and-policies)).

## Review before publishing

A workflow on **Before publishing content** with an *Approve* event:

| Field | Value |
|---|---|
| Affected sections | `guidance`, `enforcement` |
| Affected versions | *All versions* |
| Groups who approve content | Legal reviewers |
| Excluded user groups | Web team (for urgent corrections, with a record kept) |

Reviewers open the request in **My account > Collaboration** (`/collaboration/view/summary`), read the preview and press
**Approve** or **Deny** with a comment. The workflow cronjob publishes approved versions; if it does not run, nothing
appears ([Workflows](../../../../guides/workflows.md)).

## Checks

- [ ] A guidance author's change waits for approval; the legal reviewer sees it in the collaboration inbox.
- [ ] A reviewer can compare the pending version with the published one.
- [ ] Only the status keeper can set *Withdrawn*; others see the state select disabled or without that state.
- [ ] Logged out, a staging release cannot be read.

## Related pages

- [Agencies](README.md)
- [Requirements emphasis for agencies](requirements-emphasis.md)
- [Drafts and pending items](../../../../guides/drafts-and-pending.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
