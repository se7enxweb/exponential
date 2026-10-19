---
title: Contractor activity and the audit trail
audience: government, admin
category: public-sector
order: 80
summary: How to see what external users did in Exponential: sign-ins, refused views, content changes and, if switched on, downloads, filtered by login or user in the audit console or with exp:audit, and how to export it for a contract review or an incident.
keywords: audit trail, contractor activity, external users, sign-in records, access channel, content channel, read channel, downloads, exp:audit, export, incident
type: help
---
# Contractor activity and the audit trail

This page is for administrators and security officers. The audit trail records security-relevant events, on by
default, in a tamper-evident log ([Audit trail](../../../../features/6.0/audit-trail.md)).

## Contents

1. [What is recorded for external users](#what-is-recorded-for-external-users)
2. [Finding one person's activity](#finding-one-persons-activity)
3. [Downloads](#downloads)
4. [Exporting](#exporting)

## What is recorded for external users

| Channel | Examples |
|---|---|
| `access` | sign-in, failed sign-in, refused view, account lock, role assignment, password reset |
| `content` | publish, move, remove, hide, versions, for content they edit or upload |
| `read` | sampled node views, searches and downloads, only when reads are switched on |

## Finding one person's activity

In **Audit > Console** filter by **Login** or **User ID**, **Channel** and dates. On the command line:

```bash
./console exp:audit search --login=<login> --from=2026-10-01 --to=2026-10-31
./console exp:audit search --user=<user id> --channel=access
```

## Downloads

Downloads are recorded only when the `read` channel is switched on, and then sampled; its default retention is shorter
(30 days live, 90 archived). If you must know every download of a confidential extranet file, switch reads on for that
period and check the settings in the audit guide first ([the audit guide](../../../../bc/6.0/audit.md)).

## Exporting

`./console exp:audit export` with the same filters writes CSV or JSON lines; the console's export page offers
**Download now** and background exports. Verify the chain before handing records over: `./console exp:audit verify`.
Exports contain personal data; handle them under your rules.

## Related pages

- [The audit trail for who changed what](../../transparency-and-foi/the-audit-trail-for-who-changed-what.md)
- [Ending access](ending-access.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
