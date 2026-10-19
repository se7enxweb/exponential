---
title: Structure, sections and roles for emergency services
audience: government, admin, website-creator, project-manager
category: public-sector
order: 40
summary: A content tree for an emergency service site with incidents at the top, sections that separate incident content from reviewed content, and an on-call publishing role that never waits for approval.
keywords: content tree, emergency services, sections, roles, on-call, incident publishing, approval exceptions, excluded user groups, editor siteaccess, two-step sign-in
type: help
---
# Structure, sections and roles for emergency services

This page is for administrators and website creators of an emergency service site. The incident area belongs to a
small on-call team that publishes at once; everything else follows normal review. The mechanisms are in
[Sections](../../../../guides/sections.md), [Roles and policies](../../../../guides/roles-and-policies.md) and
[Workflows](../../../../guides/workflows.md).

## Contents

1. [The content tree](#the-content-tree)
2. [Sections](#sections)
3. [Roles](#roles)
4. [Approval and its exceptions](#approval-and-its-exceptions)
5. [Checks](#checks)

## The content tree

```text
Home                       current incidents at the top; the emergency number on every page
├── Incidents              Incident update objects (ongoing and ended)
├── Be prepared            Preparedness guides
├── Stay safe              prevention advice
├── Services               inspections, safety visits, permits
├── Join us                careers, volunteers
├── News
└── About us               stations, reports, plans, contact
```

## Sections

| Section | Holds | Review |
|---|---|---|
| `incidents` | Incidents | none; the audit trail records each publish |
| Standard | everything else | communications review, if wanted |

Add `incidents` to the Anonymous role's `content/read` policy.

## Roles

| Role | Policies | Assigned to |
|---|---|---|
| Incident publisher | `content/create`, `content/edit`, `content/hide`, `content/translate` limited by Section `incidents` and Class Incident update | the on-call team (duty officers, press officers) |
| Content editor | Editor role, Section Standard | communications |
| Web team | `content/*`, `section/assign`, `rss/edit`, `setup/managecache` for clearing caches | the web team |
| Administrator | `*/*` | system administrators |

The **editor siteaccess** offers a simpler interface for occasional publishers
([Logging into the Editor](../../../logging-into-the-editor.md)). Every incident publisher needs a working account at
any hour: two-step sign-in with a method that works when they are away from the office
([Two-step sign-in](../../../../guides/two-factor-login.md)), and a way to reset it at night (an administrator on call
can set the user's *Two-step sign-in* field to *E-mail one-time code*).

## Approval and its exceptions

| Content | Rule |
|---|---|
| Incident updates | outside every *Approve* event (Section `incidents` not listed); a second person reads the published update within minutes, as a procedure |
| Everything else | an *Approve* event on Section Standard if your organization wants review |

An *Approve* event that accidentally covers incidents would hold updates until the workflow cronjob runs after an
approval: test with an incident publisher's account that an update appears at once.

## Checks

- [ ] An incident publisher, signed in on a phone, publishes an update; it appears logged out within the time you set.
- [ ] An incident publisher cannot change the rest of the site.
- [ ] At least two people per shift have a working account.
- [ ] The web team can clear the caches described in [Alerts, availability, maintenance mode and static cache](alerts-availability-maintenance-and-static-cache.md).

## Related pages

- [Emergency services](README.md)
- [Requirements emphasis for emergency services](requirements-emphasis.md)
- [Caches: what each one holds and how to clear it](../../../../guides/caches.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
