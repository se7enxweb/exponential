---
title: "Role design: auditors"
audience: government, admin, project-manager
category: public-sector
order: 90
summary: A read-only role for internal auditors, information security officers and records officers - the audit trail by channel, masked logs, role pages and version history - without any right to change content, settings or the record itself.
keywords: auditor, internal audit, information security officer, read-only role, audit/read, Channel limitation, audit export, setup/logs, masked logs, evidence, accountability, role design
type: help
---
# Role design: auditors

This page is for administrators who must give an internal auditor, an information security officer or a records
officer access to the evidence without any right to change it. Exponential's audit trail is designed for this: two
policies, `audit/read` (optionally limited by channel) and `audit/manage`, and a log viewer that masks personal data
by default ([Audit trail](../../../../features/6.0/audit-trail.md#who-may-look), [Log viewer](../../../../features/6.0/log-viewer.md#who-may-use-it)).

## Contents
1. [What an auditor needs to see](#what-an-auditor-needs-to-see)
2. [The role "Auditor (read only)"](#the-role-auditor-read-only)
3. [Narrower variants](#narrower-variants)
4. [What stays out of reach](#what-stays-out-of-reach)
5. [Handing over evidence](#handing-over-evidence)
6. [Related pages](#related-pages)

## What an auditor needs to see

| Question | Where the answer is |
|---|---|
| Who signed in, who failed, who was refused | audit channel `access` |
| Who published, moved, removed, changed sections or states | audit channel `content` |
| Who changed settings, cleared caches, ran a deploy or maintenance | audit channel `system` |
| Whether the record is intact | the hash chain; "Verify now" needs `audit/manage`, the state is shown with `audit/read` |
| Who holds which role | **Users > Roles and policies**, role pages |
| What a page said on a given date | the version history of the object |

## The role "Auditor (read only)"

| Module | Function | Limitations | Why |
|---|---|---|---|
| user | login | SiteAccess: the administration siteaccess | sign in to the administration |
| audit | read | Channel: `access`, `content`, `system` (add `commerce` if you run a shop) | the **Audit** tab: dashboard, console, event, charts, alerts, export |
| setup | logs | none | **Setup > Log files**, error digest and log health, masked |
| setup | system_info | none | **Setup > System information** (reads, changes nothing) |
| content | read | none, or the areas under audit | see content |
| content | versionread, diff | none, or the areas under audit | read and compare earlier versions |

On roles: the role pages belong to the `role` module, which defines no separate read function, so a policy for it
would also allow changes. Give the auditor an export of the role pages instead, or let an administrator show them;
see [Admin access reviews](admin-access-reviews.md). Likewise the account list (**Check user accounts**, `user/hygiene`)
is not read-only: the same function disables and enables accounts, so it is not in this role.

## Narrower variants

| Auditor | Change |
|---|---|
| Information security officer | `audit/read` limited to Channel `access` and `system`; add `setup/unmasklogs` only for an investigation, and remove it afterwards |
| Records officer | `audit/read` limited to Channel `content`; `content/versionread` |
| External auditor for a fixed period | the same role, assigned to a personal account that is disabled when the engagement ends ([Role design: contractors](role-design-contractors.md)) |

## What stays out of reach

- `audit/manage`: archives, audit settings and **Verify now**. With `[AuditConsoleSettings] ReauthForManage`
  (shipped `disabled`) "Verify now" asks for the password again.
- Any content write, settings, cache or cronjob right.
- `setup/unmasklogs`: without it e-mail and IP addresses, passwords, tokens and session ids stay masked.
- Read access to channels not listed: a user without `audit/read` for a channel does not see it, and a typed URL is
  refused and recorded as `access.permission.refused`.

## Handing over evidence

- **Audit > Export** (`/audit/export`) writes the console's filter as CSV, JSON lines or JSON: **Download now**
  streams it, **Export in the background** prepares a large file that only the requesting user can download.
  CSV exports are themselves recorded in the audit trail.
- On the shell an administrator can run `./console exp:audit verify --allow-root-user` and hand over its output with
  the export, as proof the chain was intact ([Audit trail verification](../monitoring/audit-trail-verification.md)).
- Personal data: the audit truncates addresses, hashes sessions, removes query values and never records passwords
  or tokens ([Audit trail, "Why you want it"](../../../../features/6.0/audit-trail.md#why-you-want-it)). Exports may
  still contain user names; handle them under your data protection rules.

## Related pages
- [Give an auditor read access to the audit trail](../../tasks/people-and-permissions/give-an-auditor-read-access-to-the-audit-trail.md)
- [Export audit records for an auditor](../../tasks/operations/export-audit-records-for-an-auditor.md)
- [Audit alerts](../monitoring/audit-alerts.md)
- [Security and audit guide](../../../../guides/security-and-audit.md#3-read-the-audit-trail-5-minutes)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
