---
title: Structure, sections and roles for public health
audience: government, admin, website-creator, project-manager
category: public-sector
order: 40
summary: A content tree for a public health site by topic and audience, sections for advice and alerts, clinical review before publishing, and an on-call alert team that publishes without delay.
keywords: content tree, public health, sections, roles, clinical review, approval, alert team, on-call, translators, professionals area, excluded user groups
type: help
---
# Structure, sections and roles for public health

This page is for administrators and website creators of a public health site. Advice goes through clinical review;
alerts go out at once from an on-call team. The mechanisms are in [Sections](../../../../guides/sections.md),
[Roles and policies](../../../../guides/roles-and-policies.md) and [Workflows](../../../../guides/workflows.md).

## Contents

1. [The content tree](#the-content-tree)
2. [Sections](#sections)
3. [Roles](#roles)
4. [Review and its exceptions](#review-and-its-exceptions)
5. [Checks](#checks)

## The content tree

```text
Home                     shows current alerts at the top
├── Alerts               Alert objects (current and ended)
├── Health A to Z        Health topic objects
├── Staying healthy      prevention, vaccination, screening
├── Find a service       Clinic objects, booking links
├── For professionals    guidance, protocols, reporting duties
├── Data and reports
├── News and media
└── About us
```

## Sections

| Section | Holds | Review |
|---|---|---|
| `advice` | Health A to Z, Staying healthy, For professionals | clinical review |
| `alerts` | Alerts | none (on-call team), audit only |
| Standard | Find a service, news, about, data | communications review if wanted |

All public: add `advice` and `alerts` to the Anonymous role's `content/read` policy. Professional guidance is usually
public; restrict only what must be restricted, with a section Anonymous cannot read.

## Roles

| Role | Policies | Assigned to |
|---|---|---|
| Health writer | `content/create`, `content/edit`, `content/versionread` limited by Section `advice` | content writers |
| Clinical reviewer | `content/read`, `content/versionread`, `content/diff` for Section `advice` | clinicians who sign off (approvers) |
| Alert publisher | `content/create`, `content/edit`, `content/hide` limited by Section `alerts`, plus `content/translate` | the on-call alert team |
| Translator | `content/translate`, `content/edit` limited by Language (their languages) | translators |
| Communications | `content/*` limited by Section Standard | communications |
| Web team | `content/*`, `section/assign`, `rss/edit` | the web team |

The **Language** limitation keeps translators to their languages ([Content languages](../../../../guides/content-languages.md)).

## Review and its exceptions

| Content | *Approve* event settings |
|---|---|
| Health advice | **Affected sections** `advice`; **Groups who approve content** Clinical reviewers; **Affected languages** all, or only the source language if translations are checked by translators |
| Alerts | not affected (Section `alerts` not listed); every publish recorded in the audit trail |
| Corrections of advice by the web team | the web team in **Excluded user groups** only if your clinical governance allows it |

Approved advice is published by the workflow cronjob: check **Setup > Workflow processes** shows it scheduled
(*Every 5 minutes* in the frequent part is typical), or reviewed advice waits.

## Checks

- [ ] An alert publisher publishes an alert and it appears at once on the front page.
- [ ] A health writer's change waits for a clinical reviewer, who sees it in **My account > Collaboration**.
- [ ] A translator can translate into their languages only.
- [ ] Every alert publisher has two-step sign-in, and at least two people can publish alerts at any hour.

## Related pages

- [Public health](README.md)
- [Requirements emphasis for public health](requirements-emphasis.md)
- [Drafts and pending items](../../../../guides/drafts-and-pending.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
